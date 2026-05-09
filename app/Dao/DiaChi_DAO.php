<?php
namespace App\Dao;
use App\Bus\NguoiDung_BUS;
use App\Models\DiaChi;
use App\Services\database_connection;

class DiaChi_DAO {
    public function createDiaChiModel ($rs) {
        $nd = app(NguoiDung_BUS::class)->getModelById($rs['IDND']);
        // Cập nhật: Truyền thêm HOTEN và SDT từ DB vào Model
        return new DiaChi(
            $nd, 
            $rs['DIACHI'], 
            $rs['HOTEN'] ?? null, 
            $rs['SODIENTHOAI'] ?? null
        );
    }

    public function getAll() {
        $list = [];
        $rs = database_connection::executeQuery("SELECT * FROM diachi");
        while($row = $rs->fetch_assoc()) {
            $model = $this->createDiaChiModel($row);
            array_push($list, $model);
        }
        return $list;
    }
    public function getByIdND($idND) {
        $list = [];
        $query = "SELECT * FROM diachi WHERE IDND = ?";
        $rs = database_connection::executeQuery($query, $idND);
        while($row = $rs->fetch_assoc()) {
            $model = $this->createDiaChiModel($row);
            array_push($list, $model);
        }
        return $list;
    }

    public function insert($model) {
        
        $query = "INSERT INTO `diachi`(`IDND`, `DIACHI`, `HOTEN`, `SODIENTHOAI`) VALUES (?,?,?,?)";
        $args = [
            $model->getIdND()->getId(),
            $model->getDiaChi(),
            $model->getHoTen(),
            $model->getSoDienThoai()
        ];
        return database_connection::executeQuery($query, ...$args);
    }
}
?>