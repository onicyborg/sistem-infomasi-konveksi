<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductionStatus extends Model
{
    use HasFactory;

    protected $table = 'production_status';

    public function purchase_order()
    {
        return $this->belongsTo(PurchaseOrders::class, 'po_id', 'id');
    }
}
