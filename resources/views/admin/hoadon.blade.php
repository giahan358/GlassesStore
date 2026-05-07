<style>
/* Tùy chỉnh màu sắc cho các nút bootstrap-select */
.bootstrap-select > .dropdown-toggle.bs-placeholder, 
.bootstrap-select > .dropdown-toggle.btn-light,
.bootstrap-select > .dropdown-toggle {
    background-color: #ffffff !important; 
    border: 1px solid #dee2e6 !important; 
    color: #212529 !important;            
    box-shadow: none !important;        
}

/* Khi di chuột qua hoặc đang mở menu */
.bootstrap-select > .dropdown-toggle:hover,
.bootstrap-select > .dropdown-toggle:focus {
    background-color: #f8f9fa !important;
    border-color: #ced4da !important;
}

/* Màu chữ của phần text hiển thị bên trong */
.bootstrap-select .filter-option-inner-inner {
    color: #212529 !important;
}

/* Màu của mũi tên trỏ xuống */
.bootstrap-select .dropdown-toggle .caret {
    border-top-color: #212529 !important;
}
</style>
<script>
document.addEventListener("DOMContentLoaded", function () {
    // 1. Xử lý Search Form mượt hơn
    const searchForm = document.querySelector('form[method="GET"]');
    if (searchForm) {
        searchForm.addEventListener('submit', function (e) {
            e.preventDefault();
            const formData = new FormData(searchForm);
            const url = new URL(window.location.href);
            url.searchParams.set('modun', 'hoadon');
            for (const [key, value] of formData.entries()) {
        if (value && value.trim() !== '') { 
            url.searchParams.set(key, value);
        } else {
            url.searchParams.delete(key);
        }
    }
            window.location.href = url.toString();
        });
    }

    const refreshBtn = document.getElementById('refreshBtn');
    if (refreshBtn) {
        refreshBtn.addEventListener('click', function () {
            window.location.href = window.location.pathname + '?modun=hoadon';
        });
    }

    const modal = document.getElementById("staticBackdropOrderModal");
    modal.addEventListener("show.bs.modal", function (event) {
        const button = event.relatedTarget;
        
        // Lấy dữ liệu từ data attributes
        const data = {
            id: button.getAttribute("data-id"),
            email: button.getAttribute("data-email"),
            sdt: button.getAttribute("data-sdt"),
            nhanvien: button.getAttribute("data-nhanvien"),
            tenKh: button.getAttribute("data-tenkhachhang"),
            ngayTao: button.getAttribute("data-ngaytao"),
            pttt: button.getAttribute("data-pttt"),
            trangThai: button.getAttribute("data-trangthai"),
            tongTien: button.getAttribute("data-tongtien"),
            diaChi: button.getAttribute("data-diachi"),
            cthd: JSON.parse(button.getAttribute("data-cthd") || "[]")
        };
    // Gán dữ liệu vào modal
        modal.querySelector(".ma-don-hang").textContent = data.id;
        modal.querySelector(".tai-khoan").textContent = data.email;
        modal.querySelector(".so-dien-thoai").textContent = data.sdt;
        modal.querySelector(".ten-khach-hang").textContent = data.tenKh;
        modal.querySelector(".nhan-vien").textContent = data.nhanvien;
        modal.querySelector(".ngay-tao").textContent = data.ngayTao;
        modal.querySelector(".pttt").textContent = data.pttt;
        modal.querySelector(".tong-tien").textContent = data.tongTien;
        modal.querySelector(".dia-chi").textContent = data.diaChi || "Không có thông tin";
    // modal.querySelector(".modal-body .hoa-don-id").value = id;
        const idInput = modal.querySelector("input.hoa-don-id");
        if (idInput) idInput.value = data.id;



        const selectTrangThai = modal.querySelector("select.trang-thai");
        selectTrangThai.innerHTML = "";

        const allStatuses = [
            { v: "PENDING", l: "Chờ thanh toán" },
            { v: "DADAT", l: "Đã đặt" },
            { v: "PAID", l: "Đã thanh toán" },
            { v: "DANGGIAO", l: "Đang giao" },
            { v: "DAGIAO", l: "Đã giao" },
            { v: "CANCELLED", l: "Hủy đơn" },
            { v: "REFUNDED", l: "Đã hoàn tiền" }
        ];

        let allowed = [];
        if (data.trangThai === "PENDING") {
        // Nếu là PENDING, không cho phép chọn gì cả
        allowed = ["PENDING"]; 
        selectTrangThai.disabled = true; // Khóa luôn ô chọn, Admin không bấm vào được
    } else {
        // Nếu không phải PENDING, mở khóa ô chọn để Admin thao tác
        selectTrangThai.disabled = false;

        if (data.trangThai === "DADAT") {
            allowed = (data.pttt === "Tiền mặt") ? ["DADAT", "DANGGIAO", "CANCELLED"] : ["DADAT", "PAID", "CANCELLED"];
        } else if (data.trangThai === "PAID") {
            allowed = ["PAID", "DANGGIAO"];
        } else if (data.trangThai === "DANGGIAO") {
            allowed = ["DANGGIAO", "DAGIAO"];
        } else if (data.trangThai === "DAGIAO") {
           allowed = ["DAGIAO"];
        } else {
            allowed = [data.trangThai]; 
        }
    }

        allStatuses.forEach(s => {
            if (allowed.includes(s.v)) {
                const opt = new Option(s.l, s.v);
                if (s.v === data.trangThai) opt.selected = true;
                selectTrangThai.add(opt);
            }
        });

       const tbody = modal.querySelector(".cthd-body");

// Bộ định dạng tiền tệ tiêu chuẩn
const formatVND = new Intl.NumberFormat('vi-VN', {
    style: 'currency',
    currency: 'VND',
});

tbody.innerHTML = (data.cthd && data.cthd.length > 0) ? data.cthd.map(item => {
    // Lấy dữ liệu Seri và Đơn giá (hỗ trợ cả key HOA và thường)
    const tenSP = item.TENSANPHAM || item.tenSP || item.tensanpham || 'N/A';
    const seri = item.SOSERI || item.seri || 'N/A';
    const donGia = item.GIALUCDAT || item.dongia || item.DONGIA || 0;

    return `
        <tr>
            <td class="text-dark">${tenSP}</td>
            <td class="text-dark">${seri}</td> 
            <td class="text-dark">${formatVND.format(donGia).replace('₫', 'đ')}</td>
        </tr>
    `;
}).join('') : '<tr><td colspan="2" class="text-center">Không có chi tiết sản phẩm</td></tr>';
        });
});
</script>




<div class="p-4 bg-light">
    <div class="col-md-12 d-flex flex-wrap align-items-center gap-3">
        <form class="d-flex flex-wrap w-100 gap-2" method="GET" role="search">
            <input class="form-control me-2" type="search" 
                placeholder="Tìm tài khoản (email, sdt)" 
                aria-label="Search" 
                name="keyword" 
                value="{{ request('keyword') }}" 
                style="min-width: 380px; max-width: 500px;">

            <select name="trangthai" class="selectpicker" title="Chọn trạng thái" style="max-width: 200px;" data-live-search="true" data-size="5">
                <option disabled {{ request('trangthai') ? '' : 'selected' }}>Chọn trạng thái</option>
                @foreach ($hoaDonStatuses as $status)
                <option value="{{ $status->value }}" {{ request('trangthai') == $status->value ? 'selected' : '' }}>
                    @switch($status)
                        @case(\App\Enum\HoaDonEnum::PENDING)
                            Chờ thanh toán
                            @break
                        @case(\App\Enum\HoaDonEnum::PAID)
                            Đã thanh toán
                            @break
                        @case(\App\Enum\HoaDonEnum::EXPIRED)
                            Hết hạn
                            @break
                        @case(\App\Enum\HoaDonEnum::CANCELLED)
                            Đã hủy
                            @break
                        @case(\App\Enum\HoaDonEnum::REFUNDED)
                            Đã hoàn tiền
                            @break
                        @case(\App\Enum\HoaDonEnum::DANGGIAO)
                            Đang giao
                            @break
                        @case(\App\Enum\HoaDonEnum::DAGIAO)
                            Đã giao
                            @break
                        @case(\App\Enum\HoaDonEnum::DADAT)
                            Đã đặt
                            @break
                        @default
                            {{ $status->value }}
                    @endswitch
                </option>
                @endforeach
            </select>

            <!-- <div class="d-flex align-items-center gap-2">
                <span class="fw-bold">Tiền:</span>
                <a href="?order=asc" class="btn btn-outline-primary">
                    <i class="fa-solid fa-arrow-up-wide-short"></i>
                </a>
                <a href="?order=desc" class="btn btn-outline-primary">
                    <i class="fa-solid fa-arrow-down-wide-short"></i>
                </a>
            </div> -->
            <select name="sortDate" class="selectpicker" title="Sắp xếp ngày" style="max-width: 200px;">
    <option value="desc" {{ request('sortDate') == 'desc' ? 'selected' : '' }}>Mới nhất</option>
    <option value="asc" {{ request('sortDate') == 'asc' ? 'selected' : '' }}>Cũ nhất</option>
</select>
            <button class="btn btn-success" type="submit">Tìm kiếm</button>
            <button class="btn btn-info" id="refreshBtn" type="button">Làm mới</button>
        </form>
    </div>
    
    <div class="table-responsive mt-4">
        <table class="table table-striped table-bordered text-center table-hover">
            <thead class="">
                <tr>
                    <th scope="col">Mã hóa đơn</th>
                    <th scope="col">Email</th>
                    <th scope="col">Số điện thoại</th>
                    <!--<th scope="col">Nhân viên</th>-->
                    <th scope="col">Tổng tiền</th>
                    <th scope="col">PTTT</th>
                    <th scope="col">Ngày tạo</th>
                    <!-- <th scope="col">DVVC</th> -->
                    <th scope="col">Trạng thái</th>
                    <th scope="col">Hành động</th>
                </tr>
            </thead>
            <tbody>
            @if (!empty($listHoaDon) && count($listHoaDon) > 0)
                @foreach($listHoaDon as $hoaDon)
                <tr>
                    <td>{{ $hoaDon->getId() }}</td>
                    <td>{{ $hoaDon->getEmail()->getEmail() }}</td>
                    <td>{{ $hoaDon->getSoDienThoai() }}</td>
                    
                    <td>{{ $hoaDon->getTongTien() }}</td>
                    <td>{{ $mapPTTT[$hoaDon->getIdPTTT()->getId()] }}</td>
                    <td>{{ $hoaDon->getNgayTao()->format('d/m/Y H:i') }}</td>
                    <td>
                        @if($hoaDon->getTrangThai() == \App\Enum\HoaDonEnum::PAID)
                            <span class="badge bg-success">Đã thanh toán</span>
                        @elseif($hoaDon->getTrangThai() == \App\Enum\HoaDonEnum::PENDING)
                            <span class="badge bg-warning text-dark">Chờ thanh toán</span>
                        @elseif($hoaDon->getTrangThai() == \App\Enum\HoaDonEnum::EXPIRED)
                            <span class="badge bg-secondary">Hết hạn</span>
                        @elseif($hoaDon->getTrangThai() == \App\Enum\HoaDonEnum::CANCELLED)
                            <span class="badge bg-secondary">Đã hủy</span>
                        @elseif($hoaDon->getTrangThai() == \App\Enum\HoaDonEnum::REFUNDED)
                            <span class="badge bg-success">Đã hoàn tiền</span>
                        @elseif($hoaDon->getTrangThai() == \App\Enum\HoaDonEnum::DANGGIAO)
                        <span class="badge bg-success">Đang giao</span>
                        @elseif($hoaDon->getTrangThai() == \App\Enum\HoaDonEnum::DAGIAO)
                        <span class="badge bg-success">Đã giao</span>
                        @elseif($hoaDon->getTrangThai() == \App\Enum\HoaDonEnum::DADAT)
                        <span class="badge bg-success">Đã đặt</span>
                        @endif
                    </td>
                    <td>
                    <button class="btn btn-warning btn-sm"
                        data-bs-toggle="modal"
                        data-bs-target="#staticBackdropOrderModal"
                        data-id="{{ $hoaDon->getId() }}"
                        data-email="{{ $hoaDon->getEmail()->getEmail() }}"
                        data-sdt="{{ $hoaDon->getSoDienThoai() }}"
                        
                        data-tenkhachhang="{{ $hoaDon?->getHoTen() ?? 'Khách hàng' }}"
                        data-ngaytao="{{ $hoaDon->getNgayTao()->format('d/m/Y H:i') }}"
                        data-pttt="{{ $mapPTTT[$hoaDon->getIdPTTT()->getId()] }}"
                        data-trangthai="{{ $hoaDon->getTrangThai() }}"
                        data-tongtien="{{ $hoaDon->getTongTien() }}"
                        data-diachi="{{ $hoaDon->getDiaChi() }}"
                        data-cthd='@json($mapCTHD[$hoaDon->getId()])'>
                        Chi tiết
                    </button>
                    </td>
                </tr>
                @endforeach
            @else
            <tr>
                <td colspan="9">Không tìm thấy hóa đơn nào.</td>
            </tr>
            @endif
            </tbody>
        </table>
    </div>
    
    <nav aria-label="Page navigation example" class="d-flex justify-content-center">
            <ul class="pagination">
                <!-- Hiển thị PREV nếu không phải trang đầu tiên -->
                <?php
                $queryString = isset($_GET['keyword']) ? '&keyword=' . urlencode($_GET['keyword']) : '';
                $query = $_GET;
                $items_per_page = 8; 
                $allHoaDon = app(App\Bus\HoaDon_BUS::class)->getAllModels(); 
                $total_items = is_array($allHoaDon) ? count($allHoaDon) : 0;
                $current_page = request()->input('page', 1); 

                $total_page = ceil((int)$total_items / $items_per_page);

                $query = request()->query();
                // PREV
                if ($current_page > 1) {
                    echo '<li class="page-item">
                            <a class="page-link" href="?' . http_build_query(array_merge($query, ['page' => $current_page - 1])) . '" aria-label="Previous">
                                <span aria-hidden="true">&laquo;</span>
                            </a>
                        </li>';
                }

                // Hiển thị các trang phân trang xung quanh trang hiện tại
                $page_range = 1; // Hiển thị 1 trang trước và 1 trang sau
                $start_page = max(1, $current_page - $page_range);
                $end_page = min($total_page, $current_page + $page_range);

                for ($i = $start_page; $i <= $end_page; $i++) {
                    if ($i == $current_page) {
                        echo '<li class="page-item active"><span class="page-link">' . $i . '</span></li>';
                    } else {
                        echo '<li class="page-item"><a class="page-link" href="?' . http_build_query(array_merge($query, ['page' => $i])) . '">' . $i . '</a></li>';
                    }
                }
                
                // NEXT
                
                if ($current_page < $total_page) {
                    echo '<li class="page-item">
                            <a class="page-link" href="?' . http_build_query(array_merge($query, ['page' => $current_page + 1])) . '" aria-label="Next">
                                <span aria-hidden="true">&raquo;</span>
                            </a>
                        </li>';
                }
                ?>
            </ul>
          </nav>
</div>

<div class="modal fade" id="staticBackdropOrderModal" aria-hidden="true" aria-labelledby="staticBackdropLabelInfoOrder" tabindex="-1">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5" id="staticBackdropLabelInfoOrder">Chỉnh sửa đơn hàng</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="updateStatusForm" action="{{ route('admin.hoadon.update') }}" method="POST">
                @csrf
                <input type="hidden" name="id" class="hoa-don-id">
                <div class="modal-body">
                    <div class="row">
                        <!-- col bảng sản phẩm trong đơn hàng -->
                        <div class="col-md-8">
                            <div class="cart-product pb-2 px-2 border rounded-3 shadow bg-white table-responsive">
                                <div style="max-height: 400px; overflow-y: auto;">
                                    <table class="table">
                                        <thead>
                                            <tr>
                                                <th scope="col" class="text-dark">Tên sản phẩm</th>
                                                <th scope="col" class="text-dark">Số seri sản phẩm</th>
                                                <th scope="col" class="text-dark">Giá tiền</th>
                                                
                                            </tr>
                                        </thead>
                                        <tbody class="cthd-body"></tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                        <!-- col thông tin thanh toán -->
                        <div class="col-md-4">
                            <div class="row pe-3">
                                <div class="card border shadow rounded-3 w-100" style="width: 18rem;">
                                    <ul class="list-group list-group-flush">
                                        <li class="list-group-item fw-bold py-2">Thông tin đơn hàng</li>
                                        <li class="list-group-item">
                                            <div class="mt-2 mb-2 d-flex justify-content-between align-items-center small">
                                                <strong>Mã hóa đơn</strong>
                                                <span class="ma-don-hang opacity-50 fw-medium"></span>
                                            </div>
                                            <div class="mt-2 mb-2 d-flex justify-content-between align-items-center small">
                                                <strong>Email</strong>
                                                <span class="tai-khoan opacity-50 fw-medium"></span>
                                            </div>
                                            <div class="mt-2 mb-2 d-flex justify-content-between align-items-center small">
                                                <strong>Tên khách hàng</strong>
                                                <span class="ten-khach-hang opacity-50 fw-medium"></span>
                                            </div>
                                            <div class="mt-2 mb-2 d-flex justify-content-between align-items-center small">
                                                <strong>Số điện thoại</strong> <span class="so-dien-thoai opacity-50 fw-medium"></span>
                                            </div>
                                            <div class="mt-2 mb-2 d-flex justify-content-between align-items-center small">
                                                <!--<strong>Nhân viên</strong>-->
                                                <span class="nhan-vien opacity-50 fw-medium"></span>
                                            </div>
                                            <div class="mt-2 mb-2 small address-css">
                                                <div><strong>Địa chỉ giao hàng</strong></div>
                                                <div class="dia-chi opacity-50 fw-medium"></div>
                                            </div>
                                            <div class="mt-2 mb-2 d-flex justify-content-between align-items-center small">
                                                <strong>Ngày tạo đơn hàng</strong>
                                                <span class="ngay-tao opacity-50 fw-medium"></span>
                                            </div>
                                            <div class="mb-2 d-flex justify-content-between align-items-center small">
                                                <strong>Phương thức thanh toán</strong>
                                                <span class="pttt opacity-50 fw-medium"></span>
                                            </div>
                                        </li>
                                        <!-- <li class="list-group-item py-2">
                                            <div class="mt-2 mb-2 d-flex justify-content-between align-items-center small">
                                                <strong>Trạng thái</strong>
                                                <span class="trang-thai opacity-50 fw-medium"></span>
                                            </div>
                                        </li> -->
                                        <li class="list-group-item py-2">
                                            <div class="mt-2 mb-2 d-flex justify-content-between align-items-center small">
                                                <strong>Trạng thái</strong>
                                                <select class="form-select trang-thai" name="trangthai" style="max-width: 200px;">
                                                    @foreach ($hoaDonStatuses as $status)
                                                        <option value="{{ $status->value }}">
                                                            @switch($status)
                                                                @case(\App\Enum\HoaDonEnum::PENDING)
                                                                    Chờ thanh toán
                                                                    @break
                                                                @case(\App\Enum\HoaDonEnum::PAID)
                                                                    Đã thanh toán
                                                                    @break
                                                                @case(\App\Enum\HoaDonEnum::EXPIRED)
                                                                    Hết hạn
                                                                    @break
                                                                @case(\App\Enum\HoaDonEnum::CANCELLED)
                                                                    Đã hủy
                                                                    @break
                                                                @case(\App\Enum\HoaDonEnum::REFUNDED)
                                                                    Đã hoàn tiền
                                                                    @break
                                                                @case(\App\Enum\HoaDonEnum::DANGGIAO)
                                                                    Đang giao
                                                                    @break
                                                                @case(\App\Enum\HoaDonEnum::DAGIAO)
                                                                    Đã giao
                                                                    @break
                                                                @default
                                                                    {{ $status->value }}
                                                            @endswitch
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </li>
                                        <li class="list-group-item">
                                            <div class="mb-2 mt-2 d-flex justify-content-between align-items-center small">
                                                <strong>Tổng tiền</strong>
                                                <span class="tong-tien opacity-50 fw-medium"></span>
                                            </div>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
                    <button type="submit" class="btn btn-primary">Cập nhật trạng thái</button>
                </div>
            </form>
        </div>
    </div>
</div>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>

<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-select@1.14.0-beta3/dist/css/bootstrap-select.min.css">
<script src="https://cdn.jsdelivr.net/npm/bootstrap-select@1.14.0-beta3/dist/js/bootstrap-select.min.js"></script>


