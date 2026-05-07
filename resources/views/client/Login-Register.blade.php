<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../../css/client/include/footer.css">
    <link rel="stylesheet" href="../../css/client/include/navbar.css">
    <link rel="stylesheet" href="../../css/client/Login-Register.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="https://site-assets.fontawesome.com/releases/v6.7.2/css/all.css">
    <title>Login/Register</title>
</head>
@if(session('error'))
<div class="alert alert-danger">
    {{ session('error') }}
</div>
@endif

<body>
    <div class="submenu card">
        <div class="card-menu d-flex ">

        </div>
    </div>
    <div class="content-ctn">
        <div class="content-left w-50" style="z-index: 1;">
            <img src="{{ asset('client/img/img-login.jpeg') }}" class="w-100 img-fluid  p-auto" alt="" style="border-radius: 30px;overflow: hidden;">
        </div>
        <div class="content-right w-50 ">
            <form style="width:80%;margin:0 auto;display: block;text-align: center;" method="POST" action="{{ route('login') }}">
            @csrf
                <h1>ĐĂNG NHẬP</h1>
                <p>Hãy đăng nhập để được hưởng những đặc quyền dành cho riêng bạn</p>
                <div class="form-group">
                    <label for="email-login" class="form-label">Địa chỉ Email</label>
                    <input type="email" class="form-control" id="email-login" name="email-login" placeholder="Nhập địa chỉ Email" value="{{old('email-login')}}" required>
                </div>
                <div class="form-group">
                    <label for="password-login" class="form-label">Mật khẩu</label>
                    <input type="password" id="password-login" name="password-login" value="{{old('password-login')}}" class="form-control" aria-describedby="passwordHelpBlock" placeholder="Nhập mật khẩu" required>
                    <div id="passwordHelpBlock" class="form-text text-start">
                        Mật khẩu của bạn phải dài từ 6 ký tự.
                    </div>
                </div>
                
                <button type="submit" class="w-100">Đăng nhập ngay</button>
                <!-- <p class="text-start my-4"><a href="#">Quên mật khẩu?</a></p> -->
                <p>Bạn chưa có tài khoản? <a href="/register" class="link-register" >Đăng ký ngay</a></p>
                <!-- <p><a href="/admin/login">Đăng nhập quản trị</a></p> -->
            </form>
        </div>
    </div>

    <!-- Footer -->
    <div class="container-custom">
        <a><i class="fa-solid fa-shield-check fa-beat"></i>
            <p>Bảo hành trọn đời</p>
        </a>
        <a><i class="fa-solid fa-flower-daffodil fa-beat"></i>
            <p>Đo mắt miễn phí</p>
        </a>
        <a><i class="fa-solid fa-rotate fa-spin"></i>
            <p>Thu cũ đổi mới</p>
        </a>
        <a><i class="fa-solid fa-spray-can-sparkles fa-shake"></i>
            <p>Vệ sinh & Bảo quản</p>
        </a>
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
        <div class="product-info">
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
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
<script src="../../js/client/include/navbar.js"></script>
<script src="../../js/client/Login-Register.js"></script>

</html>