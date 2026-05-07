<?php
use App\Bus\HoaDon_BUS;
use App\Enum\HoaDonEnum;

$hoaDonBus = app(HoaDon_BUS::class);
$allHoaDon = $hoaDonBus->getAllModels();

$date_from = request('date_from');
$date_to = request('date_to');
$selected_month = request('month'); 

if (!$selected_month && !$date_from && !$date_to) {
    $selected_month = '2026-05';
}

$totalRevenueAllTime = 0; 
$totalOrdersSystem = 0;  

$revenueSuccess = 0;
$revenueWaiting = 0;
$revenueCancelled = 0;

$countSuccess = 0;  
$countWaiting = 0;  
$countCancelled = 0;

$filteredHoaDons = [];

foreach ($allHoaDon as $hd) {
   
    $statusValue = $hd->getTrangThai() instanceof HoaDonEnum ? $hd->getTrangThai()->value : $hd->getTrangThai();

   
    if ($statusValue === HoaDonEnum::DAGIAO->value) {
        $totalRevenueAllTime += $hd->getTongTien();
        $totalOrdersSystem++;
    }

   
    $hdDateStr = $hd->getNgayTao()->format('Y-m-d');
    $hdTimestamp = strtotime($hdDateStr);
    $isInRange = true;

    if ($selected_month) {
        if (date('Y-m', $hdTimestamp) !== $selected_month) $isInRange = false;
    } elseif ($date_from && $date_to) {
        if ($hdTimestamp < strtotime($date_from) || $hdTimestamp > strtotime($date_to)) $isInRange = false;
    }

    if ($isInRange) {
        $filteredHoaDons[] = $hd;
        
        
        if ($statusValue === HoaDonEnum::CANCELLED->value) {
            $revenueCancelled += $hd->getTongTien();
            $countCancelled++;
        } elseif ($statusValue === HoaDonEnum::DAGIAO->value) {
            $revenueSuccess += $hd->getTongTien();
            $countSuccess++;
        } elseif ($statusValue === HoaDonEnum::PENDING->value) {
            $revenueWaiting += $hd->getTongTien();
            $countWaiting++;
        }
    }
}


$jsChartData = [];
foreach ($filteredHoaDons as $hd) {
    $statusValue = $hd->getTrangThai() instanceof HoaDonEnum ? $hd->getTrangThai()->value : $hd->getTrangThai();
    $jsChartData[] = [
        'date' => $hd->getNgayTao()->format('Y-m-d'),
        'amount' => (int)$hd->getTongTien(),
        'status' => $statusValue
    ];
}

$display_filter = $selected_month ? "Tháng " . date('m/Y', strtotime($selected_month)) : 
                 (($date_from && $date_to) ? date('d/m/Y', strtotime($date_from)) . " - " . date('d/m/Y', strtotime($date_to)) : "");

$jsonData = json_encode([
    'rawData' => $jsChartData,
    'stats' => [
        'counts' => [
            'success' => $countSuccess,
            'waiting' => $countWaiting,
            'cancelled' => $countCancelled
        ],
        'revenue' => [
            'success' => $revenueSuccess,
            'waiting' => $revenueWaiting,
            'cancelled' => $revenueCancelled
        ]
    ],
    'config' => [
        'month' => $selected_month,
        'date_from' => $date_from,
        'date_to' => $date_to
    ]
]);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Thống Kê Doanh Thu</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css'>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        body { background-color: #f8f9fa; }
        .card { border: none; border-radius: 12px; box-shadow: 0 4px 10px rgba(0,0,0,0.05); }
        .total-box { border-left: 4px solid #55d5d2; background: white; padding: 15px; border-radius: 10px; }
    </style>
</head>
<body>

<div class="container py-4">
    <div class="row mb-4">
        <div class="col-md-6 mb-3" >
            <div class="total-box">
                <small class="text-muted fw-bold" >TỔNG ĐƠN HÀNG HỆ THỐNG</small>
                <h3 class="fw-bold mb-0" style="color: #55d5d2;">{{ number_format($totalOrdersSystem) }}</h3>
            </div>
        </div>
        <div class="col-md-6 mb-3">
            <div class="total-box">
                <small class="text-muted fw-bold" >TỔNG DOANH THU HỆ THỐNG</small>
                <h3 class="fw-bold mb-0" style="color: #55d5d2 ;">{{ number_format($totalRevenueAllTime) }} VNĐ</h3>
            </div>
        </div>
    </div>

    <div class="card mb-4 p-4">
        <form action="" method="GET" class="row g-3">
            <input type="hidden" name="modun" value="thongke">
            <div class="col-md-3 border-end" style="border-right: 2px solid #2d3338;">
                <label class="small fw-bold" >Tháng</label>
                <input type="month" class="form-control" name="month" value="{{ $selected_month }}">
            </div>
            <div class="col-md-3">
                <label class="small fw-bold" >Từ ngày</label>
                <input type="date" class="form-control" name="date_from" value="{{ $date_from }}">
            </div>
            <div class="col-md-3">
                <label class="small fw-bold">Đến ngày</label>
                <input type="date" class="form-control" name="date_to" value="{{ $date_to }}">
            </div>
            <div class="col-md-3 d-flex align-items-end">
               <button type="submit" class="btn w-100" style="background-color: #46bd62; color: white;">
                    <i class='bx bx-filter-alt'></i> Thống kê
                </button>
            </div>
        </form>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="card p-4 text-white" style="background: #3498db;">
                <small class="opacity-75">TỔNG ĐƠN ĐÃ ĐẶT HIỆN TẠI</small>
                <h5 class="fw-bold">{{ number_format($countSuccess) }} đơn</h5>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card p-4 text-white" style="background: #50cfcc;">
                <small class="opacity-75">DOANH THU HIỆN TẠI</small>
                <h5 class="fw-bold">{{ number_format($revenueSuccess) }} VNĐ</h5>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card p-4 text-dark" style="background: #f1c40f;">
                <small class="opacity-75">ĐANG ĐỢI THANH TOÁN</small>
                <h5 class="fw-bold">{{ number_format($revenueWaiting) }} VNĐ</h5>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card p-4 text-white" style="background: #e74c3c;">
                <small class="opacity-75">TIỀN ĐƠN ĐÃ HỦY</small>
                <h5 class="fw-bold">{{ number_format($revenueCancelled) }} VNĐ</h5>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <div class="card p-4">
                <h6 class="fw-bold mb-3">Biến động doanh thu: <span class="text-success">{{ $display_filter }}</span></h6>
                <div style="position: relative; height: 350px;">
                    <canvas id="revenueChart"></canvas>
                </div>
            </div>
        </div>
        <div class="col-lg-4">
            <div class="card p-4">
                <h6 class="fw-bold mb-3 text-center">Trạng thái đơn hàng (Số lượng)</h6>
                <div style="position: relative; height: 350px;">
                    <canvas id="statusChart"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>

<script id="server-data" type="application/json">{!! $jsonData !!}</script>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const serverData = JSON.parse(document.getElementById('server-data').textContent);
    const { rawData, stats, config } = serverData;

    // Biểu đồ Doanh Thu (Bar Chart)
    let labels = [];
    let chartData = [];
    if (config.month) {
        labels = ['Tuần 1', 'Tuần 2', 'Tuần 3', 'Tuần 4'];
        chartData = [0, 0, 0, 0];
        rawData.forEach(item => {
            if (item.status === 'DAGIAO') {
                const day = new Date(item.date).getDate();
                if (day <= 7) chartData[0] += item.amount;
                else if (day <= 14) chartData[1] += item.amount;
                else if (day <= 21) chartData[2] += item.amount;
                else chartData[3] += item.amount;
            }
        });
    } else {
        labels = ['Doanh thu'];
        chartData = [stats.revenue.success];
    }

    new Chart(document.getElementById('revenueChart'), {
        type: 'bar',
        data: {
            labels: labels,
            datasets: [{ label: 'Doanh thu (VNĐ)', data: chartData, backgroundColor: '#55d5d2' }]
        },
        options: { responsive: true, maintainAspectRatio: false }
    });

    // Biểu đồ Trạng Thái (HIỆN THEO SỐ LƯỢNG)
    new Chart(document.getElementById('statusChart'), {
        type: 'doughnut',
        data: {
            labels: ['Đã giao', 'Chờ thanh toán', 'Đã hủy'],
            datasets: [{
                data: [stats.counts.success, stats.counts.waiting, stats.counts.cancelled],
                backgroundColor: ['#2ecc71', '#f1c40f', '#e74c3c'],
                borderWidth: 0
            }]
        },
        options: { 
            plugins: { 
                legend: { position: 'bottom' },
                tooltip: {
                    callbacks: {
                        label: (ctx) => ` ${ctx.label}: ${ctx.raw} đơn hàng`
                    }
                }
            } 
        }
    });
});
</script>
</body>
</html>