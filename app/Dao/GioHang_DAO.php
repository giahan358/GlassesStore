<?php
namespace App\Dao;

use App\Interface\DAOInterface;
use App\Models\GioHang;
use App\Services\database_connection;
use InvalidArgumentException;
use PhpParser\Node\Expr\List_;
use SanPham_BUS;
use SanPham_DAO;

class GioHang_DAO
{
    /**
     * Đọc toàn bộ dữ liệu từ bảng GIOHANG
     */
    public function readDatabase(): array
    {
        $list = [];
        $rs = database_connection::executeQuery("SELECT * FROM giohang");
        while ($row = $rs->fetch_assoc()) {
            $model = $this->createGioHangModel($row);
            array_push($list, $model);
        }
        return $list;
    }

    /**
     * Tạo mô hình GioHang từ dữ liệu cơ sở dữ liệu
     */
    private function createGioHangModel($row): GioHang
    {
        return new GioHang(
            $row['ID'],
            $row['EMAIL'],
            $row['CREATEDAT'],
            $row['TRANGTHAIHD']
        );
    }

    /**
     * Lấy tất cả bản ghi từ bảng GIOHANG
     */
    public function getAll(): array
    {
        return $this->readDatabase();
    }

    /**
     * Lấy một bản ghi theo email và idSanPham (giả sử đây là khóa chính)
     */
    public function getById($id) {
        $query = "SELECT * FROM giohang WHERE ID = ?";
        $result = database_connection::executeQuery($query, $id);
        
        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            if ($row) {
                return $this->createGioHangModel($row);
            }
        }
        return null;
    }
    public function getByEmail($email)
    {
        $query = "SELECT * FROM giohang WHERE email = ?";
        $result = database_connection::executeQuery($query, $email);
        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            if ($row) {
                return $this->createGioHangModel($row);
            }
        }
        return null;
    }

    /**
     * Thêm một bản ghi vào bảng GIOHANG
     */
    public function insert($e)
    {
        // if (!$e instanceof GioHang) {
        //     throw new InvalidArgumentException("Tham số phải là instance của GioHang");
        // }

        $query = "INSERT INTO `giohang`(`EMAIL`, `CREATEDAT`, `TRANGTHAIHD`) VALUES (?, ?, ?)";
        $args = [$e->getEmail(), $e->getCreatedAt(), $e->getTrangThaiHD()];
        $rs = database_connection::executeUpdate($query, ...$args);
        return is_int($rs) ? $rs : 0;
    }

    /**
     * Cập nhật một bản ghi trong bảng GIOHANG
     */
    public function update($e): int
    {
        $query = "UPDATE giohang SET createdAt = ? and trangThaiHD = ? WHERE email = ?";
        $args = [$e->getCreatedAt(), $e->getTrangThaiHD(), $e->getEmail()];
        $rs = database_connection::executeUpdate($query, ...$args);
        return is_int($rs) ? $rs : 0;
    }

    /**
     * Xóa một bản ghi từ bảng GIOHANG
     */
    public function controlDelete($e, $active): int
    {
        $query = "UPDATE giohang SET trangThaiHD = ? WHERE ID = ?";
        $args = [$active, $e];
        $rs = database_connection::executeUpdate($query,...$args);
        return is_int($rs) ? $rs : 0;
    }

    /**
     * Kiểm tra xem một bản ghi có tồn tại không
     */

    /**
     * Tìm kiếm bản ghi theo điều kiện
     */
    public function search(string $condition, array $columnNames = []): array
    {
        $columns = empty($columnNames)
            ? ['email', 'idSanPham', 'soSeri']
            : $columnNames;

        $query = "SELECT * FROM giohang WHERE " . implode(" LIKE ? OR ", $columns) . " LIKE ?";
        $args = array_fill(0, count($columns), "%" . $condition . "%");
        $rs = database_connection::executeQuery($query, ...$args);

        $list = [];
        while ($row = $rs->fetch_assoc()) {
            $list[] = $this->createGioHangModel($row);
        }
        return $list;
    }
    public function getTotalAmount($idGioHang): float
    {
    $query = "SELECT SUM(ct.SO_LUONG * sp.GIA_BAN) as tong_tien 
              FROM CHITIETGIOHANG ct 
              JOIN SANPHAM sp ON ct.ID_SANPHAM = sp.ID 
              WHERE ct.ID_GIOHANG = ?";
    
    $rs = database_connection::executeQuery($query, $idGioHang);
    $row = $rs->fetch_assoc();
    
    return $row['tong_tien'] ?? 0;
    }
}
?>