<?php

use App\Bus\Auth_BUS;
use App\Bus\CTQ_BUS;
use App\Bus\TaiKhoan_BUS;

  $email = app(Auth_BUS::class)->getEmailFromToken();
  $user = app(TaiKhoan_BUS::class)->getModelById($email);
  $ctq = app(CTQ_BUS::class)->getModelById($user->getIdQuyen()->getId());
  $tenquyen = "";
  if($user->getIdQuyen()->getId() == 1){
    $tenquyen = "Quản lý";
  }else{
    $tenquyen = "Nhân viên";
  }
  $currentModun = $_GET['modun'] ?? '';
?>
<style>
.sidebar-nav {
    max-height: calc(100vh - 100px); 
    overflow-y: auto;
    scrollbar-width: thin; 
    scrollbar-color: #B0BEC5 #1A2526; 
}
.sidebar-item.active {
    background-color: rgba(255, 255, 255, 0.1);
    border-left: 3px solid #3b7ddd;
}

.sidebar-item.active a.sidebar-link {
    color: #3b7ddd !important;
}

.sidebar-nav {
    max-height: calc(100vh - 100px); 
    overflow-y: auto;
    scrollbar-width: thin; 
    scrollbar-color: #B0BEC5 #1A2526; 
}

</style>
<aside id="sidebar" class="expand d-block">
<div class="d-flex p-4" style="background-color: #2c3e50;">
  <div class="sidebar-logo">
    <a href="">
      <img src="https://img.ws.mms.shopee.vn/vn-11134216-7r98o-lq2sgdy60w5uba" 
           alt="Logo" 
           class="img-fluid rounded-5" 
           style="height: 70px;">
    </a>
  </div>
  <div class="sidebar-infoAcc ms-3" style="color: white; font-weight: 500;">
    <small class="d-block">Tên TK: <?= $user->getTenTK() ?></small>
    <small class="d-block">Mã ND: <?= $user->getIdNguoiDung()->getId() ?></small>
    <small class="d-block">Quyền: <?= $tenquyen ?></small>
  </div>
</div>

  <ul class="sidebar-nav">
    @if(app(CTQ_BUS::class)->checkChucNangExistInListCTQ($ctq, 5))
      <li class="sidebar-item <?= ($currentModun == 'nguoidung') ? 'active' : '' ?>" id="nguoidung">
        <a href="/admin?modun=nguoidung" class="sidebar-link">
          <i class='bx bxs-user'></i>
          <span>Người dùng</span>
        </a>
      </li>
      @endif
    @if(app(CTQ_BUS::class)->checkChucNangExistInListCTQ($ctq, 5))
      <li class="sidebar-item <?= ($currentModun == 'taikhoan') ? 'active' : '' ?>" id="taikhoan">
        <a href="/admin?modun=taikhoan" class="sidebar-link">
          <i class='bx bxs-user-account'></i>
          <span>Tài khoản</span>
        </a>
      </li>
    @endif
    @if(app(CTQ_BUS::class)->checkChucNangExistInListCTQ($ctq, 1))
      <li class="sidebar-item <?= ($currentModun == 'sanpham') ? 'active' : '' ?>" id="sanpham">
        <a href="/admin?modun=sanpham" class="sidebar-link">
          <i class='bx bx-glasses'></i>
          <span>Sản phẩm</span>
        </a>
      </li>
    @endif
    @if(app(CTQ_BUS::class)->checkChucNangExistInListCTQ($ctq, 13))
    <li class="sidebar-item <?= ($currentModun == 'loaisanpham') ? 'active' : '' ?>" id="loai sanpham">
      <a href="/admin?modun=loaisanpham" class="sidebar-link">
        <i class='bx bx-bar-chart'></i>
        <span>Loại sản phẩm</span>
      </a>
    </li>
    @endif
    @if(app(CTQ_BUS::class)->checkChucNangExistInListCTQ($ctq, 14))
    <li class="sidebar-item <?= ($currentModun == 'hang') ? 'active' : '' ?>" id="hang">
      <a href="/admin?modun=hang" class="sidebar-link">
        <i class='bx bx-bar-chart'></i>
        <span>Hãng</span>
      </a>
    </li>
    @endif
    @if(app(CTQ_BUS::class)->checkChucNangExistInListCTQ($ctq, 2))
      <li class="sidebar-item <?= ($currentModun == 'hoadon') ? 'active' : '' ?>" id="hoadon">
        <a href="/admin?modun=hoadon" class="sidebar-link">
          <i class='bx bx-cart'></i>
          <span>Đơn hàng</span>
        </a>
      </li>
    @endif

    @if(app(CTQ_BUS::class)->checkChucNangExistInListCTQ($ctq, 15))
    <li class="sidebar-item <?= ($currentModun == 'thanhpho') ? 'active' : '' ?>" id="thanhpho">
      <a href="/admin?modun=thanhpho" class="sidebar-link" >
        <i class='bx bxs-truck'></i>
        <span>Thành phố</span>
      </a>
    </li>
    @endif
    @if(app(CTQ_BUS::class)->checkChucNangExistInListCTQ($ctq, 3))
      <li class="sidebar-item <?= ($currentModun == 'nhacungcap') ? 'active' : '' ?>" id="ncc">
        <a href="/admin?modun=nhacungcap" class="sidebar-link">
          <i class='bx bx-edit-alt'></i>
          <span>Nhà cung cấp</span>
        </a>
      </li>
    @endif
    @if(app(CTQ_BUS::class)->checkChucNangExistInListCTQ($ctq, 4))
    <li class="sidebar-item <?= ($currentModun == 'kho') ? 'active' : '' ?>" id="kho">
      <a href="/admin?modun=kho" class="sidebar-link"  >
        <i class='bx bx-home-alt'></i>  
        <span>Nhập kho</span>
      </a>
    </li>
    @endif


    @if(app(CTQ_BUS::class)->checkChucNangExistInListCTQ($ctq, 7))
    <li class="sidebar-item <?= ($currentModun == 'thongke') ? 'active' : '' ?>" id="thongke">
      <a href="/admin?modun=thongke" class="sidebar-link">
        <i class='bx bx-bar-chart'></i>
        <span>Thống kê</span>
      </a>
    </li>
    @endif
    @if(app(CTQ_BUS::class)->checkChucNangExistInListCTQ($ctq, 9))
    <li class="sidebar-item <?= ($currentModun == 'quyen') ? 'active' : '' ?>" id="quyen">
      <a href="/admin?modun=quyen" class="sidebar-link">
        <i class='bx bx-bug'></i>
        <span>Quyền</span>
      </a>
    </li>
    @endif
    
  </ul>
</aside>
