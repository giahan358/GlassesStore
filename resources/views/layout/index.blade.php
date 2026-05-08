<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Trang chính</title>
    <meta http-equiv="Content-Security-Policy" content="upgrade-insecure-requests">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
     <link rel="stylesheet" href="{{ asset('css/bootstrap.min.css') }}">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="{{ asset('css/client/include/navbar.css') }}">
    <link rel="stylesheet" href="{{ asset('css/client/include/footer.css') }}">
    <link rel="stylesheet" href="{{ asset('css/client/Login-Register.css') }}">
    <link rel="stylesheet" href="{{ asset('css/client/HomePageClient.css') }}">
    <link rel="stylesheet" href="{{ asset('css/client/AcctInfoOH.css') }}">
    @vite(['resources/css/admin/admin.css'])
</head>
<body>
    <div class="wrapper">
        @include('admin.includes.navbar')
        <div id="main-content">
            <?php

use Illuminate\Support\Facades\View;
                echo View::make('client.index', [
                    'listSP' => $listSP,
                    'listLSP' => $listLSP,
                    'listHang' => $listHang,
                    'tmp' => $tmp,
                    'current_page' => $current_page,
                    'total_page' => $total_page,
                    'isLogin' => $isLogin,
                    'user' => $user,
                    'top4Product' => $top4Product,
                    'sanPham' => $sanPham
                ])->render();
            ?>
        </div>
    </div>
    
</body>
</html>
