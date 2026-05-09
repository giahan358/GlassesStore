<?php
namespace App\Dao;

use App\Interface\DAOInterface;
use App\Models\LoaiSanPham;
use App\Services\database_connection;

class LoaiSanPham_DAO implements DAOInterface {

  
    public function readDatabase(): array {
        $list = [];
        $query = "SELECT * FROM loaisanpham"; 

      
        $rs = database_connection::executeQuery($query);
        while ($row = $rs->fetch_assoc()) {
           
            $list[] = $this->createLoaiSanPhamModel($row);
        }

        return $list;

    }


    public function getAll(): array {
        $list = [];
        $query = "SELECT * FROM loaisanpham WHERE TRANGTHAIHD >= 0";

        $rs = database_connection::executeQuery($query);
        while ($row = $rs->fetch_assoc()) {
            $list[] = $this->createLoaiSanPhamModel($row);
        }
        
        return $list;

    }

    public function getAllIsActive(): array {
    $list = [];
    $query = "SELECT * FROM loaisanpham WHERE TRANGTHAIHD = 1";

    $rs = database_connection::executeQuery($query);
    while ($row = $rs->fetch_assoc()) {
        $list[] = $this->createLoaiSanPhamModel($row);
    }
    
    // Sửa lỗi: Trả về danh sách đã lấy được thay vì mảng rỗng
    return $list; 
}
 
    private function createLoaiSanPhamModel($row){
        // return new LoaiSanPham(
        //     $row['ID'], $row['TENLSP'], $row['MOTA'], $row['TRANGTHAIHD']
        // );
        $id = $row['ID'];
        $tenlsp = $row['TENLSP'];
        $mota = $row['MOTA'];
        $trangThaiHD = $row['TRANGTHAIHD'];
        return new LoaiSanPham($id, $tenlsp, $mota, $trangThaiHD);
    }

    public function getById($id) {
        $query = "SELECT * FROM loaisanpham WHERE id = ?";
        $result = database_connection::executeQuery($query, $id);
        if($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            if($row) {
                return $this->createLoaiSanPhamModel($row);
            }
        }
        return null;
    }

    public function insert($model): int {
        $query = "INSERT INTO loaisanpham (ID, TENLSP, MOTA, TRANGTHAIHD) VALUES (?, ?, ?, ?)";
        $args = [ $model->getId(), $model->gettenLSP(), $model->getmoTa(), $model->gettrangThaiHD()];
        $result = database_connection::executeUpdate($query, ...$args);
        return is_int($result) ? $result : 0;
    }


    public function update($model): int {
        $query = "UPDATE loaisanpham SET TENLSP = ?, MOTA = ?, TRANGTHAIHD = ? WHERE ID = ?";
        $args = [$model->gettenLSP(), $model->getmoTa(), $model->gettrangThaiHD(), $model->getId()];
        $result = database_connection::executeUpdate($query, ...$args);
        return is_int($result) ? $result : 0;
    }

  
    public function delete(int $id): int {
    $queryGet = "SELECT TRANGTHAIHD FROM loaisanpham WHERE ID = ?";
    $rs = database_connection::executeQuery($queryGet, $id);
    $row = $rs->fetch_assoc();
    
    if (!$row) return 0;
    $newStatus = ($row['TRANGTHAIHD'] == 1) ? 0 : 1;
    $query = "UPDATE loaisanpham SET TRANGTHAIHD = ? WHERE ID = ?";
    $result = database_connection::executeUpdate($query, $newStatus, $id);
    return is_int($result) ? $result : 0;
}

    public function search(string $condition, array $columnNames = []): array {
        $list = [];
        if (empty($columnNames)) {
            $query = "SELECT * FROM loaisanpham WHERE TENLSP LIKE ? OR MOTA LIKE ?";
            $args = array_fill(0, 2, "%$condition%");
        } else {
            $whereClauses = implode(" LIKE ? OR ", $columnNames) . " LIKE ?";
            $query = "SELECT * FROM loaisanpham WHERE $whereClauses";
            $args = array_fill(0, count($columnNames), "%$condition%");
        }

        $rs = database_connection::executeQuery($query, ...$args);
        while ($row = $rs->fetch_assoc()) {
            $list[] = $this->createLoaiSanPhamModel($row);
        }

        return $list;
    }
    public function checkExistName(string $name, $id = null): bool {
    // Nếu có ID (trường hợp Sửa), ta bỏ qua kiểm tra chính bản thân nó
    if ($id) {
        $query = "SELECT COUNT(*) as count FROM loaisanpham WHERE LOWER(TENLSP) = LOWER(?) AND ID != ?";
        $rs = database_connection::executeQuery($query, $name, $id);
    } else {
        $query = "SELECT COUNT(*) as count FROM loaisanpham WHERE LOWER(TENLSP) = LOWER(?)";
        $rs = database_connection::executeQuery($query, $name);
    }
    
    $row = $rs->fetch_assoc();
    return $row['count'] > 0;
}
}
?>
