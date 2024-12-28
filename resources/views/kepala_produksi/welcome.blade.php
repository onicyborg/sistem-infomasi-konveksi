@extends('layouts.master')

@section('title')
    Dashboard - Kepala Produksi
@endsection

@push('styles')
@endpush

@push('scripts')
    <script src="{{ asset('vendor/chart.js/Chart.bundle.min.js') }}"></script>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            // Ambil data penjualan dari server
            fetch('/sales-data')
                .then(response => response.json())
                .then(data => {
                    // Data dari controller
                    const months = data.months;
                    const totals = data.totals;

                    // Konfigurasi Chart.js
                    const barChart_2 = document.getElementById("barChart_2").getContext('2d');
                    const barChart_2gradientStroke = barChart_2.createLinearGradient(0, 0, 0, 250);
                    barChart_2gradientStroke.addColorStop(0, "rgba(26, 51, 213, 1)");
                    barChart_2gradientStroke.addColorStop(1, "rgba(26, 51, 213, 0.5)");

                    new Chart(barChart_2, {
                        type: 'bar',
                        data: {
                            defaultFontFamily: 'Poppins',
                            labels: months, // Ambil nama bulan dari controller
                            datasets: [{
                                label: "Total Pesanan",
                                data: totals, // Ambil jumlah penjualan dari controller
                                borderColor: barChart_2gradientStroke,
                                borderWidth: "0",
                                backgroundColor: barChart_2gradientStroke,
                                hoverBackgroundColor: barChart_2gradientStroke
                            }]
                        },
                        options: {
                            legend: false,
                            scales: {
                                yAxes: [{
                                    ticks: {
                                        beginAtZero: true
                                    }
                                }],
                                xAxes: [{
                                    barPercentage: 0.5
                                }]
                            }
                        }
                    });
                })
                .catch(error => console.error('Error fetching sales data:', error));
        });
    </script>
@endpush

@section('content')
    <div class="container-fluid">
        <div class="row page-titles mx-0">
            <div class="col-sm-6 p-md-0">
                <div class="welcome-text">
                    <h4>Hi, welcome back {{ Auth::user()->name }}!</h4>
                    <p class="mb-0">Your business dashboard template</p>
                </div>
            </div>
            <div class="col-sm-6 p-md-0 justify-content-sm-end mt-2 mt-sm-0 d-flex">
                <ol class="breadcrumb">
                    {{-- <li class="breadcrumb-item">App</li> --}}
                    <li class="breadcrumb-item active">Dashboard</li>
                </ol>
            </div>
        </div>
        <div class="row">
            <div class="col-lg-3 col-sm-6">
                <div class="card">
                    <div class="stat-widget-two card-body">
                        <div class="stat-content">
                            <div class="stat-text">Total Customer</div>
                            <div class="stat-digit">{{ $customer }}</div>
                        </div>
                        <div class="progress">
                            <div class="progress-bar progress-bar-success w-100" role="progressbar" aria-valuenow="100"
                                aria-valuemin="0" aria-valuemax="100"></div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-sm-6">
                <div class="card">
                    <div class="stat-widget-two card-body">
                        <div class="stat-content">
                            <div class="stat-text">Total Pesanan Masuk</div>
                            <div class="stat-digit">{{ $po }}</div>
                        </div>
                        <div class="progress">
                            <div class="progress-bar progress-bar-primary w-100" role="progressbar" aria-valuenow="100"
                                aria-valuemin="0" aria-valuemax="100"></div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-sm-6">
                <div class="card">
                    <div class="stat-widget-two card-body">
                        <div class="stat-content">
                            <div class="stat-text">Pesanan Selesai</div>
                            <div class="stat-digit">{{ $completedOrdersCount }}</div>
                        </div>
                        <div class="progress">
                            <div class="progress-bar progress-bar-warning w-100" role="progressbar" aria-valuenow="100"
                                aria-valuemin="0" aria-valuemax="100"></div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-sm-6">
                <div class="card">
                    <div class="stat-widget-two card-body">
                        <div class="stat-content">
                            <div class="stat-text">Pesanan Diproses</div>
                            <div class="stat-digit">{{ $onprogressOrdersCount }}</div>
                        </div>
                        <div class="progress">
                            <div class="progress-bar progress-bar-danger w-100" role="progressbar" aria-valuenow="100"
                                aria-valuemin="0" aria-valuemax="100"></div>
                        </div>
                    </div>
                </div>
                <!-- /# card -->
            </div>
            <!-- /# column -->
        </div>
        <div class="row">
            <div class="col-xl-6 col-lg-6 col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">Grafik Pesanan 7 Bulan Terakhir</h4>
                    </div>
                    <div class="card-body">
                        <canvas id="barChart_2"></canvas>
                    </div>
                </div>
            </div>
            <div class="col-xl-6 col-lg-6 col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">Pesanan Masuk</h4>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table mb-0">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Nama</th>
                                        <th>Tanggal Pesan</th>
                                        <th>Deadline</th>
                                        <th>Quantity</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($orderData as $item)
                                        <tr>
                                            <td>
                                                <div class="round-img">
                                                    <a href=""><img width="35"
                                                            src="{{ asset('images/avatar/1.png') }}" alt=""></a>
                                                </div>
                                            </td>
                                            <td>{{ $item->customer_name }}</td>
                                            <td>{{ \Carbon\Carbon::parse($item->order_date)->format('d-m-Y') }}</td>
                                            <td>{{ \Carbon\Carbon::parse($item->deadline_date)->format('d-m-Y') }}</td>
                                            <td>{{ $item->total_quantity }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
