<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pre_purchase_orders', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('customer_order_id');
            $table->string('po_number', 100)->unique(); // Nomor unik PO
            $table->date('request_date'); // Tanggal permintaan PO
            $table->date('expected_delivery_date'); // Perkiraan tanggal pengiriman
            $table->string('process_status', 50)->default('pending'); // pending, under_review, approved, finalized, canceled
            $table->text('remarks')->nullable(); // Catatan tambahan

            // Harga total
            $table->decimal('subtotal_price', 15, 2)->default(0); // Harga barang sebelum biaya tambahan
            $table->decimal('shipping_cost', 15, 2)->default(0); // Ongkos kirim
            $table->decimal('other_cost', 15, 2)->default(0); // Biaya tambahan lain
            $table->decimal('total_price', 15, 2)->storedAs('subtotal_price + shipping_cost + other_cost'); // Harga total

            // User tracking
            $table->unsignedBigInteger('created_by'); // User yang membuat PO
            $table->unsignedBigInteger('approved_by')->nullable(); // User yang menyetujui PO
            $table->unsignedBigInteger('finalized_by')->nullable(); // User yang memilih supplier

            $table->timestamps();

            // Foreign keys
            $table->foreign('created_by')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('approved_by')->references('id')->on('users')->onDelete('set null');
            $table->foreign('finalized_by')->references('id')->on('users')->onDelete('set null');
            $table->foreign('customer_order_id')->references('id')->on('customer_orders')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('pre_purchase_orders');
    }
};
