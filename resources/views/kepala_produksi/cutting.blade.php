@extends('layouts.master')

@section('title')
    Pesanan Cutting - Kepala Produksi
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

            $('.view-customer').on('click', function() {
                const customer = $(this).data('customer');
                $('#customerModal .modal-body').html(`
                <p><strong>Nama:</strong> ${customer.name}</p>
                <p><strong>No. Whatsapp:</strong> ${customer.phone_number}</p>
                <p><strong>Alamat:</strong> ${customer.address}</p>
            `);
                $('#customerModal').modal('show');
            });
        });

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
                    <h4>Hi, welcome back {{ Auth::user()->name }}!</h4>
                </div>
            </div>
            <div class="col-sm-6 p-md-0 justify-content-sm-end mt-2 mt-sm-0 d-flex">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item">Orders</li>
                    <li class="breadcrumb-item active">Cutting</li>
                </ol>
            </div>
        </div>

        <!-- Card untuk menampilkan tabel -->
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h4 class="card-title">List Pesanan Pada Proses Cutting</h4>
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
                                    <td class="text-center">
                                        <a href="/kepala-produksi/orders-cutting/{{ $order->id }}"
                                            class="btn btn-info btn-sm"><i class="ti-panel"></i></a>
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
    </div>
@endsection
