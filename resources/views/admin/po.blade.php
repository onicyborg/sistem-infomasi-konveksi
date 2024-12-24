<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice PO</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
        }
        .container {
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
        }
        .header h1 {
            margin: 0;
            font-size: 24px;
        }
        .header p {
            margin: 5px 0;
            font-size: 14px;
        }
        .section-title {
            margin-top: 30px;
            margin-bottom: 10px;
            font-size: 18px;
            font-weight: bold;
            color: #333;
        }
        .info {
            margin-bottom: 20px;
        }
        .info p {
            margin: 5px 0;
        }
        .table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        .table th, .table td {
            border: 1px solid #ddd;
            padding: 10px;
            text-align: left;
            font-size: 14px;
        }
        .table th {
            background-color: #f2f2f2;
        }
        .table tfoot th {
            text-align: right;
            background-color: #f9f9f9;
            font-weight: bold;
        }
        .badge {
            margin-top: 10px;
            display: inline-block;
            padding: 5px 10px;
            border-radius: 5px;
            font-size: 12px;
            color: #fff;
        }
        .badge.success {
            background-color: #28a745;
        }
        .badge.danger {
            background-color: #dc3545;
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Header -->
        <div class="header">
            <h1>PURCHASE ORDER</h1>
            <p><strong>Order ID:</strong> {{ $order->po_number }}</p>
        </div>

        <!-- Customer Details -->
        <div class="info">
            <h3 class="section-title">Customer Details</h3>
            <p><strong>Name:</strong> {{ $order->customer->name }}</p>
            <p><strong>Phone:</strong> {{ $order->customer->phone_number }}</p>
            <p><strong>Address:</strong> {{ $order->customer->address }}</p>
        </div>

        <!-- Order Dates -->
        <div class="info">
            <h3 class="section-title">Order Information</h3>
            <p><strong>Order Date:</strong> {{ \Carbon\Carbon::parse($order->order_date)->format('d M Y') }}</p>
            <p><strong>Deadline:</strong> {{ \Carbon\Carbon::parse($order->deadline_date)->format('d M Y') }}</p>
        </div>

        <!-- Order Table -->
        <div>
            <h3 class="section-title">Order Details</h3>
            <table class="table">
                <thead>
                    <tr>
                        <th>Size</th>
                        <th>Quantity</th>
                        <th>Unit (Meter)</th>
                        <th>Total (Meter)</th>
                        <th>Total (Yard)</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                        $totalMeters = 0;
                        $totalYards = 0;
                        $yardConversion = 1.09361;
                        $sizes = [
                            'S' => 2,
                            'M' => 2,
                            'L' => 2.5,
                            'XL' => 3,
                        ];
                    @endphp
                    @foreach ($sizes as $size => $unit)
                        @php
                            $quantity = $order->{'size_' . strtolower($size)};
                            $totalMeter = $quantity * $unit;
                            $totalYard = $totalMeter * $yardConversion;
                            $totalMeters += $totalMeter;
                            $totalYards += $totalYard;
                        @endphp
                        <tr>
                            <td>{{ $size }}</td>
                            <td>{{ $quantity }}</td>
                            <td>{{ $unit }}</td>
                            <td>{{ number_format($totalMeter, 2, ',', '.') }}</td>
                            <td>{{ number_format($totalYard, 2, ',', '.') }}</td>
                        </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr>
                        <th colspan="3">Total</th>
                        <th>{{ number_format($totalMeters, 2, ',', '.') }} m</th>
                        <th>{{ number_format($totalYards, 2, ',', '.') }} yd</th>
                    </tr>
                </tfoot>
            </table>
        </div>

        <!-- Material Information -->
        <div class="info">
            <h3 class="section-title">Material Details</h3>
            <p><strong>Raw Material Quantity:</strong> {{ $order->raw_material_quantity }} yard</p>
        </div>

        <!-- Payment Information -->
        <div class="info">
            <h3 class="section-title">Payment Information</h3>
            @if ($order->remaining_payment > 0)
                <p><strong>DP:</strong> Rp {{ number_format($order->dp, 0, ',', '.') }}</p>
                <p><strong>Remaining Payment:</strong> Rp {{ number_format($order->remaining_payment, 0, ',', '.') }}</p>
                <p><strong>Status:</strong> <span class="badge danger">Belum Lunas</span></p>
            @else
                <p><strong>Total Payment:</strong> Rp {{ number_format($order->total_price, 0, ',', '.') }}</p>
                <p><strong>Status:</strong> <span class="badge success">Lunas</span></p>
            @endif
        </div>
    </div>
</body>
</html>
