<?php
namespace App\Dao;

use App\Bus\ChucNang_BUS;
use App\Bus\Quyen_BUS;
use App\Interface\DAOInterface;
use App\Models\CTQ;
use App\Models\Tinh;
use App\Services\database_connection;
use Exception;
use InvalidArgumentException;

use function Laravel\Prompts\alert;

class CTQ_DAO implements DAOInterface {
    public function readDatabase(): array
    {
        $list = [];
        $rs = database_connection::executeQuery("SELECT * FROM ctq");
        while ($row = $rs->fetch_assoc()) {
            $model = $this->createCTQModel($row);
            array_push($list, $model);
        }
        return $list;
    }
    public function createCTQModel($rs) {
        $idChucNang = app(ChucNang_BUS::class)->getModelById($rs['IDCHUCNANG']);
        $idQuyen = app(Quyen_BUS::class)->getModelById($rs['IDQUYEN']);
        $trangThaiHD = $rs['TRANGTHAIHD'];
        return new CTQ($idQuyen, $idChucNang, $trangThaiHD);
    }
    public function getAll() : array {
        $list = [];
        $rs = database_connection::executeQuery("SELECT * FROM ctq");
        while($row = $rs->fetch_assoc()) {
            $model = $this->createCTQModel($row);
            array_push($list, $model);
        }
        return $list;
    }
    public function getById($id) {
        $list = [];
        $query = "SELECT * FROM ctq WHERE idQuyen = ?";
        $rs = database_connection::executeQuery($query, $id);
        while($row = $rs->fetch_assoc()) {
            $model = $this->createCTQModel($row);
            array_push($list, $model);
        }
        return $list;
    }
    public function insert($model): int {
        $query = "INSERT INTO `ctq`(`IDQUYEN`, `IDCHUCNANG`, `TRANGTHAIHD`) VALUES (?,?,?)";
        $args = [$model->getIdQuyen()->getId(), $model->getIdChucNang()->getId(), $model->getTrangThaiHD()];
        return database_connection::executeQuery($query, ...$args);
    }
    public function update($model): int {
        $query = "UPDATE ctq SET idChucNang = ?, trangThaiHD = ? WHERE idQuyen = ?";
        $args = [$model->getIdChucNang()->getId(), $model->getTrangThaiHD(), $model->getIdQuyen()->getId()];
        $result = database_connection::executeUpdate($query, ...$args);
        return is_int($result) ? $result : 0;  
    }
    public function delete(int $id): int
    {
        $query = "UPDATE ctq SET trangThaiHD = 0 WHERE idQuyen = ?";
        $result = database_connection::executeUpdate($query, ...[$id]);
        
        return is_int($result) ? $result : 0;
    }
    public function deleteByIdQuyenAndIdChucNang($idQuyen, $idChucNang): int
    {
        $query = "UPDATE ctq SET trangThaiHD = false WHERE idQuyen = ? AND idChucNang = ?";
        $result = database_connection::executeUpdate($query, $idQuyen, $idChucNang);
        
        return is_int($result) ? $result : 0;
    }

    public function deleteByQuyenId($quyenId): int
    {
        $query = "UPDATE ctq SET trangThaiHD = false WHERE idQuyen = ?";
        $result = database_connection::executeUpdate($query, $quyenId);
        
        return is_int($result) ? $result : 0;
    }

    public function search(string $condition, $columnNames): array
    {
        if (empty($condition)) {
            throw new InvalidArgumentException("Search condition cannot be empty or null");
        }
        $query = "";
        if ($columnNames === null || count($columnNames) === 0) {
            $query = "SELECT * FROM ctq WHERE idquyen LIKE ? OR idChucNang LIKE ? OR thaoTac LIKE ? OR trangThaiHD LIKE ? ";
            $args = array_fill(0,  4, "%" . $condition . "%");
        } else if (count($columnNames) === 1) {
            $column = $columnNames[0];
            $query = "SELECT * FROM ctq WHERE $column LIKE ?";
            $args = ["%" . $condition . "%"];
        } else {
            $query = "SELECT * FROM ctq WHERE " . implode(" LIKE ? OR ", $columnNames) . " LIKE ?";
            $args = array_fill(0, count($columnNames), "%" . $condition . "%");
        }
        $rs = database_connection::executeQuery($query, ...$args);
        $list = [];
        while ($row = $rs->fetch_assoc()) {
            $model = $this->createCTQModel($row);
            array_push($list, $model);
        }
        if (count($list) === 0) {
            return [];
        }
        return $list;
    }

}   
?>