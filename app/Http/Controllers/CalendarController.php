<?php

namespace App\Http\Controllers;

use App\Models\PurchaseOrders;
use Illuminate\Http\Request;

class CalendarController extends Controller
{
    public function index()
    {
        $orders = PurchaseOrders::all();

        return view('admin.calendar', compact('orders'));
    }
}
