<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CalendarController;
use App\Http\Controllers\CustomersController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\OrdersController;
use App\Http\Controllers\OrdersProcessController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::middleware(['guest'])->group(function () {
    Route::get('/login', function () {
        return view('login');
    })->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('auth');
});

Route::middleware(['auth'])->group(function () {

    Route::get('/', [DashboardController::class, 'index']);
    Route::get('/sales-data', [DashboardController::class, 'getSalesData']);
    Route::post('/purchase-orders/export', [DashboardController::class, 'exportToExcel'])->name('purchase_orders.export');

    Route::group(['middleware' => 'role:admin'], function () {
        Route::get('/admin/customers', [CustomersController::class, 'index'])->name('customers.index');
        Route::post('/admin/customers', [CustomersController::class, 'store'])->name('customers.store');
        Route::put('/admin/customers/{id}', [CustomersController::class, 'update'])->name('customers.update');
        Route::delete('/admin/customers/{id}', [CustomersController::class, 'destroy'])->name('customers.destroy');


        Route::get('/admin/orders', [OrdersController::class, 'index']);
        Route::post('/admin/orders-store', [OrdersController::class, 'store']);
        Route::get('/admin/orders/{id}', [OrdersController::class, 'details']);
        Route::put('/admin/update-payment/{id}', [OrdersController::class, 'updatePayment']);
        Route::get('/order/{id}/print', [OrdersController::class, 'printInvoice'])->name('order.print');

        Route::get('/admin/calendar', [CalendarController::class, 'index']);
    });

    Route::group(['middleware' => 'role:kepala_produksi'], function () {
        Route::get('/kepala-produksi/orders-pending', [OrdersProcessController::class, 'index_pending']);
        Route::get('/kepala-produksi/orders-pending/{id}', [OrdersProcessController::class, 'detail_pending']);
        Route::put('/process-to-pattern/{id}', [OrdersProcessController::class, 'process_to_pattern']);

        Route::get('/kepala-produksi/orders-pattern', [OrdersProcessController::class, 'index_pattern']);
        Route::get('/kepala-produksi/orders-pattern/{id}', [OrdersProcessController::class, 'detail_pattern']);
        Route::put('/process-to-cutting/{id}', [OrdersProcessController::class, 'process_to_cutting']);

        Route::get('/kepala-produksi/orders-cutting', [OrdersProcessController::class, 'index_cutting']);
        Route::get('/kepala-produksi/orders-cutting/{id}', [OrdersProcessController::class, 'detail_cutting']);
        Route::put('/process-to-sewing/{id}', [OrdersProcessController::class, 'process_to_sewing']);

        Route::get('/kepala-produksi/orders-sewing', [OrdersProcessController::class, 'index_sewing']);
        Route::get('/kepala-produksi/orders-sewing/{id}', [OrdersProcessController::class, 'detail_sewing']);
        Route::put('/process-to-qc/{id}', [OrdersProcessController::class, 'process_to_qc']);

        Route::get('/kepala-produksi/orders-qc', [OrdersProcessController::class, 'index_qc']);
        Route::get('/kepala-produksi/orders-qc/{id}', [OrdersProcessController::class, 'detail_qc']);
        Route::put('/process-to-packing/{id}', [OrdersProcessController::class, 'process_to_packing']);
        Route::put('/update-reject-product/{id}', [OrdersProcessController::class, 'updateReject']);

        Route::get('/kepala-produksi/orders-packing', [OrdersProcessController::class, 'index_packing']);
        Route::get('/kepala-produksi/orders-packing/{id}', [OrdersProcessController::class, 'detail_packing']);
        Route::put('/process-to-done/{id}', [OrdersProcessController::class, 'process_to_done']);

        Route::get('/kepala-produksi/orders-done', [OrdersProcessController::class, 'index_done']);
        Route::get('/kepala-produksi/orders-done/{id}', [OrdersProcessController::class, 'detail_done']);
    });

    Route::get('/logout', [AuthController::class, 'logout']);
});
