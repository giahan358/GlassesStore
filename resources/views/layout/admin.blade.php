<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Trang quản trị</title>
    <meta http-equiv="Content-Security-Policy" content="upgrade-insecure-requests">
    <link rel="stylesheet" href="{{ asset('css/bootstrap.min.css') }}">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    @vite(['resources/css/admin/admin.css'])
</head>
<body>
    <div class="wrapper">
        @include('admin.includes.sidebar')
        <div class="main bg-light" id="content">
        @include('admin.includes.navbar')
            <?php
                use App\Enum\HoaDonEnum;
                use App\Bus\SanPham_BUS;
                use App\Bus\CTHD_BUS;
                use App\Bus\DVVC_BUS;
                use App\Bus\Hang_BUS;
                use App\Bus\HoaDon_BUS;
                use App\Bus\LoaiSanPham_BUS;
                use App\Bus\NguoiDung_BUS;
                use App\Bus\PTTT_BUS;
                use App\Bus\Quyen_BUS;
                use App\Bus\TaiKhoan_BUS;
                use App\Bus\KhuyenMai_BUS;
                use App\Bus\ThongKe_BUS;
                use App\Bus\Auth_BUS;
                use App\Bus\ChiTietBaoHanh_BUS;
                use App\Bus\CPVC_BUS;
                use App\Bus\CTPN_BUS;
                use App\Bus\CTQ_BUS;
                use App\Bus\KieuDang_BUS;
                use App\Bus\NCC_BUS;
                use App\Bus\PhieuNhap_BUS;
                use App\Bus\Tinh_BUS;
                use App\Bus\CTSP_BUS;
                use App\Models\DVVC;
                use Illuminate\Support\Facades\View as FacadesView;

                $page = $_GET['modun'] ?? 'nguoidung';
                switch ($page) {
                    case 'taikhoan':
                        $taikhoanBUS = app(TaiKhoan_BUS::class);
                        $quyenBUS = app(Quyen_BUS::class);
                        $ndBUS = app(NguoiDung_BUS::class);
                        $listTK = $taikhoanBUS->getAllModels();
                        $listQ = $quyenBUS->getAllModels();
                        $listND = $ndBUS->getAllModels();
                        if (isset($_GET['keyword']) || !empty($_GET['keyword'])) {
                            $keyword = $_GET['keyword'];
                            $listTK = $taikhoanBUS->searchModel($keyword, []);
                        } elseif (isset($_GET['keywordQuyen']) || !empty($_GET['keywordQuyen'])) {
                            $keywordQuyen = $_GET['keywordQuyen'];
                            $listTK = $taikhoanBUS->searchByQuyen($keywordQuyen);
                        }

                        $current_page = request()->query('page', 1);
                        $limit = 8;
                        $total_record = count($listTK ?? []);
                        $total_page = ceil($total_record / $limit);
                        $current_page = max(1, min($current_page, $total_page));
                        $start = ($current_page - 1) * $limit;
                        if(empty($listTK)) {
                            $tmp = [];
                        } else {
                            $tmp = array_slice($listTK, $start, $limit);
                        }

                        echo FacadesView::make('admin.taikhoan', [
                            'tmp' => $tmp,
                            'listQ' => $listQ,
                            'listND' => $listND,
                            'current_page' => $current_page,
                            'total_page' => $total_page
                        ])->render();
                        break;
                    case 'nguoidung':
    $ndBUS = app(NguoiDung_BUS::class);
    $tinhBUS = app(Tinh_BUS::class);
    
    // Lấy toàn bộ danh sách ban đầu
    $listND = $ndBUS->getAllModels();
    $listTinh = $tinhBUS->getAllModels();

    // 1. Nhận tham số từ URL
    $keyword = isset($_GET['keyword']) && !empty(trim($_GET['keyword'])) ? trim($_GET['keyword']) : null;
    $keywordActive = isset($_GET['keywordActive']) && $_GET['keywordActive'] !== "" ? $_GET['keywordActive'] : null;

    // 2. Lọc theo Trạng thái (Thay thế lọc theo Tỉnh)
    if ($keywordActive !== null) {
        $listND = array_filter($listND, function($nd) use ($keywordActive) {
            // Giả sử model có hàm getTrangThaiHD() trả về 1 hoặc 0
            return (string)$nd->getTrangThaiHD() === (string)$keywordActive;
        });
    }

    // 3. Lọc theo từ khóa (Ưu tiên tìm theo Số điện thoại)
    if ($keyword) {
        $listND = array_filter($listND, function($nd) use ($keyword) {
            // Chỉ tập trung tìm kiếm trong cột Số điện thoại theo yêu cầu
            $sdt = $nd->getSodienthoai(); 
            return stripos($sdt, $keyword) !== false;
        });
    }

    // --- Phần Phân trang giữ nguyên ---
    $current_page = request()->query('page', 1);
    $limit = 8;
    $total_record = count($listND ?? []);
    $total_page = ceil($total_record / $limit);
    $current_page = max(1, min($current_page, $total_page));
    $start = ($current_page - 1) * $limit;

    if(empty($listND)) {
        $tmp = [];
    } else {
        // Reset lại key của array sau khi filter để array_slice hoạt động đúng
        $tmp = array_slice(array_values($listND), $start, $limit);
    }

    echo FacadesView::make('admin.nguoidung', [
        'tmp' => $tmp,
        'listTinh' => $listTinh,
        'current_page' => $current_page,
        'total_page' => $total_page
    ])->render();
    break;
                        case 'hang':
                            $hangBUS = app(Hang_BUS::class);
                        
                            $keyword = isset($_GET['keyword']) && !empty(trim($_GET['keyword'])) ? trim($_GET['keyword']) : null;
                            $trangThai = isset($_GET['keywordTrangThai']) && $_GET['keywordTrangThai'] !== '' ? (int)$_GET['keywordTrangThai'] : null;
                        
                            // Tạo danh sách trạng thái tĩnh
                            $listtrangthai = collect([
                                (object) ['id' => 1, 'trangThaiHD' => 'Đang kinh doanh'],
                                (object) ['id' => 3, 'trangThaiHD' => 'Ngừng kinh doanh'],
                            ]);
                        
                            // Lấy dữ liệu
                            $listHang = $hangBUS->getAllModels($keyword, $trangThai);
                        
                            $current_page = request()->query('page', 1);
                            $limit = 8;
                            $total_record = count($listHang ?? []);
                            $total_page = ceil($total_record / $limit);
                            $current_page = max(1, min($current_page, $total_page));
                            $start = ($current_page - 1) * $limit;
                        
                            if (empty($listHang)) {
                                $tmp = [];
                            } else {
                                $tmp = array_slice($listHang, $start, $limit);
                            }
                        
                            echo \Illuminate\Support\Facades\View::make('admin.hang', [
                                'tmp' => $tmp,
                                'listtrangthai' => $listtrangthai,
                                'current_page' => $current_page,
                                'total_page' => $total_page,
                                'keyword' => $keyword,
                                'keywordTrangThai' => $trangThai
                            ])->render();
                            break;
                            
                    case 'quyen':
                        $quyenBUS = app(Quyen_BUS::class);
                        $listQuyen = $quyenBUS->getAllModels();
                        // Lọc chỉ lấy quyền hoạt động
                        $listQuyen = array_filter($listQuyen, function($quyen) {
                            return $quyen->getTrangThaiHD() == 1;
                        });
                        if (isset($_GET['keyword']) || !empty($_GET['keyword'])) {
                            $keyword = $_GET['keyword'];
                            $listQuyen = $quyenBUS->searchModel($keyword, []);
                            // Lọc lại sau khi tìm kiếm
                            $listQuyen = array_filter($listQuyen, function($quyen) {
                                return $quyen->getTrangThaiHD() == 1;
                            });
                        }

                        $current_page = request()->query('page', 1);
                        $limit = 8;
                        $total_record = count($listQuyen ?? []);
                        $total_page = ceil($total_record / $limit);
                        $current_page = max(1, min($current_page, $total_page));
                        $start = ($current_page - 1) * $limit;
                        if(empty($listQuyen)) {
                            $tmp = [];
                        } else {
                            $tmp = array_slice($listQuyen, $start, $limit);
                        }

                        echo FacadesView::make('admin.quyen', [
                            'listQuyen' => $tmp,
                            'current_page' => $current_page,
                            'total_page' => $total_page
                        ])->render();
                        break;
            
                    case 'loaisanpham':
                        $bus = app(LoaiSanPham_BUS::class);
                        $list = $bus->getAllModels();

                        $keyword = trim(request('keyword'));
                        if ($keyword === '') {
                            $list = $bus->getAllModels();
                        } else {
                            $list = $bus->searchModel($keyword, []);
                        }

                        $current_page = request()->query('page', 1);
                        $limit = 8;
                        $total_record = count($list ?? []);
                        $total_page = ceil($total_record / $limit);
                        $current_page = max(1, min($current_page, $total_page));
                        $start = ($current_page - 1) * $limit;
                        if(empty($list)) {
                            $tmp = [];
                        } else {
                            $tmp = array_slice($list, $start, $limit);
                        }

                        echo FacadesView::make('admin.loaisanpham', [
                            'listLSP' => $tmp,
                            'current_page' => $current_page,
                            'total_page' => $total_page
              
                        ])->render();
                
                        break;  
                    case 'sanpham':
                    $loaiSanPhamBUS = app(LoaiSanPham_BUS::class);
                    $hangBUS = app(Hang_BUS::class);
                    $sanPhamBUS = app(SanPham_BUS::class);
                    $kieuDangBUS = app(KieuDang_BUS::class);
                    $ctpnBUS = app(CTPN_BUS::class);

                    $listLSP = $loaiSanPhamBUS->getAllModels();
                    $listLSPIsActive = $loaiSanPhamBUS->getAllModelsActive();
                    $listHang = $hangBUS->getAllModels();
                    $listHangIsActive = $hangBUS->getActiveHangs();
                    $listKieuDang = $kieuDangBUS->getAllModels();

                    // 1. Xử lý tìm kiếm theo Keyword
                    $keyword = trim(request('keyword'));
                    if ($keyword === '') {
                        $listSP = $sanPhamBUS->getAllModels();
                    } else {
                        $listSP = $sanPhamBUS->searchModel($keyword, []);
                    }

                    // 2. BỔ SUNG: Lọc theo trạng thái kinh doanh (1: Đang bán, 0: Ngừng bán)
                    $trangThai = request('trangthai'); // Lấy từ URL (?trangthai=1 hoặc ?trangthai=0)
                    if ($trangThai !== null && $trangThai !== '') {
                        $listSP = array_filter($listSP, function($sp) use ($trangThai) {
                            // Đảm bảo hàm getTrangThai() trả về đúng giá trị 0 hoặc 1 trong Model
                            return (string)$sp->getTrangThaiHD() === (string)$trangThai;
                        });
                        // Reset lại index của mảng sau khi filter để array_slice không bị lỗi
                        $listSP = array_values($listSP);
                    }

                    // 3. Cập nhật đơn giá (Giữ nguyên logic cũ của bạn)
                    foreach ($listSP as $sanPham) {
                        $sanPham->setDonGia($ctpnBUS->getGiaBanCaoNhatByIDSP($sanPham->getId()));
                        $sanPhamBUS->updateModel($sanPham);
                    }

                    // --- Tạo các Map dữ liệu (Giữ nguyên) ---
                    $mapTenHang = [];
                    foreach ($listHang as $hang) { $mapTenHang[$hang->getId()] = $hang->gettenHang(); }

                    $mapTenLoaiSP = [];
                    foreach ($listLSP as $loaiSP) { $mapTenLoaiSP[$loaiSP->getId()] = $loaiSP->getTenLSP(); }

                    $mapTenKieuDang = [];
                    foreach ($listKieuDang as $kieuDang) { $mapTenKieuDang[$kieuDang->getId()] = $kieuDang->getTenKieuDang(); }

                    // 4. Phân trang (Sử dụng danh sách đã được lọc)
                    $current_page = request()->query('page', 1);
                    $limit = 8;
                    $total_record = count($listSP ?? []);
                    $total_page = ceil($total_record / $limit);
                    $current_page = max(1, min($current_page, $total_page));
                    $start = ($current_page - 1) * $limit;

                    if(empty($listSP)) {
                        $tmp = [];
                    } else {
                        $tmp = array_slice($listSP, $start, $limit);            
                    }

                    echo FacadesView::make('admin.sanpham', [
                        'listSP' => $tmp,
                        'listHang' => $listHang,
                        'listHangIsActive' => $listHangIsActive,
                        'listLSP' => $listLSP,
                        'listLSPIsActive' => $listLSPIsActive,
                        'listKieuDang' => $listKieuDang,
                        'mapTenLoaiSP' => $mapTenLoaiSP, 
                        'mapTenHang' => $mapTenHang,
                        'mapTenKieuDang' => $mapTenKieuDang,
                        'current_page' => $current_page,
                        'total_page' => $total_page
                    ])->render();
                    break;
                    
                    case 'hoadon':
    // 1. Khởi tạo các BUS cần thiết
    $cthdBUS = app(CTHD_BUS::class);
    $hoaDonBUS = app(HoaDon_BUS::class);
    $ctspBUS = app(CTSP_BUS::class);
    $tinhBUS = app(Tinh_BUS::class);
    $nguoiDungBUS = app(NguoiDung_BUS::class);
    $pttBUS = app(PTTT_BUS::class);
   
    $taiKhoanBUS = app(TaiKhoan_BUS::class);

    // 2. Lấy danh sách hóa đơn gốc
    $listHoaDon = $hoaDonBUS->getAllModels();

    // 3. Xử lý các bộ lọc tìm kiếm (Search Filters)
    if (!empty($_GET['keywordTinh'])) {
        $listHoaDon = $hoaDonBUS->searchByTinh($_GET['keywordTinh']);
    }

    if (!empty($_GET['trangthai'])) {
        $listHoaDon = $hoaDonBUS->getHoaDonsByTrangThai($_GET['trangthai']);
    }

    if (!empty($_GET['ngaybatdau']) && !empty($_GET['ngayketthuc'])) {
        $listHoaDon = $hoaDonBUS->getHoaDonsByNgay($_GET['ngaybatdau'], $_GET['ngayketthuc']);
    } 

    if (!empty($_GET['keywordSoSeri'])) {
        $listHoaDon = $hoaDonBUS->getHoaDonsBySoseri($_GET['keywordSoSeri']);
    }

    if (!empty($_GET['keyword']) && !empty(trim($_GET['keyword']))) {
        $listHoaDon = $hoaDonBUS->searchByEmailOrSDT(trim($_GET['keyword']));
    }

    // 4. Sắp xếp danh sách hóa đơn theo ngày
    $sortDate = $_GET['sortDate'] ?? 'desc';
    if (!empty($listHoaDon)) {
        usort($listHoaDon, function($a, $b) use ($sortDate) {
            $t1 = $a->getNgayTao()->getTimestamp();
            $t2 = $b->getNgayTao()->getTimestamp();
            return ($sortDate === 'asc') ? ($t1 - $t2) : ($t2 - $t1);
        });
    }

    // 5. Chuẩn bị dữ liệu Map để hiển thị thông tin liên kết
    $mapCTHD = [];
    foreach ($listHoaDon as $hoaDon) {
        $details = $cthdBUS->getCTHTbyIDHD($hoaDon->getId());
        $enrichedDetails = [];

        if (!empty($details)) {
            $counts = [];
            $tempDetails = [];
            foreach ($details as $cthd) {
                // Tra cứu thông tin sản phẩm từ số seri[cite: 5]
                $sp = $ctspBUS->getSPBySoSeri($cthd->getSoSeri());
                $tenSP = $sp ? $sp->getTenSanPham() : 'N/A';
                $counts[$tenSP] = ($counts[$tenSP] ?? 0) + 1;
                $enrichedDetails[] = [
                    'SOSERI' => $cthd->getSoSeri(),
                    'GIALUCDAT' => $cthd->getGiaLucDat(),
                    'TENSANPHAM' => $sp ? $sp->getTenSanPham() : 'Sản phẩm không tồn tại'
                ];
            }
            foreach ($tempDetails as $item) {
                $item['SOLUONG'] = $counts[$item['TENSANPHAM']];
                $enrichedDetails[] = $item;
            }
        }
        $mapCTHD[$hoaDon->getId()] = $enrichedDetails;
    }

    // Map tên người dùng theo Email
    $listtaiKhoan = $taiKhoanBUS->getAllModels();
    $mapHoTenByEmail = [];
    foreach ($listtaiKhoan as $taiKhoan) {
        $nguoiDung = $taiKhoan->getIdNguoiDung();
        $mapHoTenByEmail[$taiKhoan->getEmail()] = $nguoiDung->getHoTen();
    }

    // Map các danh mục khác
    $mapTinh = [];
    foreach ($tinhBUS->getAllModels() as $tinh) {
        $mapTinh[$tinh->getId()] = $tinh->getTenTinh();
    }

    $mapPTTT = [];
    foreach ($pttBUS->getAllModels() as $pttt) {
        $mapPTTT[$pttt->getId()] = $pttt->gettenPTTT();
    }

    

    // 6. Phân trang dữ liệu
    $current_page = request()->query('page', 1);
    $limit = 8;
    $total_record = count($listHoaDon ?? []);
    $total_page = ceil($total_record / $limit);
    $current_page = max(1, min($current_page, $total_page));
    $start = ($current_page - 1) * $limit;
    
    $paginatedList = empty($listHoaDon) ? [] : array_slice($listHoaDon, $start, $limit);

    // 7. Trả về View
    echo FacadesView::make('admin.hoadon', [
        'listHoaDon' => $paginatedList,
        'mapCTHD' => $mapCTHD,
        'mapHoTenByEmail' => $mapHoTenByEmail,
        'mapPTTT' => $mapPTTT,
       
        'mapTinh' => $mapTinh,
        'listTinh' => $tinhBUS->getAllModels(),
        'current_page' => $current_page,
        'total_page' => $total_page,
        'hoaDonStatuses' => HoaDonEnum::cases(),
    ])->render();
    break;
                   
                    case 'kho':
                        $phieuNhapBUS = app(PhieuNhap_BUS::class);
                        $nccBUS = app(NCC_BUS::class);
                        $spBUS = app(SanPham_BUS::class);
                        $listSanPham = $spBUS->getAllModels();
                        $listNCC = $nccBUS->getAllModels();
                        $listPhieuNhap = $phieuNhapBUS->getAllModels();
                        
                        if (isset($_GET['keyword']) || !empty($_GET['keyword'])) {
                            $keyword = $_GET['keyword'];
                            $listPhieuNhap = $phieuNhapBUS->searchModel($keyword, []);
                        }
    
                        $current_page = request()->query('page', 1);
                        $limit = 8;
                        $total_record = count($listPhieuNhap ?? []);
                        $total_page = ceil($total_record / $limit);
                        $current_page = max(1, min($current_page, $total_page));
                        $start = ($current_page - 1) * $limit;
                        if (empty($listPhieuNhap)) {
                            $tmp = [];
                        } else {
                            $tmp = array_slice($listPhieuNhap, $start, $limit);
                        }
    
                        echo FacadesView::make('admin.phieunhap', [
                            'listPhieuNhap' => $tmp,
                            'listNCC' => $listNCC,
                            'listSanPham' => $listSanPham,
                            'current_page' => $current_page,
                            'total_page' => $total_page
                        ])->render();
                        break;
                    case 'nhacungcap':
                        $nccBUS = app(NCC_BUS::class);
                        $listNCC = $nccBUS->getAllModels();
                        if (isset($_GET['keyword']) || !empty($_GET['keyword'])) {
                            $keyword = $_GET['keyword'];
                            $listNCC = $nccBUS->searchModel($keyword, []);
                        }
    
                        $current_page = request()->query('page', 1);
                        $limit = 8;
                        $total_record = count($listNCC ?? []);
                        $total_page = ceil($total_record / $limit);
                        $current_page = max(1, min($current_page, $total_page));
                        $start = ($current_page - 1) * $limit;
                        if (empty($listNCC)) {
                            $tmp = [];
                        } else {
                            $tmp = array_slice($listNCC, $start, $limit);
                        }
    
                        echo FacadesView::make('admin.nhacungcap', [
                            'listNCC' => $tmp,
                            'current_page' => $current_page,
                            'total_page' => $total_page
                        ])->render();
                        break;
                   
                case 'thanhpho':
                    $tinhBUS = app(Tinh_BUS::class);
                    $listTinh = $tinhBUS->getAllModels();
                    if (isset($_GET['keyword']) || !empty($_GET['keyword'])) {
                        $keyword = $_GET['keyword'];
                        $listTinh = $tinhBUS->searchModel($keyword, []);
                    }
                    $limit = 8;
                    $total_record = count($listTinh ?? []);
                    $total_page = ceil($total_record / $limit);
                    $current_page = 1;
                    $tmp = array_slice($listTinh, 0, $limit);
                    echo FacadesView::make('admin.thanhpho', [
                        'listTinh' => $tmp,
                        'current_page' => $current_page,
                        'total_page' => $total_page
                    ])->render();
                    break;
                case 'thongke':
                    $thongkeBUS = app(ThongKe_BUS::class);
                
                    // Lấy dữ liệu từ POST hoặc mặc định 1 tháng qua
                    $to = $_POST['to'] ?? date('Y-m-d'); // Ngày hiện tại
                    $from = $_POST['from'] ?? date('Y-m-d', strtotime('-1 month', strtotime($to))); // 1 tháng trước
                
                    // Lấy top 5 khách hàng
                    $topCustomers = $thongkeBUS->getTop5KhachHang($from, $to);
                
                    // Không lấy dữ liệu đơn hàng và chi tiết hóa đơn ban đầu
                    $hoaDonHang = [];
                    $CTHDList = [];
                
                    // Render view thongke
                    echo FacadesView::make('admin.thongke', [
                        'topCustomers' => $topCustomers,
                        'hoaDonHang' => $hoaDonHang,
                        'CTHDList' => $CTHDList,
                        'from' => $from,
                        'to' => $to
                    ])->render();
                    break;  
                    default:
                        include base_path('resources/views/admin/nguoidung.blade.php');
                        break;
                }
            ?>
        </div>
    </div>
</body>
</html>
