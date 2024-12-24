<?php

namespace App\Http\Controllers;

use App\Models\Customers;
use App\Models\ProductionStatus;
use App\Models\PurchaseOrders;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class OrdersController extends Controller
{
    public function index()
    {
        // Mendapatkan semua data purchase orders dan customers
        $orders = PurchaseOrders::all();
        $customers = Customers::all();

        // Membuat UUID unik untuk po_number
        $po_number = Str::uuid();

        // Mengirimkan data ke view
        return view('admin.orders', [
            'orders' => $orders,
            'customers' => $customers,
            'po_number' => $po_number // Mengirimkan UUID baru ke view
        ]);
    }

    public function store(Request $request)
    {
        // Validasi input dari form
        $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'po_number' => 'required|string',
            'description' => 'required|string',
            'order_date' => 'required|date',
            'deadline_date' => 'required|date',
            'size_s' => 'nullable|integer|min:0',
            'size_m' => 'nullable|integer|min:0',
            'size_l' => 'nullable|integer|min:0',
            'size_xl' => 'nullable|integer|min:0',
            'material_needed' => 'required|numeric|min:0',
            'raw_material_quantity' => 'required|numeric|min:0|gte:material_needed', // Validasi raw_material_quantity
            'dp' => 'nullable|numeric|min:0',
            'total_price' => 'required|numeric|min:0',
        ]);

        // Cek apakah ada pembayaran DP
        if ($request->has('dp') && $request->dp > 0) {
            // Jika ada DP, hitung remaining_payment dan set cash_payment ke null
            $remainingPayment = $request->total_price - $request->dp;
            $cashPayment = null;
        } else {
            // Jika tidak ada DP, set cash_payment ke total_price dan remaining_payment menjadi null
            $remainingPayment = null;
            $cashPayment = $request->total_price;
        }

        // Membuat data pesanan baru
        $newOrder = new PurchaseOrders();

        $newOrder->customer_id = $request->customer_id;
        $newOrder->po_number = $request->po_number;
        $newOrder->description = $request->description;
        $newOrder->order_date = $request->order_date;
        $newOrder->deadline_date = $request->deadline_date;
        $newOrder->size_s = $request->size_s;
        $newOrder->size_m = $request->size_m;
        $newOrder->size_l = $request->size_l;
        $newOrder->size_xl = $request->size_xl;
        $newOrder->raw_material_quantity = $request->raw_material_quantity;
        $newOrder->dp = $request->dp ?? null;
        $newOrder->cash_payment = $cashPayment;
        $newOrder->remaining_payment = $remainingPayment;
        $newOrder->total_price = $request->total_price;
        $newOrder->save();

        $newProcess = new ProductionStatus();
        $newProcess->po_id = $newOrder->id;
        $newProcess->pattern_status = 'Pending';
        $newProcess->cutting_status = 'Pending';
        $newProcess->sewing_status = 'Pending';
        $newProcess->qc_status = 'Pending';
        $newProcess->packing_status = 'Pending';
        $newProcess->save();

        // Redirect ke halaman dengan pesan sukses
        return redirect()->back()->with('success', 'Pesanan berhasil disimpan!');
    }

    public function details($id)
    {
        // Mengambil data pesanan berdasarkan ID
        $order = PurchaseOrders::findOrFail($id);

        // Mengambil data pelanggan berdasarkan ID
        $customer = Customers::findOrFail($order->customer_id);

        // Mengambil data status produksi berdasarkan ID
        $process = ProductionStatus::where('po_id', $id)->first();

        // Mengirimkan data ke view
        return view('admin.detail-order', [
            'order' => $order,
            'customer' => $customer,
            'process' => $process
        ]);
    }

    public function updatePayment($id)
    {
        $order = PurchaseOrders::findOrFail($id);

        $order->remaining_payment = 0;
        $order->save();

        // Mengirimkan data ke view
        return redirect()->back()->with('success', 'Pembayaran berhasil diverifikasi!');
    }

    public function printInvoice($id)
    {
        $order = PurchaseOrders::findOrFail($id);

        // Kirim data ke view khusus untuk invoice
        $pdf = Pdf::loadView('admin.po', compact('order'));

        // Unduh atau tampilkan file PDF
        return $pdf->stream('invoice-po-' . $order->po_number . '.pdf');
    }
}
