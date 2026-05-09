<?php

namespace App\Dao;

use App\Bus\NCC_BUS;
use App\Bus\NguoiDung_BUS;
use App\Enum\ReceiptStatus;
use App\Interface\DAOInterface;
use App\Models\NCC;
use App\Models\PhieuNhap;
use App\Services\database_connection;
use Illuminate\Support\Arr;
use InvalidArgumentException;

use function Laravel\Prompts\error;

class PhieuNhap_DAO 
{
    public function readDatabase(): array
    {
        $list = [];
        $rs = database_connection::executeQuery("SELECT * FROM phieunhap");
        while ($row = $rs->fetch_assoc()) {
            $model = $this->createPhieuNhapModel($row);
            $list[] = $model;
        }
        return $list;
    }
    public function createPhieuNhapModel($rs): PhieuNhap
    {        
        $id = $rs['ID'];
        $idNCC = app(NCC_BUS::class)->getModelById($rs['IDNCC']);
        $tongTien = $rs['TONGTIEN'];
        $ngayTao = $rs['NGAYTAO'];
        $idNhanVien = app(NguoiDung_BUS::class)->getModelById($rs['IDNHANVIEN']);
        $trangThaiHD = $rs['TRANGTHAIHD'];
        return new PhieuNhap($id, $idNCC, $tongTien, $ngayTao, $idNhanVien, $trangThaiHD);
    }
    public function getAll(): array
    {
        $list = [];
        $rs = database_connection::executeQuery("SELECT * FROM phieunhap");
        while ($row = $rs->fetch_assoc()) {
            $model = $this->createPhieuNhapModel($row);
            array_push($list, $model);
        }
        return $list;
    }
    public function getById($id)
    {
        $query = "SELECT * FROM phieunhap WHERE ID = ?";
        $result = database_connection::executeQuery($query, $id);
        if ($result->num_rows > 0) {
            return $this->createPhieuNhapModel($result->fetch_assoc());
        }
        return null;
    }
    public function insert($e)
    {
        $query = "INSERT INTO phieunhap (id, idNCC, tongTien, ngayTao, idNhanVien, trangThaiHD) VALUES (?,?,?,?,?,?)";
        $args = [$e->getId(), $e->getIdNCC()->getIdNCC(), $e->getTongTien(), $e->getNgayTao(), $e->getIdNhanVien()->getId(), $e->getTrangThaiPN()];
        $rs = database_connection::executeQuery($query, ...$args);
        return is_int($rs) ? $rs : 0;

    }
    public function getLastPN() {
        $query = "SELECT * FROM phieunhap ORDER BY id DESC LIMIT 1";
        $result = database_connection::executeQuery($query);
        if ($result->num_rows > 0) {
            return $this->createPhieuNhapModel($result->fetch_assoc());
        }
        return null;
    }
    public function update($e): int
    {
        $query = "UPDATE phieunhap SET idNCC = ?, tongTien = ?, ngayTao = ?, idNhanVien = ?, trangThaiHD = ? WHERE id = ?";
        $args = [$e->getIdNCC()->getIdNCC(), $e->getTongTien(), $e->getNgayTao(), $e->getIdNhanVien()->getId(), $e->getTrangThaiPN(), $e->getId()];
        $rs = database_connection::executeUpdate($query, ...$args);
        return is_int($rs) ? $rs : 0;
    }
    public function delete(int $id): int
    {
        $query = "DELETE FROM phieunhap WHERE id = ?";
        $rs = database_connection::executeUpdate($query, $id);
        return is_int($rs) ? $rs : 0;
    }
    public function exists(int $id): bool
    {
        $query = "SELECT COUNT(*) as count FROM phieunhap WHERE id = ?";
        $rs = database_connection::executeQuery($query, $id);
        $row = $rs->fetch_assoc();
        return $row['count'] > 0;
    }

    public function search(string $condition, array $columnNames = []): array
    {
        if (empty($condition)) {
            throw new InvalidArgumentException("Search condition cannot be empty or null");
        }

        // Danh sách cột mặc định nếu không truyền vào
        $columns = empty($columnNames)
            ? ["ID", "IDNCC", "TONGTIEN", "IDNHANVIEN", "NGAYTAO", "TRANGTHAIHD"]
            : $columnNames;

        // Xây dựng câu lệnh SQL với các cột được chỉ định
        $query = "SELECT * FROM phieunhap WHERE " . implode(" LIKE ? OR ", $columns) . " LIKE ?";

        // Mảng chứa các tham số tìm kiếm
        $args = array_fill(0, count($columns), "%" . $condition . "%");

        $rs = database_connection::executeQuery($query, ...$args);
        $list = [];

        while ($row = $rs->fetch_assoc()) {
            $list[] = $this->createPhieuNhapModel($row);
        }

        return $list;
    }
}
