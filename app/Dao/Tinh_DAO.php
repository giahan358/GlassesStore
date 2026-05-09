<?php
namespace App\Dao;

use App\Interface\DAOInterface;
use App\Models\Tinh;
use App\Services\database_connection;
use Exception;
use InvalidArgumentException;

use function Laravel\Prompts\alert;

class Tinh_DAO  {
    public function readDatabase(): array
    {
        $list = [];
        $rs = database_connection::executeQuery("SELECT * FROM tinh");
        while ($row = $rs->fetch_assoc()) {
            $model = $this->createTinhModel($row);
            array_push($list, $model);
        }
        return $list;
    }
    public function createTinhModel($rs) {
        $id = $rs['ID'];
        $tenTinh = $rs['TENTINH'];
        $trangThaiHD = $rs['TRANGTHAIHD'];
        return new Tinh($id, $tenTinh, $trangThaiHD);
    }
    public function getAll() : array {
        $list = [];
        $rs = database_connection::executeQuery("SELECT * FROM tinh WHERE TRANGTHAIHD = 1");
        while($row = $rs->fetch_assoc()) {
            $model = $this->createTinhModel($row);
            array_push($list, $model);
        }
        return $list;
    }
    public function getById($id) {
        $query = "SELECT * FROM tinh WHERE id = ?";
        $result = database_connection::executeQuery($query, $id);
        if($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            if($row) {
                return $this->createTinhModel($row);
            }
        }
        return null;
    }
    public function insert($tenTinh): int {
        $query = "INSERT INTO tinh (TENTINH, TRANGTHAIHD) VALUES (?,1)";
        $rs = database_connection::executeQuery($query, $tenTinh);
        return is_int($rs) ? $rs : 0;
    }
    public function update($model): int {
        $query = "UPDATE tinh SET tenTinh = ?, trangThaiHD = ? WHERE id = ?";
        $args = [$model->getTenTinh(), $model->getTrangThaiHD(), $model->getId()];
        $result = database_connection::executeUpdate($query, ...$args);
        return is_int($result) ? $result : 0;  
    }
    public function delete($id): int
    {
        $query = "UPDATE tinh SET TRANGTHAIHD = 0 WHERE id = ?";
        $result = database_connection::executeUpdate($query,$id);
        
        return is_int($result) ? $result : 0;
    }

    public function search(string $condition, array $n): array
    {
        if (empty($condition)) {
            throw new InvalidArgumentException("Search condition cannot be empty or null");
        }
    
        $query = "SELECT * FROM tinh WHERE tenTinh LIKE ?";
        $args = ["%" . $condition . "%"];
    
        $rs = database_connection::executeQuery($query, ...$args);
        $list = [];
    
        while ($row = $rs->fetch_assoc()) {
            $model = $this->createTinhModel($row);
            $list[] = $model;
        }
    
        return $list;
    }
    

}   
?>