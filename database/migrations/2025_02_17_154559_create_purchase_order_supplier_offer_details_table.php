<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('purchase_order_supplier_offer_details', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('offer_id'); // Referensi ke header supplier offer
            $table->unsignedBigInteger('purchase_order_detail_id'); // Referensi ke barang dalam PO
            $table->decimal('quantity', 15, 2)->default(1); // Jumlah barang sesuai PO
            $table->decimal('offered_price_per_unit', 15, 2)->default(0); // Harga penawaran per unit
            $table->decimal('total_price', 15, 2)->storedAs('offered_price_per_unit * quantity'); // Harga total

            $table->timestamps();

            // Foreign keys
            $table->foreign('offer_id')->references('id')->on('purchase_order_supplier_offers')->onDelete('cascade');
            $table->foreign('purchase_order_detail_id')->references('id')->on('purchase_order_details')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('purchase_order_supplier_offer_details');
    }
};
