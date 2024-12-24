<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('reject_products', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('po_id');
            $table->integer('size_s');
            $table->integer('size_m');
            $table->integer('size_l');
            $table->integer('size_xl');
            $table->timestamps();

            $table->foreign('po_id')->references('id')->on('purchase_orders');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reject_products');
    }
};
