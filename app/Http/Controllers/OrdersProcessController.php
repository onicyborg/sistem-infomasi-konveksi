<?php

namespace App\Http\Controllers;

use App\Models\Customers;
use App\Models\ProductionStatus;
use App\Models\PurchaseOrders;
use App\Models\RejectProduct;
use Illuminate\Http\Request;

class OrdersProcessController extends Controller
{
    public function index_pending()
    {
        // Query untuk mendapatkan data dengan status Pending di semua tahapan produksi
        $orders = PurchaseOrders::whereHas('production_status', function ($query) {
            $query->where('pattern_status', 'Pending')
                ->where('cutting_status', 'Pending')
                ->where('sewing_status', 'Pending')
                ->where('qc_status', 'Pending')
                ->where('packing_status', 'Pending');
        })->get();

        return view('kepala_produksi.pending', compact('orders'));
    }
    public function detail_pending($id)
    {
        // Mengambil data pesanan berdasarkan ID
        $order = PurchaseOrders::findOrFail($id);

        // Mengambil data pelanggan berdasarkan ID
        $customer = Customers::findOrFail($order->customer_id);

        // Mengambil data status produksi berdasarkan ID
        $process = ProductionStatus::where('po_id', $id)->first();

        // Mengirimkan data ke view
        return view('kepala_produksi.detail-pending', [
            'order' => $order,
            'customer' => $customer,
            'process' => $process
        ]);
    }
    public function process_to_pattern($id)
    {
        $process = ProductionStatus::where('po_id', $id)->first();

        $process->pattern_status = 'Process';
        $process->save();

        return redirect('/kepala-produksi/orders-pending')->with('success', 'Pesanan Berhasil Diproses ke Pembuatan Pola');
    }


    public function index_pattern()
    {
        // Query untuk mendapatkan data dengan status Pending di semua tahapan produksi
        $orders = PurchaseOrders::whereHas('production_status', function ($query) {
            $query->where('pattern_status', 'Process');
        })->get();

        return view('kepala_produksi.pattern', compact('orders'));
    }
    public function detail_pattern($id)
    {
        // Mengambil data pesanan berdasarkan ID
        $order = PurchaseOrders::findOrFail($id);

        // Mengambil data pelanggan berdasarkan ID
        $customer = Customers::findOrFail($order->customer_id);

        // Mengambil data status produksi berdasarkan ID
        $process = ProductionStatus::where('po_id', $id)->first();

        // Mengirimkan data ke view
        return view('kepala_produksi.detail-pattern', [
            'order' => $order,
            'customer' => $customer,
            'process' => $process
        ]);
    }
    public function process_to_cutting($id)
    {
        $process = ProductionStatus::where('po_id', $id)->first();

        $process->pattern_status = 'Done';
        $process->cutting_status = 'Process';
        $process->save();

        return redirect('/kepala-produksi/orders-pattern')->with('success', 'Pesanan Berhasil Diproses ke Cutting');
    }



    public function index_cutting()
    {
        // Query untuk mendapatkan data dengan status Pending di semua tahapan produksi
        $orders = PurchaseOrders::whereHas('production_status', function ($query) {
            $query->where('cutting_status', 'Process');
        })->get();

        return view('kepala_produksi.cutting', compact('orders'));
    }
    public function detail_cutting($id)
    {
        // Mengambil data pesanan berdasarkan ID
        $order = PurchaseOrders::findOrFail($id);

        // Mengambil data pelanggan berdasarkan ID
        $customer = Customers::findOrFail($order->customer_id);

        // Mengambil data status produksi berdasarkan ID
        $process = ProductionStatus::where('po_id', $id)->first();

        // Mengirimkan data ke view
        return view('kepala_produksi.detail-cutting', [
            'order' => $order,
            'customer' => $customer,
            'process' => $process
        ]);
    }
    public function process_to_sewing($id)
    {
        $process = ProductionStatus::where('po_id', $id)->first();

        $process->cutting_status = 'Done';
        $process->sewing_status = 'Process';
        $process->save();

        return redirect('/kepala-produksi/orders-cutting')->with('success', 'Pesanan Berhasil Diproses ke Proses Jahit');
    }


    public function index_sewing()
    {
        // Query untuk mendapatkan data dengan status Pending di semua tahapan produksi
        $orders = PurchaseOrders::whereHas('production_status', function ($query) {
            $query->where('sewing_status', 'Process');
        })->get();

        return view('kepala_produksi.sewing', compact('orders'));
    }
    public function detail_sewing($id)
    {
        // Mengambil data pesanan berdasarkan ID
        $order = PurchaseOrders::findOrFail($id);

        // Mengambil data pelanggan berdasarkan ID
        $customer = Customers::findOrFail($order->customer_id);

        // Mengambil data status produksi berdasarkan ID
        $process = ProductionStatus::where('po_id', $id)->first();

        // Mengirimkan data ke view
        return view('kepala_produksi.detail-sewing', [
            'order' => $order,
            'customer' => $customer,
            'process' => $process
        ]);
    }
    public function process_to_qc($id)
    {
        $process = ProductionStatus::where('po_id', $id)->first();

        $process->sewing_status = 'Done';
        $process->qc_status = 'Process';
        $process->save();

        return redirect('/kepala-produksi/orders-sewing')->with('success', 'Pesanan Berhasil Diproses ke Quality Controll');
    }

    public function index_qc()
    {
        // Query untuk mendapatkan data dengan status Pending di semua tahapan produksi
        $orders = PurchaseOrders::whereHas('production_status', function ($query) {
            $query->where('qc_status', 'Process');
        })->get();

        return view('kepala_produksi.quality-controll', compact('orders'));
    }
    public function detail_qc($id)
    {
        // Mengambil data pesanan berdasarkan ID
        $order = PurchaseOrders::findOrFail($id);

        // Mengambil data pelanggan berdasarkan ID
        $customer = Customers::findOrFail($order->customer_id);

        // Mengambil data status produksi berdasarkan ID
        $process = ProductionStatus::where('po_id', $id)->first();

        // dd($order->reject_product);

        // Mengirimkan data ke view
        return view('kepala_produksi.detail-quality-controll', [
            'order' => $order,
            'customer' => $customer,
            'process' => $process
        ]);
    }
    public function process_to_packing($id)
    {
        $process = ProductionStatus::where('po_id', $id)->first();

        $process->qc_status = 'Done';
        $process->packing_status = 'Process';
        $process->save();

        return redirect('/kepala-produksi/orders-qc')->with('success', 'Pesanan Berhasil Diproses ke Packing');
    }
    public function updateReject(Request $request, $id)
    {
        // Validasi input
        $request->validate([
            'size_s' => 'required|integer|min:0',
            'size_m' => 'required|integer|min:0',
            'size_l' => 'required|integer|min:0',
            'size_xl' => 'required|integer|min:0',
        ]);

        // Cari data berdasarkan po_id
        $rejectProduct = RejectProduct::where('po_id', $id)->first();

        if ($rejectProduct) {
            // Jika data sudah ada, lakukan update
            $rejectProduct->update([
                'size_s' => $request->input('size_s'),
                'size_m' => $request->input('size_m'),
                'size_l' => $request->input('size_l'),
                'size_xl' => $request->input('size_xl'),
            ]);
        } else {
            // Jika data belum ada, buat data baru
            RejectProduct::create([
                'po_id' => $id,
                'size_s' => $request->input('size_s'),
                'size_m' => $request->input('size_m'),
                'size_l' => $request->input('size_l'),
                'size_xl' => $request->input('size_xl'),
            ]);
        }

        // Redirect kembali ke halaman sebelumnya dengan pesan sukses
        return redirect()->back()->with('success', 'Data reject berhasil diperbarui.');
    }


    public function index_packing()
    {
        // Query untuk mendapatkan data dengan status Pending di semua tahapan produksi
        $orders = PurchaseOrders::whereHas('production_status', function ($query) {
            $query->where('packing_status', 'Process');
        })->get();

        return view('kepala_produksi.packing', compact('orders'));
    }
    public function detail_packing($id)
    {
        // Mengambil data pesanan berdasarkan ID
        $order = PurchaseOrders::findOrFail($id);

        // Mengambil data pelanggan berdasarkan ID
        $customer = Customers::findOrFail($order->customer_id);

        // Mengambil data status produksi berdasarkan ID
        $process = ProductionStatus::where('po_id', $id)->first();

        // Mengirimkan data ke view
        return view('kepala_produksi.detail-packing', [
            'order' => $order,
            'customer' => $customer,
            'process' => $process
        ]);
    }
    public function process_to_done($id)
    {
        $process = ProductionStatus::where('po_id', $id)->first();

        $process->packing_status = 'Done';
        $process->save();

        return redirect('/kepala-produksi/orders-packing')->with('success', 'Pesanan Berhasil Diproses ke Pesanan Selesai');
    }


    public function index_done()
    {
        // Query untuk mendapatkan data dengan status Pending di semua tahapan produksi
        $orders = PurchaseOrders::whereHas('production_status', function ($query) {
            $query->where('pattern_status', 'Done')
            ->where('cutting_status', 'Done')
            ->where('sewing_status', 'Done')
            ->where('qc_status', 'Done')
            ->where('packing_status', 'Done');
        })->get();

        return view('kepala_produksi.done', compact('orders'));
    }
    public function detail_done($id)
    {
        // Mengambil data pesanan berdasarkan ID
        $order = PurchaseOrders::findOrFail($id);

        // Mengambil data pelanggan berdasarkan ID
        $customer = Customers::findOrFail($order->customer_id);

        // Mengambil data status produksi berdasarkan ID
        $process = ProductionStatus::where('po_id', $id)->first();

        // Mengirimkan data ke view
        return view('kepala_produksi.detail-done', [
            'order' => $order,
            'customer' => $customer,
            'process' => $process
        ]);
    }
}
