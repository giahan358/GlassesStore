<?php

namespace App\Dao;

use App\Models\NCC;
use App\Services\database_connection;
use App\Interface\DAOInterface;
use InvalidArgumentException;

class NCC_DAO implements DAOInterface
{
    public function readDatabase(): array
    {
        $list = [];
        $rs = database_connection::executeQuery("SELECT * FROM ncc");
        while ($row = $rs->fetch_assoc()) {
            $model = $this->createNCCModel($row);
            array_push($list, $model);
        }
        return $list;
    }

    public function getAll(): array
    {
        $list = [];
        $rs = database_connection::executeQuery("SELECT * FROM ncc");
        while ($row = $rs->fetch_assoc()) {
            $model = $this->createNCCModel($row);
            array_push($list, $model);
        }
        return $list;
    }

    public function getById($id)
    {
        $query = "SELECT * FROM ncc WHERE ID = ?";
        $result = database_connection::executeQuery($query, $id);
        if ($result && $result->num_rows > 0) {
            $row = $result->fetch_assoc();
            return $this->createNCCModel($row);
        }
        return null;
    }

   public function insert($model): int
{
    $query = "INSERT INTO ncc (TENNCC, SODIENTHOAI, DIACHI, MOTA, TRANGTHAIHD) VALUES (?, ?, ?, ?, ?)";
    $args = [
        $model->getTenNCC(), 
        $model->getSdtNCC(), 
        $model->getDiachi(), 
        $model->getMoTa(), 
        $model->getTrangthaiHD()
    ];
    
    $result = database_connection::executeUpdate($query, ...$args);
    
    // Nếu vẫn không được, hãy bỏ comment dòng dưới để xem lỗi thực tế là gì
    // if ($result === 0) dd("Lỗi SQL hoặc không có dòng nào được chèn");

    return $result;
}

    public function update($model): int
    {
        $query = "UPDATE ncc SET TENNCC = ?, SODIENTHOAI = ?, DIACHI = ?, MOTA = ?, TRANGTHAIHD = ? WHERE ID = ?";
        $args = [$model->getTenNCC(), $model->getSdtNCC(), $model->getDiachi(), $model->getMoTa(), $model->getTrangthaiHD(), $model->getIdNCC()];
        return database_connection::executeUpdate($query, ...$args);
    }

    // Sửa hàm delete thành hàm xử lý trạng thái
public function delete($id): int
{
    // Bước 1: Lấy trạng thái hiện tại để đảo ngược
    $ncc = $this->getById($id);
    if (!$ncc) {
        return 0;
    }

    // Bước 2: Đảo ngược trạng thái (Nếu đang 1 thì thành 0, nếu 0 thành 1)
    $newStatus = ($ncc->getTrangthaiHD() == 1) ? 0 : 1;

    // Bước 3: Cập nhật vào Database
    $query = "UPDATE ncc SET TRANGTHAIHD = ? WHERE ID = ?";
    return database_connection::executeUpdate($query, $newStatus, $id);
}
// Thêm hàm này vào trong class NCC_DAO (App\Dao\NCC_DAO)
public function updateStatus(int $id, int $status): int
{
    $query = "UPDATE ncc SET TRANGTHAIHD = ? WHERE ID = ?";
    // Sử dụng executeUpdate để thực hiện lệnh thay đổi dữ liệu
    return database_connection::executeUpdate($query, $status, $id);
}
    public function search($value, $columns): array
    {
        if (empty($value)) {
            throw new InvalidArgumentException("Search condition cannot be empty or null");
        }
        $query = "";
        if ($columns === null || count($columns) === 0) {
            $query = "SELECT * FROM ncc WHERE ID LIKE ? OR TENNCC LIKE ? OR SODIENTHOAI LIKE ? OR DIACHI LIKE ? OR MOTA LIKE ? OR TRANGTHAIHD LIKE ?";
            $args = array_fill(0, 6, "%" . $value . "%");
        } else if (count($columns) === 1) {
            $column = $columns[0];
            $query = "SELECT * FROM ncc WHERE $column LIKE ?";
            $args = ["%" . $value . "%"];
        } else {
            $query = "SELECT * FROM ncc WHERE " . implode(" LIKE ? OR ", $columns) . " LIKE ?";
            $args = array_fill(0, count($columns), "%" . $value . "%");
        }
        $rs = database_connection::executeQuery($query, ...$args);
        $list = [];
        while ($row = $rs->fetch_assoc()) {
            $model = $this->createNCCModel($row);
            array_push($list, $model);
        }
        if (count($list) === 0) {
            return [];
        }
        return $list;
    }

    private function createNCCModel($row): NCC
    {
        return new NCC(
            $row['ID'],
            $row['TENNCC'],
            $row['SODIENTHOAI'],
            $row['MOTA'],
            $row['DIACHI'],
            $row['TRANGTHAIHD']
        );
    }
public function getAllActive(): array
{
    $list = [];
    // Chỉ lấy các nhà cung cấp có TRANGTHAIHD = 1 ngay từ câu lệnh SQL
    $query = "SELECT * FROM ncc WHERE TRANGTHAIHD = 1";
    $rs = database_connection::executeQuery($query);
    
    while ($row = $rs->fetch_assoc()) {
        $model = $this->createNCCModel($row);
        array_push($list, $model);
    }
    return $list;
}
  
}
