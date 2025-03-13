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
        Schema::create('quotation_comparison_details', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('offer_id'); // Referensi ke header supplier offer
            $table->unsignedBigInteger('pre_purchase_order_detail_id'); // Referensi ke barang dalam PO
            $table->decimal('quantity', 15, 2)->default(1); // Jumlah barang sesuai PO
            $table->decimal('offered_price_per_unit', 15, 2)->default(0); // Harga penawaran per unit
            $table->decimal('total_price', 15, 2)->storedAs('offered_price_per_unit * quantity'); // Harga total

            $table->timestamps();

            // Foreign keys
            $table->foreign('offer_id')->references('id')->on('quotation_comparisons')->onDelete('cascade');
            $table->foreign('pre_purchase_order_detail_id')->references('id')->on('pre_purchase_order_details')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('quotation_comparison_details');
    }
};
