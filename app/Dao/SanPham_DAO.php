<?php
namespace App\Dao;

use App\Bus\Hang_BUS;
use App\Bus\KieuDang_BUS;
use App\Bus\LoaiSanPham_BUS;
use App\Interface\DAOInterface;
use App\Models\Hang;
use App\Models\SanPham;
use App\Services\database_connection;
use InvalidArgumentException;

class SanPham_DAO implements DAOInterface{

    public function readDatabase(): array
    {
        // $list = [];
        // $rs = database_connection::executeQuery("SELECT * FROM SanPham");
        // while ($row = $rs->fetch_assoc()) {
        //     $model = $this->createSanPhamModel($row);
        //     array_push($list, $model);
        // }
        // return $list;
        return [];
    }

    public function getById($id) {
        $query = "SELECT * FROM sanpham WHERE id = ?";
        $result = database_connection::executeQuery($query, $id);
        if($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            if($row) {
                return $this->createSanPhamModel($row);
            }
        }
        return null;
    }

    // public function insert($e): int
    // {
    //     $sql = "INSERT INTO `sanpham`(`TENSANPHAM`, `IDHANG`, `IDLSP`, `IDKIEUDANG`, `MOTA`, `DONGIA`, `THOIGIANBAOHANH`, `TRANGTHAIHD`, `soLuong`)
    //     VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
    //     $args = [$e->getTenSanPham(), $e->getIdHang()->getId(), $e->getIdLSP()->getId(), $e->getIdKieuDang()->getId(), $e->getMoTa(), $e->getDonGia(), $e->getThoiGianBaoHanh(), $e->getTrangThaiHD(), $e->getSoLuong()];
    //     $result = database_connection::executeUpdate($sql, ...$args);
    //     // Lấy ID của sản phẩm vừa được chèn vào
    //     if ($result) {
    //         return database_connection::getLastInsertId();
    //     }

    //     return 0;
    // }
    public function insert($e): int
    {
        $sql = "INSERT INTO `sanpham` (`TENSANPHAM`, `IDHANG`, `IDLSP`, `IDKIEUDANG`, `MOTA`, `DONGIA`, `THOIGIANBAOHANH`, `TRANGTHAIHD`, `soLuong`) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
        
        $args = [
            $e->getTenSanPham(),
            $e->getIdHang()->getId(),
            $e->getIdLSP()->getId(),
            $e->getIdKieuDang()->getId(),
            $e->getMoTa(),
            $e->getDonGia(),
            $e->getThoiGianBaoHanh(),
            $e->getTrangThaiHD(),
            $e->getSoLuong()
        ];

        $result = database_connection::executeUpdate($sql, ...$args);

        if ($result) {
            return database_connection::getLastInsertId();
        }

        return 0;
    }


    public function update($e): int
    {
        $sql = "UPDATE sanpham SET tenSanPham = ?, idHang = ?, idLSP = ?, idKieuDang = ?, moTa = ?, donGia = ?, thoiGianBaoHanh = ?, soLuong = ?, trangThaiHD = ?
        WHERE id = ?";
        $args = [$e->getTenSanPham(), $e->getIdHang()->getId(), $e->getIdLSP()->getId(), $e->getIdKieuDang()->getId(), $e->getMoTa(), $e->getDonGia(), $e->getThoiGianBaoHanh(), $e->getSoLuong(), $e->getTrangThaiHD(), $e->getId()];
        $result = database_connection::executeUpdate($sql, ...$args);
        if ($result) {
            return $e->getId();
        }

        return 0;
    }

    public function delete(int $id): int
    {
        $sql = "DELETE FROM `sanpham` WHERE ID = ?";
        $result = database_connection::executeUpdate($sql, ...[$id]);
        return is_int($result)? $result : 0;
    }
    public function controlActive($id,$active) {
        $sql = "UPDATE sanpham SET trangThaiHD = ? WHERE id = ?";
        $args = [$active, $id];
        $result = database_connection::executeUpdate($sql, ...$args);
        return is_int($result)? $result : 0;
    }

    // public function search(string $condition, array $columnNames): array
    // {
    //     // $column = $columnNames[0];
    //     // $query = "SELECT * FROM SanPham WHERE $column LIKE ?";
    //     // $args = ["%" . $condition . "%"];
    //     // $rs = database_connection::executeQuery($query, ...$args);
    //     // $cartsList = [];
    //     // while ($row = $rs->fetch_assoc()) {
    //     //     $cartsModel = $this->createSanPhamModel($row);
    //     //     array_push($cartsList, $cartsModel);
    //     // }
    //     // if (count($cartsList) === 0) {
    //     //     return [];
    //     // }
    //     // return $cartsList;
    //     if (empty($condition)) {
    //         throw new InvalidArgumentException("Search condition cannot be empty or null");
    //     }
    //     // $query = "";
    //     $query = "
    //     SELECT *
    //         FROM sanpham
    //         JOIN hang ON hang.ID = sanpham.IDHANG
    //         JOIN loaisanpham ON loaisanpham.ID = sanpham.IDLSP
    //         WHERE (
    //             sanpham.mota = ?
    //             sanpham.TENSANPHAM LIKE ?
    //             OR hang.TENHANG LIKE ?
    //             OR loaisanpham.TENLSP LIKE ?
    //         );
    //     ";
    //     $args = array_fill(0,  4, "%" . $condition . "%");
    //     $rs = database_connection::executeQuery($query, ...$args);
    //     $list = [];
    //     while ($row = $rs->fetch_assoc()) {
    //         $model = $this->createSanPhamModel($row);
    //         array_push($list, $model);
    //     }
    //     if (count($list) === 0) {
    //         return [];
    //     }
    //     return $list;
    // }

    public function search(string $condition, array $columnNames): array
{
    if (empty($condition)) {
        throw new InvalidArgumentException("Điều kiện tìm kiếm không được rỗng hoặc null");
    }

    $query = "
        SELECT sanpham.ID, sanpham.TENSANPHAM, sanpham.MOTA, sanpham.DONGIA, 
               sanpham.THOIGIANBAOHANH, sanpham.TRANGTHAIHD, sanpham.soLuong,
               sanpham.IDHANG, sanpham.IDLSP, sanpham.IDKIEUDANG,
               hang.TENHANG, loaisanpham.TENLSP, kieudang.TENKIEUDANG
        FROM sanpham
        JOIN hang ON hang.ID = sanpham.IDHANG
        JOIN loaisanpham ON loaisanpham.ID = sanpham.IDLSP
        LEFT JOIN kieudang ON kieudang.ID = sanpham.IDKIEUDANG
        WHERE sanpham.TENSANPHAM LIKE CONCAT('%', ?, '%')
    ";

    $rs = database_connection::executeQuery($query, $condition);
    $list = [];
    
    while ($row = $rs->fetch_assoc()) {
        if (!isset($row['ID'])) {
            error_log("Cảnh báo: Thiếu sanpham.ID trong kết quả truy vấn: " . json_encode($row));
            continue;
        }
        
        $model = $this->createSanPhamModel($row);
        array_push($list, $model);
    }
    
    return $list;
}
    public function searchByKhoangGia($startPrice, $endPrice) {
        $list = [];
        $query = "
        SELECT *
            FROM sanpham
            WHERE DONGIA BETWEEN ? AND ?;
        ";
        $args = [$startPrice, $endPrice];
        $rs = database_connection::executeQuery($query, ...$args);
        while($row = $rs->fetch_assoc()) {
            $model = $this->createSanPhamModel($row);
            array_push($list, $model);
        }
        return $list;
    }

    public function searchByLSPAndModel($keyword,$idlsp) {
        $list = [];
        $query = "
            SELECT *
                FROM sanpham
                JOIN hang ON hang.ID = sanpham.IDHANG
                JOIN loaisanpham ON loaisanpham.ID = sanpham.IDLSP
                WHERE (
                    (
                        sanpham.MOTA LIKE CONCAT('%', ?, '%')
                        OR sanpham.TENSANPHAM LIKE CONCAT('%', ?, '%')
                        OR hang.TENHANG LIKE CONCAT('%', ?, '%')
                        OR loaisanpham.TENLSP LIKE CONCAT('%', ?, '%')
                    )
                    AND sanpham.IDLSP = ?
                )
        ";
        $args = [$keyword, $keyword, $keyword, $keyword, $idlsp];
        $rs = database_connection::executeQuery($query, ...$args);
        while($row = $rs->fetch_assoc()) {
            $model = $this->createSanPhamModel($row);
            array_push($list, $model);
        }
        return $list;
    }

    public function searchByKhoangGiaAndLSP($idlsp,$startprice,$endprice) {
        $list = [];
        $query = "
            SELECT *
                FROM sanpham
                JOIN hang ON hang.ID = sanpham.IDHANG
                JOIN loaisanpham ON loaisanpham.ID = sanpham.IDLSP
                WHERE (
                    (DONGIA BETWEEN ? AND ?)
                    AND sanpham.IDLSP = ?
                )
        ";
        $args = [$startprice, $endprice, $idlsp];
        $rs = database_connection::executeQuery($query, ...$args);
        while($row = $rs->fetch_assoc()) {
            $model = $this->createSanPhamModel($row);
            array_push($list, $model);
        }
        return $list;
    }

    public function searchByKhoangGiaAndModel($keyword, $startprice, $endprice)
{
    $list = [];
    $query = "
        SELECT *
        FROM sanpham
        JOIN hang ON hang.ID = sanpham.IDHANG
        JOIN loaisanpham ON loaisanpham.ID = sanpham.IDLSP
        WHERE (
            (DONGIA BETWEEN ? AND ?)
            AND (
                sanpham.MOTA LIKE CONCAT('%', ?, '%')
                OR sanpham.TENSANPHAM LIKE CONCAT('%', ?, '%')
                OR hang.TENHANG LIKE CONCAT('%', ?, '%')
                OR loaisanpham.TENLSP LIKE CONCAT('%', ?, '%')
            )
        )
    ";
    $params = [(float)$startprice, (float)$endprice, $keyword, $keyword, $keyword, $keyword];
    $types = "ddssss"; // Xây dựng tĩnh hoặc động

    // Kiểm tra số placeholder
    $placeholderCount = substr_count($query, '?');
    if ($placeholderCount !== count($params) || $placeholderCount !== strlen($types)) {
        error_log("Error: Số placeholder ($placeholderCount) không khớp với params (" . count($params) . ") hoặc types (" . strlen($types) . ")");
        return $list;
    }

    // Log để debug
    error_log("Query: $query, Types: $types, Params: " . json_encode($params));

    // Gọi executeQuery chỉ với $query và $params
    $rs = database_connection::executeQuery($query, ...$params);

    while ($row = $rs->fetch_assoc()) {
        $model = $this->createSanPhamModel($row);
        array_push($list, $model);
    }

    return $list;
}
    public function searchByKhoangGiaAndLSPAndModel($keyword,$idlsp,$startprice,$endprice) {
        $list = [];
        $query = "
            SELECT *
                FROM sanpham
                JOIN hang ON hang.ID = sanpham.IDHANG
                JOIN loaisanpham ON loaisanpham.ID = sanpham.IDLSP
                WHERE (
                    (DONGIA BETWEEN ? AND ?)
                    AND (
                        sanpham.MOTA LIKE CONCAT('%', ?, '%')
                        OR sanpham.TENSANPHAM LIKE CONCAT('%', ?, '%')
                        OR hang.TENHANG LIKE CONCAT('%', ?, '%')
                        OR loaisanpham.TENLSP LIKE CONCAT('%', ?, '%')
                    )
                    AND sanpham.IDLSP = ?
                )
        ";
        $args = [$startprice, $endprice, $keyword, $keyword, $keyword, $keyword, $idlsp];
        $rs = database_connection::executeQuery($query, ...$args);
        while($row = $rs->fetch_assoc()) {
            $model = $this->createSanPhamModel($row);
            array_push($list, $model);
        }
        return $list;
    }


    public function createSanPhamModel($rs) {
        $id = $rs['ID'];
        $tenSanPham = $rs['TENSANPHAM'];
        $idHang = app(Hang_BUS::class)->getModelById($rs['IDHANG']);
        $idLSP = app(LoaiSanPham_BUS::class)->getModelById($rs['IDLSP']);
        $idKieuDang = app(KieuDang_BUS::class)->getModelById($rs['IDKIEUDANG']);
        $moTa = $rs['MOTA'];
        $donGia = $rs['DONGIA'];
        $thoiGianBaoHanh = $rs['THOIGIANBAOHANH'];
        $soLuong = $rs['soLuong'];
        $trangThaiHD = $rs['TRANGTHAIHD'];
        return new SanPham($id, $tenSanPham, $idHang, $idLSP, $idKieuDang, $moTa, $donGia, $thoiGianBaoHanh, $soLuong, $trangThaiHD);
    }

    public function getAll() : array {
        $list = [];
        $rs = database_connection::executeQuery("SELECT * FROM sanpham");
        while($row = $rs->fetch_assoc()) {
            $model = $this->createSanPhamModel($row);
            array_push($list, $model);
        }
        return $list;
    }

    public function getAllModelsActive() : array {
        $list = [];
        $rs = database_connection::executeQuery("SELECT * FROM sanpham WHERE TRANGTHAIHD = 1");
        while($row = $rs->fetch_assoc()) {
            $model = $this->createSanPhamModel($row);
            array_push($list, $model);
        }
        return $list;

    }

    public function searchByLoaiSanPham($idLSP) {
        $list = [];
        $query = "SELECT * FROM sanpham WHERE IDLSP = ?";
        $rs = database_connection::executeQuery($query, $idLSP);
        while($row = $rs->fetch_assoc()) {
            $model = $this->createSanPhamModel($row);
            array_push($list, $model);
        }
        return $list;
    }

    public function searchByHang($idHang) {
        $list = [];
        $query = "SELECT * FROM sanpham WHERE IDHANG = ?";
        $rs = database_connection::executeQuery($query, $idHang);
        while($row = $rs->fetch_assoc()) {
            $model = $this->createSanPhamModel($row);
            array_push($list, $model);
        }
        return $list;
    }

    public function searchByLSPAndHang($lsp,$hang) {
        $list = [];
        $query = "SELECT * FROM sanpham WHERE IDLSP = ? AND IDHANG = ?";
        $rs = database_connection::executeQuery($query, $lsp, $hang);
        while($row = $rs->fetch_assoc()) {
            $model = $this->createSanPhamModel($row);
            array_push($list, $model);
        }
        return $list;
    }

    public function getTop4ProductWasHigestSale() {
    $list = [];
    // Truy vấn: cthd nối với ctsp qua SOSERI, sau đó ctsp nối với sanpham qua IDSP
    $query = "SELECT sp.*, COUNT(ctsp.IDSP) as total_sold 
              FROM cthd
              JOIN ctsp ON cthd.SOSERI = ctsp.SOSERI
              JOIN sanpham sp ON ctsp.IDSP = sp.ID
              JOIN hoadon hd ON cthd.IDHD = hd.ID
              WHERE hd.TRANGTHAI = 'DAGIAO' 
              GROUP BY sp.ID
              ORDER BY total_sold DESC
              LIMIT 4";

    $rs = database_connection::executeQuery($query);

    while ($row = $rs->fetch_assoc()) {
        $model = $this->createSanPhamModel($row);
        array_push($list, $model);
    }
    return $list;
}

    public function getStock($idPd) {
        $query = "SELECT sp.ID, COUNT(ctsp.SOSERI) AS SoLuongSoSeri
                    FROM sanpham sp
                    JOIN ctsp ON sp.ID = ctsp.IDSP
                    WHERE sp.ID = ? AND ctsp.trangThaiHD = 1
                    GROUP BY sp.ID";
        $result = database_connection::executeQuery($query, $idPd);
        if($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            if($row) {
                return $row["SoLuongSoSeri"];
            }
        }
        return null;
    }
    public function searchByCriteria($idHang = null, $idLSP = null, $idKieuDang = null, $startPrice = null, $endPrice = null, $keyword = null)
{
    $list = [];
    $query = "SELECT * FROM sanpham WHERE TRANGTHAIHD = 1";
    $params = [];
    $types = "";

    // Xây dựng câu truy vấn động và giữ $types
    if ($keyword !== null && $keyword !== '') {
        $query .= " AND TENSANPHAM LIKE ?";
        $params[] = '%' . $keyword . '%';
        $types .= "s";
    }
    if ($idHang !== null && $idHang != 0) {
        $query .= " AND IDHANG = ?";
        $params[] = $idHang;
        $types .= "i";
    }
    if ($idLSP !== null && $idLSP != 0) {
        $query .= " AND IDLSP = ?";
        $params[] = $idLSP;
        $types .= "i";
    }
    if ($idKieuDang !== null && $idKieuDang != 0) {
        $query .= " AND IDKIEUDANG = ?";
        $params[] = $idKieuDang;
        $types .= "i";
    }
    if ($startPrice !== null && $endPrice !== null) {
        $query .= " AND DONGIA >= ? AND DONGIA <= ?";
        // Ép kiểu $startPrice và $endPrice thành số thực
        $params[] = (float)$startPrice;
        $params[] = (float)$endPrice;
        $types .= "dd";
    }

    // Kiểm tra số placeholder khớp với $types và $params
    $placeholderCount = substr_count($query, '?');
    if ($placeholderCount !== strlen($types) || $placeholderCount !== count($params)) {
        error_log("Error: Số placeholder ($placeholderCount) không khớp với types (" . strlen($types) . ") hoặc params (" . count($params) . ")");
        return $list; // Trả về mảng rỗng nếu không khớp
    }

    // Log để debug
    error_log("Query: $query, Types: $types, Params: " . json_encode($params));

    // Gọi executeQuery chỉ với $query và $params
    $rs = database_connection::executeQuery($query, ...$params);

    while ($row = $rs->fetch_assoc()) {
        $model = $this->createSanPhamModel($row);
        array_push($list, $model);
    }

    return $list;
}
    public function updateStockAndPrice($idSP, $soLuongThem, $giaBanMoi) {
    // CHỈ cập nhật Đơn giá và Trạng thái, KHÔNG cộng dồn SOLUONG nữa
    // Vì việc cộng dồn đã do hàm taoCTSPTuDong xử lý rồi
    $query = "UPDATE sanpham SET DONGIA = ?, TRANGTHAIHD = 1 WHERE id = ?";
    return database_connection::executeQuery($query, $giaBanMoi, $idSP);
}
}