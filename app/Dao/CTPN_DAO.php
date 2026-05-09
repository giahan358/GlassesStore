<?php

namespace App\Dao;

use App\Bus\CTSP_BUS;
use App\Bus\PhieuNhap_BUS;
use App\Bus\SanPham_BUS;
use App\Interface\DAOInterface;
use App\Models\CTPN;
use App\Models\CTSP;
use App\Services\database_connection;

class CTPN_DAO implements DAOInterface {
    public function readDatabase(): array
    {
        $list = [];
        $rs = database_connection::executeQuery("SELECT * FROM ctpn");
        while ($row = $rs->fetch_assoc()) {
            $model = $this->createCTPNModel($row);
            array_push($list, $model);
        }
        return $list;
    }

    public function createCTPNModel($rs): CTPN {
        $idPN = app(PhieuNhap_BUS::class)->getModelById($rs['IDPN']);
        $idSP = app(SanPham_BUS::class)->getModelById($rs['IDSP']);
        $soLuong = $rs['SOLUONG'];
        $giaNhap = $rs['GIANHAP'];
        $phanTramLN = $rs['PHANTRAMLN'];
        $trangThaiPN = $rs['TRANGTHAIHD'];

        return new CTPN($idPN, $idSP, $soLuong, $giaNhap,$phanTramLN, $trangThaiPN);
    }

    public function getAll(): array {
        return $this->readDatabase();
    }

    public function getById($idPN, $idSP): ?CTPN {
        $sql = "SELECT * FROM ctpn WHERE idPN = ? AND idSP = ?";
        $rs = database_connection::executeQuery($sql, $idPN, $idSP);
        if ($row = $rs->fetch_assoc()) {
            return $this->createCTPNModel($row);
        }
        return null;
    }

    public function getByPhieuNhapId($idPN) {
        $list = [];
        $sql = "SELECT * FROM ctpn WHERE idPN = ?";
        $rs = database_connection::executeQuery($sql, $idPN);
        while ($row = $rs->fetch_assoc()) {
            $model = $this->createCTPNModel($row);
            array_push($list, $model);
        }
        return $list;
    }

    public function taoCTSPTuDong($idsp, $soLuong = 1) {
        $idspFormatted = str_pad($idsp, 3, '0', STR_PAD_LEFT); // 3 chữ số
        
        // Đếm số CTSP đã tồn tại với IDSP này
        $dsCTSP = app(CTSP_BUS::class)->getCTSPByIDSP($idsp); // Hàm này bạn phải có
        $soLuongHienTai = count($dsCTSP);
    
        $dsCTSPMoi = [];
    
        for ($i = 1; $i <= $soLuong; $i++) {
            $stt = $soLuongHienTai + $i;
            $sttFormatted = str_pad($stt, 5, '0', STR_PAD_LEFT); // 5 chữ số
    
            $soseri = $idspFormatted . $sttFormatted;
            $idsanpham = app(SanPham_BUS::class)->getModelById($idsp);
            // Tạo model CTSP mới (giả sử bạn có class CTSP)
            $ctsp = new CTSP($idsanpham, $soseri,1);
            // $ctsp->setSoSeri($soseri);
            // $ctsp->setIdSP($idsp);
    
            // Lưu vào database
            app(CTSP_BUS::class)->addModel($ctsp);
    
            // Lưu lại danh sách để kiểm tra
            $dsCTSPMoi[] = $ctsp;
        }
    
        return $dsCTSPMoi;
    }
    
    public function insert($e): int {
        $sql = "INSERT INTO ctpn (idPN, idSP, soLuong, giaNhap, phanTramLN, TRANGTHAIHD) 
        VALUES (?, ?, ?, ?, ?, 1)";
        $args = [
            $e->getIdPN()->getId(), 
            $e->getIdSP()->getId(), 
            $e->getSoLuong(), 
            $e->getGiaNhap(), 
            $e->getPhanTramLN()
        ];
        $rs = database_connection::executeQuery($sql, ...$args);
        $this->taoCTSPTuDong($e->getIdSP()->getId(), $e->getSoLuong());
        $sp = app(SanPham_BUS::class)->getModelById($e->getIdSP()->getId());
        $total = $sp->getSoLuong() + $e->getSoLuong();
        $sp->setSoLuong($total);
        app(SanPham_BUS::class)->updateModel($sp);
        return $rs;
    }

    public function update($e): int
{
    // 1. Loại bỏ thoiGianBaoHanh ra khỏi câu lệnh SET
    // Lưu ý: Đảm bảo tên cột khớp với Database (TENSANPHAM, IDHANG... hoặc tenSanPham, idHang...)
    $sql = "UPDATE sanpham SET 
                tenSanPham = ?, 
                idHang = ?, 
                idLSP = ?, 
                idKieuDang = ?, 
                moTa = ?, 
                donGia = ?, 
                soLuong = ?, 
                trangThaiHD = ?
            WHERE id = ?";

    // 2. Loại bỏ $e->getThoiGianBaoHanh() ra khỏi mảng $args
    $args = [
        $e->getTenSanPham(), 
        $e->getIdHang()->getId(), 
        $e->getIdLSP()->getId(), 
        $e->getIdKieuDang()->getId(), 
        $e->getMoTa(), 
        $e->getDonGia(), 
        $e->getSoLuong(), 
        $e->getTrangThaiHD(), 
        $e->getId()
    ];

    $result = database_connection::executeUpdate($sql, ...$args);
    
    if ($result) {
        return $e->getId();
    }

    return 0;
}

    public function delete(int $id): int {
        // For CTPN, we need both idPN and idSP, so we'll split the id
        $ids = explode('_', $id);
        if (count($ids) !== 2) {
            return 0;
        }
        $idPN = $ids[0];
        $idSP = $ids[1];
        $sql = "DELETE FROM ctpn WHERE idPN = ? AND idSP = ?";
        return database_connection::executeUpdate($sql, $idPN, $idSP);
    }

    public function deleteByPhieuNhapId($idPN): int {
        $sql = "DELETE FROM ctpn WHERE idPN = ?";
        return database_connection::executeUpdate($sql, $idPN);
    }

    public function search(string $condition, array $columnNames): array {
        $sql = "SELECT * FROM ctpn WHERE ";
        $whereClauses = [];
        foreach ($columnNames as $column) {
            $whereClauses[] = "$column LIKE ?";
        }
        $sql .= implode(" AND ", $whereClauses);
        
        $params = array_fill(0, count($columnNames), "%$condition%");
        $rs = database_connection::executeQuery($sql, ...$params);
        
        $list = [];
        while ($row = $rs->fetch_assoc()) {
            $model = $this->createCTPNModel($row);
            array_push($list, $model);
        }
        return $list;
    }

    public function getGiaBanCaoNhatByIDSP(int $idSP): ?float {
        $sql = "SELECT GIANHAP, PHANTRAMLN 
                FROM ctpn 
                WHERE IDSP = ? 
                ORDER BY PHANTRAMLN DESC 
                LIMIT 1";
        
        $rs = database_connection::executeQuery($sql, $idSP);
        if ($row = $rs->fetch_assoc()) {
            $giaNhap = (float)$row['GIANHAP'];
            $phanTramLN = (float)$row['PHANTRAMLN'];
            $giaBan = $giaNhap + ($giaNhap * $phanTramLN/100);
            return $giaBan;
        }
    
        return null;
    }
    

    
}