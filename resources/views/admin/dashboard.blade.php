{{-- resources/views/admin/dashboard.blade.php --}}
{{-- Full Dashboard with Demo Data and Charts using Chart.js --}}

@extends('layouts.app')

@push('styles')
    <!-- Chart.js CDN -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        .stat-card { transition: transform 0.2s; }
        .stat-card:hover { transform: translateY(-2px); }
        .chart-container { position: relative; height: 400px; width: 100%; }
    </style>
@endpush

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="row mb-3">
        <div class="col-sm-12">
            <div class="page-title-box">
                <div class="float-left">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="#">Dashboard</a></li>
                        <li class="breadcrumb-item active">Overview</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>
    <!-- /Page Header -->

    @php
        // Demo Stats
        $demoStats = [
            'totalBookings' => 245,
            'pendingBookings' => 23,
            'activeAmc' => 56,
            'totalServices' => 18,
            'totalTechnicians' => 12,
            'totalCustomers' => 89
        ];

        // Demo Chart Data (Line Chart)
        $demoChartData = [
            'labels' => ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct'],
            'datasets' => [
                [
                    'label' => 'Total Bookings',
                    'data' => [65,59,80,81,56,55,40,67,72,90],
                    'borderColor' => '#1e0041',
                    'backgroundColor' => 'rgba(30,0,65,0.1)',
                    'fill' => true
                ],
                [
                    'label' => 'Completed',
                    'data' => [28,48,40,19,86,27,90,45,32,65],
                    'borderColor' => '#28a745',
                    'backgroundColor' => 'rgba(40,167,69,0.1)',
                    'fill' => true
                ]
            ]
        ];

        // Demo Pie Chart Data
        $demoPieData = [
            'labels' => ['Pending','In Progress','Completed'],
            'datasets' => [[
                'data' => [23,45,177],
                'backgroundColor' => ['#ffc107','#007bff','#28a745'],
                'borderWidth' => 1
            ]]
        ];

        // Demo Bookings
        $demoBookings = [
            ['id'=>245,'service'=>'AC Maintenance','status'=>'completed','technician'=>'John Doe','customer'=>'Alice Johnson','date'=>'2025-10-05'],
            ['id'=>244,'service'=>'Pest Control','status'=>'pending','technician'=>null,'customer'=>'Bob Smith','date'=>'2025-10-09'],
            ['id'=>243,'service'=>'Cleaning Service','status'=>'in_progress','technician'=>'Jane Smith','customer'=>'Charlie Brown','date'=>'2025-10-08'],
            ['id'=>242,'service'=>'AC Repair','status'=>'completed','technician'=>'Mike Johnson','customer'=>'Diana Prince','date'=>'2025-10-07'],
            ['id'=>241,'service'=>'Pest Inspection','status'=>'pending','technician'=>null,'customer'=>'Eve Davis','date'=>'2025-10-10'],
        ];
    @endphp

    <!-- Stats Row -->
    <div class="row mb-4">
        @foreach (['totalBookings'=>'Total Bookings','activeAmc'=>'Active AMC','pendingBookings'=>'Pending Bookings','totalServices'=>'Total Services'] as $key => $label)
        <div class="col-xl-3 col-sm-6 mb-3">
            <div class="card stat-card text-white h-100">
                @php
                    $bg = $key == 'totalBookings' ? 'bg-primary' : ($key=='activeAmc' ? 'bg-success' : ($key=='pendingBookings' ? 'bg-warning' : 'bg-info'));
                @endphp
                <div class="card-body {{ $bg }}">
                    <div class="d-flex justify-content-between">
                        <div>
                            <p class="mb-0 text-white">{{ $label }}</p>
                            <h4 class="mb-0 text-white">{{ $demoStats[$key] }}</h4>
                        </div>
                        <i class="feather-grid fs-24 opacity-75"></i>
                    </div>
                </div>
            </div>
        </div>
        @endforeach
    </div>
    <!-- /Stats Row -->

    <!-- Charts Row -->
    <div class="row mb-4">
        <div class="col-xl-8 mb-4">
            <div class="card">
                <div class="card-header"><h4 class="card-title mb-0">Bookings Over Time</h4></div>
                <div class="card-body">
                    <div class="chart-container">
                        <canvas id="bookingsChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-4 mb-4">
            <div class="card">
                <div class="card-header"><h4 class="card-title mb-0">Booking Status Distribution</h4></div>
                <div class="card-body">
                    <div class="chart-container">
                        <canvas id="statusPieChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- /Charts Row -->

    <!-- Recent Bookings Table -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header"><h4 class="card-title mb-0">Recent Bookings</h4></div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>ID</th><th>Service</th><th>Status</th><th>Technician</th><th>Customer</th><th>Date</th><th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($demoBookings as $booking)
                                <tr>
                                    <td>{{ $booking['id'] }}</td>
                                    <td>{{ $booking['service'] }}</td>
                                    <td>
                                        <span class="badge {{ $booking['status']=='pending' ? 'bg-warning' : ($booking['status']=='in_progress' ? 'bg-info' : 'bg-success') }}">
                                            {{ ucfirst(str_replace('_',' ',$booking['status'])) }}
                                        </span>
                                    </td>
                                    <td>{{ $booking['technician'] ?? 'Unassigned' }}</td>
                                    <td>{{ $booking['customer'] }}</td>
                                    <td>{{ $booking['date'] }}</td>
                                    <td>
                                        <a href="#" class="btn btn-sm btn-primary"><i class="fas fa-eye"></i></a>
                                        <a href="#" class="btn btn-sm btn-warning"><i class="fas fa-edit"></i></a>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- /Recent Bookings Table -->
</div>
@endsection

@push('scripts')
<script>
    // Line Chart: Bookings Over Time
    const demoChartData = @json($demoChartData);
    const ctxLine = document.getElementById('bookingsChart').getContext('2d');
    new Chart(ctxLine, {
        type: 'line',
        data: demoChartData,
        options: { responsive: true, maintainAspectRatio: false, scales: { y: { beginAtZero: true } } }
    });

    // Pie Chart: Booking Status Distribution
    const demoPieData = @json($demoPieData);
    const ctxPie = document.getElementById('statusPieChart').getContext('2d');
    new Chart(ctxPie, {
        type: 'pie',
        data: demoPieData,
        options: { responsive: true, maintainAspectRatio: false }
    });
</script>
@endpush
