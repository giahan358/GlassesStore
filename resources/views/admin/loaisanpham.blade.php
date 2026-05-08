
<script>
document.addEventListener('DOMContentLoaded', function () {
    // --- 1. XỬ LÝ FORM TÌM KIẾM (GET) ---
    const searchForm = document.querySelector('form[method="GET"]');
    
    if (searchForm) {
        searchForm.addEventListener('submit', function (e) {
            e.preventDefault();
            
            const formData = new FormData(searchForm);
            const url = new URL(window.location.href);
            const currentParams = new URLSearchParams(window.location.search);
            
            // Đảm bảo luôn có modun để tránh mất trang hiện tại
            if (!currentParams.has('modun')) {
                currentParams.set('modun', 'hoadon');
            }
            
            // Duyệt qua các trường trong form để cập nhật tham số URL
            for (const [key, value] of formData.entries()) {
                if (value.trim() !== '') {
                    currentParams.set(key, value);
                } else {
                    currentParams.delete(key);
                }
            }
            
            // Xóa tham số phân trang 'page' khi bắt đầu tìm kiếm mới
            currentParams.delete('page');
            
            url.search = currentParams.toString();
            window.location.href = url.toString();
        });

        // Tự động submit khi thay đổi lựa chọn ở select "Tỉnh"
        const tinhSelect = searchForm.querySelector('select[name="keywordTinh"]');
        if (tinhSelect) {
            tinhSelect.addEventListener('change', function () {
                searchForm.dispatchEvent(new Event('submit'));
            });
        }
    }

    // --- 2. XỬ LÝ THÔNG BÁO (ALERT) TỰ TẮT SAU 3 GIÂY ---
    const alerts = document.querySelectorAll('.alert');
    
    alerts.forEach(function(alert) {
        setTimeout(function() {
            // Sử dụng hiệu ứng fade của Bootstrap bằng cách gỡ class 'show'
            alert.classList.remove('show');
            
            // Chờ hiệu ứng mờ kết thúc (150ms) rồi mới xóa hẳn khỏi giao diện
            setTimeout(function() {
                if (alert.parentNode) {
                    alert.remove();
                }
            }, 150); 
        }, 3000); // 3000ms = 3 giây
    });
});
</script>

<div class="p-3 bg-light">
    <form class="d-flex me-2 mb-3" method="get" role="search">
        <input class="form-control me-2 w-25" type="search" placeholder="Tìm kiếm" aria-label="Search" id="keyword" name="keyword" value="{{ request('keyword') }}">
        <button class="btn btn-outline-success me-2" type="submit">Tìm</button>    
        <button class="btn btn-info ms-2" id="refreshBtn" type="button">Làm mới</button>
    </form>
    
    <!-- Nút Plus để mở Modal -->
    <button type="button" class="btn btn-primary mb-2" data-bs-toggle="modal" data-bs-target="#addTypeProductModal">
        <i class='bx bx-plus'></i>
    </button>

    <!-- Bảng hiển thị dữ liệu -->
    <table class="table table-hover">
        <thead>
            <tr>
                <th scope="col">ID</th>
                <th scope="col">Tên loại sản phẩm</th>
                <th scope="col">Mô tả</th>
                <th scope="col">Trạng thái</th>
                <th scope="col">Hành động</th>
            </tr>
        </thead>
        <tbody>
            @foreach($listLSP as $loai)
            <tr>
                <td>{{ $loai->getId() }}</td>
                <td>{{ $loai->gettenLSP() }}</td>
                <td>{{ $loai->getmoTa() }}</td>
                <td>
                    <span class="badge {{ $loai->getTrangThaiHD() ? 'bg-success' : 'bg-danger' }}">
                        {{ $loai->getTrangThaiHD() ? 'Hoạt động' : 'Ngừng hoạt động' }}
                    </span>
                </td>
                <td>
    <button class="btn btn-warning btn-sm edit-btn" 
            data-bs-toggle="modal" 
            data-bs-target="#editTypeProductModal" 
            data-id="{{ $loai->getId() }}" 
            data-tenlsp="{{ $loai->gettenLSP() }}" 
            data-mota="{{ $loai->getmoTa() }}" 
            data-trangthai="{{ $loai->getTrangThaiHD() ? 1 : 0 }}">Sửa</button>

    <form method="POST" action="{{ route('admin.loaisanpham.delete') }}" style="display:inline;">
        @csrf
        @method('DELETE')
        <input type="hidden" name="id" value="{{ $loai->getId() }}">
        
        @if($loai->getTrangThaiHD() == 1)
            {{-- Đang hoạt động -> Nút Ngừng hoạt động (Nền đỏ, chữ trắng) --}}
            <button type="submit" class="btn btn-danger text-white btn-sm" onclick="return confirm('Ngừng hoạt động loại sản phẩm này?')">
                Ngừng
            </button>
        @else
            {{-- Đang ngừng -> Nút Kích hoạt (Nền xanh lá, chữ trắng) --}}
            <button type="submit" class="submit btn btn-success text-white btn-sm" onclick="return confirm('Kích hoạt lại loại sản phẩm này?')">
                Kích hoạt
            </button>
        @endif
    </form>
</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <!-- Phân trang -->
    <nav aria-label="Page navigation example" class="d-flex justify-content-center">
        <ul class="pagination">
            <?php
            $queryString = isset($_GET['keyword']) ? '&keyword=' . urlencode($_GET['keyword']) : '';
            $query = $_GET;
            if ($current_page > 1) {
                echo '<li class="page-item">
                        <a class="page-link" href="?' . http_build_query(array_merge($query, ['page' => $current_page - 1])) . '" aria-label="Previous">
                            <span aria-hidden="true">«</span>
                        </a>
                    </li>';
            }
            $page_range = 1;
            $start_page = max(1, $current_page - $page_range);
            $end_page = min($total_page, $current_page + $page_range);
            for ($i = $start_page; $i <= $end_page; $i++) {
                if ($i == $current_page) {
                    echo '<li class="page-item active"><span class="page-link">' . $i . '</span></li>';
                } else {
                    echo '<li class="page-item"><a class="page-link" href="?' . http_build_query(array_merge($query, ['page' => $i])) . '">' . $i . '</a></li>';
                }
            }
            if ($current_page < $total_page) {
                echo '<li class="page-item">
                        <a class="page-link" href="?' . http_build_query(array_merge($query, ['page' => $current_page + 1])) . '" aria-label="Next">
                            <span aria-hidden="true">»</span>
                        </a>
                    </li>';
            }
            ?>
        </ul>
    </nav>
</div>

<!-- Modal Form Thêm Sản Phẩm -->
<div class="modal fade" id="addTypeProductModal" tabindex="-1" aria-labelledby="addTypeProductModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addTypeProductModalLabel">Thêm loại sản phẩm</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Đóng"></button>
            </div>
            <div class="modal-body">
                <form method="post" action="{{ route('admin.loaisanpham.store') }}">
                    @csrf 
                    <div class="row mb-3">
                        <div class="col">
                            <label class="form-label">Tên loại sản phẩm</label>
                            <input type="text" name="tenLSP" class="form-control" placeholder="Nhập tên loại sản phẩm" value="{{ old('tenLSP') }}">
                            @if($errors->addLSP->has('tenLSP'))
                                <div class="text-danger small mt-1">{{ $errors->addLSP->first('tenLSP') }}</div>
                            @endif
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col">
                            <label class="form-label">Mô tả</label>
                            <textarea class="form-control" name="moTa" rows="3" placeholder="Nhập mô tả">{{ old('moTa') }}</textarea>
                            @if($errors->addLSP->has('moTa'))
                                <div class="text-danger small mt-1">{{ $errors->addLSP->first('moTa') }}</div>
                            @endif
                        </div>
                    </div>
                    <div class="modal-footer justify-content-start px-0"> <button type="submit" class="btn btn-primary">Lưu</button>
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Modal Form Sửa Loại Sản Phẩm -->
<div class="modal fade" id="editTypeProductModal" tabindex="-1" aria-labelledby="editTypeProductModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editTypeProductModalLabel">Sửa loại sản phẩm</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Đóng"></button>
            </div>
            <div class="modal-body">
                <form method="post" id="editTypeProductForm" action="">
                    @csrf
                    @method('PUT')
                    <div class="row mb-3">
                        <div class="col">
                            <label class="form-label">Tên loại sản phẩm</label>
                            <input type="text" name="tenLSP" class="form-control bg-light" id="editTenLSP" readonly style="cursor: not-allowed;">
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col">
                            <label class="form-label">Mô tả</label>
                            <textarea class="form-control" name="moTa" id="editMoTa" rows="3">{{ old('moTa') }}</textarea>
                            @if($errors->updateLSP->has('moTa'))
                                <div class="text-danger small mt-1">{{ $errors->updateLSP->first('moTa') }}</div>
                            @endif
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col">
                            <label class="form-label">Trạng thái</label>
                            <select name="trangThaiHD" id="editTrangThaiHD" class="form-control">
                                <option value="1">Hoạt động</option>
                                <option value="0">Ngừng hoạt động</option>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer justify-content-start px-0"> <button type="submit" class="btn btn-primary">Lưu</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Modal kích hoạt Loại Sản Phẩm -->
<div class="modal fade" id="deleteProductTypeModal" tabindex="-1" aria-labelledby="deleteProductTypeModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteProductTypeModalLabel">Xóa Loại Sản Phẩm</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Đóng"></button>
            </div>
            <div class="modal-body">
                <p>Bạn có chắc chắn muốn xóa loại sản phẩm <strong id="productTypeName"></strong> (ID: <span id="productTypeId"></span>)?</p>
                <form id="deleteProductTypeForm" method="post" action="{{ route('admin.loaisanpham.delete')}}">
                    @csrf
                    @method('DELETE')
                    <input type="hidden" name="id" id="deleteProductTypeId">
                    <button type="submit" class="btn btn-danger">Xóa</button>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                </form>
            </div>
        </div>
    </div>
</div>

<div id="alert-container">
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show auto-close-alert" role="alert">
            <i class="fas fa-check-circle me-2"></i>
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if(session('error') || $errors->any())
        <div class="alert alert-danger alert-dismissible fade show auto-close-alert" role="alert">
            <i class="fas fa-exclamation-triangle me-2"></i>
            @if(session('error'))
                {{ session('error') }}
            @else
                {{ $errors->first() }} @endif
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif
</div>
<div id="lsp-modal-status" 
     data-add-error="{{ $errors->hasBag('addLSP') && $errors->addLSP->any() ? '1' : '0' }}"
     data-update-error="{{ $errors->hasBag('updateLSP') && $errors->updateLSP->any() ? '1' : '0' }}">
</div>
<script>
document.addEventListener('DOMContentLoaded', function () {
    // Chuyển đổi trạng thái lỗi từ Blade sang JS an toàn
    var hasAddError = "{{ $errors->hasBag('addLSP') && $errors->addLSP->any() ? '1' : '0' }}";
    var hasUpdateError = "{{ $errors->hasBag('updateLSP') && $errors->updateLSP->any() ? '1' : '0' }}";

    // Tự động mở Modal Thêm nếu có lỗi
    if (hasAddError === '1') {
        var addModalEl = document.getElementById('addTypeProductModal');
        if (addModalEl) {
            new bootstrap.Modal(addModalEl).show();
        }
    }

    // Tự động mở Modal Sửa nếu có lỗi
    if (hasUpdateError === '1') {
        var editModalEl = document.getElementById('editTypeProductModal');
        if (editModalEl) {
            new bootstrap.Modal(editModalEl).show();
        }
    }

    // Xử lý đổ dữ liệu vào Modal Sửa khi nhấn nút .btn-edit
    const editButtons = document.querySelectorAll('.edit-btn');
    editButtons.forEach(btn => {
        btn.addEventListener('click', function() {
            const id = this.dataset.id;
            const ten = this.dataset.tenlsp;   
            const mota = this.dataset.mota;
            const status = this.dataset.trangthai;

            const form = document.getElementById('editTypeProductForm');
            if (form) {
                // Thay thế ID vào route update
                form.action = "{{ route('admin.loaisanpham.update', ['id' => ':id']) }}".replace(':id', id);
                
                document.getElementById('editTenLSP').value = ten;
                document.getElementById('editMoTa').value = mota;
                document.getElementById('editTrangThaiHD').value = status;
            }
        });
    });
});
</script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>