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
        Schema::create('purchase_order_new_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('purchase_order_id')->constrained('purchase_order_news')->onDelete('cascade');
            $table->foreignId('quotation_comparison_detail_id')->nullable()->constrained()->onDelete('set null');
            $table->foreignId('item_request_detail_id')->nullable()->constrained()->onDelete('set null');
            $table->foreignId('manual_item_request_detail_id')->nullable()->constrained()->onDelete('set null');
            $table->foreignId('item_id')->nullable()->constrained()->onDelete('set null');
            $table->string('item_name', 255);
            $table->text('specification')->nullable();
            $table->foreignId('item_uom_id')->nullable()->constrained()->onDelete('set null');
            $table->string('unit', 50)->nullable();
            $table->decimal('quantity', 15, 3);
            $table->decimal('original_price', 15, 2);
            $table->decimal('price', 15, 2);
            $table->decimal('discount_percentage', 5, 2)->default(0);
            $table->decimal('discount_amount', 15, 2)->default(0);
            $table->decimal('subtotal', 15, 2);
            $table->text('remarks')->nullable();
            $table->timestamps();

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('purchase_order_new_details');
    }
};
