<?php

namespace App\Bus;

use App\Dao\PaymentGatewayLog_DAO;

class PaymentGatewayLog_BUS
{
    protected $dao;

    // Laravel sẽ tự động tìm và nạp class DAO này vào mà không cần bạn phải dùng chữ 'new'
    public function __construct(PaymentGatewayLog_DAO $dao)
    {
        $this->dao = $dao;
    }

    public function logIPNReceive($orderId, $attemptId, $vnpayData, $request, $isValidSignature)
    {
        return $this->dao->addModel([
            'payment_attempt_id' => $attemptId, // BỔ SUNG DÒNG NÀY ĐỂ LIÊN KẾT DỮ LIỆU
            'order_id'           => $orderId,
            'provider'           => 'vnpay',
            'log_type'           => 'ipn_webhook', 
            'http_method'        => $request->method(), 
            'endpoint'           => $request->url(), // Đã dùng url() rất chuẩn!
            'payload_json'       => $vnpayData,
            'note'               => 'Nhận webhook IPN từ VNPay',
            'is_signature_valid' => $isValidSignature ? 1 : 0,
            'response_code'      => $vnpayData['vnp_ResponseCode'] ?? null, // Lưu mã phản hồi nếu có
        ]);
    }

    // HÀM 2: GHI LOG CHIỀU GỬI ĐI (Bỏ vào hàm createPayment lúc tạo URL)
    public function logCreateRequest($orderId, $attemptId, $vnpayUrl, $requestData)
    {
        // Lấy phần đầu của URL (trước dấu ?), bỏ phần tham số dài ngoằng đi
        $baseUrl = explode('?', $vnpayUrl)[0];

        return $this->dao->addModel([
            'payment_attempt_id' => $attemptId,
            'order_id'      => $orderId,
            'provider'      => 'vnpay',
            'log_type'      => 'create_request', // Đánh dấu đây là log gửi đi
            'http_method'   => 'GET', 
            'endpoint'      => $baseUrl, // Lưu lại cái URL VNPay mà bạn vừa tạo
            'payload_json'  => $requestData, // Lưu lại mảng params bạn dùng để tạo URL
            'note'          => 'Tạo URL redirect khách hàng sang VNPay'
        ]);
    }
}