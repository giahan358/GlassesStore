<?php

namespace App\Bus;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Bus\HoaDon_BUS;

class PaymentRefund_BUS
{
    /**
     * Gọi API Hoàn tiền VNPay
     * 
     * @param string $orderId Mã đơn hàng gốc (vd: DH12345_20260501) tương ứng vnp_TxnRef
     * @param int $amount Số tiền cần hoàn (VNĐ)
     * @param string $vnpayTransactionNo Mã giao dịch của VNPay (Lấy từ bảng payment_transactions)
     * @param string $payDate Ngày thanh toán của đơn gốc (Định dạng: YmdHis)
     * @param string $createBy Người thực hiện hoàn tiền (Tên admin)
     * @return array Kết quả trả về từ VNPay
     */
    public function callRefundApi($orderId, $amount, $vnpayTransactionNo, $payDate, $createBy = 'Admin')
    {
        $vnp_TmnCode = config('vnpay.tmn_code');
        $vnp_HashSecret = config('vnpay.hash_secret');
        $vnp_ApiUrl = config('vnpay.api_url'); // Link https://sandbox.vnpayment.vn/merchant_webapi/api/transaction

        // 1. Khởi tạo các tham số bắt buộc
        $vnp_RequestId = rand(1, 10000) . time(); // Mã yêu cầu hoàn tiền (Unique ngẫu nhiên)
        $vnp_Version = '2.1.0';
        $vnp_Command = 'refund';
        $vnp_TransactionType = '02'; // '02' là Hoàn toàn phần, '03' là Hoàn một phần
        $vnp_TxnRef = $orderId; 
        $vnp_Amount = $amount * 100; // Nhớ nhân 100 theo chuẩn VNPay
        $vnp_OrderInfo = "Hoan tien cho don hang " . $orderId;
        $vnp_TransactionNo = $vnpayTransactionNo; 
        $vnp_TransactionDate = $payDate; // CỰC KỲ QUAN TRỌNG: Đây là thời gian khách bấm thanh toán đơn gốc
        $vnp_CreateDate = date('YmdHis'); // Thời gian bấm nút hoàn tiền lúc này
        $vnp_IpAddr = request()->ip(); // IP của máy chủ hoặc người đang thao tác

        // 2. Tạo chữ ký bảo mật (Checksum)
        // LƯU Ý: Khác với IPN, Hash của API Hoàn tiền ghép chuỗi bằng dấu "|" chứ không dùng ksort()
        $hash_data = implode('|', [
            $vnp_RequestId,
            $vnp_Version,
            $vnp_Command,
            $vnp_TmnCode,
            $vnp_TransactionType,
            $vnp_TxnRef,
            $vnp_Amount,
            $vnp_TransactionNo,
            $vnp_TransactionDate,
            $createBy,
            $vnp_CreateDate,
            $vnp_IpAddr,
            $vnp_OrderInfo
        ]);

        $vnp_SecureHash = hash_hmac('sha512', $hash_data, $vnp_HashSecret);

        // 3. Gom dữ liệu thành mảng JSON
        $data = [
            "vnp_RequestId" => $vnp_RequestId,
            "vnp_Version" => $vnp_Version,
            "vnp_Command" => $vnp_Command,
            "vnp_TmnCode" => $vnp_TmnCode,
            "vnp_TransactionType" => $vnp_TransactionType,
            "vnp_TxnRef" => $vnp_TxnRef,
            "vnp_Amount" => $vnp_Amount,
            "vnp_OrderInfo" => $vnp_OrderInfo,
            "vnp_TransactionNo" => $vnp_TransactionNo,
            "vnp_TransactionDate" => $vnp_TransactionDate,
            "vnp_CreateBy" => $createBy,
            "vnp_CreateDate" => $vnp_CreateDate,
            "vnp_IpAddr" => $vnp_IpAddr,
            "vnp_SecureHash" => $vnp_SecureHash
        ];

        // 4. Gửi Request POST sang VNPay
        try {
            $response = Http::withHeaders([
                'Content-Type' => 'application/json'
            ])->post($vnp_ApiUrl, $data);

            $result = $response->json();
            if ($result === null) {
                Log::info('--- VNPAY TRẢ VỀ LỖI (KHÔNG PHẢI JSON) ---', ['raw_body' => $response->body()]);
                return [
                    'success' => false,
                    'message' => 'Lỗi kết nối: VNPAY không trả về dữ liệu JSON hợp lệ.'
                ];
            }

            Log::info('--- KẾT QUẢ HOÀN TIỀN VNPAY ---', $result);

            // Kiểm tra kết quả
            if (isset($result['vnp_ResponseCode']) && $result['vnp_ResponseCode'] == '00') {
                $parts = explode('_', $orderId); // Kết quả: ['DH91', '20260504182800']
                $idHoaDon = str_replace('DH', '', $parts[0]);
                $hoaDonBUS = app(\App\Bus\HoaDon_BUS::class);
                $hd = $hoaDonBUS->getModelById($idHoaDon);
                $hd->setTrangThai(\App\Enum\HoaDonEnum::REFUNDED);
                $hoaDonBUS->updateModel($hd);
                $hoaDonBUS->hoanKho($idHoaDon);
                
                return [
                    'success' => true,
                    'message' => 'Hoàn tiền thành công!',
                    'data' => $result
                ];
            } else {
                return [
                    'success' => false,
                    'message' => 'Lỗi từ VNPAY: ' . ($result['vnp_Message'] ?? 'Không xác định'),
                    'code' => $result['vnp_ResponseCode'] ?? 'Unknown'
                ];
            }

        } catch (\Exception $e) {
            Log::error('Lỗi gọi API Hoàn tiền: ' . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Lỗi kết nối máy chủ'
            ];
        }
    }
}