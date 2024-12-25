@extends('layouts.master')

@section('title')
    Pesanan - Admin
@endsection

@push('styles')
    <link href="{{ asset('vendor/datatables/css/jquery.dataTables.min.css') }}" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('vendor/select2/css/select2.min.css') }}">
@endpush

@push('scripts')
    <script src="{{ asset('vendor/datatables/js/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('vendor/select2/js/select2.full.min.js') }}"></script>
    <script>
        $(document).ready(function() {
            $('#ordersTable').DataTable({
                createdRow: function(row, data, index) {
                    $(row).addClass('selected')
                }
            });

            $("#customer_id").select2({
                placeholder: "Pilih Customer",
                allowClear: true
            }).val(null).trigger('change'); // Mengatur nilai awal kosong


            // Handle modal data
            $('.view-customer').on('click', function() {
                const customer = $(this).data('customer');
                $('#customerModal .modal-body').html(`
                <p><strong>Nama:</strong> ${customer.name}</p>
                <p><strong>No. Whatsapp:</strong> ${customer.phone_number}</p>
                <p><strong>Alamat:</strong> ${customer.address}</p>
            `);
                $('#customerModal').modal('show');
            });

            $('.view-order').on('click', function() {
                const order = $(this).data('order');
                $('#orderModal .modal-body').html(`
                <p><strong>PO Number:</strong> ${order.po_number}</p>
                <p><strong>Deskripsi:</strong> ${order.description}</p>
                <p><strong>Tanggal Order:</strong> ${order.order_date}</p>
                <p><strong>Tanggal Deadline:</strong> ${order.deadline_date}</p>
            `);
                $('#orderModal').modal('show');
            });

            $('.pay-order').on('click', function() {
                const orderId = $(this).data('order-id');
                $('#paymentForm').attr('action', `/orders/${orderId}/pay`);
                $('#paymentModal').modal('show');
            });

            // Hitung material needed berdasarkan ukuran
            function calculateMaterial() {
                let size_s = parseInt($('#size_s').val()) || 0;
                let size_m = parseInt($('#size_m').val()) || 0;
                let size_l = parseInt($('#size_l').val()) || 0;
                let size_xl = parseInt($('#size_xl').val()) || 0;

                // Perhitungan material dalam meter
                let material_needed = (size_s * 2) + (size_m * 2) + (size_l * 2.5) + (size_xl * 3);
                let material_needed_in_yard = material_needed * 1.09361; // Konversi ke yard

                $('#material_needed').val(material_needed_in_yard.toFixed(2));
            }

            // Event listener untuk perubahan ukuran
            $('#size_s, #size_m, #size_l, #size_xl').on('input', function() {
                calculateMaterial();
            });

            // Validasi raw_material_quantity
            $('#raw_material_quantity').on('input', function() {
                let material_needed = parseFloat($('#material_needed').val()) || 0;
                let raw_material_quantity = parseFloat($(this).val()) || 0;
                if (raw_material_quantity < material_needed) {
                    $('#raw_material_quantity_error').show();
                } else {
                    $('#raw_material_quantity_error').hide();
                }
            });

            // Toggle pembayaran
            $('#paymentToggle').prop('checked', false);
            $('#dpSection').hide(); // By default, DP section is hidden

            // Toggle pembayaran DP atau cash lunas
            $('#paymentToggle').on('change', function() {
                if ($(this).prop('checked')) {
                    $('#dpSection').show(); // Show DP section if DP is selected
                } else {
                    $('#dpSection').hide(); // Hide DP section if cash payment is selected
                }
            });
        });
    </script>

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
                    <h4>Hi, welcome back {{ Auth::user()->name }}!</h4>
                    <p class="mb-0">Your business dashboard template</p>
                </div>
            </div>
            <div class="col-sm-6 p-md-0 justify-content-sm-end mt-2 mt-sm-0 d-flex">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item">App</li>
                    <li class="breadcrumb-item active">Pesanan</li>
                </ol>
            </div>
        </div>

        <!-- Card untuk menampilkan tabel -->
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h4 class="card-title">Data Pesanan</h4>
                <button class="btn btn-primary" data-toggle="modal" data-target="#addOrderModal">
                    Tambah Pesanan Baru
                </button>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table id="ordersTable" class="display" style="width:100%">
                        <thead>
                            <tr>
                                <th>Nama Customer</th>
                                <th>PO Number</th>
                                <th>Deskripsi</th>
                                <th class="text-center">Tanggal Order</th>
                                <th class="text-center">Deadline</th>
                                <th class="text-center">Status Pembayaran</th>
                                <th class="text-center">Progress</th>
                                <th class="text-center">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($orders as $order)
                                <tr>
                                    <td>
                                        <a href="javascript:void(0)" class="view-customer text-primary"
                                            data-customer="{{ json_encode($order->customer) }}">
                                            {{ $order->customer->name }}
                                        </a>
                                    </td>
                                    <td class="text-muted">{{ $order->po_number }}</td>
                                    <td class="text-muted">{{ $order->description }}</td>
                                    <td class="text-muted text-center">
                                        {{ \Carbon\Carbon::parse($order->order_date)->format('d-m-Y') }}</td>
                                    <td class="text-muted text-center">
                                        {{ \Carbon\Carbon::parse($order->deadline_date)->format('d-m-Y') }}</td>
                                    <td class="text-muted text-center">
                                        @if ($order->remaining_payment == 0)
                                            <span class="badge badge-success">Lunas</span>
                                        @else
                                            <span class="badge badge-warning">Belum Lunas</span>
                                        @endif
                                    </td>
                                    <td class="text-muted">
                                        @if (
                                            $order->production_status->pattern_status == 'done' &&
                                                $order->production_status->cutting_status == 'done' &&
                                                $order->production_status->sewing_status == 'done' &&
                                                $order->production_status->qc_status == 'done' &&
                                                $order->production_status->packing_status == 'done')
                                            <span class="badge badge-success">Selesai</span>
                                        @elseif (
                                            $order->production_status->pattern_status == 'done' &&
                                                $order->production_status->cutting_status == 'done' &&
                                                $order->production_status->sewing_status == 'done' &&
                                                $order->production_status->qc_status == 'done')
                                            <span class="badge badge-info">Dipacking</span>
                                        @elseif (
                                            $order->production_status->pattern_status == 'done' &&
                                                $order->production_status->cutting_status == 'done' &&
                                                $order->production_status->sewing_status == 'done')
                                            <span class="badge badge-info">Quality Controll</span>
                                        @elseif ($order->production_status->pattern_status == 'done' && $order->production_status->cutting_status == 'done')
                                            <span class="badge badge-info">Dijahit</span>
                                        @elseif ($order->production_status->pattern_status == 'done')
                                            <span class="badge badge-info">Cutting</span>
                                        @elseif ($order->production_status->pattern_status == 'process')
                                            <span class="badge badge-info">Pembuatan Pola</span>
                                        @else
                                            <span class="badge badge-warning">Pending</span>
                                        @endif
                                    </td>
                                    <td>
                                        <a href="/admin/orders/{{ $order->id }}" class="btn btn-info btn-sm"><i
                                                class="ti-panel"></i></a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr>
                                <th>Nama Customer</th>
                                <th>PO Number</th>
                                <th>Deskripsi</th>
                                <th class="text-center">Tanggal Order</th>
                                <th class="text-center">Deadline</th>
                                <th class="text-center">Status Pembayaran</th>
                                <th class="text-center">Progress</th>
                                <th class="text-center">Action</th>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>

        <!-- Modal untuk detail customer -->
        <div class="modal fade" id="customerModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Detail Customer</h5>
                        <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
                    </div>
                    <div class="modal-body">
                        <!-- Isi data customer di sini -->
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal untuk tambah order baru -->
        <div class="modal fade" id="addOrderModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog">
                <form action="/admin/orders-store" method="POST">
                    @csrf
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">Tambah Pesanan Baru</h5>
                            <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
                        </div>
                        <div class="modal-body">
                            <div class="mb-3">
                                <label for="customer_id" class="form-label">Pelanggan</label>
                                <select id="customer_id" name="customer_id" class="form-control">
                                    @foreach ($customers as $customer)
                                        <option value="{{ $customer->id }}">{{ $customer->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="mb-3">
                                <label for="description" class="form-label">Deskripsi</label>
                                <textarea id="description" name="description" class="form-control" required></textarea>
                            </div>
                            <div class="mb-3">
                                <label for="order_date" class="form-label">Tanggal Pesanan</label>
                                <input type="date" id="order_date" name="order_date" class="form-control" required>
                            </div>
                            <div class="mb-3">
                                <label for="deadline_date" class="form-label">Tanggal Batas Waktu</label>
                                <input type="date" id="deadline_date" name="deadline_date" class="form-control"
                                    required>
                            </div>

                            <!-- Size Fields -->
                            <div class="mb-3">
                                <label for="size_s" class="form-label">Ukuran S</label>
                                <input type="number" id="size_s" name="size_s" class="form-control">
                            </div>
                            <div class="mb-3">
                                <label for="size_m" class="form-label">Ukuran M</label>
                                <input type="number" id="size_m" name="size_m" class="form-control">
                            </div>
                            <div class="mb-3">
                                <label for="size_l" class="form-label">Ukuran L</label>
                                <input type="number" id="size_l" name="size_l" class="form-control">
                            </div>
                            <div class="mb-3">
                                <label for="size_xl" class="form-label">Ukuran XL</label>
                                <input type="number" id="size_xl" name="size_xl" class="form-control">
                            </div>

                            <!-- Material Needed (readonly) -->
                            <div class="mb-3">
                                <label for="material_needed" class="form-label">Material yang Dibutuhkan (yard)</label>
                                <input type="text" id="material_needed" name="material_needed" class="form-control"
                                    readonly>
                            </div>

                            <!-- Raw Material Quantity -->
                            <div class="mb-3">
                                <label for="raw_material_quantity" class="form-label">Jumlah Material Mentah
                                    (yard)</label>
                                <input type="number" id="raw_material_quantity" name="raw_material_quantity"
                                    class="form-control">
                                <small id="raw_material_quantity_error" class="text-danger" style="display:none;">Jumlah
                                    material harus lebih besar atau sama dengan material yang dibutuhkan.</small>
                            </div>

                            <!-- Payment Method Toggle -->
                            <div class="form-check form-switch mb-3">
                                <input class="form-check-input" type="checkbox" id="paymentToggle"
                                    name="payment_toggle">
                                <label class="form-check-label" for="paymentToggle">Pembayaran DP</label>
                            </div>

                            <!-- DP or Full Payment -->
                            <div class="mb-3" id="dpSection">
                                <label for="dp" class="form-label">DP</label>
                                <input type="number" id="dp" name="dp" class="form-control">
                            </div>

                            <div class="mb-3" id="fullPaymentSection" style="display: none;">
                                <label for="total_price" class="form-label">Harga Total</label>
                                <input type="number" id="total_price" name="total_price" class="form-control" readonly>
                            </div>

                            <!-- Cash Payment -->
                            <div class="mb-3">
                                <label for="total_price" class="form-label">Harga Total</label>
                                <input type="number" id="total_price" name="total_price" class="form-control">
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="submit" class="btn btn-primary">Simpan</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
