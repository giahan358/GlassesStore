<?php

namespace App\Dao;

use App\Models\PaymentTransaction;

class PaymentTransaction_DAO
{
    public function addModel($data)
    {
        return PaymentTransaction::create($data);
    }

    /**
     * Kiểm tra xem mã giao dịch ngân hàng đã tồn tại chưa (để tránh trùng)
     */
    public function findByBankTransactionNo($bankTransNo)
    {
        return PaymentTransaction::where('bank_transaction_no', $bankTransNo)->first();
    }

    /**
     * Lấy danh sách giao dịch theo ID đơn hàng
     */
    public function getByOrderId($orderId)
    {
        return PaymentTransaction::where('order_id', $orderId)->get();
    }
}