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
            // Inisialisasi FullCalendar
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
                                phone_number: '{{ $order->customer->phone_number }}',
                                address: '{{ $order->customer->address }}',
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
                                phone_number: '{{ $order->customer->phone_number }}',
                                address: '{{ $order->customer->address }}',
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
                    // Ambil properti tambahan dari event
                    const props = event.extendedProps;

                    // Isi data ke modal
                    $('#po-number').text(props.po_number);
                    $('#customer-name').text(props.customer_name);
                    $('#phone-number').text(props.phone_number);
                    $('#address').text(props.address);
                    $('#description').text(props.description);
                    $('#order-date').text(moment(props.order_date).format('DD MMMM YYYY'));
                    $('#deadline-date').text(moment(props.deadline_date).format('DD MMMM YYYY'));
                    $('#raw-material').text(props.raw_material_quantity);
                    $('#size-s').text(props.size_s);
                    $('#size-m').text(props.size_m);
                    $('#size-l').text(props.size_l);
                    $('#size-xl').text(props.size_xl);
                    $('#total-price').text(props.total_price);
                    $('#dp').text(props.dp);
                    $('#remaining-payment').text(props.remaining_payment);
                    $('#cash-payment').text(props.cash_payment);

                    // Tampilkan modal
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
                            <div class="row">
                                <div class="col-md-6">
                                    <p class="text-muted"><strong>PO Number:</strong> <span id="po-number"></span></p>
                                    <p class="text-muted"><strong>Customer Name:</strong> <span id="customer-name"></span>
                                    </p>
                                    <p class="text-muted"><strong>Phone Number:</strong> <span id="phone-number"></span></p>
                                    <p class="text-muted"><strong>Address:</strong> <span id="address"></span></p>
                                    <p class="text-muted"><strong>Description:</strong> <span id="description"></span></p>
                                </div>
                                <div class="col-md-6">
                                    <p class="text-muted"><strong>Order Date:</strong> <span id="order-date"></span></p>
                                    <p class="text-muted"><strong>Deadline Date:</strong> <span id="deadline-date"></span>
                                    </p>
                                    <p class="text-muted"><strong>Raw Material Quantity:</strong> <span
                                            id="raw-material"></span> yard</p>
                                    <p class="text-muted"><strong>Sizes:</strong>
                                        S: <span id="size-s"></span> PCS,
                                        M: <span id="size-m"></span> PCS,
                                        L: <span id="size-l"></span> PCS,
                                        XL: <span id="size-xl"></span> PCS
                                    </p>
                                    <p class="text-muted"><strong>Total Price:</strong> Rp <span id="total-price"></span>
                                    </p>
                                    <p class="text-muted"><strong>DP:</strong> Rp <span id="dp"></span></p>
                                    <p class="text-muted"><strong>Remaining Payment:</strong> Rp <span
                                            id="remaining-payment"></span></p>
                                    <p class="text-muted"><strong>Cash Payment:</strong> Rp <span id="cash-payment"></span>
                                    </p>
                                </div>
                            </div>
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
