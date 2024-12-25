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

    @if ($errors->any())
        <script>
            let errorMessage = '<ul>';
            @foreach ($errors->all() as $error)
                errorMessage += '<li>{{ $error }}</li>';
            @endforeach
            errorMessage += '</ul>';

            Swal.fire({
                icon: 'error',
                title: 'Gagal!',
                html: errorMessage, // Menggunakan 'html' untuk menampilkan daftar error
                confirmButtonText: 'OK'
            });
        </script>
    @endif
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
                    <li class="breadcrumb-item"><a href="#">Orders</a></li>
                    <li class="breadcrumb-item"><a href="#">Quality Controll</a></li>
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
                    @if (!$order->reject_product)
                        <p class="text-center">Belum ada data produk yang reject</p>
                        <div class="card-footer mt-3">
                            <button type="button" class="btn btn-primary" data-toggle="modal"
                                data-target="#patternProcessModal" disabled>
                                Proses ke Packing
                            </button>
                            <p class="mt-2 text-danger">
                                Harap submit data reject terlebih dahulu untuk melanjutkan ke proses packing.
                            </p>
                        </div>
                    @else
                        <div class="card-footer mt-3">
                            <button type="button" class="btn btn-primary" data-toggle="modal"
                                data-target="#patternProcessModal">
                                Proses ke Packing
                            </button>
                        </div>
                    @endif

                </div>
            </div>
        </div>

        <div class="modal fade" id="patternProcessModal" tabindex="-1" aria-labelledby="patternProcessModalLabel"
            aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="patternProcessModalLabel">Konfirmasi Pemrosesan</h5>
                        <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
                    </div>
                    <div class="modal-body">
                        Apakah Anda yakin ingin memproses pesanan ini ke tahap <strong>Packing</strong>? Setelah anda
                        konfirmasi data reject tidak akan dapat di update lagi.
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                        <form action="/process-to-packing/{{ $order->id }}" method="POST">
                            @csrf
                            @method('PUT')
                            <button type="submit" class="btn btn-primary">Ya, Proses</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Row for Detail Reject -->
        <div class="row mt-4">
            <div class="col-md-12">
                <div class="card">
                    <h5 class="card-title">Detail Reject</h5>
                    <div class="card-body">
                        @if (!$order->reject_product)
                            <p class="text-center">Belum ada data produk yang reject</p>
                        @else
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>Size S</th>
                                        <th>Size M</th>
                                        <th>Size L</th>
                                        <th>Size XL</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>{{ $order->reject_product->size_s }} PCS</td>
                                        <td>{{ $order->reject_product->size_m }} PCS</td>
                                        <td>{{ $order->reject_product->size_l }} PCS</td>
                                        <td>{{ $order->reject_product->size_xl }} PCS</td>
                                    </tr>
                                </tbody>
                            </table>
                        @endif
                        <button class="btn btn-primary mt-3" data-toggle="modal" data-target="#updateRejectModal">
                            Update Data Reject
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal untuk Input Data Reject -->
        <div class="modal fade" id="updateRejectModal" tabindex="-1" aria-labelledby="updateRejectModalLabel"
            aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <form action="/update-reject-product/{{ $order->id }}" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="modal-header">
                            <h5 class="modal-title" id="updateRejectModalLabel">Update Data Reject</h5>
                            <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
                        </div>
                        <div class="modal-body">
                            <div class="mb-3">
                                <label for="size_s" class="form-label">Reject Size S</label>
                                <input type="number" class="form-control" id="size_s" name="size_s" min="0"
                                    value="{{ $order->reject_product->size_s ?? 0 }}" required>
                            </div>
                            <div class="mb-3">
                                <label for="size_m" class="form-label">Reject Size M</label>
                                <input type="number" class="form-control" id="size_m" name="size_m" min="0"
                                    value="{{ $order->reject_product->size_m ?? 0 }}" required>
                            </div>
                            <div class="mb-3">
                                <label for="size_l" class="form-label">Reject Size L</label>
                                <input type="number" class="form-control" id="size_l" name="size_l" min="0"
                                    value="{{ $order->reject_product->size_l ?? 0 }}" required>
                            </div>
                            <div class="mb-3">
                                <label for="size_xl" class="form-label">Reject Size XL</label>
                                <input type="number" class="form-control" id="size_xl" name="size_xl" min="0"
                                    value="{{ $order->reject_product->size_xl ?? 0 }}" required>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="submit" class="btn btn-primary">Save Changes</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

    </div>
@endsection
