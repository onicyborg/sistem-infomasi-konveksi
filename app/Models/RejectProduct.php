<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RejectProduct extends Model
{
    use HasFactory;

    protected $table = 'reject_products';

    protected $fillable = [
        'po_id',
        'size_s',
        'size_m',
        'size_l',
        'size_xl',
    ];

    public function purchase_order()
    {
        return $this->belongsTo(PurchaseOrders::class, 'po_id', 'id');
    }
}
