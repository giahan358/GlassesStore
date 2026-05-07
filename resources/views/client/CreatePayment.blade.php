<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
<link rel="stylesheet" href="{{ asset('css/client/include/navbar.css') }}">
<link rel="stylesheet" href="{{ asset('css/client/include/footer.css') }}">
<?php
    use App\Bus\SanPham_BUS;
    use App\Bus\CTSP_BUS;
    use App\Bus\DiaChi_BUS;
    use App\Bus\NguoiDung_BUS;
    use App\Bus\Auth_BUS;
    use App\Bus\TaiKhoan_BUS;
    use App\Models\DiaChi;

    // --- XỬ LÝ DANH SÁCH SẢN PHẨM (listSP) ---
    $listSP = session('listSP', []); // Mặc định là mảng rỗng nếu không có session

    // Chuẩn hóa dữ liệu để luôn là Object/Array có thể foreach
    if (is_string($listSP)) {
        $listSP = json_decode($listSP); 
    } elseif (is_array($listSP)) {
        // Chuyển mảng lồng mảng thành mảng đối tượng để dùng được toán tử ->
        $listSP = json_decode(json_encode($listSP)); 
    }

    // --- LẤY THÔNG TIN SESSION KHÁC ---
    $listPTTT = session('listPTTT', []);
    $listDVVC = session('listDVVC', []);
    $listTinh = session('listTinh', []);
    $user     = session('user');
    $isLogin  = session('isLogin', false);

    // ========================================================
    // LỚP BẢO VỆ 1: CỨU HỘ USER (Tránh lỗi null khi quay lại)
    // ========================================================
    if (!$user) {
        $email = app(Auth_BUS::class)->getEmailFromToken();
        if ($email) {
            $user = app(TaiKhoan_BUS::class)->getModelById($email);
            if ($user) {
                session(['user' => $user]); 
            }
        }
    }

    // ========================================================
    // LỚP BẢO VỆ 2: LẤY ĐỊA CHỈ AN TOÀN
    // ========================================================
    $listDiaChi = [];
    if ($user && method_exists($user, 'getIdNguoiDung') && $user->getIdNguoiDung()) {
        $nguoiDung = $user->getIdNguoiDung();
        if (method_exists($nguoiDung, 'getId')) {
            $listDiaChi = app(DiaChi_BUS::class)->getByIdND($nguoiDung->getId());
        }
    }

    // ========================================================
    // LỚP BẢO VỆ 3: TÍNH TỔNG TIỀN (Chặn lỗi Foreach Null)
    // ========================================================
    $sum = 0;
    $tongTien = 0; // Thêm biến này nếu bạn dùng ở dưới

    if (!empty($listSP) && (is_array($listSP) || is_object($listSP))) {
        foreach ($listSP as $key) {
            // Kiểm tra thuộc tính idsp tồn tại để không sập code
            if (isset($key->idsp)) {
                $tmp = app(SanPham_BUS::class)->getModelById($key->idsp);
                
                if ($tmp) {
                    $soLuong = $key->quantity ?? 1;
                    $sum += $tmp->getDonGia() * $soLuong;
                }
            }
        }
    }
    
    // Gán lại cho tongTien nếu cần
    $tongTien = $sum;
?>
@if(session('success'))
<div class="alert alert-success alert-dismissible fade show" role="alert" id="successAlert">
    {{ session('success') }}
</div>
@endif
@if(session('error'))
<div class="alert alert-danger alert-dismissible fade show" role="alert" id="errorAlert">
    <strong>Lỗi:</strong> {{ session('error') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
</div>
@endif
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    document.addEventListener("DOMContentLoaded", function() {
        const ptttSelect = document.getElementById("pttt");
        const btnDatHang = document.getElementById("saveHoaDon");
        const btnThanhToan = document.getElementById("btnThanhToan");

        function updateButton() {
            const selectedValue = ptttSelect.value;
            if (selectedValue === 1) {
                btnDatHang.style.display = "none";
                btnThanhToan.style.display = "inline-block";
            } else {
                btnDatHang.style.display = "inline-block";
                btnThanhToan.style.display = "none";
            }
        }

        // Gọi một lần khi trang load
        updateButton();

        // Gọi mỗi khi select thay đổi
        ptttSelect.addEventListener("change", updateButton);
    });
    function formatCurrency(amount) {
        return amount.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".") + "₫";
    }
    $(document).on('change', '.form-check-input', function() {
    let val = $(this).val();
    let name = $(this).attr('name');

    if(name === 'radioHoTen') {
        $('#hienThiHoTen').text(val);
        $('#hoten_data').val(val);
    } else if(name === 'radioSDT') {
        $('#hienThiSDT').text(val);
        $('#sodienthoai_data').val(val);
    } else if(name === 'radioDefault') {
        $('#btn-diachi div').text(val);
        $('#diachidata').val(val);
    }
});
    $(document).on('change', 'input[name="radioDefault"]', function () {
        let diaChi = $(this).val();
        
        // Cập nhật text địa chỉ hiển thị
        $('#btn-diachi div').text(diaChi);

        // Gán vào hidden input để gửi form nếu cần
        $('#diachidata').val(diaChi);

        // Đóng modal
        $('#accountUpdateModal').modal('hide');
        $('#diachidata').val(diaChi);
    });
    $(document).on('change', 'select[name="pttt"]', function () {
        let pttt = $(this).val();
        $('#idpttt').val(pttt);
    });
    // --- 1. Khi chọn Radio HỌ TÊN ---
$(document).on('change', 'input[name="radioHoTen"]', function() {
    let selectedValue = $(this).val(); // Lấy giá trị từ radio được chọn
    $('#hienThiHoTen').text(selectedValue); // Cập nhật text hiển thị trên giao diện
    $('#hoten_data').val(selectedValue);    // Cập nhật vào input ẩn để gửi form
    $('#modalHoTen').modal('hide');         // Đóng modal sau khi chọn
});

// --- 2. Khi chọn Radio SỐ ĐIỆN THOẠI ---
$(document).on('change', 'input[name="radioSDT"]', function() {
    let selectedValue = $(this).val();
    $('#hienThiSDT').text(selectedValue);  
    $('#sodienthoai_data').val(selectedValue); 
    $('#modalSDT').modal('hide');
});

// --- 3. Khi chọn Radio ĐỊA CHỈ ---
$(document).on('change', 'input[name="radioDefault"]', function() {
    let selectedValue = $(this).val();
    $('#btn-diachi div').text(selectedValue); 
    $('#diachidata').val(selectedValue);                   
    $('#accountUpdateModal').modal('hide');
});
    $(document).ready(function () {
    // Hàm dùng chung để thu thập dữ liệu hiện tại trên UI và gửi AJAX lưu vào DB.
    function syncAndSaveData(overrideAddress = null, overrideName = null, overridePhone = null) {
        // Lấy dữ liệu đang hiển thị trên màn hình
        let currentHT = overrideName || $('#hienThiHoTen').text().trim();
        let currentSDT = overridePhone || $('#hienThiSDT').text().trim();
        let currentDC = overrideAddress || $('#btn-diachi div').text().trim();

        $.ajax({
            url: "{{ route('user.addAddress') }}",
            method: 'POST',
            data: {
                hoten: currentHT,
                sodienthoai: currentSDT,
                diachi: currentDC,
                _token: '{{ csrf_token() }}'
            },
            success: function (response) {
    if (response.status === 'success') {
        // Kiểm tra và thêm Họ tên mới nếu chưa có trong danh sách hiển thị
        if (overrideName) {
            let isExist = false;
            $('#listHoTenContainer input').each(function() {
                if($(this).val() === overrideName) isExist = true;
            });
            if(!isExist) {
                $('#listHoTenContainer').prepend(`<div class="form-check py-2 border-bottom"><input class="form-check-input" type="radio" name="radioHoTen" value="${overrideName}" checked><label class="form-check-label w-100">${overrideName}</label></div>`);
            }
        }

        // Kiểm tra và thêm SĐT mới nếu chưa có
        if (overridePhone) {
            let isExist = false;
            $('#listSDTContainer input').each(function() {
                if($(this).val() === overridePhone) isExist = true;
            });
            if(!isExist) {
                $('#listSDTContainer').prepend(`<div class="form-check py-2 border-bottom"><input class="form-check-input" type="radio" name="radioSDT" value="${overridePhone}" checked><label class="form-check-label w-100">${overridePhone}</label></div>`);
            }
        }

        // Kiểm tra và thêm Địa chỉ mới nếu chưa có
        if (overrideAddress) {
            let isExist = false;
            $('#listDiaChiContainer input').each(function() {
                if($(this).val() === overrideAddress) isExist = true;
            });
            if(!isExist) {
                $('#listDiaChiContainer').prepend(`<div class="form-check py-2 border-bottom"><input class="form-check-input" type="radio" name="radioDefault" value="${overrideAddress}" checked><label class="form-check-label w-100">${overrideAddress}</label></div>`);
            }
        }
        // --- 4. CẬP NHẬT HIỂN THỊ VÀ ĐÓNG MODAL ---
        $('#hienThiHoTen').text(currentHT);
        $('#hoten_data').val(currentHT);
        $('#hienThiSDT').text(currentSDT);
        $('#sodienthoai_data').val(currentSDT);
        $('#btn-diachi div').text(currentDC);
        $('#diachidata').val(currentDC);

        $('.modal').modal('hide'); 
        alert("Đã lưu thông tin mới!");
    }
},
        });
    }

    // --- XỬ LÝ LƯU HỌ TÊN MỚI ---
    $('#btnSaveHoTen').on('click', function(e) {
        e.preventDefault();
        let newVal = $('#inputNewHoTen').val().trim();
        if(newVal === "") return alert("Vui lòng nhập họ tên!");
        syncAndSaveData(null, newVal, null);
    });

    // --- XỬ LÝ LƯU SỐ ĐIỆN THOẠI MỚI ---
   $('#btnSaveSDT').on('click', function(e) {
    e.preventDefault();
    let newVal = $('#inputNewSDT').val().trim();
    
    // Regex: bắt đầu là số, độ dài đúng 10
    const sdtRegex = /^[0-9]{10}$/;

    if(newVal === "") return alert("Vui lòng nhập số điện thoại!");
    if(!sdtRegex.test(newVal)) return alert("Số điện thoại phải nhập đúng 10 chữ số!");
    
    syncAndSaveData(null, null, newVal);
});

    // --- XỬ LÝ LƯU ĐỊA CHỈ MỚI ---
    $('#btnDiaChi').on('click', function (e) {
        e.preventDefault();
        let newVal = $('#addDiaChi').val().trim();
        if (newVal === '') return alert("Vui lòng nhập địa chỉ!");
        syncAndSaveData(newVal, null, null);
    });
});
    

    
</script>
    <div class="top-nav p-3">
        <ul class="list-top-nav d-flex ms-auto gap-2">
          <li class="nav-item px-3 py-1 bg-secondary text-white fw-medium rounded-pill " id="chinhsach"><a href="/yourInfo">Thông tin cá nhân</a></li>
          <li class="nav-item px-3 py-1 bg-secondary text-white fw-medium rounded-pill" id="tracuudonhang">
              <a href="{{ route('order.history') }}">Tra cứu đơn hàng</a>
          </li>
          @if($isLogin) 
          @if($user->getIdQuyen()->getId() == 1 || $user->getIdQuyen()->getId() == 2) 
            <li class="nav-item px-3 py-1 bg-secondary text-white fw-medium rounded-pill" id="tracuudonhang"><a href="/admin">Trang quản trị</a></li>
          @endif
          <li class="nav-item px-3 py-1 bg-secondary text-white fw-medium rounded-pill" id="userDropdownBtn" style="position: relative; cursor: pointer;">
            {{$user->getTenTK()}}
            <div id="userDropdownMenu" class="" style="display: none ; width: 150px; height: auto; position: absolute; right: 0; background: white; border: 1px solid #ccc; padding: 10px; z-index: 999;align-items: center; border-radius: 5px; padding: 15px;">
                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button type="submit" class="btn btn-danger btn-sm" style="height: 40px; width: 120px; margin: auto;">Đăng xuất</button>
                </form>
            </div>
          </li>
          @else 
          <li class="nav-item px-3 py-1 bg-secondary text-white fw-medium rounded-pill" id="taikhoan"><a href="/login">Đăng nhập</a></li>
          @endif
        </ul>
    </div>
    <div class="d-flex justify-content-between gap-5 p-5 bg-gray-200 bg-light">
        <div class="d-flex flex-column gap-3 p-3" style="width: 50%;">
            <h1 class="text-dark fw-semibold">Thanh toán</h1>
            <!-- <form id="paymentForm" class="d-flex flex-column gap-3 p-3" method="get"> -->
                <!-- <input type="hidden" name="listSP" id="listSPInput">     -->
                <input type="hidden" name="tongtien" value="{{ $sum ?? 0 }}">

<!-- Khu vực Họ tên -->
<div class="d-flex flex-column mb-3">
    <label class="text-dark fw-semibold">Họ tên *</label>
    <button class="bg-white border border-0 p-2 rounded d-flex justify-content-between" type="button" 
            data-bs-toggle="modal" data-bs-target="#modalHoTen">
        <div id="hienThiHoTen">{{ ($user && $user->getIdNguoiDung()) ? $user->getIdNguoiDung()->getHoTen() : 'Nhập họ tên' }}</div>
        <i class="fa-solid fa-arrow-right pt-1"></i>
    </button>
</div>

<!-- Khu vực Số điện thoại -->
<div class="d-flex flex-column mb-3">
    <label class="text-dark fw-semibold">Số điện thoại *</label>
    <button class="bg-white border border-0 p-2 rounded d-flex justify-content-between" type="button" 
            data-bs-toggle="modal" data-bs-target="#modalSDT">
        <div id="hienThiSDT">{{ ($user && $user->getIdNguoiDung()) ? $user->getIdNguoiDung()->getSoDienThoai() : 'Nhập số điện thoại' }}</div>
        <i class="fa-solid fa-arrow-right pt-1"></i>
    </button>
</div>

<div class="d-flex flex-column">
    <label class="text-dark fw-semibold" for="">Email *</label>
    {{-- Email thường nằm trực tiếp ở đối tượng TaiKhoan ($user) --}}
    <input disabled class="p-2 border border-0 rounded hover:border-blue-500" type="text" 
           value="{{ $user ? $user->getEmail() : '' }}" required>
</div>

<div class="d-flex flex-column">
    <label class="text-dark fw-semibold text-break" for="">Địa chỉ *</label>
    <button class="bg-white border border-0 p-2 rounded d-flex justify-content-between" type="button" id="btn-diachi" 
            data-bs-toggle="modal" data-bs-target="#accountUpdateModal">
        <div id="hienThiDiachi" class="text-break">
            {{-- Kiểm tra địa chỉ, nếu không có thì hiện thông báo nhắc chọn --}}
            @if($user && $user->getIdNguoiDung())
                {{ $user->getIdNguoiDung()->getDiaChi() ?? 'Chưa có địa chỉ, vui lòng cập nhật' }}
            @else
                Vui lòng đăng nhập để chọn địa chỉ
            @endif
        </div>
        <i class="fa-solid fa-arrow-right pt-1"></i>
    </button>
</div>
                
                <div class="d-flex flex-column">
                    <label class="text-dark fw-semibold" for="">Phương thức thanh toán *</label>
                    <!-- <input class="rounded hover:border-blue-500" type="text" name="pttt" id="" required> -->
                    <select class="p-2 rounded hover:border-blue-500 border border-0" name="pttt" id="pttt">
                        <option value="" disabled>Chọn phương thức thanh toán</option>
                        @foreach($listPTTT as $pttt) 
                            <option value="{{$pttt->getId()}}">{{$pttt->getTenPTTT()}}</option>
                        @endforeach
                    </select>
                </div>
                
                
            <!-- </form> -->
        </div>  
        
        <div class="d-flex flex-column gap-3 p-3 bg-body-secondary rounded" style="width: 50%;height: 100%;">
            <form class="d-flex flex-column gap-3 p-3" id="formSubmit" action="{{route('payment.changestatus')}}" method="post">
                @csrf
                <meta name="csrf-token" content="{{ csrf_token() }}">
                
                {{-- Lớp bảo vệ cho ID Tỉnh --}}
                <input type="hidden" name="tinh" id="idtinh" 
                    value="{{ ($user && $user->getIdNguoiDung() && $user->getIdNguoiDung()->getTinh()) ? $user->getIdNguoiDung()->getTinh()->getId() : '' }}">
                
                <input type="hidden" name="pttt" id="idpttt" value="1">
                <input type="hidden" name="hoten" id="hoten_data" 
                    value="{{ ($user && $user->getIdNguoiDung()) ? $user->getIdNguoiDung()->getHoTen() : '' }}">
                <input type="hidden" name="sodienthoai" id="sodienthoai_data" 
                    value="{{ ($user && $user->getIdNguoiDung()) ? $user->getIdNguoiDung()->getSoDienThoai() : '' }}">

                {{-- Lớp bảo vệ cho Địa chỉ --}}
                <input type="hidden" name="diachi" id="diachidata" 
                    value="{{ ($user && $user->getIdNguoiDung()) ? $user->getIdNguoiDung()->getDiaChi() : '' }}">

                <div class="d-flex justify-content-between">
                    <p class="fw-semibold fs-5" style="color: black;">Sản phẩm</p>
                    <p class="fw-semibold fs-5" style="color: black;">Thành tiền</p>
                </div>
                <hr style="color: gray;">
                <div id="divSP">
                    @if(is_array($listSP) || is_object($listSP))
                        @foreach($listSP as $sp)
                            {{-- xử lý sản phẩm --}}
                            @php
                                $sanPham = app(SanPham_BUS::class)->getModelById($sp->idsp);
                                $total = $sanPham->getDonGia() * $sp->quantity;
                                $tongTien += $total;
                                $soluong = count(app(CTSP_BUS::class)->getCTSPIsNotSoldByIDSP($key->idsp));
                                $flag = false;
                                $tmp = false;
                                if($soluong < $sp->quantity) {
                                    $flag = true;
                                    $tmp = true;
                                }
                            @endphp
                            @if($tmp==true)
                                <div class="alert alert-danger" role="alert">
                                    Số lượng tồn kho không đủ để tiếp tục mua hàng
                                </div>
                            @endif
                            <div data-idsp="{{$sp->idsp}}" data-quantity="{{$sp->quantity}}" class="d-flex justify-content-between gap-3">
                                <div class="d-flex flex-row gap-3">
                                    <img src="/productImg/{{ $sp->idsp }}.webp" style="height: 150px;width: 150px;" class="card-img-top object-fit-cover rounded-top-5" alt="Ảnh sản phẩm">
                                    <div class="d-flex flex-column gap-2">
                                        <p class="text-dark fw-semibold fs-4">{{$sanPham->getTenSanPham()}}</p>
                                        <p class="text-dark fw-semibold fs-6">x{{$sp->quantity}}</p>
                                        <p class="text-dark fw-semibold fs-6">
                                        {{ number_format($sanPham->getDonGia(), 0, ',', '.') }}₫
                                        </p>
                                    </div>
                                </div>
                                <p class="text-danger fw-semibold fs-4">{{ number_format($total, 0, ',', '.') }}₫</p>
                            </div>
                            <hr style="color: gray;">
                            @php
                                $tmp = false;
                            @endphp
                        @endforeach
                    @else
                        <p>Không có sản phẩm nào trong giỏ hàng.</p>
                    @endif
                </div>
                
                <div class="d-flex flex-column gap-3">
                
                    
                    <div class="d-flex justify-content-between">
                        <p class="text-dark fw-semibold fs-4">Tổng tiền</p>
                        <p class="text-danger fw-semibold fs-4" id="tongtien">{{ number_format($sum, 0, ',', '.') }}₫</p>
                    </div>
                    <div class="d-flex justify-content-between ">
                        <p class="text-dark fw-semibold fs-4">Phí vận chuyển:</p>
                        <p class="text-danger fw-semibold fs-4">Miễn phí</p>
                    </div>
                </div>
                {{-- Kiểm tra nếu biến flag tồn tại và có giá trị true --}}
@if(isset($flag) && $flag == true)
    <div class="d-flex flex-column gap-3" style="align-items: center;">
        {{-- Nút thanh toán thường bị disable khi đang chờ phản hồi từ cổng thanh toán --}}
        <button disabled id="saveHoaDon" class="btn btn-info text-white fs-4 fw-semibold" style="width: 300px;" type="submit">
            Thanh toán
        </button>
    </div>    
@else
    <div class="d-flex flex-column gap-3" style="align-items: center;">
        {{-- Mặc định hiện nút Đặt hàng nếu không có flag hoặc flag là false --}}
        <button id="saveHoaDon" class="btn btn-info text-white fs-4 fw-semibold" style="width: 300px;" type="submit">
            Đặt hàng
        </button>
    </div>
@endif
                
            </form>
            
        </div>
    </div>
<!-- Modal chọn Họ tên (Có danh sách) -->
<div class="modal fade" id="modalHoTen" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Chọn hoặc nhập họ tên mới</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <div class="d-flex flex-column gap-4">
            <div class="w-full">
                <div class="d-flex justify-content-between gap-2">
                    <input class="form-control flex-grow-1" type="text" id="inputNewHoTen" placeholder="Thêm họ tên mới...">
                    <button class="btn btn-info text-white" id="btnSaveHoTen">Lưu</button>
                </div>
            </div>
            <hr>
           <div id="listHoTenContainer">
            @php $seenNames = []; @endphp
    @foreach($listDiaChi as $dc)
        @php $hoTen = trim($dc->getHoTen()); @endphp
        @if(!empty($hoTen) && !in_array($hoTen, $seenNames))
            @php $seenNames[] = $hoTen; @endphp
        <div class="form-check py-2 border-bottom d-flex justify-content-between align-items-center">
            <div class="flex-grow-1">
                <input class="form-check-input" type="radio" name="radioHoTen" value="{{ $hoTen }}">
                <label class="form-check-label w-100">{{ $hoTen }}</label>
            </div>
        </div>
        @endif
    @endforeach
</div>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Modal chọn Số điện thoại (Có danh sách) -->
<div class="modal fade" id="modalSDT" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Chọn hoặc nhập số điện thoại mới</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <div class="d-flex flex-column gap-4">
            <div class="w-full">
                <div class="d-flex justify-content-between gap-2">
                    <input class="form-control flex-grow-1" type="text" id="inputNewSDT" placeholder="Thêm số điện thoại mới...">
                    <button class="btn btn-info text-white" id="btnSaveSDT">Lưu</button>
                </div>
            </div>
            <hr>
            <div id="listSDTContainer">
       @php $seenPhones = []; @endphp
    @foreach($listDiaChi as $dc)
        @php $sdt = trim($dc->getSoDienThoai()); @endphp
        @if(!empty($sdt) && !in_array($sdt, $seenPhones))
            @php $seenPhones[] = $sdt; @endphp
        <div class="form-check py-2 border-bottom d-flex justify-content-between align-items-center">
            <div class="flex-grow-1">
                <input class="form-check-input" type="radio" name="radioSDT" value="{{ $sdt }}">
                <label class="form-check-label w-100">{{ $sdt }}</label>
            </div>
        </div>
        @endif
    @endforeach
</div>
        </div>
      </div>
    </div>
  </div>
</div>
<div class="modal fade" id="accountUpdateModal" tabindex="-1" aria-labelledby="userModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg"> <!-- modal-lg để modal to hơn -->
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="userModalLabel">Chọn địa chỉ nhận hàng</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <div class="flex flex-column gap-5">
            <div class=" w-full">
                <form class="d-flex justify-content-between align-items-center gap-2" action="">
    <div class="flex-grow-1">
        <input class="form-control flex-grow-1" 
               type="text" 
               id="addDiaChi" 
               placeholder="Thêm địa chỉ mới..."
               style="border: 2px solid #bcc5cc; border-radius: 4px; padding: 6px 12px; height: 45px;">
    </div>
                    <button class="btn btn-info text-white" id="btnDiaChi" style="height: 50px;">Lưu</button>
                </form>
            </div>
            <hr>
            <div id="listDiaChiContainer">
   @php $seenAddresses = []; @endphp
    @foreach($listDiaChi as $dc)
        @php $diachi = trim($dc->getDiaChi()); @endphp
        @if(!empty($diachi) && !in_array($diachi, $seenAddresses))
            @php $seenAddresses[] = $diachi; @endphp
        <div class="form-check py-2 border-bottom d-flex justify-content-between align-items-center">
            <div class="flex-grow-1">
                <input class="form-check-input" type="radio" name="radioDefault" value="{{ $diachi }}">
                <label class="form-check-label w-100">{{ $diachi }}</label>
            </div>
        </div>
        @endif
    @endforeach
</div>
            <!-- <button></button> -->
        </div>
      </div>
      
    </div>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
