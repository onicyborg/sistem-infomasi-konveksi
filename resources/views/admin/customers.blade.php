@extends('layouts.master')

@section('title')
    Customers - Admin
@endsection

@push('styles')
    <link href="{{ asset('vendor/datatables/css/jquery.dataTables.min.css') }}" rel="stylesheet">
@endpush

@push('scripts')
    <script src="{{ asset('vendor/datatables/js/jquery.dataTables.min.js') }}"></script>
    <script>
        $(document).ready(function() {
            // Inisialisasi DataTable
            const table = $('#customersTable').DataTable({
                processing: true,
                serverSide: true,
                ajax: '{{ route('customers.index') }}',
                columns: [{
                        data: 'name',
                        name: 'name',
                        title: 'Nama'
                    },
                    {
                        data: 'phone_number',
                        name: 'phone_number',
                        title: 'No. HP'
                    },
                    {
                        data: 'address',
                        name: 'address',
                        title: 'Alamat'
                    },
                    {
                        data: 'id',
                        name: 'actions',
                        title: 'Actions',
                        orderable: false,
                        searchable: false,
                        render: function(data, type, row) {
                            return `
                                <button class="btn btn-info btn-sm edit-customer" data-id="${data}" data-name="${row.name}" data-phone="${row.phone_number}" data-address="${row.address}">
                                    Edit
                                </button>
                                <button class="btn btn-danger btn-sm delete-customer" data-id="${data}">
                                    Delete
                                </button>
                            `;
                        }
                    }
                ]
            });

            // Tambah Customer
            $('#addCustomerForm').on('submit', function(e) {
                e.preventDefault();
                const formData = $(this).serialize();
                $.post('{{ route('customers.store') }}', formData, function(response) {
                    $('#addCustomerModal').modal('hide');
                    table.ajax.reload();

                    // SweetAlert success
                    Swal.fire({
                        title: 'Berhasil!',
                        text: 'Customer berhasil ditambahkan.',
                        icon: 'success',
                        confirmButtonText: 'OK'
                    });
                }).fail(function(error) {
                    // SweetAlert error
                    Swal.fire({
                        title: 'Error!',
                        text: 'Terjadi kesalahan saat menambahkan customer.',
                        icon: 'error',
                        confirmButtonText: 'OK'
                    });
                });
            });


            // Isi Data untuk Edit Customer
            $(document).on('click', '.edit-customer', function() {
                const id = $(this).data('id');
                const name = $(this).data('name');
                const phone = $(this).data('phone');
                const address = $(this).data('address');

                $('#editCustomerModal #edit_id').val(id);
                $('#editCustomerModal #edit_name').val(name);
                $('#editCustomerModal #edit_phone_number').val(phone);
                $('#editCustomerModal #edit_address').val(address);

                $('#editCustomerModal').modal('show');
            });

            // Edit Customer
            $('#editCustomerForm').on('submit', function(e) {
                e.preventDefault();
                const id = $('#edit_id').val();
                const formData = $(this).serialize();
                $.ajax({
                    url: `/admin/customers/${id}`,
                    type: 'PUT',
                    data: formData,
                    success: function(response) {
                        $('#editCustomerModal').modal('hide');
                        Swal.fire({
                            title: 'Berhasil!',
                            text: 'Customer berhasil diperbarui.',
                            icon: 'success',
                            confirmButtonText: 'OK'
                        });
                        table.ajax.reload();
                    },
                    error: function(error) {
                        Swal.fire({
                            title: 'Error!',
                            text: 'Terjadi kesalahan saat memperbarui customer.',
                            icon: 'error',
                            confirmButtonText: 'OK'
                        });
                    }
                });
            });

            // Delete Customer
            $(document).on('click', '.delete-customer', function() {
                const id = $(this).data('id');

                // Konfirmasi dengan SweetAlert
                Swal.fire({
                    title: 'Apakah Anda yakin?',
                    text: 'Customer ini akan dihapus secara permanen!',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Ya, hapus!',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        // Lakukan permintaan AJAX untuk menghapus dengan menambahkan token CSRF
                        $.ajax({
                            url: `/admin/customers/${id}`,
                            type: 'DELETE',
                            data: {
                                _token: $('meta[name="csrf-token"]').attr(
                                    'content') // Menambahkan token CSRF
                            },
                            success: function(response) {
                                Swal.fire({
                                    title: 'Berhasil!',
                                    text: 'Customer berhasil dihapus.',
                                    icon: 'success',
                                    confirmButtonText: 'OK'
                                });
                                table.ajax.reload();
                            },
                            error: function(error) {
                                Swal.fire({
                                    title: 'Error!',
                                    text: 'Terjadi kesalahan saat menghapus customer.',
                                    icon: 'error',
                                    confirmButtonText: 'OK'
                                });
                            }
                        });
                    }
                });
            });
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
                    <li class="breadcrumb-item">App</li>
                    <li class="breadcrumb-item active">Customers</li>
                </ol>
            </div>
        </div>

        <!-- Card untuk tabel -->
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h4 class="card-title">Data Customers</h4>
                <button class="btn btn-primary" data-toggle="modal" data-target="#addCustomerModal">Tambah Customer
                    Baru</button>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table id="customersTable" class="display" style="width:100%"></table>
                </div>
            </div>
        </div>

        <!-- Modal Tambah Customer -->
        <div class="modal fade" id="addCustomerModal" tabindex="-1" role="dialog" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <form id="addCustomerForm">
                    @csrf
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">Tambah Customer Baru</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <div class="form-group">
                                <label for="name">Nama</label>
                                <input type="text" name="name" id="name" class="form-control" required>
                            </div>
                            <div class="form-group">
                                <label for="phone_number">No. HP</label>
                                <input type="text" name="phone_number" id="phone_number" class="form-control" required>
                            </div>
                            <div class="form-group">
                                <label for="address">Alamat</label>
                                <textarea name="address" id="address" class="form-control" required></textarea>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="submit" class="btn btn-primary">Simpan</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- Modal Edit Customer -->
        <div class="modal fade" id="editCustomerModal" tabindex="-1" role="dialog" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <form id="editCustomerForm">
                    @csrf
                    @method('PUT')
                    <input type="hidden" id="edit_id" name="id">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">Edit Customer</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <div class="form-group">
                                <label for="edit_name">Nama</label>
                                <input type="text" name="name" id="edit_name" class="form-control" required>
                            </div>
                            <div class="form-group">
                                <label for="edit_phone_number">No. HP</label>
                                <input type="text" name="phone_number" id="edit_phone_number" class="form-control"
                                    required>
                            </div>
                            <div class="form-group">
                                <label for="edit_address">Alamat</label>
                                <textarea name="address" id="edit_address" class="form-control" required></textarea>
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
