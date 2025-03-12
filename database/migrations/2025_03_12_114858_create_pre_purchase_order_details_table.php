<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pre_purchase_order_details', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('purchase_order_id'); // Referensi ke PO
            $table->unsignedBigInteger('item_request_detail_id'); // Barang yang diajukan dari kebutuhan pembelian
            $table->decimal('quantity', 15, 3)->default(0); // Jumlah barang yang dibutuhkan
            $table->unsignedBigInteger('item_uom_id'); // Satuan unit pembelian
            $table->text('remarks')->nullable(); // Catatan tambahan

            $table->timestamps();

            // Foreign keys
            $table->foreign('purchase_order_id')->references('id')->on('purchase_orders')->onDelete('cascade');
            $table->foreign('item_request_detail_id')->references('id')->on('item_request_details')->onDelete('cascade');
            $table->foreign('item_uom_id')->references('id')->on('item_uoms')->onDelete('cascade');

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('pre_purchase_order_details');
    }
};
