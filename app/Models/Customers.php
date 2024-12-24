<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Customers extends Model
{
    use HasFactory;

    protected $table = 'customers';

    protected $fillable = ['name', 'phone_number', 'address'];

    public function purchase_orders()
    {
        return $this->hasMany(PurchaseOrders::class, 'customer_id', 'id');
    }
}
