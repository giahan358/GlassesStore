<?php
namespace App\Dao;

use App\Bus\SanPham_BUS;
use App\Interface\DAOInterface;
use App\Models\CTSP;
use App\Services\database_connection;

class CTSP_DAO{
    public function insert($e): int
    {
        $sql = "INSERT INTO ctsp (idSP, soSeri, TRANGTHAIHD) 
        VALUES (?, ?, ?)";
        $args = [$e->getIdSP()->getId(), $e->getSoSeri(), 1];
        return database_connection::executeQuery($sql, ...$args);
    }
    public function getSPBySoSeri($soseri) {
        $query = "SELECT * FROM ctsp WHERE SOSERI = ?";
        $result = database_connection::executeQuery($query, $soseri);
        if($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            if($row) {
                return app(SanPham_BUS::class)->getModelById($row['IDSP']);
            }
        }
        return null;
    }
    // public function createModelCTSP($model) {
    //     return new CTSP();
    // }
    public function getCTSPBySoSeri($soseri) {
        $query = "SELECT * FROM ctsp WHERE SOSERI = ?";
        $result = database_connection::executeQuery($query, $soseri);
        if($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            if($row) {
                return $this->createCTSPModel($row);
            }
        }
        return null;
    }
    public function getCTSPByIDSP($idsp) {
        $list = [];
        $query = "SELECT * FROM ctsp WHERE IDSP = ?";
        $rs = database_connection::executeQuery($query, $idsp);
        while ($row = $rs->fetch_assoc()) {
            $model = $row['SOSERI'];
            array_push($list, $model);
        }
        return $list;
    }
    // public function getCTSPBySoSeri($soseri) {

    // }
    public function getSeriOfCTSPNotSale($idsp) {
        $list = [];
        $query = "SELECT * FROM ctsp WHERE IDSP = ? AND TRANGTHAIHD = 1";
        $rs = database_connection::executeQuery($query, $idsp);
        while ($row = $rs->fetch_assoc()) {
            $model = $row['SOSERI'];
            array_push($list, $model);
        }
        return $list[0];
    }
    public function checkCTSPIsSold($soseri) {
        $query = "SELECT * FROM ctsp WHERE SOSERI = ?";
        $result = database_connection::executeQuery($query, $soseri);
        if($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            if($row) {
                return $row['TRANGTHAIHD'];
            }
        }
        return null;
    }
    public function createCTSPModel($rs) {
        $idsp = app(SanPham_BUS::class)->getModelById($rs['IDSP']); 
        return new CTSP($idsp, $rs['SOSERI'],$rs['TRANGTHAIHD']);
    }
    public function getCTSPIsNotSoldByIDSP($idsp) {
        $list = [];
        $rs = database_connection::executeQuery("SELECT * FROM ctsp WHERE IDSP = ? AND TRANGTHAIHD = 1", $idsp);
        while ($row = $rs->fetch_assoc()) {
            $model = $this->createCTSPModel($row);
            array_push($list, $model);
        }
        return $list;
    }
    public function updateStatus($soseri, $active) {
        $query = 'UPDATE ctsp SET TRANGTHAIHD = ? WHERE SOSERI = ?';
        $args = [$active, $soseri];
        $rs = database_connection::executeUpdate($query, ...$args);
        return is_int($rs) ? $rs : 0;  
    }
}