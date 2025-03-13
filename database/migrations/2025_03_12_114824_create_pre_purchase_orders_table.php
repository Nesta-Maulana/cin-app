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
            $table->string('pre_po_number', 100)->unique(); // Nomor unik PO
            $table->string('process_status', 50)->default('pending'); // pending, under_review, approved, finalized, canceled
            $table->json('other_cost')->nullable();
            $table->decimal('grand_total', 15, 2)->default(0); // Total keseluruhan (diupdate setelah detail diinput)

            $table->text('remarks')->nullable(); // Catatan tambahan

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
