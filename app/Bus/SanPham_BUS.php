<?php
namespace App\Bus;
use App\Interface\BUSInterface;
use Illuminate\Support\Facades\Validator;
use App\Dao\SanPham_DAO;

class SanPham_BUS implements BUSInterface {
    private $listSanPham = array();
    private $sanPhamDAO;
    public function __construct(SanPham_DAO $sanPhamDAO)
    {
        $this->sanPhamDAO = $sanPhamDAO;
        $this->refreshData();
    }

    public function getAllModels() {
        return $this->listSanPham;
    }

    public function getAllModelsActive() {
        return $this->sanPhamDAO->getAllModelsActive();
    }

    public function refreshData(): void {
       $this->listSanPham = $this->sanPhamDAO->getAll();
    }

    public function getModelById($id) {
        return $this->sanPhamDAO->getById($id);
    }

    public function addModel($model) {
        
        // Validate dữ liệu
        // $validator = Validator::make($model->toArray(), [
        //     'tenSanPham' => 'required|string|max:255',
        //     'idHang' => 'required|integer|exists:hangs,id',
        //     'idLSP' => 'required|integer|exists:loai_san_phams,id',
        //     'moTa' => 'nullable|string',
        //     'donGia' => 'required|numeric|min:0',
        //     'thoiGianBaoHanh' => 'nullable|string|max:50',
        // ]);
         
        // Thêm sản phẩm vào cơ sở dữ liệu và lấy ID mới
        $sanPhamId = $this->sanPhamDAO->insert($model);
        return $sanPhamId;
    }

    public function updateModel($model): int {
        return $this->sanPhamDAO->update($model);
    }

    public function deleteModel(int $id): int {
        return $this->sanPhamDAO->delete($id);
    }
    public function controlActive($id) {
        $sp = $this->getModelById($id);
        if($sp->getTrangThaiHD() == 1) {
            return $this->sanPhamDAO->controlActive($id,0);
        } else {
            return $this->sanPhamDAO->controlActive($id,1);
        }
    }

    public function searchModel(string $value, array $columns): array {
        return $this->sanPhamDAO->search($value, $columns);
    }
    public function searchByLoaiSanPham($idLSP) {
        return $this->sanPhamDAO->searchByLoaiSanPham($idLSP);
    }
    public function searchByHang($idHang) {
        return $this->sanPhamDAO->searchByHang($idHang);
    }
    public function searchByKhoangGia($startPrice, $endPrice) {
        return $this->sanPhamDAO->searchByKhoangGia($startPrice, $endPrice);
    }
    public function searchByLSPAndHang($lsp,$hang) {
        return $this->sanPhamDAO->searchByLSPAndHang($lsp,$hang);
    }
    public function searchByKhoangGiaAndLSPAndModel($keyword,$idlsp,$startprice,$endprice) {
        return $this->sanPhamDAO->searchByKhoangGiaAndLSPAndModel($keyword,$idlsp,$startprice,$endprice);
    }
    public function searchByLSPAndModel($keyword,$idlsp) {
        return $this->sanPhamDAO->searchByLSPAndModel($keyword,$idlsp);
    }
    public function searchByKhoangGiaAndLSP($idlsp,$startprice,$endprice) {
        return $this->sanPhamDAO->searchByKhoangGiaAndLSP($idlsp,$startprice,$endprice);
    }
    public function searchByKhoangGiaAndModel($keyword,$startprice,$endprice) {
        return $this->sanPhamDAO->searchByKhoangGiaAndModel($keyword,$startprice,$endprice);
    }
    public function getTop4ProductWasHigestSale() {
    return $this->sanPhamDAO->getTop4ProductWasHigestSale();
    }
    public function getStock($idPd) {
        return $this->sanPhamDAO->getStock($idPd);
    }
   public function searchByCriteria($idHang = null, $idLSP = null, $idKieuDang = null, $startPrice = null, $endPrice = null, $keyword = null)
{
    return $this->sanPhamDAO->searchByCriteria($idHang, $idLSP, $idKieuDang, $startPrice, $endPrice, $keyword);
}
    public function updateStockAndPrice($idSP, $soLuongThem, $giaBanMoi) {
    // Gọi xuống lớp DAO để thực thi câu lệnh SQL
    return $this->sanPhamDAO->updateStockAndPrice($idSP, $soLuongThem, $giaBanMoi);
}
}