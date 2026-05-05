<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Lịch sử đơn hàng</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="{{ asset('css/client/include/navbar.css') }}">
    <link rel="stylesheet" href="{{ asset('css/client/include/footer.css') }}">
    <style>
    body {
        display: flex;
        flex-direction: column;
        min-height: 100vh;
        margin: 0;
    }
    main {
        flex: 1 0 auto;
        display: flex;
        flex-direction: column;
        justify-content: center; 
        align-items: center;
        padding: 10px 0;
    }
    .footer {
        flex-shrink: 0;
        background-color: rgb(57, 193, 195);
        padding: 1rem;
        text-align: center;
        font-size: 0.8rem;
        color: white;
    }
    .footer a {
        color: rgb(218, 226, 233);
        margin: 0 0.5rem;
        text-decoration: none;
    }
    .footer a:hover {
        color: #000;
    }
    .navbar-custom {
        background-color: #f8f9fa;
        padding: 0.5rem 1rem;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    }
    .top-nav {
        display: flex;
        justify-content: space-between;
        align-items: center;
        width: 100%;
        padding: 5px 0.5rem;
    }
    .top-nav p {
        margin: 0;
        color: #55d5d2;
        font-size: 16px;
        font-weight: 600;
    }
    .list-top-nav {
        display: flex;
        gap: 10px;
        margin: 0;
        padding: 0;
        list-style: none;
    }
    .list-top-nav .nav-item a {
        color: white;
        text-decoration: none;
    }
    .list-top-nav .nav-item:hover a {
        color: #ccc;
    }
    .product-tab {
        display: flex;
        justify-content: center;
        padding: 0.5rem 1rem;
        border-bottom: 1px solid rgba(222, 226, 230, 0.1);
        width: 100%;
        margin-top: 10px;
        margin-bottom: 20px;
    }
    .product-tab span {
        font-size: 23px;
        color: #6c757d;
        cursor: pointer;
    }
    .product-tab span.active {
        color: #000;
        font-weight: bold;
    }
    .product-tab span:hover {
        color: #000;
    }
    .filter-section {
        display: flex;
        justify-content: flex-end;
        width: 100%;
        padding: 0 1rem;
        margin-bottom: 20px;
        gap: 20px;
        align-items: center;
    }
    .filter-section .date-filter,
    .filter-section .sort-filter,
    .filter-section .search-filter,
    .filter-section .refresh-btn {
        display: flex;
        gap: 10px;
        align-items: center;
    }
    .search-filter form {
        display: flex;
        align-items: center;
        gap: 8px;
    }
    .search-filter label {
        margin-bottom: 0;
        white-space: nowrap;
        font-size: 0.9rem;
        color: black;
    }
    .search-filter input.form-control {
        height: 34px;
        font-size: 0.9rem;
        width: 200px;
    }
    .search-filter button {
        height: 34px;
        padding: 0 15px;
        font-size: 0.9rem;
        background-color: rgb(32, 186, 207);
        color: #fff;
        border: none;
        border-radius: 4px;
        transition: background-color 0.3s;
    }
    .search-filter button:hover {
        background-color: rgb(9, 117, 167);
    }
    .pagination .page-link {
        color: #6c757d;
    }
    .pagination .page-item.active .page-link {
        background-color: #007bff;
        border-color: #007bff;
    }
    .alert {
        position: fixed;
        top: 20px;
        right: 20px;
        z-index: 1050;
        max-width: 400px;
    }
    .table-custom {
        width: 100%;
        padding: 0 1rem;
    }
    .header-row {
        background-color: #f8f9fa;
        font-weight: bold;
        padding: 0.5rem;
        display: flex;
        justify-content: space-between;
        align-items: center;
        border-bottom: 2px solid #dee2e6;
    }
    .data-row {
        padding: 0.5rem 0;
        border-bottom: 1px solid #dee2e6;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
    .data-row:last-child {
        border-bottom: none;
    }
    .col-1 { width: 7%; text-align: center; }
    .col-2 { width: 14%; text-align: center; }
    .col-3 { width: 18%; text-align: center; }
    .col-4 { width: 14%; text-align: center; }
    .btn-view-order {
        display: inline-block;
        padding: 5px 10px;
        border: 1px solid #007bff;
        color: #007bff;
        text-decoration: none;
        border-radius: 4px;
        transition: background-color 0.3s, color 0.3s;
    }
    .btn-view-order:hover {
        background-color: #007bff;
        color: #fff;
    }
    .btn-refresh {
        padding: 5px 10px;
        border: 2px solid rgb(40, 167, 165);
        color: rgb(31, 129, 132);
        background-color: #fff;
        border-radius: 4px;
        text-decoration: none;
        transition: background-color 0.3s, color 0.3s;
    }
    .btn-refresh:hover {
        background-color: #55d5d2;
        color: #fff;
    }
    @media (max-width: 768px) {
        .top-nav {
            flex-direction: column;
            align-items: center;
            gap: 10px;
        }
        .list-top-nav {
            flex-wrap: wrap;
            justify-content: center;
        }
        .filter-section {
            flex-direction: column;
            align-items: flex-start;
            gap: 10px;
            padding: 0 0.5rem;
        }
        .search-filter input.form-control {
            width: 100%;
            max-width: 250px;
        }
        .search-filter, .date-filter, .sort-filter, .refresh-btn {
            width: 100%;
            justify-content: flex-start;
        }
        .table-custom {
            padding: 0 0.5rem;
            overflow-x: auto;
        }
        .header-row, .data-row {
            flex-direction: row;
            min-width: 600px;
        }
        .col-1, .col-2, .col-3, .col-4 {
            font-size: 0.8rem;
            padding: 0 5px;
        }
        .modal-dialog {
            margin: 0.5rem;
        }
        .modal-content .table {
            font-size: 0.8rem;
            overflow-x: auto;
        }
        .modal-content .table th, .modal-content .table td {
            padding: 0.5rem;
        }
        .footer {
            font-size: 0.7rem;
            padding: 0.5rem;
        }
    }
    @media (max-width: 576px) {
        .search-filter input.form-control {
            max-width: 200px;
        }
        .search-filter button, .btn-refresh {
            padding: 5px 10px;
            font-size: 0.8rem;
        }
        .col-1, .col-2, .col-3, .col-4 {
            font-size: 0.75rem;
        }
        .modal-content .table {
            font-size: 0.75rem;
        }
    }
    </style>
</head>
<body>
    <?php
        use App\Bus\HoaDon_BUS;
        use App\Enum\HoaDonEnum;
    ?>
    @if(session('error')) 
        <div class="alert alert-danger" role="alert">
            {{session('error')}}
        </div>
    @endif
    <header>
        <div class="text-white" id="navbar-ctn">
            <div class="top-nav">
                <ul class="list-top-nav d-flex ms-auto gap-2">
                    @if($isLogin)
                        @if($user->getIdQuyen()->getId() == 1 || $user->getIdQuyen()->getId() == 2)
                            <li class="nav-item px-3 py-1 bg-secondary text-white fw-medium rounded-pill" id="tracuudonhang"><a href="/admin">Trang quản trị</a></li>
                        @endif
                        <li class="nav-item px-3 py-1 bg-secondary text-white fw-medium rounded-pill" id="trangchu"><a href="/index">Trang Chủ</a></li>
                        <li class="nav-item px-3 py-1 bg-secondary text-white fw-medium rounded-pill" id="userDropdownBtn" style="position: relative; cursor: pointer;">
                            {{ $user->getTenTK() }}
                            <div id="userDropdownMenu" style="display: none; width: 150px; position: absolute; right: 0; background: white; border: 1px solid #ccc; padding: 10px; z-index: 999; border-radius: 5px;">
                                <form action="{{ route('logout') }}" method="POST" id="logout-form">
                                    @csrf
                                    <button type="submit" class="btn btn-danger btn-sm" style="height: 40px; width: 120px; margin: auto;">Đăng xuất</button>
                                </form>
                            </div>
                        </li>
                    @else
                        <li class="nav-item px-3 py-1 bg-secondary text-white fw-medium rounded-pill" id="trangchu"><a href="/index">Trang Chủ</a></li>
                        <li class="nav-item px-3 py-1 bg-secondary text-white fw-medium rounded-pill" id="taikhoan"><a href="/login">Đăng nhập</a></li>
                    @endif
                </ul>
            </div>
        </div>
    </header>

    <main>
        <div class="container">
            @if($error)
                <div class="alert alert-danger alert-dismissible fade show" role="alert" id="errorAlert">
                    {{ $error }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert" id="successAlert">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            <div class="product-tab">
               <!-- <span class="active">Sản phẩm đã mua</span>-->
                 <h1 style="font-size:40px">Lịch sử đơn hàng</h1>
            </div>

            <div class="filter-section">
                <div class="search-filter">
                    <form action="{{ route('order.history') }}" method="get" id="search-form">
                        <label for="search-keyword">Tìm kiếm:</label>
                        <input class="rounded p-1 shadow border border-0" type="text" id="search-keyword" name="keyword" value="{{ request('keyword') }}" placeholder="Nhập tên sản phẩm" class="form-control">
                        <button type="submit">Tìm</button>
                        <input type="hidden" name="filter_date" value="{{ request('filter_date') }}">
                        <input type="hidden" name="sort_order" value="{{ request('sort_order', 'desc') }}">
                        <input type="hidden" name="page" value="{{ request('page', 1) }}">
                    </form>
                </div>
                <div class="date-filter">
                    <form action="{{ route('order.history') }}" method="get" id="filter-form">
                        <label for="filter-date">Lọc theo ngày:</label>
                        <input  class="rounded p-1 shadow border border-0" type="date" id="filter-date" name="filter_date" value="{{ $filter_date ?? '' }}" onchange="document.getElementById('filter-form').submit()">
                        <input type="hidden" name="keyword" value="{{ request('keyword') }}">
                        <input type="hidden" name="sort_order" value="{{ request('sort_order', 'desc') }}">
                        <input type="hidden" name="page" value="{{ request('page', 1) }}">
                    </form>
                </div>
                
                <div class="refresh-btn">
                    <a href="{{ route('order.history') }}" class="btn-refresh">Làm mới</a>
                </div>
            </div>

            @if(isset($orders) && is_array($orders) && count($orders) > 0)
    <div class="table-custom">
        <div class="header-row">
            <div class="col-1">STT</div>
            <div class="col-2">Số lượng sản phẩm</div>
            <div class="col-2">Trạng thái</div>
            <div class="col-2">Ngày đặt hàng</div>
            <div class="col-2">Tổng tiền</div>
            <div class="col-4">PTTT</div>
            <div class="col-2">Thao tác</div>
        </div>
        @php
            $stt = ($current_page - 1) * 8 + 1; // Tính STT bắt đầu dựa trên trang hiện tại
        @endphp
        @foreach($orders as $index => $orderData)
            @php
                $quantity = count($orderData['chiTietHoaDons'] ?? []);
            @endphp
            @if(is_array($orderData) && isset($orderData['id']))
                <div class="data-row">
                    <div class="col-1">{{ $stt++ }}</div> <!-- Sử dụng STT tăng dần -->
                    <div class="col-2">{{ $quantity }}</div>
                    <div class="col-2">
                        <span class="badge {{ ($orderData['trangThai'] ?? 'PENDING') == 'EXPIRED' ? 'bg-secondary' : (in_array(($orderData['trangThai'] ?? 'PENDING'), ['PAID', 'REFUNDED', 'DAGIAO', 'DADAT']) ? 'bg-success' : 'bg-danger') }}">
                            {{ collect($statuses)->firstWhere('value', $orderData['trangThai'] ?? 'PENDING')['label'] ?? 'Không xác định' }}
                        </span>
                    </div>
                    <div class="col-2">{{ $orderData['ngayTao'] ? \Carbon\Carbon::parse($orderData['ngayTao'])->format('d/m/Y H:i') : 'N/A' }}</div>
                    <div class="col-2">{{ number_format($orderData['tongTien'] ?? 0, 0, ',', '.') }} VNĐ</div>
                    <div class="col-4">{{ $orderData['phuongThucThanhToan'] ?? 'Không xác định' }}</div>
                    <div class="col-2">
                        <a href="#" class="btn-view-order" data-bs-toggle="modal" data-bs-target="#orderDetailModal-{{ $orderData['id'] }}" data-order-id="{{ $orderData['id'] }}">Chi tiết</a>
                    </div>
                </div>
            @else
                <div class="data-row">
                    <div class="col-12 text-center">Dữ liệu không hợp lệ</div>
                </div>
            @endif
        @endforeach
    </div>
@else
    <div class="text-center py-3">Không có đơn hàng nào để hiển thị.</div>
@endif

@if(($total_page ?? 0) > 1)
    <nav aria-label="Page navigation">
        <ul class="pagination justify-content-center mt-3">
            <li class="page-item {{ ($current_page ?? 1) == 1 ? 'disabled' : '' }}">
                <a class="page-link" href="{{ route('order.history') }}?page={{ ($current_page ?? 1) - 1 }}&filter_date={{ $filter_date ?? '' }}&sort_order={{ $sort_order ?? 'desc' }}&keyword={{ request('keyword') }}" aria-label="Previous">
                    <span aria-hidden="true">«</span>
                </a>
            </li>
            @for($i = 1; $i <= ($total_page ?? 1); $i++)
                <li class="page-item {{ ($current_page ?? 1) == $i ? 'active' : '' }}">
                    <a class="page-link" href="{{ route('order.history') }}?page={{ $i }}&filter_date={{ $filter_date ?? '' }}&sort_order={{ $sort_order ?? 'desc' }}&keyword={{ request('keyword') }}">{{ $i }}</a>
                </li>
            @endfor
            <li class="page-item {{ ($current_page ?? 1) == ($total_page ?? 1) ? 'disabled' : '' }}">
                <a class="page-link" href="{{ route('order.history') }}?page={{ ($current_page ?? 1) + 1 }}&filter_date={{ $filter_date ?? '' }}&sort_order={{ $sort_order ?? 'desc' }}&keyword={{ request('keyword') }}" aria-label="Next">
                    <span aria-hidden="true">»</span>
                </a>
            </li>
        </ul>
    </nav>
@endif

            @foreach($orders as $orderData)
                @if(is_array($orderData) && isset($orderData['id']))
                    @php
                        $groupedProducts = [];
                        foreach ($orderData['chiTietHoaDons'] ?? [] as $cthd) {
                            if (is_array($cthd) && isset($cthd['tenSanPham'], $cthd['soSeri'])) {
                                $key = $cthd['tenSanPham'] . '-' . $cthd['soSeri'];
                                if (!isset($groupedProducts[$key])) {
                                    $groupedProducts[$key] = [
                                        'tenSanPham' => $cthd['tenSanPham'],
                                        'soSeri' => $cthd['soSeri'],
                                        'giaLucDat' => $cthd['giaLucDat'] ?? 0,
                                        'trangThaiHD' => isset($cthd['trangThaiHD']) && $cthd['trangThaiHD'],
                                        'thoiGianBaoHanh' => $cthd['thoiGianBaoHanh'] ?? 0,
                                    ];
                                }
                            }
                        }
                        $hoaDon = app(HoaDon_BUS::class)->getModelById($orderData['id']);
                    @endphp
                    <div class="modal fade" id="orderDetailModal-{{ $orderData['id'] }}" tabindex="-1" aria-labelledby="orderDetailModalLabel-{{ $orderData['id'] }}" aria-hidden="true">
                        <div class="modal-dialog modal-lg">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="orderDetailModalLabel-{{ $orderData['id'] }}">Chi tiết đơn hàng #{{ $orderData['id'] }}</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    <!--<p><strong>Phương thức thanh toán:</strong> {{ $orderData['phuongThucThanhToan'] ?? 'Không xác định' }}</p>-->
                                    <!-- <p><strong>Đơn vị vận chuyển:</strong> {{ $orderData['donViVanChuyen'] ?? 'Không xác định' }}</p> -->
                                    <p><strong>Họ tên người nhận:</strong> {{ $orderData['hoten'] ?? 'Không xác định' }}</p>
                                    <p><strong>Số điện thoại người nhận:</strong> {{ $orderData['sodienthoai'] ?? 'Không xác định' }}</p>
                                    <p><strong>Email:</strong> {{ $orderData['emailKhachHang'] ?? 'Không xác định' }}</p>
                                    <p><strong>Tỉnh:</strong> {{ $orderData['tinh'] ?? 'Không xác định' }}</p>
                                    <p><strong>Địa chỉ:</strong> {{ $orderData['diaChi'] ?? 'Không xác định' }}</p>
                                    <!-- <p>{{$hoaDon->getTrangThai()}}</p> -->
                                    @if($hoaDon->getIdPTTT()->getId()!=1 && $hoaDon->getTrangThai() === HoaDonEnum::DADAT)
                                    <!-- <form action="{{ route('payment.paid') }}" method="POST">
                                        @csrf
                                        <input type="hidden" name="id" value="{{ $hoaDon->getId() }}">
                                        <input type="hidden" name="tongtien" value="{{ $hoaDon->getTongTien() }}">
                                        <input type="hidden" name="ordercode" value="{{ $hoaDon->getOrderCode() }}">
                                        <button type="submit" class="btn btn-info mb-3">Thanh toán với PayOS</button>
                                    </form> -->
                                    @endif
                                    <h6>Danh sách sản phẩm:</h6>
                                    <table class="table table-striped">
                                        <thead>
                                            <tr>
                                                <th>Tên sản phẩm</th>
                                                <th>Số Seri</th>
                                                <th>Giá lúc đặt</th>
                                                <th>Bảo hành (Tháng)</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @if(!empty($groupedProducts))
                                                @foreach($groupedProducts as $product)
                                                    <tr>
                                                        <td>{{ $product['tenSanPham'] }}</td>
                                                        <td>{{ $product['soSeri'] }}</td>
                                                        <td>{{ number_format($product['giaLucDat'], 0, ',', '.') }} VNĐ</td>
                                                        <td>
                                                            {{-- Hiển thị số tháng bảo hành lấy từ bảng sản phẩm --}}
                                                            {{ $product['thoiGianBaoHanh'] ?? 0 }} tháng
                                                        </td>
                                                @endforeach
                                            @else
                                                <tr><td colspan="4" class="text-center">Không có chi tiết hóa đơn</td></tr>
                                            @endif
                                        </tbody>
                                    </table>
                                </div>
                                <div class="modal-footer">
                                    @if ($hoaDon->getTrangThai() === HoaDonEnum::PENDING)
                                    <a href="{{ route('payment.retry', $hoaDon->getId()) }}" class="btn btn-success">
                                        Thanh toán ngay
                                    </a>
                                    <button type="button" class="btn btn-danger btn-huy-don" data-id="{{ $hoaDon->getId() }}">Huỷ đơn hàng</button>
                                    @elseif ($hoaDon->getTrangThai() === HoaDonEnum::DADAT)
                                    <button type="button" class="btn btn-danger btn-huy-don" data-id="{{ $hoaDon->getId() }}">Huỷ đơn hàng</button>
                                    @elseif ($hoaDon->getTrangThai() === HoaDonEnum::PAID)
                                    <button type="button" class="btn btn-danger btn-hoan-tien" data-id="{{ $hoaDon->getId() }}">Huỷ đơn hàng</button>
                                    @endif
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif
            @endforeach
        </div>
    </main>

    <footer class="footer">
        <p>Anna © 2018 - 2026. Design by OkHub VietNam</p>
        <div>
            <a>Hệ Thống Cửa Hàng</a>
            <a>Cửa Hàng</a>
        </div>
    </footer>

    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const successAlert = document.getElementById('successAlert');
            const errorAlert = document.getElementById('errorAlert');
            [successAlert, errorAlert].forEach(alert => {
                if (alert) {
                    setTimeout(() => {
                        alert.classList.remove('show');
                        alert.classList.add('fade');
                        alert.style.opacity = 0;
                    }, 3000);
                    setTimeout(() => {
                        alert.remove();
                    }, 4000);
                }
            });

            const viewButtons = document.querySelectorAll('.btn-view-order');
            viewButtons.forEach(button => {
                button.addEventListener('click', function () {
                    const orderId = this.dataset.orderId;
                    const modal = document.querySelector(`#orderDetailModal-${orderId}`);
                    if (!modal) alert('Không tìm thấy chi tiết đơn hàng!');
                });
            });

            const searchForms = document.querySelectorAll('form[role="search"]');
            searchForms.forEach(function (searchForm) {
                searchForm.addEventListener('submit', function (e) {
                    e.preventDefault();
                    const currentUrl = new URL(window.location.href);
                    const keywordInput = searchForm.querySelector('input[name="keyword"]');
                    if (keywordInput && keywordInput.value.trim()) {
                        currentUrl.searchParams.set('keyword', keywordInput.value.trim());
                        currentUrl.searchParams.delete('lsp');
                        currentUrl.searchParams.delete('hang');
                    } else {
                        currentUrl.searchParams.delete('keyword');
                        currentUrl.searchParams.delete('lsp');
                        currentUrl.searchParams.delete('hang');
                    }
                    currentUrl.searchParams.delete('page');
                    window.location.href = currentUrl.toString();
                });
            });

            const pageLinks = document.querySelectorAll('.pagination .page-link');
            pageLinks.forEach(link => {
                link.addEventListener('click', function (e) {
                    e.preventDefault();
                    const href = this.getAttribute('href');
                    if (href) {
                        document.body.style.opacity = 0.5;
                        setTimeout(() => {
                            window.location.href = href;
                        }, 300);
                    }
                });
            });

            const searchForm = document.getElementById('search-form');
            const searchInput = document.getElementById('search-keyword');
            if (searchForm && searchInput) {
                searchForm.addEventListener('submit', function (e) {
                    e.preventDefault();
                    if (!searchInput.value.trim()) {
                        const alertDiv = document.createElement('div');
                        alertDiv.className = 'alert alert-danger alert-dismissible fade show';
                        alertDiv.role = 'alert';
                        alertDiv.innerHTML = `
                            Vui lòng nhập tên sản phẩm để tìm kiếm.
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        `;
                        document.querySelector('.container').prepend(alertDiv);
                        setTimeout(() => {
                            alertDiv.classList.remove('show');
                            alertDiv.classList.add('fade');
                            alertDiv.style.opacity = 0;
                        }, 3000);
                        setTimeout(() => {
                            alertDiv.remove();
                        }, 3500);
                        return;
                    }
                    const url = new URL(window.location.href);
                    url.searchParams.set('keyword', searchInput.value.trim());
                    url.searchParams.set('filter_date', searchForm.querySelector('input[name="filter_date"]').value);
                    url.searchParams.set('sort_order', searchForm.querySelector('input[name="sort_order"]').value);
                    url.searchParams.set('page', searchForm.querySelector('input[name="page"]').value);
                    window.location.href = url.toString();
                });
            }

            const userDropdownBtn = document.getElementById('userDropdownBtn');
            const userDropdownMenu = document.getElementById('userDropdownMenu');
            if (userDropdownBtn && userDropdownMenu) {
                userDropdownBtn.addEventListener('click', function () {
                    userDropdownMenu.style.display = userDropdownMenu.style.display === 'none' ? 'block' : 'none';
                });
                document.addEventListener('click', function (e) {
                    if (!userDropdownBtn.contains(e.target) && !userDropdownMenu.contains(e.target)) {
                        userDropdownMenu.style.display = 'none';
                    }
                });
            }

            const logoutForm = document.getElementById('logout-form');
            if (logoutForm) {
                logoutForm.addEventListener('submit', function(e) {
                    e.preventDefault();
                    fetch(this.action, {
                        method: 'POST',
                        body: new FormData(this),
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                        }
                    }).then(response => {
                        if (response.ok) {
                            window.location.href = '/index';
                        } else {
                            alert('Đăng xuất thất bại. Vui lòng thử lại.');
                        }
                    }).catch(error => {
                        console.error('Lỗi đăng xuất:', error);
                        alert('Đã xảy ra lỗi khi đăng xuất.');
                    });
                });
            }
        });
       const statuses = [
    { value: "PENDING", label: "Đang xử lý" },
    { value: "PAID", label: "Đã thanh toán" },
    { value: "EXPIRED", label: "Hết hạn" },
    { value: "CANCELLED", label: "Đã hủy" },
    { value: "REFUNDED", label: "Đã hoàn tiền" },
    { value: "DADAT", label: "Đã đặt" },
    { value: "DANGGIAO", label: "Đang giao" },
    { value: "DAGIAO", label: "Đã giao" }
];

    $(document).ready(function() {
        $('.btn-huy-don').on('click', function() {
            const idHoaDon = $(this).data('id');
            
            if (confirm('Bạn có chắc chắn muốn hủy đơn hàng này? Số lượng sản phẩm sẽ được hoàn lại kho.')) {
                $.ajax({
                    url: `/huy-don-hang/${idHoaDon}`,
                    type: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}' // Bắt buộc phải có CSRF Token trong Laravel
                    },
                    success: function(response) {
                        if (response.success) {
                            alert(response.message);
                            location.reload(); // Load lại trang để cập nhật trạng thái mới
                        } else {
                            alert('Lỗi: ' + response.message);
                        }
                    },
                    error: function() {
                        alert('Không thể kết nối đến máy chủ.');
                    }
                });
            }
        });
    });

    $(document).ready(function() {
        $('.btn-hoan-tien').on('click', function() {
            const idHoaDon = $(this).data('id');
            
            if (confirm('Bạn có chắc chắn muốn hủy đơn hàng này? Số lượng sản phẩm sẽ được hoàn lại kho.')) {
                $.ajax({
                    url: `/vnpay-refund/${idHoaDon}`,
                    type: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}' // Bắt buộc phải có CSRF Token trong Laravel
                    },
                    success: function(response) {
                        if (response.success) {
                            alert(response.message);
                            location.reload(); // Load lại trang để cập nhật trạng thái mới
                        } else {
                            alert('Lỗi: ' + response.message);
                        }
                    },
                    error: function(xhr, status, thrownError) {
                        // In chi tiết lỗi đỏ chót ra màn hình Console (F12)
                        console.error("Chi tiết lỗi AJAX:", xhr.responseText);
                        
                        // Báo alert mã lỗi để bạn dễ nhận biết
                        alert('Máy chủ trả về lỗi ' + xhr.status + '. Nhấn F12 chọn tab Console để xem chi tiết!');
                    }
                });
            }
        });
    });
    </script>
</body>
</html>