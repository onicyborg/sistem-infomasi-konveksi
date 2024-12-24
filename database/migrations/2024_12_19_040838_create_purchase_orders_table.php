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
        Schema::create('purchase_orders', function (Blueprint $table) {
            $table->id();
            $table->string('po_number');
            $table->unsignedBigInteger('customer_id');
            $table->text('description');
            $table->date('order_date');
            $table->date('deadline_date');
            $table->float('raw_material_quantity');
            $table->integer('size_s');
            $table->integer('size_m');
            $table->integer('size_l');
            $table->integer('size_xl');
            $table->unsignedBigInteger('total_price');
            $table->unsignedBigInteger('dp')->nullable();
            $table->unsignedBigInteger('remaining_payment')->nullable();
            $table->unsignedBigInteger('cash_payment')->nullable();
            $table->timestamps();

            $table->foreign('customer_id')->references('id')->on('customers');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('purchase_orders');
    }
};
