<?php

return [
    'tmn_code'    => env('vnp_TmnCode', ''),
    'hash_secret' => env('vnp_HashSecret', ''),
    'url'         => env('vnp_Url', 'https://sandbox.vnpayment.vn/paymentv2/vpcpay.html'),
    'return_url'  => env('vnp_Returnurl', ''),
    'api_url'     => env('apiUrl', ''),
];
?>