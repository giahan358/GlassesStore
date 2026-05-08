<?php

namespace App\Providers;
use Illuminate\Support\Facades\URL;
use App\Bus\Auth_BUS;
use App\Bus\ChiTietBaoHanh_BUS;
use App\Bus\ChucNang_BUS;
use App\Bus\ChucNangDVVC_BUS;
use App\Bus\CPVC_BUS;
use App\Bus\CTGH_BUS;
use App\Bus\CTQ_BUS;
use App\Bus\DVVC_BUS;
use App\Bus\GioHang_BUS;
use App\Bus\Hang_BUS;
use App\Bus\KhuyenMai_BUS;
use App\Bus\LoaiSanPham_BUS;
use App\Bus\NCC_BUS;
use App\Bus\NguoiDung_BUS;
use App\Bus\PhieuNhap_BUS;
use App\Bus\PTTT_BUS;
use App\Bus\Quyen_BUS;
use App\Bus\SanPham_BUS;
use App\Bus\TaiKhoan_BUS;
use App\Bus\Tinh_BUS;
use App\Dao\ChiTietBaoHanh_DAO;
use App\Dao\ChucNang_DAO;
use App\Dao\CPVC_DAO;
use App\Dao\CTQ_DAO;
use App\Dao\DVVC_DAO;
use App\Dao\GioHang_DAO;
use App\Dao\Hang_DAO;
use App\Dao\KhuyenMai_DAO;
use App\Dao\LoaiSanPham_DAO;
use App\Dao\NCC_DAO;
use App\Dao\NguoiDung_DAO;
use App\Dao\PhieuNhap_DAO;
use App\Dao\PTTT_DAO;
use App\Dao\Quyen_DAO;
use App\Dao\TaiKhoan_DAO;
use App\Dao\Tinh_DAO;
use App\Dao\SanPham_DAO;
use App\Utils\JWTUtils;
use App\Validates\validation;
use App\Bus\CTHD_BUS;
use App\Dao\CTHD_DAO;
use App\Bus\CTSP_BUS;
use App\Bus\DiaChi_BUS as BusDiaChi_BUS;
use App\Dao\CTSP_DAO;
use App\Bus\HoaDon_BUS;
use App\Bus\KieuDang_BUS;
use App\Dao\CTGH_DAO;
use App\Dao\DiaChi_DAO as DaoDiaChi_DAO;
use App\Dao\HoaDon_DAO;
use App\Dao\KieuDang_DAO;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\TaiKhoanController;
use DiaChi_BUS;
use DiaChi_DAO;
use Illuminate\Support\ServiceProvider;
use Illuminate\Pagination\Paginator;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Định nghĩa các lớp DAO và BUS.
     *
     * @var array
     */
    protected $services = [
        'ChucNang' => [ChucNang_DAO::class, ChucNang_BUS::class],
        'CTQ' => [CTQ_DAO::class, CTQ_BUS::class],
        'NguoiDung' => [NguoiDung_DAO::class, NguoiDung_BUS::class],
        'Quyen' => [Quyen_DAO::class, Quyen_BUS::class],
        'TaiKhoan' => [TaiKhoan_DAO::class, TaiKhoan_BUS::class],
        'Tinh' => [Tinh_DAO::class, Tinh_BUS::class],
        'SanPham' => [SanPham_DAO::class, SanPham_BUS::class],
        'CTSP' => [CTSP_DAO::class, CTSP_BUS::class],
        'HoaDon' => [HoaDon_DAO::class, HoaDon_BUS::class], 
        'CTHD' => [CTHD_DAO::class, CTHD_BUS::class],
        
        
        'CTPN' => [CTSP_DAO::class, CTSP_BUS::class],
        'PhieuNhap' => [PhieuNhap_DAO::class, PhieuNhap_BUS::class],
        
        'GioHang' => [GioHang_DAO::class, GioHang_BUS::class],
        'Hang' => [Hang_DAO::class, Hang_BUS::class],
        
        'LoaiSanPham' => [LoaiSanPham_DAO::class, LoaiSanPham_BUS::class],
        'NCC' => [NCC_DAO::class, NCC_BUS::class],
        'PTTT' => [PTTT_DAO::class, PTTT_BUS::class],
        'CTGH' => [CTGH_DAO::class, CTGH_BUS::class],
        'KieuDang' => [KieuDang_DAO::class, KieuDang_BUS::class],
        'DiaChi' => [DaoDiaChi_DAO::class, BusDiaChi_BUS::class]
    ];

    /**
     * Register any application services.
     */
    public function register(): void
    {
        foreach ($this->services as $classes) {
            $this->app->bind($classes[0], function ($app) use ($classes) {
                return new $classes[0]();
            });

            $this->app->singleton($classes[1], function ($app) use ($classes) {
                // Ghi đè binding cho HoaDon_BUS
                if ($classes[1] === HoaDon_BUS::class) {
                    return new HoaDon_BUS(
                        $app->make(HoaDon_DAO::class),
                        $app->make(CTHD_DAO::class),
                        $app->make(CTHD_BUS::class)
                    );
                }
                return new $classes[1]($app->make($classes[0]));
            });
        }

        // Các binding khác
        $this->app->singleton(validation::class, function ($app) {
            return new validation();
        });

        $this->app->singleton(GioHang_BUS::class, function ($app) {
            return new GioHang_BUS($app->make(GioHang_DAO::class));
        });

        $this->app->singleton(TaiKhoan_DAO::class, function ($app) {
            return new TaiKhoan_DAO($app->make(GioHang_BUS::class));
        });

        $this->app->singleton(Auth_BUS::class, function ($app) {
            return new Auth_BUS($app->make(TaiKhoan_BUS::class), $app->make(JWTUtils::class));
        });

        $this->app->singleton(AuthController::class, function ($app) {
            return new AuthController($app->make(Auth_BUS::class));
        });

        $this->app->singleton(TaiKhoanController::class, function ($app) {
            return new TaiKhoanController(
                $app->make(TaiKhoan_BUS::class),
                $app->make(NguoiDung_BUS::class),
                $app->make(Quyen_BUS::class)
            );
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
{
    Paginator::useBootstrapFive();
    if (config('app.env') !== 'local') {
        \Illuminate\Support\Facades\URL::forceScheme('https');
    }
}
}