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
    @if(session('error'))
        <div class="alert alert-danger successAlert fixed-top left-alert" style="width: 500px;">{{ session('error') }}</div>
    @elseif(session('success'))
        <div class="alert alert-success successAlert fixed-top left-alert" style="width: 500px;">{{ session('success') }}</div>        
    @endif
    <style>
    .custom-range::-webkit-slider-thumb {
        background: #55d5d2 !important;
        border: 2px solid #fff;
        box-shadow: 0 0 5px rgba(0,0,0,0.1);
        cursor: pointer;
    }
    .txtgia {
        background-color: #55d5d2; 
        transition: all 0.3s ease-in-out !important; 
        cursor: pointer;
        display: inline-flex; 
        align-items: center;
        justify-content: center;
    }

    .txtgia:hover {
      
        filter: brightness(1.1); 
        
      
        box-shadow: 0 0 15px rgba(85, 213, 210, 0.7); 
        
   
        transform: translateY(-3px);
    }


   
    .btn-cart {
        background-color: #f8f9fa;
        color: #333;
        border: 2px solid #ced4da; 
        transition: all 0.2s ease-in-out;
    }

   
    .btn-cart:hover:not(:disabled) {
        background-color: #ffffff !important; 
        color: #000;
        
    
        border: 3px solid #6c757d !important; 
        
       
        box-shadow: 0 0 10px rgba(108, 117, 125, 0.4); 
    }

   
    .btn-buy {
        background-color: #55d5d2; 
        color: white;
        border: none; 
        transition: all 0.3s ease-in-out;
    }

   
    .btn-buy:hover:not(:disabled) {
      
        background-color: #22a5a3 !important; 
        
       
        box-shadow: 0 0 15px rgba(46, 204, 113, 0.6); 
        
       
        filter: brightness(1.1);
    }
      .left-alert {
    position: fixed;
    top: 20px;
    left: 20px;
    z-index: 1060; 
    width: 500px;
    pointer-events: none; 
}

header {
    position: fixed;
    top: 0;
    width: 100%; /* Đảm bảo header chiếm hết chiều ngang */
    transition: transform 0.3s ease-in-out; /* Chuyển động mượt */
    z-index: 1002;
    background-color: #dddd;
    border-radius: 0 0 20px 20px;
    padding: 10px 10%;
}

header.hidden {
    transform: translateY(-100%);
}

.top-nav {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 5px 0;
}

.top-nav p {
    color: #55d5d2;
    font-size: 14px;
    font-weight: 600;
    margin: 0;
}

.list-top-nav {
    display: flex;
    gap: 15px;
    margin: 0;
    padding: 0;
    list-style: none;
}

.list-top-nav .nav-item {
    position: relative;
    white-space: nowrap;
    padding: 5px 15px;
    background-color: #6c757d;
    border-radius: 20px;
}

.list-top-nav .nav-item a {
    color: white;
    text-decoration: none;
    font-weight: 500;
}

.list-top-nav .nav-item:hover {
    background-color: #5a6268;
}

#userDropdownMenu {
    display: none;
    position: absolute;
    top: 100%;
    right: 0;
    background: white;
    border: 1px solid #ccc;
    border-radius: 5px;
    padding: 10px;
    z-index: 999;
    width: 120px;
    text-align: center;
    box-shadow: 0 2px 5px rgba(0, 0, 0, 0.2);
}

.navbar {
    display: flex;
    align-items: center;
    gap: 20px;
    padding: 10px 0;
}

.navbar-brand img {
    height: 40px;
}

.navbar .d-flex {
    width: 100%;
    align-items: center;
    gap: 30px;
}

form[role="search"] {
    position: relative;
    flex-grow: 1;
}

form[role="search"] input {
    width: 100%;
    max-width: 400px;
    padding: 8px 40px 8px 10px;
    border: none;
    outline: none;
    border-radius: 20px;
    font-size: 16px;
}

form[role="search"] i {
    position: absolute;
    right: 10px;
    top: 50%;
    transform: translateY(-50%);
    color: #555;
}

.navbar ul {
    display: flex;
    gap: 40px;
    margin: 0;
    padding: 0;
    list-style: none;
}

.nav-item {
    white-space: nowrap;
}

.nav-item a {
    color: white;
    text-decoration: none;
    font-weight: 500;
}

#item-sanpham a:hover,
#item-xemthem a:hover,
#item-giohang a:hover {
    color: rgb(44, 169, 191);
    transition: color 0.2s ease;
}

#navbar-ctn {
    transition: transform 0.3s ease;
}

.nav-item .dropdown-menu {
    display: none !important; 
    position: absolute;
    background-color: white;
    border: 1px solid #ccc;
    border-radius: 5px;
    padding: 10px;
    z-index: 1000;
    min-width: 200px;
    box-shadow: 0 2px 5px rgba(0, 0, 0, 0.2);
}

.nav-item:hover .dropdown-menu {
    display: block !important;
}

.dropdown-menu .dropdown-item {
    position: relative;
    padding: 5px 10px;
    border-radius: 5px;
}
.ratio-1x1 {
    --bs-aspect-ratio: 100%;
    max-width: 100%;
    max-height: 300px;
    overflow: hidden;
}

.card-img-top {
    max-width: 100%;
    max-height: 100%;
    object-fit: contain;
    width: auto;
    height: 100%;
}

.card {
    max-width: 100%;
    width: 100%;
}

.product-item {
    max-width: 300px;
    width: 100%;
}

@media (max-width: 768px) {
    .product-item {
        max-width: 200px;
    }
    .ratio-1x1 {
        max-height: 200px;
    }
}

.dropdown-menu .dropdown-item.active a {
    color: white;
}

.dropdown-menu .dropdown-item a {
    display: block;
    padding: 5px 10px;
    color: #333;
    text-decoration: none;
}

.dropdown-menu .dropdown-item a:hover {
    background-color: #f0f0f0;
    border-radius: 3px;
}

.dropdown-menu .submenu {
    display: none;
    position: absolute;
    left: 100%;
    top: 0;
    background-color: white;
    border: 1px solid #ccc;
    border-radius: 5px;
    padding: 10px;
    z-index: 1000;
    min-width: 150px;
    box-shadow: 0 2px 5px rgba(0, 0, 0, 0.2);
}

.dropdown-item:hover .submenu {
    display: block;
}

.submenu li a {
    display: block;
    padding: 5px 10px;
    color: #333;
    text-decoration: none;
}

.submenu li a:hover {
    background-color: #f0f0f0;
    border-radius: 3px;
}

.filter-dropdown {
    position: relative;
    z-index: 1001;
}
.filter-item {
        position: relative;
        z-index: 1;
    }

    .form-select {
        cursor: pointer;
        border: 1px solid #ced4da;
    }
.filter-dropdown .dropdown-menu {
        display: none !important; /* Vô hiệu hóa menu cũ nếu còn sót */
    }

.filter-dropdown .dropdown-menu.show {
    display: block;
}

.filter-options label {
    font-weight: 500;
    margin-bottom: 5px;
    font-size: 18px;
}

.filter-options select,
.filter-options input {
    font-size: 18px;
    padding: 5px;
}

.filter-options button {
    font-size: 18px;
    padding: 5px;
}

.filter-button {
    cursor: pointer;
    height: 38px;
    line-height: 38px;
    padding: 0 8px;
    white-space: nowrap;
    width: 100%;
    border: 2px solid rgb(53, 169, 193);
    background-color: #fff;
    border-radius: 4px;
    font-size: 18px;
    font-weight: 600;
    color: black;
}

.filter-button:hover {
    background-color: rgba(46, 199, 199, 0.46);
    border-color: rgb(29, 167, 185);
    color: black;
    transition: all 0.2s ease;
}

.filter-item {
    flex: 1;
    min-width: 0;
    max-width: 30%;
}

.apply-filter-btn, #apply-lsp-filter, #apply-hang-filter {
    font-size: 18px;
    padding: 3px 6px;
    background-color: rgba(93, 225, 243, 0.63);
    border: 1px solid rgb(44, 169, 191);
    color: black;
    border-radius: 3px;
    width: 80px;
    height: 30px;
    line-height: 1.5;
    font-weight: 500;
}

.apply-filter-btn:hover, #apply-lsp-filter:hover, #apply-hang-filter:hover {
    background-color: rgba(47, 217, 243, 0.67);
    border-color: rgb(44, 169, 191);
    color: black;
    transition: all 0.2s ease;
}
.alert:not(.alert-container-top-right .alert) {
    display: none !important;
}
.alert-container-top-right {
    position: fixed;
    top: 20px;
    right: 20px;
    z-index: 9999;
    width: 320px;
}
.nav-hidden {
    transform: translateY(-100%) !important;
}
/* Hiển thị Mega Menu khi hover */
#item-sanpham:hover #sanpham-mega-menu {
    display: block !important;
    opacity: 1;
    visibility: visible;
    transform: translateX(-50%) translateY(0);
}

#sanpham-mega-menu {
    display: none;
    position: absolute;
    top: 100%;
    left: 50%;
    transform: translateX(-50%) translateY(10px);
    background-color: #fff;
    border: none;
    border-radius: 12px;
    box-shadow: 0 15px 35px rgba(0,0,0,0.15);
    transition: all 0.3s ease;
    z-index: 1050;
}

.menu-column h6 {
    font-size: 14px;
    border-bottom: 2px solid #55d5d2;
    display: inline-block;
    padding-bottom: 5px;
}

.header-filter-node {
    line-height: 1.2; /* Giảm độ cao dòng */
}

.header-filter-node a {
    display: block;
    padding: 2px 0; 
    font-size: 14px;
}

.header-filter-node a:hover {
    color: #55d5d2 !important;
    padding-left: 5px;
}

.mega-filter-link {
    display: inline-block;
    width: 100%;
    padding: 5px 0;
    transition: all 0.3s ease-in-out !important; 
    position: relative;
}

.mega-filter-link:hover {
    color: #55d5d2 !important;
    font-weight: 600;
}
/* Style tổng thể cho ô Select */
.filter-item .form-select {
    border: 1px solid #e0e0e0;
    border-radius: 6px;
    padding: 8px 12px;
    font-size: 0.9rem;
    color: #495057;
    background-color: #fff;
    transition: all 0.2s ease-in-out;
    cursor: pointer;
    box-shadow: inset 0 1px 2px rgba(0,0,0,0.05);
}


.filter-item .form-select:hover {
    border-color: #55d5d2;
}

.filter-item .form-select:focus {
    border-color: #55d5d2;
    outline: 0;
    box-shadow: 0 0 0 0.25rem rgba(85, 213, 210, 0.25); 
}

.filter-item label {
    color: #333;
    letter-spacing: 0.5px;
    display: block;
    margin-bottom: 5px;
}


.form-select::-webkit-scrollbar {
    width: 6px;
}
.form-select::-webkit-scrollbar-thumb {
    background-color: #55d5d2;
    border-radius: 10px;
}
    </style>
  <script>
  function updatePriceLabel(value) {
    const formatted = new Intl.NumberFormat('vi-VN').format(value) + 'đ';
    document.getElementById('priceLabel').textContent = formatted;
}
document.addEventListener('DOMContentLoaded', () => {
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
    // Hàm debounce
    const debounce = (func, delay) => {
        return (...args) => {
            clearTimeout(debounceTimeout);
            debounceTimeout = setTimeout(() => func(...args), delay);
        };
    };

    // Hàm tải sản phẩm qua AJAX
    const loadProducts = async (params = '', scrollToList = false) => {
        if (isLoading) return;
        isLoading = true;

        try {
            const response = await fetch('/index' + params, {
                method: 'GET',
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                    'Cache-Control': 'no-cache, no-store, must-revalidate',
                    'Pragma': 'no-cache',
                    'Expires': '0'
                }
            });

            if (!response.ok) throw new Error('Lỗi mạng');
            const data = await response.json();

            const productList = document.getElementById('product-list');
            const paginationContainer = document.querySelector('.pagination').parentElement;
            productList.innerHTML = '';

            if (data.listSP.length === 0) {
                productList.innerHTML = '<h3 class="text-center text-gray w-100">Không có sản phẩm cần tìm</h3>';
            } else {
                const productRow = document.createElement('div');
                productRow.className = 'row row-cols-1 row-cols-sm-2 row-cols-md-4 g-4 my-5 w-100';
                data.listSP.forEach(sp => {
                    const html = `
                        <div class="col rounded-5 product" 
                             data-idsp="${sp.id || ''}" 
                             data-tensp="${sp.tenSanPham || 'Không xác định'}" 
                             data-hang="${sp.hang || 'Không xác định'}" 
                             data-lsp="${sp.lsp || 'Không xác định'}" 
                             data-kieudang="${sp.kieudang || 'Không xác định'}" 
                             data-mota="${sp.moTa || ''}" 
                             data-dongia="${sp.donGia || '0₫'}" 
                             data-tgbh="${sp.thoiGianBaoHanh || '0'}" 
                             data-img="${sp.img || '/placeholder.jpg'}" 
                             data-stock="${sp.stock || '0'}" 
                             data-bs-toggle="modal" 
                             data-bs-target="#productDetailModal">
                            <div class="card shadow-sm border-0 h-100 col rounded-5 product-item">
                                <div class="ratio ratio-1x1">
                                    <img src="${sp.img || '/placeholder.jpg'}" class="card-img-top object-fit-contain rounded-top-5" alt="${sp.tenSanPham || 'Sản phẩm'}">
                                </div>
                                <div class="card-body d-flex flex-column justify-content-between h-60 p-3">
                                    <h6 class="card-title text-truncate text-center w-100" title="${sp.tenSanPham || ''}">${sp.tenSanPham || 'Tên sản phẩm'}</h6>
                                    <div class="d-flex align-items-center justify-content-between mt-auto rounded-4 bg-blue-500">
                                        <span class="fw-bold text-primary fs-5 text-center w-100 text-white rounded-4 flex justify-center p-2 txtgia" style="background-color: #55d5d2; height: 50px; cursor: pointer;">
                                            ${sp.donGia || '0₫'}
                                        </span>
                                        <i class="fa-solid fa-arrow-up-right text-success"></i>
                                    </div>
                                </div>
                            </div>
                        </div>`;
                    productRow.insertAdjacentHTML('beforeend', html);
                });
                productList.appendChild(productRow);
                attachProductClickEvents();
            }

            // Cập nhật phân trang
            if (data.pagination) {
                paginationContainer.innerHTML = data.pagination;
                attachPaginationEvents();
            }

            if (scrollToList) {
                setTimeout(() => {
                    const productListSection = document.getElementById('list-product');
                    if (productListSection) {
                        productListSection.scrollIntoView({ behavior: 'smooth', block: 'start' });
                    }
                }, 100);
            }
        } catch (error) {
            console.error('Lỗi tải sản phẩm:', error);
            productList.innerHTML = '<h3 class="text-center text-danger w-100">Lỗi tải sản phẩm. Vui lòng thử lại.</h3>';
        } finally {
            isLoading = false;
        }
    };

    // Hàm gắn sự kiện click cho các phần tử .product
    const attachProductClickEvents = () => {
        document.querySelectorAll('.product').forEach(productDiv => {
            productDiv.removeEventListener('click', handleProductClick);
            productDiv.addEventListener('click', handleProductClick);
        });
    };

    // Hàm xử lý click sản phẩm
const handleProductClick = function () {
    const modal = document.getElementById('productDetailModal');
    if (!modal) return;

    // 1. Gán các giá trị ID sản phẩm vào form
    const idspInput = modal.querySelector('input[name="idsp"]');
    const idspInput2 = modal.querySelector('input[name="idsp2"]');
    if (idspInput) {
        idspInput.value = this.dataset.idsp || '';
        idspInput2.value = this.dataset.idsp || '';
    }

    // 2. Đổ dữ liệu text và hình ảnh vào Modal
    modal.querySelector('div[name="tensp"]').textContent = this.dataset.tensp || 'Không xác định';
    modal.querySelector('div[name="hang"]').textContent = this.dataset.hang || 'Không xác định';
    modal.querySelector('div[name="lsp"]').textContent = this.dataset.lsp || 'Không xác định';
    modal.querySelector('div[name="kieudang"]').textContent = this.dataset.kieudang || 'Không xác định';
    modal.querySelector('div[name="mota"]').textContent = this.dataset.mota || 'Không có mô tả';
    modal.querySelector('div[name="dongia"]').textContent = this.dataset.dongia || '0₫';
    modal.querySelector('div[name="tgbh"]').textContent = this.dataset.tgbh || '0';
    modal.querySelector('img[name="img"]').src = this.dataset.img || '/placeholder.jpg';
    
    // Hiển thị số lượng tồn kho
    const stockValue = parseInt(this.dataset.stock) || 0;
    modal.querySelector('div[name="stock"]').textContent = stockValue;

    // 3. Xử lý KHÓA NÚT khi hết hàng (Stock = 0)
    const btnCart = modal.querySelector('.btn-cart');
    const btnBuy = modal.querySelector('.btn-buy');

    if (stockValue <= 0) {
        // Trạng thái HẾT HÀNG
        if (btnCart) {
            btnCart.disabled = true;
            btnCart.innerText = "Hết hàng";
            // Khi disable thì set màu cố định để không hover được
            btnCart.style.backgroundColor = "#e9ecef";
            btnCart.style.color = "#6c757d";
            btnCart.style.border = "2px solid #dee2e6";
            btnCart.style.cursor = "not-allowed";
        }
        if (btnBuy) {
            btnBuy.disabled = true;
            btnBuy.style.display = "none";
        }
    } else {
        // Trạng thái CÒN HÀNG
        if (btnCart) {
            btnCart.disabled = false;
            btnCart.innerText = "Thêm vào giỏ";
            
           
            btnCart.style.backgroundColor = ""; 
            btnCart.style.color = "";
            btnCart.style.border = "";
            btnCart.style.cursor = "pointer";
        }
        if (btnBuy) {
            btnBuy.disabled = false;
            btnBuy.style.display = "block";
            btnBuy.style.backgroundColor = ""; // Để CSS điều khiển màu
            btnBuy.style.cursor = "pointer";
        }
    }

    // 4. Hiển thị Modal
    new bootstrap.Modal(modal).show();
};
    // Hàm gắn sự kiện cho các nút phân trang
    const attachPaginationEvents = () => {
        document.querySelectorAll('.pagination .page-link').forEach(link => {
            link.removeEventListener('click', handlePaginationClick);
            link.addEventListener('click', handlePaginationClick);
        });
    };

    // Hàm xử lý click phân trang
    const handlePaginationClick = (e) => {
        e.preventDefault();
        const page = e.target.closest('a')?.getAttribute('data-page');
        if (page) {
            const urlParams = new URLSearchParams(window.location.search);
            urlParams.set('page', page);
            window.history.pushState({}, '', `/index?${urlParams.toString()}`);
            debounce(loadProducts, 300)(`?${urlParams.toString()}`, true);
        }
    };

    // Gắn sự kiện ban đầu
    attachProductClickEvents();
    attachPaginationEvents();

    // Tải sản phẩm ban đầu nếu có tham số
    const urlParams = new URLSearchParams(window.location.search);
    if (urlParams.toString()) {
        loadProducts(window.location.search);
    }

    // Xử lý tìm kiếm
    document.querySelectorAll('form[role="search"]').forEach(form => {
        form.removeEventListener('submit', handleFormSubmit);
        form.addEventListener('submit', handleFormSubmit);

        const keywordInput = form.querySelector('input[name="keyword"]');
        if (keywordInput) {
            keywordInput.removeEventListener('keydown', handleKeywordKeydown);
            keywordInput.addEventListener('keydown', handleKeywordKeydown);
        }
    });

    function handleFormSubmit(e) {
        e.preventDefault();
        const params = new URLSearchParams(new FormData(e.target)).toString();
        window.history.pushState({}, '', `/index?${params}`);
        debounce(loadProducts, 300)('?' + params, true);
    }

    function handleKeywordKeydown(e) {
        if (e.key === 'Enter') {
            e.preventDefault();
            const form = e.target.closest('form');
            if (form) {
                const keywordInput = form.querySelector('input[name="keyword"]');
                if (keywordInput.value.trim()) {
                    form.dispatchEvent(new Event('submit'));
                } else {
                    window.history.pushState({}, '', '/index');
                    debounce(loadProducts, 300)('', true);
                }
            }
        }
    }

    // Xử lý dropdown chung
    const setupDropdown = (toggleId, dropdownId, applyBtnId, callback) => {
        const toggle = document.getElementById(toggleId);
        const dropdown = document.getElementById(dropdownId);
        const applyBtn = document.getElementById(applyBtnId);

        if (toggle && dropdown && applyBtn) {
            toggle.removeEventListener('click', handleToggleClick);
            toggle.addEventListener('click', handleToggleClick);

            document.removeEventListener('click', handleDocumentClick);
            document.addEventListener('click', handleDocumentClick);

            applyBtn.removeEventListener('click', handleApplyClick);
            applyBtn.addEventListener('click', handleApplyClick);

            function handleToggleClick(e) {
                e.preventDefault();
                e.stopPropagation();
                dropdown.classList.toggle('show');
            }

            function handleDocumentClick(e) {
                if (!toggle.contains(e.target) && !dropdown.contains(e.target)) {
                    dropdown.classList.remove('show');
                }
            }

            function handleApplyClick(e) {
                e.preventDefault();
                callback();
                dropdown.classList.remove('show');
            }
        }
    };

   /**
 * HỆ THỐNG LỌC TỰ ĐỘNG (CLICK-TO-FILTER)
 * Áp dụng cho: Mega Menu và Bộ lọc tại trang danh mục
 */

    // 1. Cấu hình các element chính
    const filterElements = {
        lsp: document.getElementById('filter-lsp'),
        kieudang: document.getElementById('filter-kieudang'),
        keyword: document.getElementById('filter-keyword'),
        hang: document.getElementById('filter-hang'),
        priceFrom: document.getElementById('filter-price-from'),
        priceTo: document.getElementById('filter-price-to')
    };

    /**
     * Hàm lõi: Thu thập tất cả params và thực hiện AJAX
     */
    const performAutoFilter = () => {
    const params = new URLSearchParams();

    // CHỈ thêm vào params nếu có giá trị thực (khác '0' và khác rỗng)
    if (filterElements.keyword && filterElements.keyword.value.trim() !== "") {
        params.set('keyword', filterElements.keyword.value.trim());
    }

    // Kiểm tra lsp: không lấy giá trị '0' hoặc chuỗi rỗng
    if (filterElements.lsp && filterElements.lsp.value && filterElements.lsp.value !== '0') {
        params.set('lsp', filterElements.lsp.value);
    }

    // Kiểm tra kieudang: không lấy giá trị '0' hoặc chuỗi rỗng
    if (filterElements.kieudang && filterElements.kieudang.value && filterElements.kieudang.value !== '0') {
        params.set('kieudang', filterElements.kieudang.value);
    }

    if (filterElements.hang && filterElements.hang.value && filterElements.hang.value !== '0') {
        params.set('hang', filterElements.hang.value);
    }

    // Xử lý lọc giá
    const pFrom = filterElements.priceFrom?.value;
    const pTo = filterElements.priceTo?.value;
    if (pFrom && pTo && parseFloat(pFrom) < parseFloat(pTo)) {
        params.set('khoanggia', `[${pFrom}-${pTo}]`);
    }

    params.delete('page'); // Reset về trang 1

    // Tạo Query String: Nếu params trống thì để rỗng, không để dấu ? lẻ loi
    const queryString = params.toString() ? '?' + params.toString() : '';
    const newUrl = window.location.pathname + queryString;

    // Cập nhật URL thanh địa chỉ (AJAX - không load lại trang)
    window.history.pushState({}, '', newUrl);

    // Gọi AJAX tải sản phẩm
    if (typeof loadProducts === 'function') {
        loadProducts(queryString || '?', true);
    }
};

    /**
     * 2. Xử lý Mega Menu (Click Link là lọc luôn)
     */
    const setupMegaMenu = () => {
    const megaLinks = document.querySelectorAll('.mega-filter-link');
    
    megaLinks.forEach(link => {
        link.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();

            const lspId = this.getAttribute('data-lsp-id');
            const kdId = this.getAttribute('data-kieudang-id');

            // 1. Xóa giá trị cũ của tất cả các bộ lọc để đảm bảo "tách biệt"
            if (filterElements.lsp) filterElements.lsp.value = "";
            if (filterElements.kieudang) filterElements.kieudang.value = "";
            if (filterElements.hang) filterElements.hang.value = "";

            // 2. Logic TÁCH BIỆT & LÀM SẠCH URL:
            // Nhấn cái nào thì chỉ giữ lại 'name' của cái đó, xóa 'name' cái kia
            if (lspId !== null) {
                if (filterElements.lsp) {
                    filterElements.lsp.name = "idLSP"; // Giữ name để gửi
                    filterElements.lsp.value = lspId;
                }
                if (filterElements.kieudang) {
                    filterElements.kieudang.removeAttribute('name'); // Xóa name để lsp= không hiện lên URL
                }
            } else if (kdId !== null) {
                if (filterElements.kieudang) {
                    filterElements.kieudang.name = "idKieuDang"; // Giữ name để gửi
                    filterElements.kieudang.value = kdId;
                }
                if (filterElements.lsp) {
                    filterElements.lsp.removeAttribute('name'); // Xóa name để kieudang= không hiện lên URL
                }
            }

            // 3. Chạy AJAX (Hàm này của bạn sẽ thực hiện pushState và load dữ liệu)
            performAutoFilter();

            // 4. Quan trọng: Trả lại 'name' sau khi AJAX chạy xong để Sidebar vẫn hoạt động bình thường
            setTimeout(() => {
                if (filterElements.lsp) filterElements.lsp.name = "idLSP";
                if (filterElements.kieudang) filterElements.kieudang.name = "idKieuDang";
            }, 100);

            // Cuộn xuống danh sách
            const listAnchor = document.getElementById('list-product');
            if (listAnchor) listAnchor.scrollIntoView({ behavior: 'smooth' });
        });
    });
};

    /**
     * 3. Xử lý các ô Select/Input (Thay đổi là lọc luôn)
     */
    const setupInputListeners = () => {
        // Đối với các ô Select: Lọc ngay khi chọn xong
        [filterElements.lsp, filterElements.kieudang, filterElements.hang].forEach(el => {
            if (el) el.addEventListener('change', performAutoFilter);
        });

        // Đối với ô nhập text (Keyword/Giá): Lọc khi dừng gõ (Debounce)
        [filterElements.keyword, filterElements.priceFrom, filterElements.priceTo].forEach(el => {
            if (el) {
                el.addEventListener('input', () => {
                    // Lưu vào session như yêu cầu cũ của bạn
                    if (el === filterElements.keyword) {
                        sessionStorage.setItem('filterKeyword', el.value);
                        sessionStorage.setItem('keywordSource', 'filter');
                    }
                    
                    // Chỉ tự động lọc keyword nếu bạn muốn, 
                    // hoặc đợi user gõ xong (Debounce được xử lý trong performAutoFilter)
                    performAutoFilter();
                });
            }
        });
    };

    /**
     * 4. Các tiện ích phụ (Modal, Alert, Khôi phục Keyword)
     */
    const setupUtilities = () => {
        // Khôi phục Keyword từ Session
        const source = sessionStorage.getItem('keywordSource');
        const searchKw = sessionStorage.getItem('searchKeyword') || '';
        const filterKw = sessionStorage.getItem('filterKeyword') || '';

        if (source === 'search' && filterElements.keyword) {
            document.querySelectorAll('input[name="keyword"]').forEach(i => i.value = searchKw);
            filterElements.keyword.value = '';
        } else if (source === 'filter' && filterElements.keyword) {
            filterElements.keyword.value = filterKw;
        }

        // Tự động ẩn Alert
        const alert = document.querySelector('.successAlert');
        if (alert) {
            setTimeout(() => {
                alert.style.opacity = '0';
                setTimeout(() => alert.remove(), 1000);
            }, 3000);
        }

        // Đóng Modal mượt mà
        const closeBtn = document.querySelector('.btn-close');
        if (closeBtn) {
            closeBtn.onclick = () => {
                const modalEl = document.getElementById('productDetailModal');
                if (modalEl && typeof bootstrap !== 'undefined') {
                    const inst = bootstrap.Modal.getInstance(modalEl);
                    if (inst) inst.hide();
                }
            };
        }
    };

    // Khởi chạy toàn bộ
    setupMegaMenu();
    setupInputListeners();
    setupUtilities();
});
</script>

    <!-- Nội dung trang chính ở đây -->
     <header id="header">
        <div class="alert-container-top-right">
    @if(session('success'))
        <div class="alert alert-success custom-alert-flat alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" style="filter: brightness(0);"></button>
        </div>
    @endif

    @if(session('error') || $errors->any())
        <div class="alert alert-danger custom-alert-flat alert-dismissible fade show" role="alert">
            {{ session('error') ?? $errors->first() }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" style="filter: brightness(0);"></button>
        </div>
    @endif
</div>
    <div class="text-white" id="navbar-ctn" style="background-color: white; padding: 10px 10%;border-radius: 0 0 20px 20px;">
      <div class="top-nav">
        <ul class="list-top-nav d-flex ms-auto gap-2">
          
          @if($isLogin) 
          <li class="nav-item px-3 py-1 bg-secondary text-white fw-medium rounded-pill " id="chinhsach"><a href="/yourInfo">Thông tin cá nhân</a></li>
          <li class="nav-item px-3 py-1 bg-secondary text-white fw-medium rounded-pill" id="tracuudonhang">
              <a href="{{ route('order.history') }}">Tra cứu đơn hàng</a>
          </li>
          @if($user->getIdQuyen()->getId() == 1 || $user->getIdQuyen()->getId() == 2) 
            <li class="nav-item px-3 py-1 bg-secondary text-white fw-medium rounded-pill" id="tracuudonhang"><a href="/admin">Trang quản trị</a></li>
          @endif
          <li class="nav-item px-3 py-1 bg-secondary text-white fw-medium rounded-pill" id="userDropdownBtn" style="position: relative; cursor: pointer;">
    {{$user->getTenTK()}}
    
    <div id="userDropdownMenu" style="display: none; width: 150px; position: absolute; top: 45px; right: 0; background: white; border: 1px solid #ccc; z-index: 1000; border-radius: 5px; padding: 15px; box-shadow: 0 4px 10px rgba(0,0,0,0.2);">
        <form action="{{ route('logout') }}" method="POST" style="margin: 0; width: 100%;">
            @csrf
            <button type="submit" class="btn btn-danger btn-sm" style="width: 100%; height: 40px;">Đăng xuất</button>
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
   <li class="nav-item fw-medium my-2 mx-2" id="item-sanpham" style="position: relative;">
    <a href="#list-product" class="nav-link text-white">Danh mục sản phẩm</a>
    
    <div class="dropdown-menu p-4" id="sanpham-mega-menu" style="min-width: 450px;">
        <div class="d-flex">
            <!-- Cột 1: Loại kính -->
            <div class="menu-column flex-fill">
                <h6 class="fw-bold text-uppercase mb-3" style="color: #333; border-bottom: 2px solid #55d5d2; display: inline-block; padding-bottom: 4px;">Loại kính</h6>
                <ul class="list-unstyled d-flex flex-column">
                    
                    @foreach ($listLSP as $lsp)
                        <li class="mb-2">
                            <a href="javascript:void(0)" class="text-decoration-none text-muted mega-filter-link" 
                               data-lsp-id="{{ $lsp->getId() }}">
                               {{ $lsp->getTenLSP() }}
                            </a>
                        </li>
                    @endforeach
                </ul>
            </div>

            <!-- Cột 2: Hình dáng -->
            <div class="menu-column flex-fill ps-4 border-start">
                <h6 class="fw-bold text-uppercase mb-3" style="color: #333; border-bottom: 2px solid #55d5d2; display: inline-block; padding-bottom: 4px;">Hình dáng</h6>
                <ul class="list-unstyled d-flex flex-column">
                    
                    @foreach ($listKieuDang as $kd)
                        @if($kd->getTenKieuDang() != 'Không xác định')
                            <li class="mb-2">
                                <a href="javascript:void(0)" class="text-decoration-none text-muted mega-filter-link" 
                                   data-kieudang-id="{{ $kd->getId() }}">
                                    {{ $kd->getTenKieuDang() }}
                                </a>
                            </li>
                        @endif
                    @endforeach
                </ul>
            </div>
        </div>
    </div>
</li>
           <!-- <li class="nav-item fw-medium my-2 mx-2" id="item-sanpham"><a href="javascript:void(0)" class="nav-link text-white">Sản Phẩm </a></li> -->
            <li class="nav-item fw-medium" style="position: relative;">
    <form action="/index" method="get" role="search">
        <input type="search" name="keyword" value="" class="form-control me-2 rounded-5" placeholder="Tìm kiếm sản phẩm" aria-label="Search">
        <i class="fa-solid fa-magnifying-glass mb-3" style="position: absolute; right: 10px; color: #555; padding: 10px;"></i>
    </form>
</li>
            
            <!-- <li class="nav-item fw-medium my-2" id="item-xemthem"><a href="" class="nav-link text-white">Xem Thêm <i class="fa-regular fa-angle-up"></i></a></li> -->
            <!-- <li class="nav-item fw-medium"><a href="#" class="nav-link text-white">Hành Trình Tử Tế</a></li> -->
            @if($isLogin && ($user->getIdQuyen()->getId() != 1 || $user->getIdQuyen()->getId() != 2))
              <li class="nav-item fw-medium my-2" id="item-giohang">
                <a href="{{ url('/yourcart?email=' . $user->getEmail()) }}" class="nav-link text-white">
                  Giỏ Hàng <i class="fa-solid fa-cart-shopping mt-3" style="position: relative;font-size: 16px; margin-left: 15px; vertical-align: middle;">
                    <small style="padding: 5px;background:rgb(60, 192, 194);color: white;position: absolute;right: -15px;bottom: -15px;font-size: 12px;border-radius: 50%;">{{$totalSPinGH}}</small>
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
        
        <div class="row my-5" style="max-height: 380px;display: flex;">
        <h1 style="font-family: Sigmar; font-weight: 800; color: #555; width: 40%;">BEST SELLER</h1>
        <div class="row row-cols-1 row-cols-sm-2 row-cols-md-4 g-4 my-5 w-100">
              @foreach($top4Product as $sp)
                @php
                  $stock = $sanPham->getStock($sp->getId());
                @endphp
                <div class="col rounded-5 product"
                      data-stock="{{ $sp->getSoLuong() }}"
                      data-idsp="{{ $sp->getId() }}"
                      data-tensp="{{ $sp->getTenSanPham() }}"
                      data-hang="{{ $sp->getIdHang()->getTenHang() }}"
                      data-lsp="{{ $sp->getIdLSP()->getTenLSP() }}"
                      data-kieudang="{{ $sp->getIdKieuDang() ? $sp->getIdKieuDang()->getTenKieuDang() : 'Không xác định' }}"
                      data-mota="{{ $sp->getMoTa() }}"
                      data-dongia="{{ number_format($sp->getDonGia(), 0, ',', '.') }}₫"
                      data-price="{{$sp->getDonGia()}}"
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
                        <span class="fw-bold text-primary fs-5 text-center w-100 text-white rounded-4 flex justify-center p-2 txtgia" style="background-color: #55d5d2;height: 50px;cursor: pointer;">
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
    <div class="banner-small " style="margin-top: 200px;">
      <div class="bnsm"><img src="/client/img/small-banner1.png" class="img-fluid w-100"></div>
      <div class="bnsm"><img src="/client/img/small-banner2.png" class="img-fluid w-100"></div>
    </div>
   
<div class="ctn-danhmucsanpham" style="background-color: #f6f2f2; padding-bottom: 30px;">
  <div class="d-flex justify-content-between p-5">
    <h1 style="font-family: Sigmar; font-weight: 800; color: #555; width: 40%;">SẢN PHẨM MỚI</h1>
    <form method="get" role="search" class="d-flex justify-content-between gap-3 align-items-center" style="width: 70%;">
      
    <!-- Lọc nâng cao -->
  <div class="filter-item" style="flex: 2; min-width: 200px; padding: 0 10px;">
            <div class="d-flex justify-content-between">
                <label class="fw-bold mb-1 small">Giá tối đa:</label>
                <span id="priceLabel" style="color: #55d5d2 !important;" class="text-primary fw-bold small">{{ number_format(request('filter_price_to') ?? 1000000, 0, ',', '.') }}đ</span>
            </div>
            <input type="range" class="form-range custom-range" name="filter_price_to" id="priceRange"
                   min="0" max="5000000" step="50000" 
                   value="{{ request('filter_price_to') ?? 1000000 }}" 
                   oninput="updatePriceLabel(this.value)">
            <input type="hidden" name="filter_price_from" value="0">
        </div>

      <!-- Lọc theo loại (lsp) -->
      <div class="filter-item" style="flex: 1; min-width: 140px;">
        <label class="fw-bold mb-1 small">Loại mắt kính:</label>
        <select class="form-select" name="lsp">
            <option value="0">Tất cả</option>
            @foreach($listLSP as $lsp)
                <option value="{{ $lsp->getId() }}" {{ request('lsp') == $lsp->getId() ? 'selected' : '' }}>{{ $lsp->getTenLSP() }}</option>
            @endforeach
        </select>
    </div>
    <div class="filter-item" style="flex: 1; min-width: 130px;">
            <label class="fw-bold mb-1 small">Kiểu dáng:</label>
            <select class="form-select" name="kieudang">
                <option value="0">Tất cả</option>
                @if(!empty($listKieuDang))
                    @foreach($listKieuDang as $kd)
                        @if($kd->getTenKieuDang() != 'Không xác định')
                            <option value="{{ $kd->getId() }}" {{ request('kieudang') == $kd->getId() ? 'selected' : '' }}>{{ $kd->getTenKieuDang() }}</option>
                        @endif
                    @endforeach
                @endif
            </select>
        </div>
    <div class="filter-item" style="flex: 1; min-width: 140px;">
        <label class="fw-bold mb-1 small">Hãng:</label>
        <select class="form-select" name="hang">
            <option value="0">Tất cả</option>
            @foreach($listHang as $h)
                <option value="{{ $h->getId() }}" {{ request('hang') == $h->getId() ? 'selected' : '' }}>{{ $h->getTenHang() }}</option>
            @endforeach
        </select>
    </div>
    <div class="filter-item" style="flex: 0.5; min-width: 80px;">
            <button type="submit" class="btn fw-bold text-white w-100" 
                    style="background-color: #55d5d2; border: none; height: 38px;">
                Lọc
            </button>
        </div>
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
        <div id="filter-state-storage">
    <input type="hidden" id="filter-lsp" value="{{ request('lsp') }}">
    <input type="hidden" id="filter-kieudang" value="{{ request('kieudang') }}">
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
            @foreach($tmp as $sp)
                @if($count++ >= 8) @break @endif
                @php
                    $stock = $sanPham->getStock($sp->getId());
                @endphp
                <div class="col rounded-5 product"
                     data-stock="{{ $sp->getSoLuong() }}"
                     data-idsp="{{ $sp->getId() }}"
                     data-tensp="{{ $sp->getTenSanPham() }}"
                     data-hang="{{ $sp->getIdHang()->getTenHang() }}"
                     data-lsp="{{ $sp->getIdLSP()->getTenLSP() }}"
                     data-kieudang="{{ $sp->getIdKieuDang() ? $sp->getIdKieuDang()->getTenKieuDang() : 'Không xác định' }}"
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
                                <span class="fw-bold text-primary fs-5 text-center w-100 text-white rounded-4 flex justify-center p-2 txtgia" style="background-color: #55d5d2;height: 50px;cursor: pointer;">
                                    {{ number_format($sp->getDonGia(), 0, ',', '.') }}₫
                                </span>
                                <i class="fa-solid fa-arrow-up-right text-success"></i>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</div>
<nav aria-label="Page navigation example" class="d-flex justify-content-center">
    <ul class="pagination">
        @if ($current_page > 1)
            <li class="page-item">
                <a class="page-link" href="javascript:void(0)" data-page="{{ $current_page - 1 }}" aria-label="Previous">
                    <span aria-hidden="true">«</span>
                </a>
            </li>
        @endif

        @php
            $page_range = 1;
            $start_page = max(1, $current_page - $page_range);
            $end_page = min($total_page, $current_page + $page_range);
        @endphp
        @for ($i = $start_page; $i <= $end_page; $i++)
            <li class="page-item {{ $i == $current_page ? 'active' : '' }}">
                @if ($i == $current_page)
                    <span class="page-link">{{ $i }}</span>
                @else
                    <a class="page-link" href="javascript:void(0)" data-page="{{ $i }}">{{ $i }}</a>
                @endif
            </li>
        @endfor

        @if ($current_page < $total_page)
            <li class="page-item">
                <a class="page-link" href="javascript:void(0)" data-page="{{ $current_page + 1 }}" aria-label="Next">
                    <span aria-hidden="true">»</span>
                </a>
            </li>
        @endif
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
  <div class="d-flex" style="padding: 0 5%; font-family: 'Segoe UI', Roboto, Arial, sans-serif;">
    <div style="width: 40%;">
        <img src="/client/img/Artboard-2-copy-5.png" alt="Anna Eyewear" class="img-fluid w-100">
    </div>

    <div style="padding-left: 50px; width: 60%;">
        <h2 style="padding: 25px 30px; background-color: #daebeb; border-top-left-radius: 30px; border-top-right-radius: 30px; border-bottom-right-radius: 30px; color: #54c4c4; font-weight: 800; text-transform: uppercase; margin-bottom: 25px; font-size: 40px;">
            VŨ TRỤ TRUYỀN THÔNG
        </h2>
        
        <div style="color: #413f3f; line-height: 1.8; font-size: 20px;">
            <p style="font-weight: 700; font-size: 20px; margin-bottom: 22px; color: #333;">
             <i class="fa-solid fa-arrow-right-long"></i> Kính mắt Anna chắc không còn quá xa lạ với giới trẻ nữa. Đây là kênh thông tin mua sắm và giải trí dành cho giới trẻ, là “món ăn tinh thần” hằng ngày không thể thiếu của mỗi người trẻ.
            </p>
            
            <p style="margin-bottom: 15px; font-weight: 700; font-size: 20px; margin-bottom: 20px; color: #333;">
                <i class="fa-solid fa-arrow-right-long"></i> Được thành lập từ năm 2015, trải qua hơn 11 năm phát triển kính mắt Anna đã, đang và sẽ chiếm vị trí không thể thiếu đối với các bạn trẻ. 
            </p>
            
            <p  style="margin-bottom: 15px; font-weight: 700; font-size: 20px; margin-bottom: 20px; color: #333;">
                <i class="fa-solid fa-arrow-right-long"></i> Cập nhật thông tin thời trang nhanh chóng và phù hợp với thị hiếu của khán giả thông qua các mạng xã hội như 
                <span style="font-weight: 600; color: #55d5d2; font-size: 22px;">Facebook, Tiktok, Instagram, Youtube</span>, kính mắt Anna là lựa chọn hàng đầu cho những ai muốn tận hưởng các bài viết, video vừa mang tính giải trí mà vẫn có đầy đủ thông tin cần thiết.
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
       <div class="company-info mt-4"  style="color: white; line-height: 1.4;">
    <h2>Giới thiệu</h2>
    <strong>CÔNG TY CỔ PHẦN ALC PHÚ QUÝ</strong><br>
    <strong>Địa chỉ trụ sở chính:</strong> Số 10 Xuân Thủy, Phường Cầu Giấy, Thành phố Hà Nội, Việt Nam<br>
    <strong>Mã số doanh nghiệp:</strong> 0110489312 do Sở Tài Chính TP Hà Nội cấp lần đầu ngày 27/09/2023
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
    <div class="rounded-5 p-1 fw-semibold bg-primary-subtle" style="width: 120px;font-size: small;text-align: center;" name="kieudang"></div>
    <div class="fs-3 fw-semibold" name="tensp"></div>
    <div class="fs-2 fw-bold price-highlight" style="color: #55d5d2;" name="dongia"></div>
    <div class="fs-6 fw-semibold d-flex flex-row gap-3" style="color: #413f3f;">Thương hiệu: <div class="fw-bold" style="color: red;" name="hang"></div></div>
    <div class="fs-6 fw-semibold d-flex flex-row gap-3" style="color: #413f3f;">Mô tả: <div name="mota"></div></div>
    <div class="fs-6 fw-semibold d-flex flex-row gap-3" style="color: #413f3f;">Thời gian bảo hành: <div name="tgbh"></div> tháng</div>
    <div class="fs-6 fw-semibold d-flex flex-row gap-3 mb-3" style="color: #413f3f;">Số lượng: <div name="stock"></div></div>

    <div class="d-flex gap-3">
        @if($isLogin)
            @if($user->getIdQuyen()->getId() != 1 && $user->getIdQuyen()->getId() != 2)
                <form action="{{ route('index.addctgh') }}" method="post" id="form-add-cart">
                    @csrf
                    <input type="hidden" name="idgh" value="{{$gh->getIdGH()}}">
                    <input type="hidden" name="idsp" value="">
                    <button type="submit" class="btn btn-light btn-cart" style="width: 200px; border: 2px solid #ced4da">Thêm vào giỏ</button>
                </form>

                <form action="{{route('payment.muangay')}}" method="get" id="form-buy-now">
                    <input type="hidden" name="idsp2" value="">
                    <input type="hidden" name="quantity" value="1">
                    <input type="hidden" name="price" value="">
                    <button type="submit" class="btn btn-primary btn-buy" style="width: 150px; background-color: #55d5d2; border: none;">Mua ngay</button>
                </form>
            @endif
        @else
            <a href="/login" class="btn btn-light btn-cart" style="width: 200px; border: 2px solid #ced4da;">Thêm vào giỏ</a>
            <a href="/login" class="btn btn-primary btn-buy" style="width: 150px; background-color: #55d5d2; border: none;">Mua ngay</a>
        @endif
    </div>
</div>
@if(session('error'))
    <div class="alert alert-danger successAlert">{{ session('error') }}</div>
@elseif(session('success'))
    <div class="alert alert-success successAlert">{{ session('success') }}</div>        
@endif

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const alerts = document.querySelectorAll('.alert-container-top-right .alert');
        alerts.forEach(function(alert) {
            setTimeout(function() {
                // Tắt sau 3 giây
                if (typeof bootstrap !== 'undefined') {
                    const bsAlert = bootstrap.Alert.getOrCreateInstance(alert);
                    if (bsAlert) bsAlert.close();
                } else {
                    alert.remove(); 
                }
            }, 3000);
        });
    });
</script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>