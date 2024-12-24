@extends('layouts.master')

@section('title')
    Detail Pesanan - Admin
@endsection

@push('styles')
    <link href="{{ asset('vendor/datatables/css/jquery.dataTables.min.css') }}" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('vendor/select2/css/select2.min.css') }}">
    <style>
        .card {
            border-radius: 10px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            padding: 15px;
        }

        .progress-container {
            overflow-x: auto;
            /* Tambahkan scroll horizontal */
            white-space: nowrap;
            /* Elemen akan tetap dalam satu baris */
        }

        .progress-step {
            display: flex;
            justify-content: space-between;
            align-items: center;
            position: relative;
            min-width: 600px;
            /* Tambahkan lebar minimum agar responsif */
            margin-top: 15px;
        }

        .progress-step .line {
            position: absolute;
            top: 50%;
            left: 0;
            right: 0;
            height: 2px;
            background-color: #e0e0e0;
            z-index: 1;
            transform: translateY(-50%);
        }

        .progress-step .step {
            text-align: center;
            flex: 1;
            z-index: 2;
            position: relative;
        }

        .progress-step .circle {
            width: 30px;
            height: 30px;
            border-radius: 50%;
            background-color: #e0e0e0;
            line-height: 30px;
            text-align: center;
            font-size: 12px;
            margin: 0 auto;
        }

        .progress-step .circle.active {
            background-color: #4caf50;
            color: #fff;
        }

        .progress-step .label {
            margin-top: 10px;
            font-size: 12px;
            color: #6c757d;
        }

        .progress-step .label.active {
            font-weight: bold;
            color: #4caf50;
        }
    </style>
@endpush

@push('scripts')
    <script>
        @if (session('success'))
            Swal.fire({
                icon: 'success',
                title: 'Berhasil!',
                text: '{{ session('success') }}',
                confirmButtonText: 'OK'
            });
        @endif
    </script>
@endpush

@section('content')
    <div class="container-fluid">
        <div class="row page-titles mx-0">
            <div class="col-sm-6 p-md-0">
                <div class="welcome-text">
                    <h4>Detail Pesanan</h4>
                    <p class="mb-0">Informasi lengkap mengenai pesanan ini</p>
                </div>
            </div>
            <div class="col-sm-6 p-md-0 justify-content-sm-end mt-2 mt-sm-0 d-flex">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="#">App</a></li>
                    <li class="breadcrumb-item"><a href="#">Pesanan</a></li>
                    <li class="breadcrumb-item active">Detail Pesanan</li>
                </ol>
            </div>
        </div>

        <!-- Row for Customer and Pesanan Detail -->
        <div class="row">
            <!-- Card Detail Customer -->
            <div class="col-md-3">
                <div class="card p-3">
                    <h5 class="card-title">Detail Customer</h5>
                    <p><strong>Nama:</strong> {{ $order->customer->name }}</p>
                    <p><strong>Telepon:</strong> {{ $order->customer->phone_number }}</p>
                    <p><strong>Alamat:</strong> {{ $order->customer->address }}</p>
                </div>
            </div>

            <!-- Card Detail Pesanan -->
            <div class="col-md-6">
                <div class="card p-3">
                    <h5 class="card-title">Detail Pesanan</h5>
                    <div class="row">
                        <!-- Detail PO -->
                        <div class="col-md-8">
                            <p><strong>Nomor PO:</strong> {{ $order->po_number }}</p>
                            <p><strong>Deskripsi:</strong> {{ $order->description }}</p>
                            <p><strong>Tanggal Pesanan:</strong>
                                {{ \Carbon\Carbon::parse($order->order_date)->format('d-m-Y') }}</p>
                            <p><strong>Tanggal Deadline:</strong>
                                {{ \Carbon\Carbon::parse($order->deadline_date)->format('d-m-Y') }}</p>
                            <p><strong>Total Bahan:</strong>
                                {{ $order->raw_material_quantity }} Yard</p>
                        </div>
                        <!-- Detail Total Ukuran -->
                        <div class="col-md-4">
                            <p><strong>Total Ukuran:</strong></p>
                            <ul>
                                <li>S: {{ $order->size_s }} pcs</li>
                                <li>M: {{ $order->size_m }} pcs</li>
                                <li>L: {{ $order->size_l }} pcs</li>
                                <li>XL: {{ $order->size_xl }} pcs</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Card Detail Pembayaran -->
            <div class="col-md-3">
                <div class="card p-3">
                    <h5 class="card-title">Detail Pembayaran</h5>
                    <p><strong>Total Price:</strong> Rp {{ number_format($order->total_price, 0, ',', '.') }}</p>
                    <p><strong>DP:</strong> Rp {{ number_format($order->dp, 0, ',', '.') }}</p>
                    <p><strong>Sisa Bayar:</strong> Rp {{ number_format($order->remaining_payment, 0, ',', '.') }}</p>
                    <p><strong>Status:</strong>
                        <span class="badge {{ $order->remaining_payment == 0 ? 'badge-success' : 'badge-danger' }}">
                            {{ $order->remaining_payment == 0 ? 'Lunas' : 'Belum Lunas' }}
                        </span>
                    </p>
                    @if ($order->remaining_payment > 0)
                        <div class="row">
                            <div class="col-md-8">
                                <button class="btn btn-primary btn-sm btn-block mt-2" data-toggle="modal"
                                    data-target="#confirmPaymentModal">
                                    Pelunasan
                                </button>
                            </div>
                            <div class="col-md-4">
                                <a href="{{ route('order.print', $order->id) }}" target="_blank"
                                    class="btn btn-secondary btn-block btn-sm mt-2">
                                    <i class="ti-printer"></i>
                                </a>
                            </div>
                        </div>
                    @else
                        <div class="row">
                            <div class="col-md-12">
                                <a href="{{ route('order.print', $order->id) }}" target="_blank"
                                    class="btn btn-secondary btn-block btn-sm mt-2">
                                    <i class="ti-printer"></i> Print Invoice
                                </a>
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Modal -->
            <div class="modal fade" id="confirmPaymentModal" tabindex="-1" aria-labelledby="confirmPaymentModalLabel"
                aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="confirmPaymentModalLabel">Konfirmasi Pembayaran</h5>
                            <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
                        </div>
                        <div class="modal-body">
                            <p>Apakah Anda yakin client telah membayar sisa pembayaran sebesar:</p>
                            <h4 class="text-center text-primary">Rp
                                {{ number_format($order->remaining_payment, 0, ',', '.') }}</h4>
                        </div>
                        <div class="modal-footer">
                            <form action="/admin/update-payment/{{ $order->id }}" method="POST">
                                @csrf
                                @method('PUT')
                                <button type="submit" class="btn btn-success">Konfirmasi</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

        </div>

        <!-- Row for Progress Pesanan -->
        <div class="row mt-4">
            <div class="col-md-12">
                <div class="card">
                    <h5 class="card-title">Progress Pesanan</h5>
                    <div class="progress-container">
                        <div class="progress-step">
                            <div class="line"></div>
                            @php
                                $steps = [
                                    'Pending',
                                    'Pembuatan Pola',
                                    'Cutting',
                                    'Proses Jahit',
                                    'Proses QC',
                                    'Proses Packing',
                                    'Pesanan Siap',
                                ];
                                $icons = [
                                    'ti-time', // Pending
                                    'ti-pencil-alt', // Pembuatan Pola
                                    'ti-cut', // Cutting
                                    'ti-ruler-pencil', // Proses Jahit
                                    'ti-check-box', // Proses QC
                                    'ti-package', // Proses Packing
                                    'ti-check', // Pesanan Siap
                                ];

                                // Menyimpan status produksi secara eksplisit
                                $patternStatus = $order->production_status->pattern_status;
                                $cuttingStatus = $order->production_status->cutting_status;
                                $sewingStatus = $order->production_status->sewing_status;
                                $qcStatus = $order->production_status->qc_status;
                                $packingStatus = $order->production_status->packing_status;
                            @endphp

                            @foreach ($steps as $index => $step)
                                @php
                                    // Menentukan apakah langkah ini aktif
                                    $isActive = false;

                                    // Logika pengecekan status untuk setiap langkah
                                    if ($index == 0) {
                                        // Step pertama (Pending) selalu aktif
                                        $isActive = true;
                                    } elseif ($index == 1 && in_array($patternStatus, ['Process', 'Done'])) {
                                        // Jika Pembuatan Pola sudah dalam status 'Process' atau 'Done'
                                        $isActive = true;
                                    } elseif ($index == 2 && in_array($cuttingStatus, ['Process', 'Done'])) {
                                        // Jika Cutting sudah dalam status 'Process' atau 'Done'
                                        $isActive = true;
                                    } elseif ($index == 3 && in_array($sewingStatus, ['Process', 'Done'])) {
                                        // Jika Proses Jahit sudah dalam status 'Process' atau 'Done'
                                        $isActive = true;
                                    } elseif ($index == 4 && in_array($qcStatus, ['Process', 'Done'])) {
                                        // Jika Proses QC sudah dalam status 'Process' atau 'Done'
                                        $isActive = true;
                                    } elseif ($index == 5 && in_array($packingStatus, ['Process', 'Done'])) {
                                        // Jika Proses Packing sudah dalam status 'Process' atau 'Done'
                                        $isActive = true;
                                    } elseif (
                                        $index == 6 &&
                                        $patternStatus == 'Done' &&
                                        $cuttingStatus == 'Done' &&
                                        $sewingStatus == 'Done' &&
                                        $qcStatus == 'Done' &&
                                        $packingStatus == 'Done'
                                    ) {
                                        // Jika semua langkah sebelumnya sudah 'Done'
                                        $isActive = true;
                                    }
                                @endphp

                                <div class="step">
                                    <div class="circle {{ $isActive ? 'active' : '' }}">
                                        <i class="{{ $icons[$index] }} {{ $isActive ? 'text-white' : '' }}"></i>
                                    </div>
                                    <span class="label {{ $isActive ? 'active' : '' }}">{{ $step }}</span>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Row for Detail Reject -->
        <div class="row mt-4">
            <div class="col-md-12">
                <div class="card">
                    <h5 class="card-title">Detail Reject</h5>
                    @if ($order->reject_products == null)
                        <p class="text-center">Belum ada data</p>
                    @else
                        <ul>
                            @foreach ($order->reject_products as $reject)
                                <li>{{ $reject->description }} ({{ $reject->quantity }} pcs)</li>
                            @endforeach
                        </ul>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection