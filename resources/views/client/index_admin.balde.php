<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="{{ asset('css/client/include/navbar.css') }}">
    <link rel="stylesheet" href="{{ asset('css/client/include/footer.css') }}">
    <link rel="stylesheet" href="{{ asset('css/client/Login-Register.css') }}">
    <link rel="stylesheet" href="{{ asset('css/client/HomePageClient.css') }}">
    <link rel="stylesheet" href="{{ asset('css/client/AcctInfoOH.css') }}">
    <?php
      use App\Bus\Auth_BUS;
use App\Bus\CTQ_BUS;
use App\Bus\TaiKhoan_BUS;
use App\Bus\SanPham_BUS;
    $sanPham = app(SanPham_BUS::class);
    ?>
<style>
 .nav-hidden {
    transform: translateY(-100%) !important;
}
</style>
<script>
  document.addEventListener('DOMContentLoaded', function () {
  let lastScrollTop = 0;
    const navbar = document.getElementById("navbar-ctn");

    window.addEventListener("scroll", function() {
      let scrollTop = window.pageYOffset || document.documentElement.scrollTop;
      
      if (scrollTop > lastScrollTop && scrollTop > 100) {
        navbar.classList.add("nav-hidden");
      } else {
        navbar.classList.remove("nav-hidden");
      }
      
      lastScrollTop = scrollTop <= 0 ? 0 : scrollTop; 
    }, false);
   let isLoading = false;
    let debounceTimeout = null;
// 1. XỬ LÝ DROPDOWN USER 
    try {
        const userBtn = document.getElementById('userDropdownBtn');
        const userDropdown = document.getElementById('userDropdownMenu');

        if (userBtn && userDropdown) {
            userBtn.addEventListener('click', function(e) {
                e.preventDefault();
                e.stopPropagation();
                console.log("Đã nhấn vào User Dropdown");
                
                const isHidden = userDropdown.style.display === 'none' || userDropdown.style.display === '';
                userDropdown.style.display = isHidden ? 'block' : 'none';
            });

            // Ngăn đóng menu khi click vào bên trong (chỗ nút Đăng xuất)
            userDropdown.addEventListener('click', (e) => e.stopPropagation());

            // Đóng khi click ra ngoài
            document.addEventListener('click', () => {
                userDropdown.style.display = 'none';
            });
        }
    } catch (e) {
        console.error("Lỗi dropdown user:", e);
    }
    
    const searchForm = document.querySelector('form[role="search"]');

    function loadProducts(params = '') {
    fetch('/index' + params)
        .then(response => response.json())
        .then(data => {
            const productList = document.getElementById('product-list');
            productList.innerHTML = ''; // Xóa danh sách cũ

            if (data.listSP.length === 0) {
                productList.innerHTML = '<h3 class="text-center text-gray w-100">Không có sản phẩm cần tìm</h3>';
            } else {
                data.listSP.forEach(function(sp) {
                    const html = `
                        <div class="col rounded-5 product" data-idsp="${sp.id}" data-tensp="${sp.tenSanPham}">
                            <div class="card shadow-sm border-0 h-100">
                                <img src="${sp.img}" class="card-img-top" alt="${sp.tenSanPham}">
                                <div class="card-body">
                                    <h6 class="card-title">${sp.tenSanPham}</h6>
                                    <p class="card-text">${sp.dongia}</p>
                                </div>
                            </div>
                        </div>`;
                    productList.insertAdjacentHTML('beforeend', html);
                });
            }
        })
        .catch(error => console.error('Error:', error));
}

    // Gọi hàm khi trang được tải
    loadProducts(window.location.search); // Gửi tham số tìm kiếm nếu có

    // Xử lý sự kiện cho form tìm kiếm
    if (searchForm) {
        searchForm.addEventListener('submit', function (e) {
            e.preventDefault(); // Ngăn chặn hành động gửi form mặc định
            const params = new URLSearchParams(new FormData(searchForm)).toString();
            loadProducts('?' + params); // Gọi hàm để tải sản phẩm với tham số tìm kiếm
        });
    }
  const searchForms = document.querySelectorAll('form[role="search"]');

    searchForms.forEach(function (searchForm) {
      const keywordInput = searchForm.querySelector('input[name="keyword"]');

      searchForm.addEventListener('submit', function (e) {
        e.preventDefault(); // Ngăn chặn hành động gửi form mặc định

        const currentUrl = new URL(window.location.href);
        // const keywordInput = searchForm.querySelector('input[name="keyword"]'); 
        const lspSelect = searchForm.querySelector('select[name="lsp"]');
        const hangSelect = searchForm.querySelector('select[name="hang"]');
        const khoangGiaSelect = searchForm.querySelector('select[name="khoanggia"]');

        // Xóa các tham số cũ
        // currentUrl.searchParams.delete('keyword');
        // currentUrl.searchParams.delete('lsp');
        // currentUrl.searchParams.delete('hang');
        // currentUrl.searchParams.delete('khoanggia');

        // Thêm các tham số mới
        if (keywordInput && keywordInput.value.trim()) {
          currentUrl.searchParams.set('keyword', keywordInput.value.trim());
        }
        if (lspSelect && lspSelect.value ) {
          currentUrl.searchParams.set('lsp', lspSelect.value);
        }
        if (hangSelect && hangSelect.value ) {
          currentUrl.searchParams.set('hang', hangSelect.value);
        }
        if (khoangGiaSelect && khoangGiaSelect.value) {
          currentUrl.searchParams.set('khoanggia', khoangGiaSelect.value);
        }

        // Chuyển hướng đến URL mới
        window.location.href = currentUrl.toString();
      });

      // Thêm sự kiện change cho các select
      const lspSelect = searchForm.querySelector('select[name="lsp"]');
      if (lspSelect) {
        lspSelect.addEventListener('change', function () {
          searchForm.dispatchEvent(new Event('submit'));
        });
      }

      const hangSelect = searchForm.querySelector('select[name="hang"]');
      if (hangSelect) {
        hangSelect.addEventListener('change', function () {
          searchForm.dispatchEvent(new Event('submit'));
        });
      }
      const khoangGiaSelect = searchForm.querySelector('select[name="khoanggia"]');
      if (khoangGiaSelect) {
        khoangGiaSelect.addEventListener('change', function () {
          searchForm.dispatchEvent(new Event('submit'));
        });
      }
      if (keywordInput) {
        keywordInput.addEventListener('input', function () {
          searchForm.dispatchEvent(new Event('submit'));
        });
      }
    });
  const userBtn = document.getElementById('userDropdownBtn');
  const dropdownMenu = document.getElementById('userDropdownMenu');

  if (userBtn && dropdownMenu) {
    userBtn.addEventListener('click', function (e) {
      e.stopPropagation(); // tránh việc click ngoài làm tắt menu ngay lập tức
      const isVisible = dropdownMenu.style.display === 'block';
      dropdownMenu.style.display = isVisible ? 'none' : 'block';
    });

    // Click ngoài menu thì ẩn dropdown
    document.addEventListener('click', function (e) {
      if (!userBtn.contains(e.target)) {
        dropdownMenu.style.display = 'none';
      }
    });
  }
  document.querySelectorAll(".product").forEach((productDiv) => {
    productDiv.addEventListener("click", () => {
      console.log("Dữ liệu sản phẩm:", productDiv.dataset);
      const modal = document.querySelector('#productDetailModal');
      if (!modal) return;
      
      // Cập nhật thông tin modal
      modal.querySelector('input[name="idsp"]').value = productDiv.dataset.idsp;
      modal.querySelector('div[name="tensp"]').textContent = productDiv.dataset.tensp;
      modal.querySelector('div[name="hang"]').textContent = productDiv.dataset.hang;
      modal.querySelector('div[name="lsp"]').textContent = productDiv.dataset.lsp;
      modal.querySelector('div[name="mota"]').textContent = productDiv.dataset.mota;
      modal.querySelector('div[name="dongia"]').textContent = productDiv.dataset.dongia;
      modal.querySelector('div[name="tgbh"]').textContent = productDiv.dataset.tgbh;
      modal.querySelector('img[name="img"]').src = productDiv.dataset.img;
      modal.querySelector('div[name="stock"]').textContent = productDiv.dataset.stock;

      // Hiển thị modal
      // const bootstrapModal = new bootstrap.Modal(modal);
      // bootstrapModal.show();
      document.getElementById("productDetailModal").style.display = "block";

    });
  });

  // Đóng modal khi click nút đóng
  document.querySelector(".btn-close").addEventListener("click", () => {
    document.getElementById("productDetailModal").style.display = "none";
  });
  const successAlert = document.getElementById('successAlert');
  if (successAlert) {
      setTimeout(() => {
          successAlert.classList.remove('show');
          successAlert.classList.add('fade');
          successAlert.style.opacity = 0;
      }, 3000); // 3 giây

      setTimeout(() => {
          successAlert.remove(); // Xoá hẳn khỏi DOM
      }, 4000);
  }
});
</script>

    <!-- Nội dung trang chính ở đây -->
     <header>
    <div class="text-white" id="navbar-ctn">
      <div class="top-nav">
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
      <div class="navbar text-white navbar-expand" id="navbar">
      <a href="/index" class="navbar-brand">
        <img src="https://img.ws.mms.shopee.vn/vn-11134216-7r98o-lq2sgdy60w5uba" 
            alt="Logo" 
            class="img-fluid rounded-5" 
            style="height: 40px;">
      </a>
        <form action="" method="get" role="search" class="w-100">
          <ul class="d-flex justify-content-center gap-5 w-100 pt-4" >
            <li class="nav-item fw-medium my-2 mx-2" id="item-sanpham"><a href="#list-product" class="nav-link text-white">Sản Phẩm <i class="fa-regular fa-angle-up"></i></a></li>
            <li class="nav-item fw-medium" style="position: relative;"><input class="rounded-pill py-2" type="text" placeholder="Tìm kiếm sản phẩm" style="width: 300px;outline: none;border:none;padding: 0 30px 0 10px;" name="keyword" value="{{ request('keyword') }}" {{ request('keyword') ? '' : 'selected' }}><i class="fa-solid fa-magnifying-glass" style="position: absolute; right: 10px; color: #555; padding: 10px"></i></li>
            <!-- <li class="nav-item fw-medium my-2" id="item-xemthem"><a href="" class="nav-link text-white">Xem Thêm <i class="fa-regular fa-angle-up"></i></a></li> -->
            <!-- <li class="nav-item fw-medium"><a href="#" class="nav-link text-white">Hành Trình Tử Tế</a></li> -->
            @if($isLogin && ($user->getIdQuyen()->getId() != 1 || $user->getIdQuyen()->getId() != 2))
              <li class="nav-item fw-medium my-2" id="item-giohang">
                <a href="{{ url('/yourcart?email=' . $user->getEmail()) }}" class="nav-link text-white">
                  Giỏ Hàng <i class="fa-light fa-bag-shopping" style="position: relative;">
                    <small style="padding: 5px;background:rgb(232, 164, 76);color: white;position: absolute;right: -15px;bottom: -15px;font-size: 12px;border-radius: 50%;">0</small>
                  </i>
                </a>
              </li>
            @endif
          </ul>
        </form>
        
      </div>
    </div>
  </header>
  <div class="submenu card" style="z-index: 100;">
    <div class="card-menu d-flex ">

    </div>

  </div>
  <div class="ctn-content">
  <img src="{{ asset('/client/img/Cover-web-ban-PC-scaled.png') }}" class="img-fluid w-100">

    <div class="main justify-content-center d-flex">
      <div class="best-seller text-center">
        <h1 class="text-start" style="width: fit-content; ;color: #55d5d2; border-bottom: solid 5px #55d5d2;margin-right: auto; font-family: Roboto;">BÁN CHẠY NHẤT</h1>
        <div class="row my-5" style="max-height: 380px;display: flex;">
        <div class="row row-cols-1 row-cols-sm-2 row-cols-md-4 g-4 my-5 w-100">
              @foreach($top4Product as $sp)
                @php
                  $stock = $sanPham->getStock($sp->getId());
                @endphp
                <div class="col rounded-5 product"
                      data-stock="{{ $stock }}"
                      data-idsp="{{ $sp->getId() }}"
                      data-tensp="{{ $sp->getTenSanPham() }}"
                      data-hang="{{ $sp->getIdHang()->getTenHang() }}"
                      data-lsp="{{ $sp->getIdLSP()->getTenLSP() }}"
                      data-mota="{{ $sp->getMoTa() }}"
                      data-dongia="{{ number_format($sp->getDonGia(), 0, ',', '.') }}₫"
                      data-tgbh="{{ $sp->getThoiGianBaoHanh() }}"
                      data-img="/productImg/{{ $sp->getId() }}.webp"
                      data-bs-toggle="modal"
                      data-bs-target="#productDetailModal">
                  <div class="card shadow-sm border-0 h-100 col rounded-5 product-item">
                    <div class="ratio ratio-1x1">
                      <img src="/productImg/{{ $sp->getId() }}.webp" class="card-img-top object-fit-cover rounded-top-5" alt="Ảnh sản phẩm">
                    </div>
                    <div class=" card-body d-flex flex-column justify-content-between h-60 p-3">
                      <h6 class="card-title text-truncate text-center w-100" title="{{ $sp->getTenSanPham() }}">{{ $sp->getTenSanPham() }}</h6>
                      <div class="d-flex align-items-center justify-content-between mt-auto rounded-4 bg-blue-500">
                        <span class="fw-bold text-primary fs-5 text-center w-100 text-white rounded-4 flex justify-center p-2" style="background-color: #55d5d2;height: 50px;">
                          {{ number_format($sp->getDonGia(), 0, ',', '.') }}₫
                        </span>
                        <i class="fa-solid fa-arrow-up-right text-success"></i>
                      </div>
                    </div>
                  </div>
                </div>
            @endforeach
          </div>
        </div>

      </div>

    </div>
    <div class="banner-small " style="margin-top: 150px;">
      <div class="bnsm"><img src="/client/img/small-banner1.png" class="img-fluid w-100"></div>
      <div class="bnsm"><img src="/client/img/small-banner2.png" class="img-fluid w-100"></div>
    </div>
    <div class="ctn-danhmucsanpham" style="background-color: #f6f2f2;padding-bottom: 30px;">
      <div class="d-flex justify-content-between p-5">
        <h1 style="font-family: Sigmar;font-weight: 800;color: #555;width: 40%;">BỘ SƯU TẬP MỚI NHẤT</h1>
        <form method="get" role="search" class="d-flex justify-content-between gap-5" style="width: 70%;">
          <select class="form-select w-15" name="lsp" id="lsp">
              <option disabled value="" {{ request('lsp') ? '' : 'selected' }}>Lọc theo loại</option>
              <option value="0">Xem tất cả</option>
              @foreach($listLSP as $lsp)
              <option value="{{ $lsp->getId() }}" {{ request('lsp') == $lsp->getId() ? 'selected' : '' }}>{{$lsp->gettenLSP()}}</option>
              @endforeach
          </select>
          <select class="form-select w-15" name="hang" id="hang">
              <option disabled value="" {{ request('hang') ? '' : 'selected' }}>Lọc theo hãng</option>
              <option value="0">Xem tất cả</option>
              @foreach($listHang as $h)
              <option value="{{ $h->getId() }}" {{ request('hang') == $h->getId() ? 'selected' : '' }}>{{$h->gettenHang()}}</option>
              @endforeach
          </select>
          <select class="form-select w-15" name="khoanggia">
              <option value="" disabled {{ request('khoanggia') ? '' : 'selected' }}>Chọn khoảng giá</option>
              <option value="0" {{ request('khoanggia') == '0' ? 'selected' : '' }}>Xem tất cả</option>
              <option value="[0-500000]" {{ request('khoanggia') == '[0-500000]' ? 'selected' : '' }}>0 - 500.000đ</option>
              <option value="[500000-1000000]" {{ request('khoanggia') == '[500000-1000000]' ? 'selected' : '' }}>500.000đ - 1.000.000đ</option>
              <option value="[1000000-1500000]" {{ request('khoanggia') == '[1000000-1500000]' ? 'selected' : '' }}>1.000.000đ - 1.500.000đ</option>
              <option value="[1500000-2000000]" {{ request('khoanggia') == '[1500000-2000000]' ? 'selected' : '' }}>1.500.000đ - 2.000.000đ</option>
              <option value="[2000000-3000000]" {{ request('khoanggia') == '[2000000-3000000]' ? 'selected' : '' }}>2.000.000đ - 3.000.000đ</option>
              <option value="[3000000-5000000]" {{ request('khoanggia') == '[3000000-5000000]' ? 'selected' : '' }}>3.000.000đ - 5.000.000đ</option>
              <option value="[5000000-...]" {{ request('khoanggia') == '[5000000-...]' ? 'selected' : '' }}>5.000.000đ - ...</option>
          </select>
        </form>
      </div>

      <div class="content-prd " style="margin: 0 5% 0;display: flex;">
        <div class="container-filter my-5" style="width: 0%;opacity: 0;height: 0;transition: all .4s ease;">
          <div class="ft-mau-sac">
            <h3>Màu sắc</h3>
            <ul class="list-checkBox">
              <li><input type="checkbox">Cam</li>
              <li><input type="checkbox">Đỏ</li>
              <li><input type="checkbox">Vàng</li>
              <li><input type="checkbox">Đen</li>
              <li><input type="checkbox">Xám</li>
              <li><input type="checkbox">Trắng</li>
              <li><input type="checkbox">Lục</li>
              <li><input type="checkbox">Lam</li>
              <li><input type="checkbox">Tím</li>
              <li><input type="checkbox">Hồng</li>
              <li><input type="checkbox">Cam</li>
              <li><input type="checkbox">Đỏ</li>
              <li><input type="checkbox">Vàng</li>
              <li><input type="checkbox">Đen</li>
              <li><input type="checkbox">Xám</li>
              <li><input type="checkbox">Trắng</li>
              <li><input type="checkbox">Lục</li>
              <li><input type="checkbox">Lam</li>
              <li><input type="checkbox">Tím</li>
              <li><input type="checkbox">Hồng</li>
            </ul>
            <span id="ft-mausac-xemthem">Xem thêm</span>
          </div>
          <div class="ft-chat-lieu">
            <h3>Chất liệu</h3>
            <ul class="list-checkBox">
              <li><input type="checkbox">Atetace</li>
              <li><input type="checkbox">Nhựa</li>
              <li><input type="checkbox">Nhựa Dẻo</li>
              <li><input type="checkbox">Nhựa Pha Kim Loại</li>
              <li><input type="checkbox">Kim loại</li>
              <li><input type="checkbox">Titan</li>

            </ul>
          </div>
          <div class="ft-hinh-dang">
            <h3>Hình dáng</h3>
            <ul class="list-checkBox">
              <li><input type="checkbox">Mắt mèo</li>
              <li><input type="checkbox">Hình tròn</li>
              <li><input type="checkbox">Hình vuông</li>
              <li><input type="checkbox">Hình tròn</li>
              <li><input type="checkbox">Hình Oval</li>
              <li><input type="checkbox">Đa giác</li>
              <li><input type="checkbox">Chữ nhật</li>

            </ul>
          </div>
        </div>
        <div class="dmsp w-100" id="list-product">
          <div class="container-rows" style="width: 100%;display: block;" id="product-list">
          @if(empty($listSP))
            <div class="row row-cols-1 row-cols-sm-2 row-cols-md-4 g-4 my-5 w-100">
              <h3 class="text-center text-gray w-100">Không có sản phẩm cần tìm</h3>
            </div>
          @else
            @php $count = 0; @endphp
            <div class="row row-cols-1 row-cols-sm-2 row-cols-md-4 g-4 my-5 w-100">
              @foreach($listSP as $sp)
                @if($count++ >= 8) @break @endif
                @php
                  $stock = $sanPham->getStock($sp->getId());
                @endphp
                <div class="col rounded-5 product"
                      data-stock="{{ $stock }}"
                      data-idsp="{{ $sp->getId() }}"
                      data-tensp="{{ $sp->getTenSanPham() }}"
                      data-hang="{{ $sp->getIdHang()->getTenHang() }}"
                      data-lsp="{{ $sp->getIdLSP()->getTenLSP() }}"
                      data-mota="{{ $sp->getMoTa() }}"
                      data-dongia="{{ number_format($sp->getDonGia(), 0, ',', '.') }}₫"
                      data-tgbh="{{ $sp->getThoiGianBaoHanh() }}"
                      data-img="/productImg/{{ $sp->getId() }}.webp"
                      data-bs-toggle="modal"
                      data-bs-target="#productDetailModal">
                  <div class="card shadow-sm border-0 h-100 col rounded-5 product-item">
                    <div class="ratio ratio-1x1">
                      <img src="/productImg/{{ $sp->getId() }}.webp" class="card-img-top object-fit-cover rounded-top-5" alt="Ảnh sản phẩm">
                    </div>
                    <div class=" card-body d-flex flex-column justify-content-between h-60 p-3">
                      <h6 class="card-title text-truncate text-center w-100" title="{{ $sp->getTenSanPham() }}">{{ $sp->getTenSanPham() }}</h6>
                      <div class="d-flex align-items-center justify-content-between mt-auto rounded-4 bg-blue-500">
                        <span class="fw-bold text-primary fs-5 text-center w-100 text-white rounded-4 flex justify-center p-2" style="background-color: #55d5d2;height: 50px;">
                          {{ number_format($sp->getDonGia(), 0, ',', '.') }}₫
                        </span>
                        <i class="fa-solid fa-arrow-up-right text-success"></i>
                      </div>
                    </div>
                  </div>
                </div>
            @endforeach
          @endif
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

      </div>

    </div>
  </div>
  <div class="container-custom">
    <a>
      <p>Bảo hành trọn đời</p>
    </a>
    <a>
      <p>Đo mắt miễn phí</p>
    </a>
    <a>
      <p>Thu cũ đổi mới</p>
    </a>
    <a>
      <p>Vệ sinh & Bảo quản</p>
    </a>
  </div>
  <div class="d-flex" style="padding: 0 5%;">
    <div style="width: 40%;">
        <img src="/client/img/Artboard-2-copy-5.png" alt="" class="img-fluid w-100">
    </div>

    <div style="padding-left: 50px; width: 60%;">
        <h2 style="padding: 30px; background-color: #e4f4f4; border-top-left-radius: 30px; border-top-right-radius: 30px; border-bottom-right-radius: 30px; color: #55d5d2; font-weight: 800; text-transform: uppercase;">
            VŨ TRỤ TRUYỀN THÔNG
        </h2>
        
        <div style="padding-top: 20px; color: #413f3f; line-height: 1.6;">
            <p style="font-weight: bold; font-size: 1.1rem; margin-bottom: 15px;">
                Kính mắt Anna chắc không còn quá xa lạ với giới trẻ nữa. Đây là kênh thông tin mua sắm và giải trí dành cho giới trẻ, là “món ăn tinh thần” hằng ngày không thể thiếu của mỗi người trẻ.
            </p>
            
            <p>
                Được thành lập từ năm 2015, trải qua hơn 7 năm phát triển kính mắt Anna đã, đang và sẽ chiếm vị trí không thể thiếu đối với các bạn trẻ. 
            </p>
            
            <p>
                Cập nhật thông tin thời trang nhanh chóng và phù hợp với thị hiếu của khán giả thông qua các mạng xã hội như <strong>Facebook, Tiktok, Instagram, Youtube</strong>, kính mắt Anna là lựa chọn hàng đầu cho những ai muốn tận hưởng các bài viết, video vừa mang tính giải trí mà vẫn có đầy đủ thông tin cần thiết.
            </p>
        </div>
    </div>
</div>

  </div>
  <!-- Footer -->
  <footer>
    <div class="footer-container d-flex">
      <div class="footer-left">
        <div class="logo">
          <img src="/client/img/logo.svg" alt="Anna Logo">
        </div>
        <div class="newsletter">
          <p>Đăng kí để nhận tin mới nhất</p>
          <div class="email-input">
            <input type="email" placeholder="Để lại email của bạn" style="font-size:20px;padding: 5px; border-radius:20px;width:50%;">
            <button>></button>
          </div>
        </div>
        <div class="social-icons">
          <a href="#"><i class="fa-brands fa-facebook-f"></i></a>
          <a href="#"><i class="fa-brands fa-instagram"></i></a>
          <a href="#"><i class="fa-brands fa-tiktok"></i></a>
          <a href="#"><i class="fa-brands fa-youtube"></i></a>
        </div>
      </div>
      <div class="footer-center">
        <div class="product-info">
          <label for="">Sản phẩm</label>
          <ul>
             <li><a href="#">Gọng Kính</a></li>
            <li><a href="#">Kính râm</a></li>
            <li><a href="#">Kính mát</a></li>
          </ul>
        </div>
        <div class="purchase-policy">
         <label for="">Chính sách mua hàng</label>
          <ul>
            <li><a>Hình thức thanh toán</a></li>
            <li><a>Chính sách giao hàng</a></li>

          </ul>
        </div>
      </div>
      <div class="footer-right">
        <div class="contact-info">
          <label for="" style="font-size: 22px;color:#e6f4f3;">Thông tin liên hệ</label>
          <ul>
            <li>19000359</li>
            <li>marketing@kinhmatanna.com</li>
          </ul>
        </div>
        <div class="business-info">
          <p>MST: 0108195925</p>

        </div>
      </div>
    </div>
    <div class="copyright">
      <p style="margin: 0;">Anna 2015-2026. Design by OKHUB Viet Nam</p>
    </div>
  </footer>

  <!-- modal chi tiết sản phẩm -->
  <div class="modal fade " id="productDetailModal" tabindex="-1" aria-labelledby="userModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-xl"> <!-- modal-lg để modal to hơn -->
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title text-center" id="userModalLabel">Thông tin sản phẩm</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body d-flex flex-column mb-3">
        <div class="p-3 d-flex flex-row" style="height: 80%">
          <div class="" style="width: 30%; ">
          <div class="ratio ratio-1x1">
            <img name="img" src="" alt="Product Image" class="rounded" />
          </div>
          </div>
          <div class="p-2 d-flex flex-column gap-2" style="width: 70%;">
            <div class="rounded-5 p-1 fw-semibold bg-primary-subtle" style="width: 120px;font-size: small;text-align: center;" name="lsp"></div>
            <div class="fs-3 fw-semibold" name="tensp" id=""></div>
            <div class="fs-2 fw-bold" style="color: #55d5d2;" name="dongia"></div>
            <div class="fs-6 fw-semibold d-flex flex-row gap-3 align-center" style="color: #413f3f;">Thương hiệu: <div class=" fw-bold" style="color: red;" name="hang"></div></div>
            <div class="fs-6 fw-semibold d-flex flex-row gap-3 align-center" style="color: #413f3f;">Mô tả: <div name="mota"></div></div>
            <div class="fs-6 fw-semibold d-flex flex-row gap-3 align-center" style="color: #413f3f;">Thời gian bảo hành: <div name="tgbh"></div> tháng</div>
            <div class="fs-6 fw-semibold d-flex flex-row gap-3 align-center" style="color: #413f3f;">Số lượng:<div name="stock"> </div></div>
            
          </div>
        </div>
        <div class="p-5 d-flex flex-row-reverse gap-5" style="height: 20%;">
          @if($isLogin)
            @if($user->getIdQuyen()->getId() != 1 || $user->getIdQuyen()->getId() != 2)
              <button type="button" class="btn btn-danger" style="width: 150px;" class="btn-close" data-bs-dismiss="modal" aria-label="Close">Hủy</button>
              <form action="{{ route('index.addctgh') }}" method="post">
                  @csrf
                  <input type="hidden" name="idgh" value="{{$gh->getIdGH()}}">
                  <input type="hidden" name="idsp" value="">
                  <button type="submit" class="btn btn-light" style="width: 200px; border: 2px solid #ced4da;">Thêm vào giỏ</button>
              </form>
              <button type="button" class="btn btn-light" style="width: 150px;">Mua ngay</button>
            @else
              <button type="button" class="btn btn-danger" style="width: 150px;" class="btn-close" data-bs-dismiss="modal" aria-label="Close">Hủy</button>
            @endif
          @endif
        </div>
      </div>
      
    </div>
  </div>
</div>
@if(session('error'))
    <div class="alert alert-danger successAlert">{{ session('error') }}</div>
@elseif(session('success'))
    <div class="alert alert-success successAlert">{{ session('success') }}</div>        
@endif
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
