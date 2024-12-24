<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CalendarController;
use App\Http\Controllers\CustomersController;
use App\Http\Controllers\OrdersController;
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

    Route::get('/', function () {
        return view('admin.welcome');
    });

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
        Route::get('/admin/dashboard', function () {
            //
        });
        Route::get('/admin/settings', function () {
            //
        });
    });

    Route::get('/logout', [AuthController::class, 'logout']);
});
