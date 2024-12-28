<?php

namespace App\Http\Controllers;

use App\Exports\PurchaseOrdersExport;
use App\Models\Customers;
use App\Models\PurchaseOrders;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;

class DashboardController extends Controller
{
    public function index()
    {
        if (Auth::user()->role == 'admin') {
            $customer = Customers::all()->count();
            $po = PurchaseOrders::all()->count();
            $completedOrdersCount = PurchaseOrders::whereHas('production_status', function ($query) {
                $query->where('pattern_status', 'Done')
                    ->where('cutting_status', 'Done')
                    ->where('sewing_status', 'Done')
                    ->where('qc_status', 'Done')
                    ->where('packing_status', 'Done');
            })->count();
            $onprogressOrdersCount = $po - $completedOrdersCount;

            $orderData = PurchaseOrders::selectRaw("
                customers.name AS customer_name,
                purchase_orders.order_date AS order_date,
                purchase_orders.deadline_date AS deadline_date,
                (purchase_orders.size_s + purchase_orders.size_m + purchase_orders.size_l + purchase_orders.size_xl) AS total_quantity
            ")
                ->join('customers', 'purchase_orders.customer_id', '=', 'customers.id')
                ->orderBy('purchase_orders.order_date', 'desc') // Mengurutkan berdasarkan order_date yang terbaru
                ->limit(4) // Membatasi hanya 4 pesanan terbaru
                ->get();
            // dd($orderData);

            return view('admin.welcome', ['customer' => $customer, 'po' => $po, 'completedOrdersCount' => $completedOrdersCount, 'onprogressOrdersCount' => $onprogressOrdersCount, 'orderData' => $orderData]);
        } else {
            $customer = Customers::all()->count();
            $po = PurchaseOrders::all()->count();
            $completedOrdersCount = PurchaseOrders::whereHas('production_status', function ($query) {
                $query->where('pattern_status', 'Done')
                    ->where('cutting_status', 'Done')
                    ->where('sewing_status', 'Done')
                    ->where('qc_status', 'Done')
                    ->where('packing_status', 'Done');
            })->count();
            $onprogressOrdersCount = $po - $completedOrdersCount;

            $orderData = PurchaseOrders::selectRaw("
                customers.name AS customer_name,
                purchase_orders.order_date AS order_date,
                purchase_orders.deadline_date AS deadline_date,
                (purchase_orders.size_s + purchase_orders.size_m + purchase_orders.size_l + purchase_orders.size_xl) AS total_quantity
            ")
                ->join('customers', 'purchase_orders.customer_id', '=', 'customers.id')
                ->orderBy('purchase_orders.order_date', 'desc') // Mengurutkan berdasarkan order_date yang terbaru
                ->limit(4) // Membatasi hanya 4 pesanan terbaru
                ->get();
            // dd($orderData);

            return view('kepala_produksi.welcome', ['customer' => $customer, 'po' => $po, 'completedOrdersCount' => $completedOrdersCount, 'onprogressOrdersCount' => $onprogressOrdersCount, 'orderData' => $orderData]);
        }
    }

    public function getSalesData()
    {
        $startDate = Carbon::now()->subMonths(6)->startOfMonth(); // Awal 7 bulan terakhir
        $endDate = Carbon::now()->endOfMonth(); // Akhir bulan saat ini

        // Ambil semua bulan dalam range waktu
        $months = collect();
        $current = $startDate->copy();

        while ($current->lessThanOrEqualTo($endDate)) {
            $months->push($current->format('M')); // Contoh: 'Jan', 'Feb'
            $current->addMonth();
        }

        // Hitung total penjualan untuk setiap bulan
        $salesData = $months->map(function ($month, $index) use ($startDate) {
            $startOfMonth = $startDate->copy()->addMonths($index)->startOfMonth();
            $endOfMonth = $startOfMonth->copy()->endOfMonth();

            // Hitung total penjualan di bulan tersebut
            $totalSales = DB::table('purchase_orders')
                ->whereBetween('created_at', [$startOfMonth, $endOfMonth])
                ->count();

            return [
                'month' => $month,
                'total_sales' => $totalSales,
            ];
        });

        // Pisahkan bulan dan total penjualan untuk JavaScript
        $months = $salesData->pluck('month');
        $totals = $salesData->pluck('total_sales');

        return response()->json([
            'months' => $months,
            'totals' => $totals,
        ]);
    }

    public function exportToExcel(Request $request)
    {
        // Validasi input
        $request->validate([
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
        ]);

        // Cek apakah export seluruh data atau berdasarkan filter tanggal
        if ($request->has('export_all')) {
            // Export seluruh data
            return Excel::download(new PurchaseOrdersExport(null, null), 'purchase_orders_all.xlsx');
        } else {
            // Ambil data berdasarkan rentang tanggal
            $startDate = $request->input('start_date');
            $endDate = $request->input('end_date');
            return Excel::download(new PurchaseOrdersExport($startDate, $endDate), 'purchase_orders_' . $startDate . '_to_' . $endDate . '.xlsx');
        }
    }
}
