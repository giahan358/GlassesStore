<?php
namespace App\Bus;
use App\Dao\HoaDon_DAO;
use App\Dao\CTHD_DAO;
use App\Bus\CTHD_BUS;
use App\Bus\CTSP_BUS;
use App\Bus\SanPham_BUS;
use App\Bus\GioHang_BUS;
use App\Bus\CTGH_BUS;
use App\Bus\Auth_BUS;
use App\Interface\BUSInterface;
use App\Models\HoaDon;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Validator;
use App\Jobs\CheckVnpayPaymentStatus;
use Illuminate\Support\Facades\Redis;
use Carbon\Carbon;
class HoaDon_BUS{

    private $hoaDonDAO;
    private $cthdDAO;
    private $cthdBUS;
    private $listHoaDon = array();

    public function __construct(HoaDon_DAO $hoaDonDAO, CTHD_DAO $cthdDAO, CTHD_BUS $cthdBUS)
    {
        $this->hoaDonDAO = $hoaDonDAO;
        $this->cthdDAO = $cthdDAO;
        $this->cthdBUS = $cthdBUS;
        
    }

    public function getAllModels() {
        $this->refreshData();
        return $this->listHoaDon;
    }

    public function refreshData(): void {
       $this->listHoaDon = $this->hoaDonDAO->getAll();
    }

    public function getModelById(int $id) {
        $models = $this->hoaDonDAO->readDatabase();
        foreach ($models as $model) {
            if ($model->getId() === $id) {
                return $model;
            }
        }
        return null;
    }

    public function addModel($model): int {  
        return $this->hoaDonDAO->insert($model);
    }

    public function updateModel($model): int {
        return $this->hoaDonDAO->update($model);
    }

    public function searchModel(string $value, array $columns): array {
        return $this->hoaDonDAO->search($value, $columns);
    }

    public function searchByTinh($idTinh) {
        return $this->hoaDonDAO->searchByTinh($idTinh);
    }
    public function getHoaDonsByEmail($email): array
    {
        $hoaDons = $this->hoaDonDAO->getHoaDonsByEmail($email);
        $result = [];

        if (!empty($hoaDons)) {
            foreach ($hoaDons as $hoaDon) {
                if ($hoaDon) { // Kiểm tra hóa đơn hợp lệ
                    $chiTietHoaDons = $this->cthdBUS->getCTHTbyIDHD($hoaDon->getId());
                    $hoaDonData = [
                        'hoaDon' => $hoaDon,
                        'chiTietHoaDons' => $chiTietHoaDons ?? [], // Đảm bảo mảng rỗng nếu không có chi tiết
                        'quantity' => is_array($chiTietHoaDons) ? count($chiTietHoaDons) : 0
                    ];
                    $result[] = $hoaDonData;
                }
            }
        }

        return $result;
    }

    public function getAllHoaDons()
    {
        return $this->hoaDonDAO->getAllHoaDons();
    }

    public function getByOrderCode(int $orderCode) {
        return $this->hoaDonDAO->getByOrderCode($orderCode);
    }

    public function getHoaDonsByTrangThai($trangThai) {
        $hoaDons = $this->hoaDonDAO->getHoaDonsByTrangThai($trangThai);

        return $hoaDons;
    }

    public function getHoaDonsByNgay($ngayBatDau, $ngayKetThuc) {
        $hoaDons = $this->hoaDonDAO->getHoaDonsByNgay($ngayBatDau, $ngayKetThuc);

        return $hoaDons;
    }

    public function getHoaDonsBySoseri($soSeri) {
        $hoaDons = $this->hoaDonDAO->getHoaDonsBySoseri($soSeri);

        return $hoaDons;
    }
    

    public function getHoaDonsOrderByTongTien(string $order = 'ASC')
    {
        return $this->hoaDonDAO->getHoaDonsOrderByTongTien($order);
    }

    public function createHoaDon($request, $status)
{
    if (!$request) return null;

    // 1. LẤY CÁC ĐỐI TƯỢNG PHỤ THUỘC
    $tinh = app(Tinh_BUS::class)->getModelById($request->input('tinh'));
    $pttt = app(PTTT_BUS::class)->getModelById($request->input('pttt'));
    $email = app(Auth_BUS::class)->getEmailFromToken();
    $user = app(TaiKhoan_BUS::class)->getModelById($email);

    // Kiểm tra dữ liệu hợp lệ[cite: 4]
    if (!$tinh || !$pttt || !$user) {
        throw new \Exception('Dữ liệu Tỉnh/Thành, Phương thức TT hoặc User không hợp lệ!');
    }

    // 2. XỬ LÝ THÔNG TIN GIAO HÀNG (Lấy từ Form hoặc Profile mặc định)
    $diachi = $request->input('diachi');
    // Ưu tiên lấy từ Form (cho trường hợp mua hộ), nếu trống thì lấy từ Profile mặc định
    $hotenMoi = $request->input('hoten') ?: $user->getIdNguoiDung()?->getHoTen();
    $sdtMoi = $request->input('sodienthoai') ?: $user->getIdNguoiDung()?->getSoDienThoai();

    // 3. LẤY GIỎ HÀNG TỪ SESSION[cite: 4]
    $listSP = session('listSP');
    if (is_string($listSP)) {
        $listSP = json_decode($listSP); 
    } elseif (is_array($listSP) && isset($listSP[0]) && is_array($listSP[0])) {
        $listSP = json_decode(json_encode($listSP)); 
    }

    // 4. TÍNH TỔNG TIỀN HÓA ĐƠN[cite: 4]
    $sum = 0;
    foreach ($listSP as $key) {
        $sp = app(SanPham_BUS::class)->getModelById($key->idsp);
        if ($sp) {
            $sum += $sp->getDonGia() * $key->quantity;
        }
    }

    // 5. TẠO ĐỐI TƯỢNG HÓA ĐƠN (Cập nhật đúng 13 tham số theo Model mới)
    $hd = new HoaDon(
        null,           // ID (tự tăng)
        $user,          // TaiKhoan
        app(NguoiDung_BUS::class)->getModelById(1), // Nhân viên xử lý mặc định
        $sum,           // Tổng tiền
        $pttt,          // Phương thức thanh toán
        new \DateTime(),// Ngày tạo
        $diachi,        // Địa chỉ giao hàng
        $hotenMoi,      // Họ tên người nhận (MỚI)[cite: 6]
        $sdtMoi,        // Số điện thoại người nhận (MỚI)[cite: 6]
        $tinh,          // Đối tượng Tỉnh/Thành
        $status,        // Trạng thái hóa đơn (HoaDonEnum)
        null,           // OrderCode
        null            // LinkTT
    );

    // Lưu vào database và cập nhật lại ID cho Object
    $newId = $this->addModel($hd);
    $hd->setId($newId);

    // 6. TẠO CHI TIẾT HÓA ĐƠN (CTHD)[cite: 4]
    foreach ($listSP as $key) {
        $sp = app(SanPham_BUS::class)->getModelById($key->idsp);
        $listCTSP = app(CTSP_BUS::class)->getCTSPIsNotSoldByIDSP($key->idsp);

        // Đảm bảo đủ số lượng seri trong kho
        $quantity = min($key->quantity, count($listCTSP));

        for($i = 0 ; $i < $quantity ; $i++) {
            $cthd = new \App\Models\CTHD(
                $hd->getId(), 
                $sp->getDonGia(), 
                $listCTSP[$i]->getSoSeri(), 
                1
            );
            app(CTHD_BUS::class)->addModel($cthd);
            
            // Tùy chọn: Cập nhật trạng thái seri sang 'Đang giữ chỗ'
            // app(CTSP_BUS::class)->updateStatus($listCTSP[$i]->getSoSeri(), 2); 
        }
    }
    
    return $hd;
}
    public function chotDonHangSauThanhToan($request, $idHoaDon, $status) 
    {
        $source = session('checkout_source');

        // Khởi tạo các BUS cần thiết
        $cthdBus = app(CTHD_BUS::class);
        $ctspBus = app(CTSP_BUS::class);
        $spBus   = app(SanPham_BUS::class);
        $ctghBus = app(CTGH_BUS::class);
        $authBus = app(Auth_BUS::class);
        $ghBus   = app(GioHang_BUS::class);

        $hd = $this->getModelById($idHoaDon);
        if (!$hd) return false;

        $listCTHD = $cthdBus->getCTHTbyIDHD($idHoaDon);
        if (empty($listCTHD)) return false;

        $email = $authBus->getEmailFromToken();
        $gh = $ghBus->getByEmail($email);

        foreach ($listCTHD as $cthd) {
            if (!$cthd) continue;

            $soSeri = $cthd->getSoSeri();
            $ctsp = $ctspBus->getCTSPBySoSeri($soSeri);
            
            if ($ctsp) {
                $sp = $ctsp->getIdSP(); 
                if ($sp) {
                    $ctspBus->updateStatus($soSeri, 0); // Đã bán
                    $sp->setSoLuong(max(0, $sp->getSoLuong() - 1));
                    $spBus->updateModel($sp);

                    if ($gh) {
                        if ($source === 'cart') {
                            $ctghBus->deleteCTGH($gh->getIdGH(), $sp->getId());
                        } 
                        // else ($source = 'buy_now') {
                            
                        // }
                    }
                }
            }
        }

        if ($status === "PAID") {
            $hd->setTrangThai(\App\Enum\HoaDonEnum::PAID);
        }
        $this->updateModel($hd);
        session()->forget('listSP');
        return true;
    }
    
    public function huyThanhToanDonHang($idHoaDon) {
        $hd = $this->getModelById($idHoaDon);
        if (!$hd) return false;

        $hd->setTrangThai(\App\Enum\HoaDonEnum::CANCELLED);
        $this->updateModel($hd);
        return true;
    }
    public function searchByEmailOrSDT(string $keyword): array
    {
        return $this->hoaDonDAO->searchByEmailOrSDT($keyword);
    }
    public function setLinkThanhToan($idHoaDon, $link, $ref) {
        $hd = $this->getModelById($idHoaDon);
        if (!$hd) return false;

        $hd->setLinktt($link);
        $hd->setOrderCode($ref);
        $this->updateModel($hd);
        if ($hd->getIdPTTT()->getId() == 3) {
            CheckVnpayPaymentStatus::dispatch($hd->getId())->delay(now()->addMinutes(15));
        }
        return true;
    }
    public function hoanKho($idHoaDon) {
        $cthdBus = app(CTHD_BUS::class);
        $ctspBus = app(CTSP_BUS::class);
        $spBus   = app(SanPham_BUS::class);

        $listCTHD = $cthdBus->getCTHTbyIDHD($idHoaDon);
        if (empty($listCTHD)) return false;

        foreach ($listCTHD as $cthd) {
            if (!$cthd) continue;

            $soSeri = $cthd->getSoSeri();
            $ctsp = $ctspBus->getCTSPBySoSeri($soSeri);
            
            if ($ctsp) {
                $sp = $ctsp->getIdSP(); 
                if ($sp) {
                    $ctspBus->updateStatus($soSeri, 1); 
                    $sp->setSoLuong($sp->getSoLuong() + 1);
                    $spBus->updateModel($sp);
                }
            }
        }

        return true;
    }
    public function huyDonHangVaHoanKho($idHoaDon) 
    {
        $hd = $this->getModelById($idHoaDon);
        if (!$hd) return false;

        if ($hd->getIdPTTT()->getId() == 1) {
            $this->hoanKho($idHoaDon);
        }

        // 3. Cập nhật trạng thái hóa đơn thành Hủy (CANCELLED)
        // Bạn hãy kiểm tra xem trong HoaDonEnum đã có cột CANCELLED hoặc tương đương chưa
        $hd->setTrangThai(\App\Enum\HoaDonEnum::CANCELLED); 
        $this->updateModel($hd);

        return true;
    }
}