<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\TaiKhoan;
use App\Bus\NguoiDung_BUS;
use App\Bus\Quyen_BUS;
use App\Bus\TaiKhoan_BUS;

class TaiKhoanController extends Controller
{
    protected $taiKhoanBUS;
    protected $nguoiDungBUS;
    protected $quyenBUS;

    public function __construct(TaiKhoan_BUS $taiKhoanBUS, NguoiDung_BUS $nguoiDungBUS, Quyen_BUS $quyenBUS)
    {
        $this->taiKhoanBUS = $taiKhoanBUS;
        $this->nguoiDungBUS = $nguoiDungBUS;
        $this->quyenBUS = $quyenBUS;
    }

    public function store(Request $request)
{
    // 1. Định nghĩa tên hiển thị tiếng Việt
    $attributes = [
        'username' => 'Tên đăng nhập',
        'email' => 'Email',
        'password' => 'Mật khẩu',
        'idquyen' => 'Nhóm quyền',
        'idnguoidung' => 'Người dùng',
    ];

    // 2. Định nghĩa thông báo lỗi tiếng Việt
    $messages = [
        'required' => ':attribute không được để trống.',
        'unique' => ':attribute này đã tồn tại trên hệ thống.',
        'email' => ':attribute không đúng định dạng.',
        'min' => ':attribute phải có ít nhất :min ký tự.',
        'exists' => ':attribute không hợp lệ.',
        'password.regex' => 'Mật khẩu phải bao gồm ít nhất một ký tự đặc biệt (@$!%*?&).',
    ];

    // 3. Validation
    $validator = \Illuminate\Support\Facades\Validator::make($request->all(), [
        'username' => 'required|string|max:255|unique:taikhoan,tentk',
        'email' => 'required|email|unique:taikhoan,email',
        'password' => [
            'required',
            'min:6',
            'regex:/[@$!%*?&]/' 
        ],
        'idquyen' => 'required|exists:quyen,id',
        'idnguoidung' => 'required|exists:nguoidung,id',
    ], $messages, $attributes);

    if ($validator->fails()) {
        return redirect()->back()
            ->withErrors($validator, 'addAccount')
            ->withInput();
    }

    // 4. Xử lý logic thêm model
    $tenTK = $request->input('username');
    $email = $request->input('email');
    $password = $request->input('password'); 
    $idnguoidung = $this->nguoiDungBUS->getModelById($request->input('idnguoidung'));
    $idquyen = $this->quyenBUS->getModelById($request->input('idquyen'));

    $taiKhoan = new TaiKhoan($tenTK, $email, $password, $idnguoidung, $idquyen, 1);
    $this->taiKhoanBUS->addModel($taiKhoan);

    return redirect()->back()->with('success', 'Tài khoản đã được thêm thành công!');
}

public function update(Request $request) 
{
    $attributes = [
        'username' => 'Tên đăng nhập',
        'email' => 'Email',
        'password' => 'Mật khẩu',
        'idquyen' => 'Nhóm quyền',
        'idnguoidung' => 'Người dùng',
    ];

    $messages = [
        'required' => ':attribute không được để trống.',
        'unique' => ':attribute này đã bị trùng với tài khoản khác.',
        'email' => ':attribute không đúng định dạng.',
        'min' => ':attribute mới phải có ít nhất :min ký tự.',
        'password.regex' => 'Mật khẩu phải bao gồm ít nhất một ký tự đặc biệt (@$!%*?&).',
    ];

    // 1. Validation 
    // Lưu ý: Loại bỏ 'exists' ở đây để chúng ta tự check bằng BUS và trim() cho chính xác
    $validator = \Illuminate\Support\Facades\Validator::make($request->all(), [
        'username' => 'required|string',
        'email' => 'required|email', 
        'idquyen' => 'required',
        'idnguoidung' => 'required',
        'password' => [
            'required',
            'min:6',
            'regex:/[@$!%*?&]/' 
        ],
    ], $messages, $attributes);

    if ($validator->fails()) {
        return redirect()->back()
            ->withErrors($validator, 'updateAccount')
            ->withInput();
    }

    // 2. Lấy Username và dùng trim() để loại bỏ khoảng trắng dư thừa
    $tenTK = trim($request->input('username'));
    
    // Sử dụng hàm getModelByUsername đã có trim() mà tôi hướng dẫn ở bước trước
    $existingTK = $this->taiKhoanBUS->getModelByUsername($tenTK); 

    if (!$existingTK) {
        return redirect()->back()
            ->withErrors(['username' => 'Không tìm thấy tài khoản "' . $tenTK . '" trong hệ thống.'], 'updateAccount')
            ->withInput();
    }

    // 3. Kiểm tra mật khẩu mới không được trùng với mật khẩu cũ (Yêu cầu bổ sung)
    if ($request->filled('password')) {
        $newPass = $request->input('password');
        $oldPassHash = $existingTK->getPassword();

        if (password_verify($newPass, $oldPassHash)) {
            return redirect()->back()
                ->withErrors(['password' => 'Mật khẩu mới không được trùng với mật khẩu hiện tại.'], 'updateAccount')
                ->withInput();
        }
        // Nếu hợp lệ thì mới set mật khẩu (DAO sẽ tự hash)
        $existingTK->setPassword($newPass);
    }

    // 4. Cập nhật các thông tin khác
    $existingTK->setEmail($request->input('email'));

    $nguoiDungObj = $this->nguoiDungBUS->getModelById($request->input('idnguoidung'));
    $quyenObj = $this->quyenBUS->getModelById($request->input('idquyen'));

    if ($nguoiDungObj) $existingTK->setIdNguoiDung($nguoiDungObj);
    if ($quyenObj) $existingTK->setIdQuyen($quyenObj);

    // 5. Thực thi lưu
    $result = $this->taiKhoanBUS->updateModel($existingTK);

    if ($result !== false) {
        return redirect()->back()->with('success', 'Tài khoản đã được cập nhật thành công!');
    } else {
        return redirect()->back()->with('error', 'Cập nhật thất bại, vui lòng kiểm tra lại dữ liệu!');
    }
}

    public function controlDelete(Request $request) {
        $tenTK = $request->input('username');
        $email = $request->input('email');
        $password = $request->input('password');  // Mã hóa mật khẩu
        $idnguoidung = $this->nguoiDungBUS->getModelById($request->input('idnguoidung'));
        $idquyen = $this->quyenBUS->getModelById($request->input('idquyen'));
        // $taiKhoan = new TaiKhoan($tenTK, $email, $password, $idnguoidung, $idquyen, 1);
        $isActive = $this->taiKhoanBUS->getModelById($email)?->getTrangThaiHD();

        $this->taiKhoanBUS->controlDeleteModel($email, $isActive === 1 ? 0 : 1);
        return redirect()->back()->with('success','Tài khoản cập nhật hoạt động thành công!');
    }
}

?>