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
        Schema::create('production_status', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('po_id');
            $table->enum('pattern_status', ['Pending', 'Process', 'Done']);
            $table->enum('cutting_status', ['Pending', 'Process', 'Done']);
            $table->enum('sewing_status', ['Pending', 'Process', 'Done']);
            $table->enum('qc_status', ['Pending', 'Process', 'Done']);
            $table->enum('packing_status', ['Pending', 'Process', 'Done']);
            $table->timestamps();

            $table->foreign('po_id')->references('id')->on('purchase_orders');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('production_status');
    }
};
