<?php
namespace App\Dao;

use App\Bus\GioHang_BUS;
use App\Bus\NguoiDung_BUS;
use App\Bus\Quyen_BUS;
use App\Interface\DAOInterface;
use App\Models\GioHang;
use App\Models\TaiKhoan;
use App\Services\database_connection;
use Exception;
use InvalidArgumentException;
use Psy\Readline\Hoa\Console;
use Symfony\Component\Mailer\Event\MessageEvent;

class TaiKhoan_DAO{
    protected $gioHangBus;

    // Inject GioHang_BUS thông qua constructor
    public function __construct(GioHang_BUS $gioHangBus)
    {
        $this->gioHangBus = $gioHangBus;
    }

    public function readDatabase(): array
    {
        $list = [];
        $rs = database_connection::executeQuery("SELECT * FROM taikhoan");
        while ($row = $rs->fetch_assoc()) {
            $model = $this->createTaiKhoanModel($row);
            array_push($list, $model);
        }
        return $list;
    }
    public function createTaiKhoanModel($rs) {
        $tentk = $rs['TENTK'];
        $email = $rs['EMAIL'];
        $password = $rs['PASSWORD'];
        $idNguoiDung = app(NguoiDung_BUS::class)->getModelById($rs['IDNGUOIDUNG']);
        $idQuyen = app(Quyen_BUS::class)->getModelById($rs['IDQUYEN']);
        $trangThaiHD = $rs['TRANGTHAIHD'];
        return new TaiKhoan($tentk, $email, $password, $idNguoiDung, $idQuyen, $trangThaiHD);
    }
    public function getAll() : array {
        $list = [];
        $rs = database_connection::executeQuery("SELECT * FROM taikhoan");
        while($row = $rs->fetch_assoc()) {
            $model = $this->createTaiKhoanModel($row);
            array_push($list, $model);
        }
        return $list;
    }
    public function getById($id) {
        $query = "SELECT * FROM taikhoan WHERE email = ?";
        $result = database_connection::executeQuery($query, $id);
        
        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            if ($row) {
                return $this->createTaiKhoanModel($row);
            }
        }
        return null;
    }
    
    public function insert($model) {
        try {
            $query = "INSERT INTO taikhoan (tentk, email, password, idNguoiDung, idQuyen, trangthaihd) VALUES (?,?,?,?,?,?)";
            $args = [$model->getTenTK(),$model->getEmail(), password_hash($model->getPassword(), PASSWORD_DEFAULT), $model->getIdNguoiDung()->getId(), $model->getIdQuyen()->getId(), $model->getTrangThaiHD()];
            $result = database_connection::executeUpdate($query, ...$args);
            // $tmp = 0;
            if ($result >= 1) {
                $gh = new GioHang(null, $model->getEmail(), date('Y-m-d'), 1);
                $this->gioHangBus->addModel($gh);
            }
            return $result;
        } catch (Exception $e) {
            echo "Error " . $e->getMessage() . '<br>';
            return 0;
        }
    }
   public function update($model): int {
    if (!$model) return 0;

    $tenTK = $model->getTenTK();

    // 1. Lấy dữ liệu hiện tại để lấy mật khẩu cũ (đối chiếu)
    $sqlOld = "SELECT PASSWORD, EMAIL, IDNGUOIDUNG, IDQUYEN, TRANGTHAIHD FROM taikhoan WHERE TENTK = ?";
    $rs = database_connection::executeQuery($sqlOld, $tenTK);
    $oldData = $rs->fetch_assoc();
    
    if (!$oldData) return 0;

    // 2. Xử lý Hash mật khẩu
    $newPass = $model->getPassword();
    // Nếu trống hoặc trùng với Hash cũ thì không băm lại
    if (empty($newPass) || $newPass === $oldData['PASSWORD']) {
        $hashedPassword = $oldData['PASSWORD'];
    } else {
        $hashedPassword = password_hash($newPass, PASSWORD_DEFAULT);
    }

    // 3. Thực thi câu lệnh UPDATE với tên cột viết hoa theo DB
    $query = "UPDATE taikhoan SET EMAIL = ?, PASSWORD = ?, IDNGUOIDUNG = ?, IDQUYEN = ?, TRANGTHAIHD = ? WHERE TENTK = ?";
    
    $args = [
        $model->getEmail() ?: $oldData['EMAIL'],
        $hashedPassword,
        (is_object($model->getIdNguoiDung())) ? $model->getIdNguoiDung()->getId() : $oldData['IDNGUOIDUNG'],
        (is_object($model->getIdQuyen())) ? $model->getIdQuyen()->getId() : $oldData['IDQUYEN'],
        $model->getTrangThaiHD() ?? $oldData['TRANGTHAIHD'],
        $tenTK
    ];

    $result = database_connection::executeUpdate($query, ...$args);

    return ($result !== false) ? 1 : 0;
}
    public function controlDelete($email, $active): int
    {
        $query = "UPDATE taikhoan SET trangThaiHD = ? WHERE email = ?";
        $args = [$active, $email];
        $result = database_connection::executeUpdate($query, ...$args);
        if ($result) {
            $list = $this->gioHangBus->getByEmail($email);
            foreach($list as $it) {
                // $it->setEmail($email);
                $this->gioHangBus->controlDeleteModel($it->getIdGH(), $active);
            }
        }
        return is_int($result) ? $result : 0;
    }

    public function search(string $condition, $columnNames = null): array
{
    if (empty(trim($condition))) {
        return [];
    }

    $list = [];
    $searchTerm = "%" . $condition . "%";

    // CỐ ĐỊNH CHỈ TÌM TRÊN 3 CỘT VĂN BẢN
    // Không dùng $columnNames truyền vào để tránh quét trúng IDQUYEN hay IDNGUOIDUNG
    $query = "SELECT tk.* FROM taikhoan tk 
              LEFT JOIN NGUOIDUNG nd ON tk.IDNGUOIDUNG = nd.ID 
              WHERE tk.TENTK LIKE ? 
              OR tk.EMAIL LIKE ? 
              OR nd.SODIENTHOAI LIKE ?";
    
    // Chỉ truyền đúng 3 tham số
    $args = [$searchTerm, $searchTerm, $searchTerm];

    $rs = database_connection::executeQuery($query, ...$args);

    if ($rs) {
        while ($row = $rs->fetch_assoc()) {
            $model = $this->createTaiKhoanModel($row);
            $list[] = $model;
        }
    }

    return $list;
}

    public function searchByQuyen($idQuyen) {
        $list = [];
        $query = "SELECT * FROM TAIKHOAN WHERE IDQUYEN = ?";
        $rs = database_connection::executeQuery($query, $idQuyen);
        while($row = $rs->fetch_assoc()) {
            $model = $this->createTaiKhoanModel($row);
            array_push($list, $model);
        }
        return $list;
    }

    public function checkLogin($email, $password): bool {
        $query = "SELECT password FROM TAIKHOAN WHERE email = ?";
        $result = database_connection::executeQuery($query, $email);
        
        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $hashedPassword = $row['password'];
            return password_verify($password, $hashedPassword);
        }
        return false;
    }
    
    public function login($email, $password) {
        session_start();
        
        $taiKhoanDAO = app(TaiKhoan_DAO::class);
        $user = $taiKhoanDAO->getById($email);
    
        if (!$user) {
            return "Email không tồn tại!";
        }
    
        if (!password_verify($password, $user->getPassword())) {
            return "Mật khẩu không đúng!";
        }
    
        // Đăng nhập thành công
        $_SESSION['user'] = $user->getEmail();
        return "Đăng nhập thành công!";
    }
    public function logout() {
        session_start();
        session_unset();
        session_destroy();
        header("Location: login.php"); // Chuyển hướng về trang đăng nhập
        exit();
    }
       
}   
?>