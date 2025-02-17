<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('purchase_order_supplier_offers', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('purchase_order_id'); // Referensi ke PO utama
            $table->unsignedBigInteger('supplier_id'); // Supplier yang menawarkan harga
            $table->decimal('shipping_cost', 15, 2)->default(0); // Ongkos kirim per supplier
            $table->decimal('other_cost', 15, 2)->default(0); // Biaya tambahan lainnya
            $table->decimal('grand_total', 15, 2)->default(0); // Total keseluruhan (diupdate setelah detail diinput)
            $table->boolean('is_selected')->default(false); // Apakah supplier ini dipilih oleh atasan?
            $table->text('remarks')->nullable(); // Catatan tambahan

            $table->timestamps();

            // Foreign keys
            $table->foreign('purchase_order_id')->references('id')->on('purchase_orders')->onDelete('cascade');
            $table->foreign('supplier_id')->references('id')->on('suppliers')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('purchase_order_supplier_offers');
    }
};
