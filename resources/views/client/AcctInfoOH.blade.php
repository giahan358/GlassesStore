<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../../css/client/include/footer.css">
    <link rel="stylesheet" href="../../css/client/include/navbar.css">
    <link rel="stylesheet" href="../../css/client/AcctIfoOH.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    <title>Thông tin cá nhân</title>
</head>
<style>
  
.home-nav {
    position: fixed;
    top: 15px;
    right: 20px;
    z-index: 1050; 
}

.home-link {
    background-color: #18c2ce;
    color: white !important;
    padding: 8px 16px;
    border-radius: 20px;
    text-decoration: none;
    font-weight: bold;
    box-shadow: 0 2px 5px rgba(0,0,0,0.2);
    transition: 0.3s;
}

.home-link:hover {
    background-color: #0aaaaa;
    box-shadow: 0 4px 8px rgba(0,0,0,0.3);
}
.bg-custom-green {
    background-color: #3fc5cf ; 
   
}
.alert-container-top-right {
    position: fixed;
    top: 20px;
    right: 20px; 
    z-index: 9999;
    width: 320px;
}
</style>
<body>
    <header>
       <div class="alert-container-top-right">
    @if(session('success'))
        <div class="alert alert-success custom-alert-flat alert-dismissible fade show" role="alert">
            <i class="fa-solid fa-circle-check"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close" style="filter: brightness(0);"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger custom-alert-flat alert-dismissible fade show" role="alert">
            <i class="fa-solid fa-triangle-exclamation"></i> {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close" style="filter: brightness(0);"></button>
        </div>
    @endif
</div>
    <div class="text-white d-flex align-items-center justify-content-center" id="navbar-ctn" style="position: relative; min-height: 60px;">
        <h3 class="m-0 text-uppercase fw-bold" style="letter-spacing: 1px; color:black;">Thông tin cá nhân</h3>
        
        <div class="home-nav" style="position: absolute; right: 20px;">
            <a class="home-link" href="/">
                 Trang chủ
            </a>
        </div>
    </div>
</header>
    <div class="submenu card" style="z-index: 100;">
        <div class="card-menu d-flex">
        </div>
    </div>
    <div class="content-ctn">
        <div class="container">
            <div class="sidebar">
                <h2>{{ $user->getIdNguoiDung()->getHoTen() ?? 'Chưa có tên' }}</h2>
                 <ul>
                        <li>
                            <span class="icon"><i class="fa-solid fa-envelope"></i></span> 
                            Email: {{ $user->getEmail() }}
                        </li>
                        <li>
                            <span class="icon"><i class="fa-solid fa-phone"></i></span> 
                            SĐT: {{ $user->getIdNguoiDung()->getSoDienThoai() ?? 'Chưa cập nhật' }}
                        </li>
                        <li>
                            <span class="icon"><i class="fa-solid fa-location-dot"></i></span> 
                            Địa chỉ: {{ $user->getIdNguoiDung()->getDiaChi() ?? 'Chưa cập nhật' }}
                        </li>
                    </ul>
            </div>

            <div class="main-content">
                <div class="card mb-4 mt-4">
                    <div class="card-header bg-custom-green text-white d-flex justify-content-between align-items-center">
                                Thông tin tài khoản
                                <button class="btn btn-warning btn-sm" data-bs-toggle="modal" data-bs-target="#editUserModal">
                                    <i class="fa fa-edit"></i> Chỉnh sửa
                                </button>
                            </div>
                    <div class="card-body">
                        <div class="row mb-2">
                            <div class="col-md-4 fw-bold">Tên đăng nhập:</div>
                            <div class="col-md-8">{{ $user->getTenTK() }}</div>
                        </div>
                        <div class="row mb-2">
                            <div class="col-md-4 fw-bold">Họ và tên:</div>
                            <div class="col-md-8">{{ $user->getIdNguoiDung()->getHoTen() ?? 'Chưa cập nhật' }}</div>
                        </div>
                        <div class="row mb-2">
                            <div class="col-md-4 fw-bold">Email:</div>
                            <div class="col-md-8">{{ $user->getEmail() }}</div>
                        </div>
                        <div class="row mb-2">
                            <div class="col-md-4 fw-bold">Số điện thoại:</div>
                            <div class="col-md-8">{{ $user->getIdNguoiDung()->getSoDienThoai() ?? 'Chưa cập nhật' }}</div>
                        </div>
                        <div class="row mb-2">
                            <div class="col-md-4 fw-bold">Địa chỉ:</div>
                            <div class="col-md-8">{{ $user->getIdNguoiDung()->getDiaChi() ?? 'Chưa cập nhật' }}</div>
                        </div>
                    </div>
                </div>

                <div class="modal fade" id="editUserModal" tabindex="-1" aria-labelledby="editUserModalLabel" aria-hidden="true">
                  <div class="modal-dialog">
                    <form method="POST" action="{{ route('user.updateInfo') }}">
                      @csrf
                      <input type="hidden" name="id" value="{{ $user->getIdNguoiDung()->getId() }}">
                      <div class="modal-content">
                        <div class="modal-header">
                          <h5 class="modal-title" id="editUserModalLabel">Chỉnh sửa thông tin cá nhân</h5>
                          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Đóng"></button>
                        </div>
                        <div class="modal-body">
                          <div class="mb-3">
                            <label for="username" class="form-label">Tên đăng nhập</label>
                            <input type="text" class="form-control" id="username" name="username" value="{{ $user->getTenTK() }}" style="background-color: #e2e8edf3;" required readonly>
                            @error('username')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                          </div>
                          <div class="mb-3">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" class="form-control" id="email" name="email" value="{{ $user->getEmail() }}" style="background-color: #e2e8edf3;" required readonly>
                            @error('email')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                          </div>
                          <!--<div class="mb-3">
                            <label for="current_password" class="form-label">Mật khẩu hiện tại</label>
                            <input type="password" class="form-control" id="current_password" name="current_password">
                            @error('current_password')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                          </div>!-->
                          <div class="mb-3">
                            <label for="password" class="form-label">Mật khẩu mới (để trống nếu không đổi)</label>
                            <input type="password" class="form-control" id="password" name="password">
                            @error('password')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                          </div>
                          <div class="mb-3">
                            <label for="password_confirmation" class="form-label">Xác nhận mật khẩu mới</label>
                            <input type="password" class="form-control" id="password_confirmation" name="password_confirmation">
                            @error('password_confirmation')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                          </div>
                          <div class="mb-3">
                            <label for="hoTen" class="form-label">Họ và tên</label>
                            <input type="text" class="form-control" id="hoTen" name="hoTen" value="{{ $user->getIdNguoiDung()->getHoTen() }}">
                          </div>
                          <div class="mb-3">
                            <label for="soDienThoai" class="form-label">Số điện thoại</label>
                            <input type="text" class="form-control @error('soDienThoai') is-invalid @enderror" id="soDienThoai" name="soDienThoai" value="{{ $user->getIdNguoiDung()->getSoDienThoai() }}">
                            @error('soDienThoai')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                          <div class="mb-3">
                            <label for="diaChi" class="form-label">Địa chỉ</label>
                            <input type="text" class="form-control" id="diaChi" name="diaChi" value="{{ $user->getIdNguoiDung()->getDiaChi() }}">
                          </div>
                        </div>
                        <div class="modal-footer">
                          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
                          <button type="submit" class="btn btn-primary">Lưu thay đổi</button>
                        </div>
                      </div>
                    </form>
                  </div>
                </div>

                </div>
        </div>
    </div>

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
        <label for="">Sản phẩm</label>
          <ul>
            <li><a href="#">Gọng Kính</a></li>
            <li><a href="#">Kính râm</a></li>
            <li><a href="#">Kính mát</a></li>
          </ul>
        </div>
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
</body>
</html>

<div id="validation-status" 
     data-update-user-error="{{ $errors->any() ? '1' : '0' }}">
</div>

<div class="main-content">
    </div>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
    // 1. Lấy thẻ div chứa trạng thái lỗi
    const statusEl = document.getElementById('validation-status');
    
    if (statusEl) {
        // 2. Kiểm tra nếu thuộc tính data-update-user-error là '1' (có lỗi)
        if (statusEl.dataset.updateUserError === '1') {
            // 3. Tìm đúng ID modal "editUserModal"
            const editModalEl = document.getElementById('editUserModal');
            
            if (editModalEl) {
                // 4. Kích hoạt mở lại modal bằng Bootstrap 5
                const modal = bootstrap.Modal.getOrCreateInstance(editModalEl);
                modal.show();
            }
        }
    }
});
    </script>
<div id="session-alerts" 
     data-success="{{ session('success') }}" 
     data-error="{{ session('error') }}">
</div>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Tìm tất cả các alert trong container góc phải
        const alerts = document.querySelectorAll('.alert-container-top-right .alert');
        
        alerts.forEach(function(alert) {
            // Thiết lập thời gian chờ 3 giây
            setTimeout(function() {
                // Sử dụng API của Bootstrap để đóng alert
                const bsAlert = bootstrap.Alert.getOrCreateInstance(alert);
                if (bsAlert) {
                    bsAlert.close();
                }
            }, 3000);
        });
    });
</script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>

