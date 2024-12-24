<?php

namespace App\Http\Controllers;

use App\Models\Customers;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class CustomersController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = Customers::all();
            return DataTables::of($data)
                ->addIndexColumn()
                ->make(true);
        }

        return view('admin.customers');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'phone_number' => 'required|string|max:20',
            'address' => 'required|string|max:500',
        ]);

        Customers::create($request->all());

        return response()->json(['success' => 'Customer berhasil ditambahkan']);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'phone_number' => 'required|string|max:20',
            'address' => 'required|string|max:500',
        ]);

        $customer = Customers::findOrFail($id);
        $customer->update($request->all());

        return response()->json(['success' => 'Customer berhasil diperbarui']);
    }

    public function destroy($id)
    {
        $customer = Customers::findOrFail($id);
        $customer->delete();

        return response()->json(['success' => 'Customer berhasil dihapus']);
    }
}
