<?php
namespace App\Bus;

use App\Dao\NguoiDung_DAO;
use App\Interface\BUSInterface;
use App\Models\NguoiDung;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class NguoiDung_BUS{
    private $nguoiDungList = array();
    private $nguoiDungDAO;
    public function __construct(NguoiDung_DAO $nguoi_dung_dao)
    {
        $this->nguoiDungDAO = $nguoi_dung_dao;
        $this->refreshData();
    }
    public function refreshData(): void
    {
        $this->nguoiDungList = $this->nguoiDungDAO->getAll();
    }
    public function getAllModels()
    {
        return $this->nguoiDungList;
    }
    public function getModelById($id)
    {
        return $this->nguoiDungDAO->getById($id);
    }
    public function addModel($model)
    {
        // if($model == null) {
        //     error_log("Error when insert a NguoiDung");
        //     return;
        // }
        // return $this->nguoiDungDAO->insert($model);
        if($model == null) {
            Log::error("Error: Model is null when trying to insert.");
            return;
        }
        $result = $this->nguoiDungDAO->insert($model);
        if (!$result) {
            Log::error("Error inserting model: " . json_encode($model));
        }
        return $result;
    }
    public function updateModel($model)
    {
        if($model == null) {
            error_log("Error when update a NguoiDung");
            return;
        }
        return $this->nguoiDungDAO->update($model);
    }
    public function controlDeleteModel($id, $active)
    {
        if($id == null || $id == "") {
            error_log("Error when delete a NguoiDung");
            return;
        }
        return $this->nguoiDungDAO->controlDelete($id, $active);
    }
    public function searchModel(string $value, array $columns)
    {
        return $this->nguoiDungDAO->search($value, $columns);
    }
    public function searchByTinh($idTinh) {
        return $this->nguoiDungDAO->searchByTinh($idTinh);
    }
    public function checkExistingUser($sdt) {
        return DB::table('nguoidung')
            ->where('SODIENTHOAI', $sdt)
            ->exists();
    }
    public function getModelBySDT($sdt) {
        return $this->nguoiDungDAO->getBySDT($sdt);
    }

    public function getNguoiDungBySoseri($soSeri) {
        return $this->nguoiDungDAO->getNguoiDungBySoseri($soSeri);
    }
    
    public function getNguoiDungByEmail($email) {
        return $this->nguoiDungDAO->getNguoiDungByEmail($email);
    }
    
}
?>