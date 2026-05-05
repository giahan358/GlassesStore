<?php

namespace App\Bus;

use App\Dao\PaymentTransaction_DAO;
use Carbon\Carbon;

class PaymentTransaction_BUS
{
    protected $dao;

    public function __construct()
    {
        $this->dao = new PaymentTransaction_DAO();
    }

    /**
     * Lưu giao dịch thành công từ VNPay
     */
    /**
     * Lưu giao dịch thành công từ VNPay
     */
    public function saveVnpaySuccess($vnpayData, $orderId)
    {
        $existing = $this->dao->findByBankTransactionNo($vnpayData['vnp_TransactionNo']);
        if ($existing) {
            return $existing;
        }

        $paidAt = isset($vnpayData['vnp_PayDate']) 
            ? \Carbon\Carbon::createFromFormat('YmdHis', $vnpayData['vnp_PayDate'])->format('Y-m-d H:i:s')
            : now()->format('Y-m-d H:i:s');

        $amount = $vnpayData['vnp_Amount'] / 100;

        $data = [
            'order_id'                => $orderId, 
            'provider'                => 'vnpay',
            'transaction_type'        => 'payment',
            'provider_transaction_id' => $vnpayData['vnp_TransactionNo'],
            'provider_reference_no'   => $vnpayData['vnp_TxnRef'],
            'bank_code'               => $vnpayData['vnp_BankCode'] ?? null,
            'bank_transaction_no'     => $vnpayData['vnp_BankTranNo'] ?? null,
            'amount'                  => $amount,
            'currency_code'           => 'VND',
            'gateway_fee'             => 0.00,
            'net_amount'              => $amount,
            'result_code'             => $vnpayData['vnp_ResponseCode'],
            'result_message'          => $this->getVnpayMessage($vnpayData['vnp_ResponseCode']),
            'is_verified'             => 1,
            'verified_at'             => now(),
            'paid_at'                 => $paidAt,
            'raw_signature'           => $vnpayData['vnp_SecureHash'] ?? null,
        ];
        return $this->dao->addModel($data);
    }
    /**
     * Giải mã thông báo từ mã phản hồi VNPay
     */
    private function getVnpayMessage($code)
    {
        $messages = [
            '00' => 'Giao dịch thành công',
            '07' => 'Trừ tiền thành công. Giao dịch bị nghi ngờ (liên quan tới lừa đảo, giao dịch bất thường).',
            '09' => 'Giao dịch không thành công do: Thẻ/Tài khoản của khách hàng chưa đăng ký dịch vụ InternetBanking tại ngân hàng.',
            '10' => 'Giao dịch không thành công do: Khách hàng xác thực thông tin thẻ/tài khoản không đúng quá 3 lần',
            '11' => 'Giao dịch không thành công do: Đã hết hạn chờ thanh toán.',
            '24' => 'Giao dịch không thành công do: Khách hàng hủy giao dịch',
            // ... thêm các mã khác nếu cần
        ];

        return $messages[$code] ?? 'Lỗi không xác định';
    }
}