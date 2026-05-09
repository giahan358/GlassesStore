<?php
namespace App\Dao;

use App\Bus\NguoiDung_BUS;
use App\Bus\PTTT_BUS;
use App\Bus\TaiKhoan_BUS;
use App\Bus\Tinh_BUS;
use App\Enum\HoaDonEnum;
use App\Interface\DAOInterface;
use App\Models\HoaDon;
use App\Services\database_connection;
use function Laravel\Prompts\error;

class HoaDon_DAO{

    public function readDatabase(): array
    {
        $list = [];
        $rs = database_connection::executeQuery("SELECT * FROM hoadon");
        while ($row = $rs->fetch_assoc()) {
            $model = $this->createHoaDonModel($row);
            array_push($list, $model);
        }
        return $list;
    }

    public function insert($e): int
{
    try {
        // Thêm cột HOTEN và SDT vào câu lệnh SQL
        $sql = "INSERT INTO hoadon (EMAIL, IDNHANVIEN, TONGTIEN, IDPTTT, NGAYTAO, DIACHI, HOTEN, SODIENTHOAI, IDTINH, TRANGTHAI)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

        $args = [
            $e->getEmail()->getEmail(),
            $e->getIdNhanVien()->getId(),
            $e->getTongTien(),
            $e->getIdPTTT()->getId(),
            $e->getNgayTao()->format('Y-m-d H:i'),
            $e->getDiaChi(),
            $e->getHoTen(),     
            $e->getSoDienThoai(),
            $e->getTinh()->getId(),
            $e->getTrangThai()->value,
        ];

        $result = database_connection::executeUpdate($sql, ...$args);

        if ($result) {
            return database_connection::getLastInsertId();
        }
    } catch (\Exception $ex) {
        dd([
            'Lỗi' => $ex->getMessage(),
            'Dữ liệu' => $args
        ]);
    }
    return 0;
}

    public function update($e): int
    {
        $sql = "UPDATE hoadon SET TRANGTHAI = ?, TONGTIEN = ? , ORDERCODE = ?, IDPTTT = ?, LINKTT = ? WHERE id = ?";
        $args = [
            $e->getTrangThai()->value,
            $e->getTongTien(),
            $e->getOrderCode(),
            $e->getIdPTTT()->getId(),
            $e->getLinktt(),
            $e->getId()
        ];
        $result = database_connection::executeUpdate($sql, ...$args);
        return is_int($result)? $result : 0;
    }

    public function search(string $condition, array $columnNames): array
    {
        $column = $columnNames[0];
        $query = "SELECT * FROM hoadon WHERE $column LIKE ?";
        $args = ["%" . $condition . "%"];
        $rs = database_connection::executeQuery($query, ...$args);
        $cartsList = [];
        while ($row = $rs->fetch_assoc()) {
            $cartsModel = $this->createHoaDonModel($row);
            array_push($cartsList, $cartsModel);
        }
        if (count($cartsList) === 0) {
            return [];
        }
        return $cartsList;
    }


    public function createHoaDonModel($rs) {
    $id = $rs['ID'];
    $email = app(TaiKhoan_BUS::class)->getModelByEmail($rs['EMAIL']);
    if (!$email) {
        throw new \Exception("Không tìm thấy tài khoản với email: " . $rs['EMAIL']);
    }
    
    $idNhanVien = $rs['IDNHANVIEN'] ? app(NguoiDung_BUS::class)->getModelById($rs['IDNHANVIEN']) : app(NguoiDung_BUS::class)->getModelById(1);
    $tongTien = $rs['TONGTIEN'];
    $idPTTT = app(PTTT_BUS::class)->getModelById($rs['IDPTTT']);
    
    if (!$idPTTT) {
        throw new \Exception("Không tìm thấy phương thức thanh toán với ID: " . $rs['IDPTTT']);
    }

    $ngayTao = $rs['NGAYTAO'] ? new \DateTime($rs['NGAYTAO']) : new \DateTime();
    $diaChi = $rs['DIACHI'];
    
    // --- LẤY THÊM DỮ LIỆU MỚI TỪ DATABASE ---
    $hoTen = $rs['HOTEN'] ?? ''; // Cột mới thêm vào DB
    $sdt = $rs['SODIENTHOAI'] ?? '';     // Cột mới thêm vào DB

    // Xử lý lỗi Argument #10 ($tinh) must be of type Tinh, null given
    $tinh = app(Tinh_BUS::class)->getModelById($rs['IDTINH']);
    if (!$tinh) {
        // Nếu DB lỗi thiếu IDTINH, tạm thời lấy tỉnh đầu tiên để không bị crash trang
        $tinh = app(Tinh_BUS::class)->getModelById(1); 
    }

    $trangThaiRaw = strtoupper(trim($rs['TRANGTHAI'] ?? ''));
    
    // Ánh xạ Enum
    $trangThai = match($trangThaiRaw) {
        'PAID' => HoaDonEnum::PAID,
        'PENDING' => HoaDonEnum::PENDING,
        'EXPIRED' => HoaDonEnum::EXPIRED,
        'CANCELLED' => HoaDonEnum::CANCELLED,
        'REFUNDED' => HoaDonEnum::REFUNDED,
        'DADAT' => HoaDonEnum::DADAT,
        'DANGGIAO' => HoaDonEnum::DANGGIAO,
        'DAGIAO' => HoaDonEnum::DAGIAO,
        default => HoaDonEnum::PENDING,
    };

    $orderCode = $rs['ORDERCODE'];
    $linktt = $rs['LINKTT'];

    return new HoaDon(
        $id,           
        $email,        
        $idNhanVien,   
        $tongTien,    
        $idPTTT,        
        $ngayTao,      
        $diaChi,      
        $hoTen,      
        $sdt,          
        $tinh,         
        $trangThai,    
        $orderCode,     
        $linktt        
    );
}

    public function getAll() : array {
        $list = [];
        $rs = database_connection::executeQuery("SELECT * FROM hoadon");
        while($row = $rs->fetch_assoc()) {
            $model = $this->createHoaDonModel($row);
            array_push($list, $model);
        }
        return $list;
    }

    public function searchByTinh($idTinh) {
        $list = [];
        $query = "SELECT * FROM hoadon WHERE IDTINH = ?";
        $rs = database_connection::executeQuery($query, $idTinh);
        while($row = $rs->fetch_assoc()) {
            $model = $this->createHoaDonModel($row);
            array_push($list, $model);
        }
        return $list;
    }
    public function getHoaDonsByEmail($email): array
    {
        $list = [];
        $query = "SELECT * FROM hoadon WHERE email = ?";
        $rs = database_connection::executeQuery($query, [$email]); // Sử dụng mảng cho tham số
        while ($row = $rs->fetch_assoc()) {
            $model = $this->createHoaDonModel($row);
            if ($model) {
                $list[] = $model; // Sử dụng [] để thêm phần tử, ngắn gọn hơn array_push
            }
        }
        return $list;
    }
    public function getAllHoaDons() {
        $query = "SELECT * FROM hoadon";
        $result = database_connection::executeQuery($query);
        $hoaDons = [];
        while ($row = $result->fetch_assoc()) {
            $hoaDons[] = $this->createHoaDonModel($row);
        }
        return $hoaDons;
    }

    public function getByOrderCode(int $orderCode)
    {
        $sql = "SELECT * FROM hoadon WHERE ORDERCODE = ?";
        $result = database_connection::executeQuery($sql, $orderCode);
        if($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            if($row) {
                return $this->createHoaDonModel($row);
            }
        }
        return null;
    }
    
    public function getHoaDonsByTrangThai($trangThai)
    {
        
        $list = [];
        $query = "SELECT * FROM hoadon WHERE TRANGTHAI = ?";
        $rs = database_connection::executeQuery($query, ...[$trangThai]);
        while ($row = $rs->fetch_assoc()) {
            $model = $this->createHoaDonModel($row);
            if ($model) {
                $list[] = $model; 
            }
        }
        return $list;
    }

    public function getHoaDonsByNgay($ngayBatDau, $ngayKetThuc)
    {
        $list = [];
        $query = "SELECT * FROM hoadon WHERE NGAYTAO BETWEEN ? AND ?";
        $rs = database_connection::executeQuery($query, $ngayBatDau, $ngayKetThuc);
        
        while ($row = $rs->fetch_assoc()) {
            $model = $this->createHoaDonModel($row);
            if ($model) {
                $list[] = $model;
            }
        }

        return $list;
    }

    public function getHoaDonsOrderByTongTien(string $order = 'ASC')
    {
        $order = strtoupper($order) === 'DESC' ? 'DESC' : 'ASC'; // Bảo vệ injection
        $list = [];
        $query = "SELECT * FROM hoadon ORDER BY TONGTIEN $order";
        $rs = database_connection::executeQuery($query); // Không cần tham số
        while ($row = $rs->fetch_assoc()) {
            $model = $this->createHoaDonModel($row);
            if ($model) {
                $list[] = $model;
            }
        }
        return $list;
    }

    public function getHoaDonsBySoseri($soSeri)
    {
        $list = [];
        $query = "
            SELECT hd.* 
            FROM hoadon hd
            JOIN cthd ct ON hd.ID = ct.IDHD
            WHERE ct.SOSERI = ?
        ";
        $rs = database_connection::executeQuery($query, $soSeri);

        while ($row = $rs->fetch_assoc()) {
            $model = $this->createHoaDonModel($row);
            if ($model) {
                $list[] = $model;
            }
        }

        return $list;
    }
    public function searchByEmailOrSDT(string $keyword): array
{
    $list = [];
    // Chỉ JOIN các bảng liên quan đến Khách hàng để lấy Số điện thoại
    $query = "
        SELECT DISTINCT hd.* FROM hoadon hd
        LEFT JOIN taikhoan tk ON hd.EMAIL = tk.EMAIL
        LEFT JOIN nguoidung nd_kh ON tk.IDNGUOIDUNG = nd_kh.ID
        WHERE hd.EMAIL LIKE ? 
           OR nd_kh.SODIENTHOAI LIKE ?
    ";
    
    // Tham số tìm kiếm
    $param = "%" . $keyword . "%";
    
    // Chỉ truyền 2 tham số (cho Email và SDT) vào hàm executeQuery
    $rs = database_connection::executeQuery($query, $param, $param);

    while ($row = $rs->fetch_assoc()) {
        $model = $this->createHoaDonModel($row);
        if ($model) {
            $list[] = $model;
        }
    }
    return $list;
}

    public function getUserEmailByOrderId($orderId)
    {
        $query = "SELECT EMAIL FROM hoadon WHERE ID = ?";
        $rs = database_connection::executeQuery($query, $orderId);
        if ($rs->num_rows > 0) {
            $row = $rs->fetch_assoc();
            return $row['EMAIL'];
        }
        return null;
    }
}