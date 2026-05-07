<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use App\Bus\Auth_BUS;
use App\Bus\DiaChi_BUS;
use Illuminate\Http\Request;
use App\Models\TaiKhoan;
use App\Bus\NguoiDung_BUS;
use App\Bus\Quyen_BUS;
use App\Bus\TaiKhoan_BUS;
use App\Bus\Tinh_BUS;
use App\Enum\GioiTinhEnum;
use App\Models\DiaChi;
use App\Models\NguoiDung;


class NguoiDungController extends Controller
{
    protected $tinhBUS;
    protected $nguoiDungBUS;
    protected $taiKhoanBUS;

    public function __construct(Tinh_BUS $tinh_bus, NguoiDung_BUS $nguoi_dung_bus, TaiKhoan_BUS $tai_khoan_bus)
    {
        $this->tinhBUS = $tinh_bus;
        $this->nguoiDungBUS = $nguoi_dung_bus;
        $this->taiKhoanBUS = $tai_khoan_bus;
    }

    

public function store(Request $request)
{
    $maxDate = now()->subYears(16)->format('Y-m-d');

    $attributes = [
        'HOTEN' => 'Họ tên',
        'NGAYSINH' => 'Ngày sinh',
        'GIOITINH' => 'Giới tính',
        'DIACHI' => 'Địa chỉ',
        'IDTINH' => 'Tỉnh',
        'SODIENTHOAI' => 'Số điện thoại',
        'CCCD' => 'CCCD',
    ];

    $messages = [
        'required' => ':attribute không được để trống.',
        // Thông báo cho Số điện thoại
        'SODIENTHOAI.regex' => 'Số điện thoại phải nhập đúng 10 số.',
        'SODIENTHOAI.unique' => 'Số điện thoại này đã tồn tại.',
        
        // Thông báo cho CCCD
        'CCCD.digits' => 'CCCD phải nhập đúng 12 số.',
        'CCCD.unique' => 'Số CCCD này đã tồn tại.',
        
        // Các thông báo khác
        'NGAYSINH.before_or_equal' => 'Người dùng phải từ 16 tuổi trở lên.',
        'date' => ':attribute không đúng định dạng.',
    ];

    $validator = Validator::make($request->all(), [
        'HOTEN' => 'required|string|max:255',
        'NGAYSINH' => 'required|date|before_or_equal:' . $maxDate,
        'GIOITINH' => 'required',
        'DIACHI' => 'required',
        'IDTINH' => 'required',
        'SODIENTHOAI' => [
            'required',
            'regex:/^[0-9]{10}$/', 
            'unique:nguoidung,SODIENTHOAI'
        ],
        'CCCD' => [
            'required',
            'digits:12',
            'unique:nguoidung,CCCD'
        ],
    ], $messages, $attributes);

    if ($validator->fails()) {
        return redirect()->back()
            ->withErrors($validator, 'addUser')
            ->withInput();
    }

    $gioiTinh = match($request->input('GIOITINH')) {
        'MALE' => GioiTinhEnum::MALE,
        'FEMALE' => GioiTinhEnum::FEMALE,
        'UNDEFINED' => GioiTinhEnum::UNDEFINED,
        default => null,
    };

    $tinh = $this->tinhBUS->getModelById($request->input('IDTINH'));
    $nguoidung = new NguoiDung(
        null, 
        $request->input('HOTEN'), 
        $request->input('NGAYSINH'), 
        $gioiTinh, 
        $request->input('DIACHI'), 
        $tinh, 
        $request->input('SODIENTHOAI'), 
        $request->input('CCCD'), 
        1
    );
    
    $this->nguoiDungBUS->addModel($nguoidung);
    return redirect()->back()->with('success', 'Thêm thành công!');
}

public function update(Request $request)
{
    $maxDate = now()->subYears(16)->format('Y-m-d');

    $attributes = [
        'HOTEN' => 'Họ tên',
        'NGAYSINH' => 'Ngày sinh',
        'GIOITINH' => 'Giới tính',
        'DIACHI' => 'Địa chỉ',
        'IDTINH' => 'Tỉnh',
        'SODIENTHOAI' => 'Số điện thoại',
        'CCCD' => 'CCCD',
    ];

    $messages = [
        'required' => ':attribute không được để trống.',
        'digits' => ':attribute phải nhập đúng :digits số.',
        'regex' => ':attribute phải nhập đúng 10 số.',
        'unique' => ':attribute này đã bị trùng.',
        'NGAYSINH.before_or_equal' => 'Người dùng phải từ 16 tuổi trở lên.',
        'exists' => 'Bản ghi không tồn tại.',
    ];

    $validator = Validator::make($request->all(), [
        'id' => 'required|exists:nguoidung,id',
        'HOTEN' => 'required|string|max:255',
        'NGAYSINH' => 'required|date|before_or_equal:' . $maxDate,
        'GIOITINH' => 'required',
        'DIACHI' => 'required',
        'IDTINH' => 'required',
        'SODIENTHOAI' => [
            'required',
            'regex:/^[0-9]{10}$/',
            'unique:nguoidung,SODIENTHOAI,' . $request->id
        ],
        'CCCD' => [
            'required',
            'digits:12',
            'unique:nguoidung,CCCD,' . $request->id
        ],
    ], $messages, $attributes);

    if ($validator->fails()) {
        return redirect()->back()
            ->withErrors($validator, 'updateUser')
            ->withInput();
    }
    $gioiTinh = match($request->input('GIOITINH')) {
        'MALE' => GioiTinhEnum::MALE,
        'FEMALE' => GioiTinhEnum::FEMALE,
        'UNDEFINED' => GioiTinhEnum::UNDEFINED,
        default => null,
    };

    $tinh = $this->tinhBUS->getModelById($request->input('IDTINH'));
    $nguoidung = new NguoiDung(
        $request->id, 
        $request->input('HOTEN'), 
        $request->input('NGAYSINH'), 
        $gioiTinh, 
        $request->input('DIACHI'), 
        $tinh, 
        $request->input('SODIENTHOAI'), 
        $request->input('CCCD'), 
        $request->input('TRANGTHAIHD')
    );

    $this->nguoiDungBUS->updateModel($nguoidung);
    return redirect()->back()->with('success', 'Cập nhật thành công!');
}

    public function controlDelete(Request $request)
    {
        $id = $request->input('id');
        $isActive = $this->nguoiDungBUS->getModelById($id)?->getTrangThaiHD();
        $this->nguoiDungBUS->controlDeleteModel($id, $isActive === 1 ? 0 : 1);
        return redirect()->back()->with('success','Người dùng cập nhật hoạt động thành công!');
    }

    public function checkExistingUser($sdt)
    {
        // Placeholder for checking if user exists
    }

    public function updateInfo(Request $request)
    {
        // 1. Validation dữ liệu đầu vào
        $validator = Validator::make($request->all(), [
            'hoTen' => 'required|string|max:255',
            'soDienThoai' => 'required|digits:10',
            'password' => [
            'nullable',
            'min:6',
            'regex:/[@$!%*?&]/' 
        ],
        ], [
            'hoTen.required' => 'Họ tên không được để trống.',
            'soDienThoai.required' => 'Số điện thoại không được để trống.',
            'soDienThoai.digits' => 'Số điện thoại phải bao gồm đúng 10 chữ số.',
            'password.min' => 'Mật khẩu mới phải có ít nhất 6 ký tự.',
            'password.regex' => 'Mật khẩu phải bao gồm ít nhất một ký tự đặc biệt (@$!%*?&).',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        // 2. Lấy ID người dùng từ request
        $id = $request->input('id');

        // 3. TRUY NGƯỢC: Tìm tài khoản dựa trên ID người dùng
        $existingTK = $this->taiKhoanBUS->getModelByIdNguoiDung($id);

        if (!$existingTK) {
            return redirect()->back()->with('error', 'Lỗi: Không tìm thấy tài khoản liên kết với người dùng này.');
        }

        // 4. Kiểm tra mật khẩu mới có trùng mật khẩu cũ không
        if ($request->filled('password')) {
            $newPassword = $request->input('password');
            $oldPasswordHash = $existingTK->getPassword(); // Lấy mã hash hiện tại từ DB

            // password_verify kiểm tra xem chuỗi thô có khớp với mã hash không
            if (password_verify($newPassword, $oldPasswordHash)) {
                return redirect()->back()
                    ->withErrors(['password' => 'Mật khẩu mới không được trùng với mật khẩu hiện tại.'])
                    ->withInput();
            }
            
            // Nếu không trùng, gán mật khẩu mới vào object (DAO sẽ lo phần hash lại)
            $existingTK->setPassword($newPassword);
        }

        // 5. Cập nhật thông tin bảng NGUOIDUNG (Thông tin cá nhân)
        $nguoidung = $existingTK->getIdNguoiDung(); 
        if ($nguoidung) {
            $nguoidung->setHoTen($request->input('hoTen'));
            $nguoidung->setSoDienThoai($request->input('soDienThoai'));
            $nguoidung->setDiaChi($request->input('diaChi'));
            
            
            $this->nguoiDungBUS->updateModel($nguoidung);
        }

        if ($request->filled('email')) {
            $existingTK->setEmail($request->input('email'));
        }

        $result = $this->taiKhoanBUS->updateModel($existingTK);
        
        if ($result !== false) {
            return redirect()->back()->with('success', 'Cập nhật thông tin cá nhân và mật khẩu thành công!');
        }   
    
        return redirect()->back()->with('error', 'Cập nhật thất bại, vui lòng thử lại.');
    }
   
    public function addAddress(Request $request)
{
    $user = session('user');
    if (!$user) {
        return response()->json(['status' => 'error', 'message' => 'Hết phiên đăng nhập']);
    }

    $bus = app(\App\Bus\DiaChi_BUS::class);
    
    // 1. Kiểm tra số lượng địa chỉ hiện tại của người dùng
    // Bạn cần viết thêm hàm getAllByIdND trong BUS/DAO để lấy danh sách
    $currentAddresses = $bus->getByIdND($user->getIdNguoiDung());

    if (count($currentAddresses) >= 5) {
        return response()->json([
            'status' => 'error', 
            'message' => 'Bạn chỉ được lưu tối đa 5 địa chỉ. Vui lòng xóa bớt địa chỉ cũ!'
        ]);
    }

    // 2. Nếu chưa đủ 5, thực hiện logic thêm mới như cũ
    $diachi = $request->input('diachi');
    $hoten = $request->input('hoten');
    $sdt = $request->input('sodienthoai');

    try {
        $model = new \App\Models\DiaChi($user->getIdNguoiDung(), $diachi, $hoten, $sdt);
        $bus->addModel($model);
        return response()->json(['status' => 'success']);
    } catch (\Exception $e) {
        return response()->json(['status' => 'error', 'message' => $e->getMessage()]);
    }
}
}

?>
