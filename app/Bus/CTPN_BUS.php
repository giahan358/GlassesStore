<?php

namespace App\Bus;

use App\Dao\CTPN_DAO;
use App\Dao\CTSP_DAO;
use App\Dao\SanPham_DAO;
use App\Models\CTPN;
use App\Models\CTSP;
use App\Models\SanPham;
use App\Services\database_connection;
use function Laravel\Prompts\error;

class CTPN_BUS
{
    private $ctpnDAO;
    private $ctspDAO;
    private $sanPhamDAO;

    public function __construct(CTPN_DAO $ctpnDAO, CTSP_DAO $ctspDAO, SanPham_DAO $sanPhamDAO)
    {
        $this->ctpnDAO = $ctpnDAO;
        $this->ctspDAO = $ctspDAO;
        $this->sanPhamDAO = $sanPhamDAO;
    }

    private function generateUniqueSerial($idSP): string {
        $prefix = str_pad($idSP, 3, '0', STR_PAD_LEFT);
        $random = strtoupper(substr(uniqid(), -6));
        return $prefix . $random;
    }

    private function getHighestPurchasePrice($idSP): float {
        $sql = "SELECT MAX(giaNhap) as maxGiaNhap FROM ctpn WHERE idSP = ?";
        $rs = database_connection::executeQuery($sql, $idSP);
        if ($row = $rs->fetch_assoc()) {
            return (float)$row['maxGiaNhap'];
        }
        return 0;
    }

    private function calculateSellingPrice($giaNhap): float {
        // Sử dụng giá nhập cao nhất và lợi nhuận 15%
        return $giaNhap * 1.15;
    }

    private function updateProductPrice($idSP, $giaBanMoi) {
        $sanPham = $this->sanPhamDAO->getById($idSP);
        if ($sanPham) {
            $sanPham->setDonGia($giaBanMoi);
            $this->sanPhamDAO->update($sanPham);
        }
    }

    // public function addModel($model): int {
    //     try {
    //         // Thêm CTPN
    //         $result = $this->ctpnDAO->insert($model);
    //         if ($result <= 0) {
    //             return -1;
    //         }

    //         // Tạo CTSP cho mỗi sản phẩm
    //         $idSP = $model->getIdSP();
    //         $soLuong = $model->getSoLuong();
    //         $usedSerials = [];

    //         for ($i = 0; $i < $soLuong; $i++) {
    //             do {
    //                 $soSeri = $this->generateUniqueSerial($idSP);
    //             } while (in_array($soSeri, $usedSerials));

    //             $usedSerials[] = $soSeri;
    //             $ctsp = new CTSP($idSP, $soSeri);
    //             $this->ctspDAO->insert($ctsp);
    //         }

    //         // Tính toán và cập nhật giá bán
    //         $giaNhapCaoNhat = $this->getHighestPurchasePrice($idSP);
    //         $giaBanMoi = $this->calculateSellingPrice($giaNhapCaoNhat);
    //         $this->updateProductPrice($idSP, $giaBanMoi);

    //         return $result;
    //     } catch (\Exception $e) {
    //         error_log("Error adding purchase order detail: " . $e->getMessage());
    //         return -1;
    //     }
    // }
    public function addModel($model) {
        return $this->ctpnDAO->insert($model);
    }

    public function updateModel($model): int {
        try {
            return $this->ctpnDAO->update($model);
        } catch (\Exception $e) {
            error_log("Error updating purchase order detail: " . $e->getMessage());
            return -1;
        }
    }

    public function getAllModels() {
        try {
            return $this->ctpnDAO->readDatabase();
        } catch (\Exception $e) {
            error_log("Error getting purchase order details: " . $e->getMessage());
            return [];
        }
    }

    public function refreshData(): void {
        try {
            $this->ctpnDAO->getAll();
        } catch (\Exception $e) {
            error_log("Error refreshing purchase order details: " . $e->getMessage());
        }
    }

    /**
     * Get all purchase order details
     * @return array
     */
    public function getAll()
    {
        return $this->ctpnDAO->getAll();
    }

    /**
     * Get purchase order details by ID
     * @param int $idPN
     * @param int $idSP
     * @return CTPN|null
     */
    public function getById($idPN, $idSP)
    {
        try {
            return $this->ctpnDAO->getById($idPN, $idSP);
        } catch (\Exception $e) {
            error_log("Error getting purchase order detail: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Get purchase order details by purchase order ID
     * @param int $idPN
     * @return array
     */
    public function getByPhieuNhapId($idPN)
    {
        return $this->ctpnDAO->getByPhieuNhapId($idPN);
    }

    /**
     * Create new purchase order detail
     * @param array $data
     * @return bool
     */
    public function create($data)
    {
        return $this->ctpnDAO->insert($data);
    }

    /**
     * Update purchase order detail
     * @param int $idPN
     * @param int $idSP
     * @param array $data
     * @return bool
     */
    public function update($idPN, $idSP, $data)
    {
        return $this->ctpnDAO->update($data);
    }

    /**
     * Delete purchase order detail
     * @param int $idPN
     * @param int $idSP
     * @return int
     */
    public function delete($idPN, $idSP)
    {
        try {
            $id = $idPN . '_' . $idSP;
            return $this->ctpnDAO->delete($id);
        } catch (\Exception $e) {
            error_log("Error deleting purchase order detail: " . $e->getMessage());
            return -1;
        }
    }

    /**
     * Delete all purchase order details by purchase order ID
     * @param int $idPN
     * @return int
     */
    public function deleteByPhieuNhapId($idPN)
    {
        try {
            return $this->ctpnDAO->deleteByPhieuNhapId($idPN);
        } catch (\Exception $e) {
            error_log("Error deleting purchase order details: " . $e->getMessage());
            return -1;
        }
    }

    /**
     * Calculate total amount for a purchase order
     * @param int $idPN
     * @return float
     */
    public function calculateTotalAmount($idPN)
    {
        try {
            $details = $this->getByPhieuNhapId($idPN);
            $total = 0;

            foreach ($details as $detail) {
                $total += $detail->getSoLuong() * $detail->getGiaNhap();
            }

            return $total;
        } catch (\Exception $e) {
            error_log("Error calculating total amount: " . $e->getMessage());
            return 0;
        }
    }
    public function taoCTSPTuDong($idsp, $soLuong = 1) {
        return $this->ctpnDAO->taoCTSPTuDong($idsp, $soLuong = 1);
    }

    public function getGiaBanCaoNhatByIDSP(int $idSP): ?float {
        return $this->ctpnDAO->getGiaBanCaoNhatByIDSP($idSP);
    }
}