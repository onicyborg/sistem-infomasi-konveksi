@extends('layouts.master')

@section('title')
    Kalender - Admin
@endsection

@push('styles')
    <link href="{{ asset('vendor/fullcalendar/css/fullcalendar.min.css') }}" rel="stylesheet">
@endpush

@push('scripts')
    <script src="{{ asset('vendor/moment/moment.min.js') }}"></script>
    <script src="{{ asset('vendor/fullcalendar/js/fullcalendar.min.js') }}"></script>
    <script>
        $(document).ready(function() {
            // Inisialisasi FullCalendar dengan styling yang disesuaikan
            $('#calendar').fullCalendar({
                header: {
                    left: 'prev,next today',
                    center: 'title',
                    right: 'month,agendaWeek,agendaDay'
                },
                slotDuration: "00:15:00",
                minTime: "08:00:00",
                maxTime: "19:00:00",
                height: $(window).height() - 100,
                handleWindowResize: true,
                events: [
                    @foreach ($orders as $order)
                        {
                            id: {{ $order->id }},
                            title: 'PO - {{ $order->customer->name }}',
                            start: '{{ $order->order_date }}',
                            className: 'bg-primary',
                            extendedProps: {
                                po_number: '{{ $order->po_number }}',
                                customer_name: '{{ $order->customer->name }}',
                                phone_number: '{{ $order->phone_number }}',
                                address: '{{ $order->address }}',
                                description: '{{ $order->description }}',
                                order_date: '{{ $order->order_date }}',
                                deadline_date: '{{ $order->deadline_date }}',
                                raw_material_quantity: '{{ $order->raw_material_quantity }}',
                                size_s: '{{ $order->size_s }}',
                                size_m: '{{ $order->size_m }}',
                                size_l: '{{ $order->size_l }}',
                                size_xl: '{{ $order->size_xl }}',
                                total_price: '{{ $order->total_price }}',
                                dp: '{{ $order->dp ?? 0 }}',
                                remaining_payment: '{{ $order->remaining_payment ?? 0 }}',
                                cash_payment: '{{ $order->cash_payment ?? 0 }}'
                            }
                        }, {
                            id: {{ $order->id }},
                            title: 'Deadline - {{ $order->customer->name }}',
                            start: '{{ $order->deadline_date }}',
                            className: 'bg-danger',
                            extendedProps: {
                                po_number: '{{ $order->po_number }}',
                                customer_name: '{{ $order->customer->name }}',
                                phone_number: '{{ $order->phone_number }}',
                                address: '{{ $order->address }}',
                                description: '{{ $order->description }}',
                                order_date: '{{ $order->order_date }}',
                                deadline_date: '{{ $order->deadline_date }}',
                                raw_material_quantity: '{{ $order->raw_material_quantity }}',
                                size_s: '{{ $order->size_s }}',
                                size_m: '{{ $order->size_m }}',
                                size_l: '{{ $order->size_l }}',
                                size_xl: '{{ $order->size_xl }}',
                                total_price: '{{ $order->total_price }}',
                                dp: '{{ $order->dp ?? 0 }}',
                                remaining_payment: '{{ $order->remaining_payment ?? 0 }}',
                                cash_payment: '{{ $order->cash_payment ?? 0 }}'
                            }
                        },
                    @endforeach
                ],
                editable: false,
                droppable: false,
                eventLimit: true,
                selectable: false,
                eventClick: function(event) {
                    // Tampilkan detail order di modal
                    const props = event.extendedProps;
                    $('#event-modal .modal-title').text(event.title);
                    $('#event-modal .modal-body').html(`
                <p><strong>PO Number:</strong> ${props.po_number}</p>
                <p><strong>Customer Name:</strong> ${props.customer_name}</p>
                <p><strong>Phone Number:</strong> ${props.phone_number}</p>
                <p><strong>Address:</strong> ${props.address}</p>
                <p><strong>Description:</strong> ${props.description}</p>
                <p><strong>Order Date:</strong> ${moment(props.order_date).format('DD MMMM YYYY')}</p>
                <p><strong>Deadline Date:</strong> ${moment(props.deadline_date).format('DD MMMM YYYY')}</p>
                <p><strong>Raw Material Quantity:</strong> ${props.raw_material_quantity} yard</p>
                <p><strong>Sizes:</strong> S: ${props.size_s}, M: ${props.size_m}, L: ${props.size_l}, XL: ${props.size_xl}</p>
                <p><strong>Total Price:</strong> Rp ${props.total_price}</p>
                <p><strong>DP:</strong> Rp ${props.dp}</p>
                <p><strong>Remaining Payment:</strong> Rp ${props.remaining_payment}</p>
                <p><strong>Cash Payment:</strong> Rp ${props.cash_payment}</p>
            `);
                    $('#event-modal').modal('show');
                }
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
                    <p class="mb-0">Kalender Pesanan</p>
                </div>
            </div>
            <div class="col-sm-6 p-md-0 justify-content-sm-end mt-2 mt-sm-0 d-flex">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item">App</li>
                    <li class="breadcrumb-item active">Kalender</li>
                </ol>
            </div>
        </div>

        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-body">
                        <div id="calendar" class="app-fullcalendar"></div>
                    </div>
                </div>
            </div>

            <!-- Modal untuk detail event -->
            <div class="modal fade none-border" id="event-modal">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h4 class="modal-title">Event Details</h4>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <!-- Konten detail akan diisi oleh JavaScript -->
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
