<?php

namespace App\Jobs;

use App\Models\HoaDon;
use App\Bus\HoaDon_BUS;
use App\Dao\HoaDon_DAO;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redis;

class CheckVnpayPaymentStatus implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $orderId;

    public function __construct($orderId)
    {
        $this->orderId = $orderId;
    }

    public function handle()
    {
        // 1. Kiểm tra trạng thái trong DB cục bộ trước
        // Nếu đã thanh toán (Paid) hoặc đã hủy (Canceled) thì không làm gì cả
        $order = app(\App\BUS\HoaDon_BUS::class)->getModelById($this->orderId);
        Log::info("Kiểm tra đơn hàng ID: " . $this->orderId);
        Log::info("Dữ liệu đơn hàng:". $order->getOrderCode());
        if (!$order) {
            Log::error("LỖI: Không tìm thấy đơn hàng trong database!");
            return;
        }
        Log::info("Trang thai don hang: " . $order->getTrangThai()->value);
        if ($order->getTrangThai()->value != 'PENDING') {
            return;
        }

        // 2. Gọi API QueryDR của VNPAY để kiểm tra trạng thái thực tế
        $vnpayData = $this->queryVnpayStatus($order);

        if ($vnpayData && isset($vnpayData['vnp_ResponseCode'])) {
            Log::info("kết quả check chữ ký: " . ($this->verifyQueryDRHash($vnpayData) ? "Hợp lệ" : "Không hợp lệ"));
            // CHỈ LẤY TransactionStatus KHI NÓ TỒN TẠI
            if ($this->verifyQueryDRHash($vnpayData)) {
                
                $status = $vnpayData['vnp_TransactionStatus'] ?? null;
                Log::info("Status từ VNPAY: " . $status);
                if ($status === '00') {
                    $order->setTrangThai(\App\Enum\HoaDonEnum::PAID);
                    app(\App\Bus\HoaDon_BUS::class)->updateModel($order);
                    app(\App\Bus\Payment_BUS::class)->updatePaymentAttemptStatus($vnpayData['vnp_TxnRef'], $status);
                } else {
                    $order->setTrangThai(\App\Enum\HoaDonEnum::EXPIRED);
                    app(\App\Bus\HoaDon_BUS::class)->updateModel($order);
                    app(\App\Bus\Payment_BUS::class)->updatePaymentAttemptStatus($vnpayData['vnp_TxnRef'], $status);
                    app(\App\Bus\HoaDon_BUS::class)->hoanKho($order->getId());
                    }
            } else {
                // Nếu không có TransactionStatus, log lại lỗi từ VNPAY
                Log::warning("VNPAY không trả về Status. Response Code: " . ($vnpayData['vnp_ResponseCode'] ?? 'Unknown'));
                Log::warning("Thông điệp từ VNPAY: " . ($vnpayData['vnp_Message'] ?? 'No message'));
            }
        }
    }

    private function queryVnpayStatus($order)
{
    $vnp_TmnCode = config('vnpay.tmn_code');
    $vnp_HashSecret = config('vnpay.hash_secret');
    $vnp_ApiUrl = config('vnpay.api_url');

    // 1. Chuẩn bị dữ liệu (Dùng TxnRef để lấy lại TransactionDate gốc như đã thảo luận)
    $vnp_TxnRef = $order->getOrderCode();
    $parts = explode('_', $vnp_TxnRef);
    $vnp_TransactionDate = end($parts); 

    $vnp_RequestId = (string)str_replace('.', '', microtime(true));
    $vnp_CreateDate = date('YmdHis');
    $vnp_IpAddr = request()->ip() ?? '127.0.0.1';
    $vnp_OrderInfo = "Kiem tra giao dich don hang " . $order->getId();

    $datarq = [
        "vnp_RequestId"      => $vnp_RequestId,
        "vnp_Version"        => "2.1.0",
        "vnp_Command"        => "querydr",
        "vnp_TmnCode"        => $vnp_TmnCode,
        "vnp_TxnRef"         => $vnp_TxnRef,
        "vnp_OrderInfo"      => $vnp_OrderInfo,
        "vnp_TransactionDate"=> $vnp_TransactionDate,
        "vnp_CreateDate"     => $vnp_CreateDate,
        "vnp_IpAddr"         => $vnp_IpAddr
    ];

    // 2. Tạo chuỗi Hash theo đúng format của VNPay mẫu (%s|%s...)
    $format = '%s|%s|%s|%s|%s|%s|%s|%s|%s';
    $dataHash = sprintf(
        $format,
        $datarq['vnp_RequestId'],
        $datarq['vnp_Version'],
        $datarq['vnp_Command'],
        $datarq['vnp_TmnCode'],
        $datarq['vnp_TxnRef'],
        $datarq['vnp_TransactionDate'],
        $datarq['vnp_CreateDate'],
        $datarq['vnp_IpAddr'],
        $datarq['vnp_OrderInfo']
    );

    // 3. Tạo chữ ký SHA512
    $datarq["vnp_SecureHash"] = hash_hmac('SHA512', $dataHash, $vnp_HashSecret);

    // 4. Gửi API bằng Laravel Http (thay thế cURL thủ công)
    try {
        $response = Http::withHeaders(['Content-Type' => 'application/json'])
                        ->post($vnp_ApiUrl, $datarq);
        
        Log::info("VNPay Query Request: " . $dataHash);
        return $response->json();
    } catch (\Exception $e) {
        Log::error("VNPay QueryDR Connection Error: " . $e->getMessage());
        return null;
    }
}

    private function verifyQueryDRHash($vnpayData) 
{
    $vnp_HashSecret = config('vnpay.hash_secret');
    $vnp_SecureHash = $vnpayData['vnp_SecureHash'] ?? '';

    // DANH SÁCH 15 TRƯỜNG THEO ĐÚNG QUY TẮC BẠN VỪA TRÍCH DẪN
    $fields = [
        'vnp_ResponseId',      // 1
        'vnp_Command',         // 2
        'vnp_ResponseCode',    // 3
        'vnp_Message',         // 4
        'vnp_TmnCode',         // 5
        'vnp_TxnRef',          // 6
        'vnp_Amount',          // 7
        'vnp_BankCode',        // 8
        'vnp_PayDate',         // 9
        'vnp_TransactionNo',   // 10
        'vnp_TransactionType', // 11
        'vnp_TransactionStatus',// 12
        'vnp_OrderInfo',       // 13
        'vnp_PromotionCode',   // 14
        'vnp_PromotionAmount'  // 15
    ];

    $hashDataArray = [];
    foreach ($fields as $field) {
        // Lấy giá trị từ VNPAY, nếu không có thì để rỗng
        $val = $vnpayData[$field] ?? '';
        
        // Xử lý riêng cho vnp_Amount nếu có phần thập phân
        if ($field === 'vnp_Amount') {
            $val = (string)explode('.', $val)[0];
        }
        
        $hashDataArray[] = (string)$val;
    }

    // Nối 15 trường -> PHẢI CÓ ĐÚNG 14 DẤU GẠCH ĐỨNG |
    $dataHash = implode('|', $hashDataArray);

    // Tạo chữ ký đối chứng
    $checkHash = hash_hmac('SHA512', $dataHash, $vnp_HashSecret);

    // So sánh không phân biệt hoa thường
    $isValid = strcasecmp($checkHash, $vnp_SecureHash) === 0;

    // if (!$isValid) {
    //     Log::error("--- SAI CHỮ KÝ THEO CHUẨN 15 TRƯỜNG ---");
    //     Log::error("Chuỗi nối (15 trường): " . $dataHash);
    //     Log::error("Số lượng dấu gạch đứng: " . substr_count($dataHash, '|'));
    //     Log::error("Chữ ký VNPay: " . $vnp_SecureHash);
    //     Log::error("Chữ ký mình tính: " . $checkHash);
    // }

    return $isValid;
}
}