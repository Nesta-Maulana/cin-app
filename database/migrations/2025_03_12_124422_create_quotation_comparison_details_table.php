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
            $table->unsignedBigInteger('quotation_comparison_id');
            $table->unsignedBigInteger('pre_purchase_order_detail_id');
            $table->unsignedBigInteger('item_uom_id');
            $table->decimal('quantity', 15, 2);
            $table->decimal('offered_price_per_unit', 15, 2);
            $table->decimal('subtotal_price', 15, 2);
            $table->decimal('shipping_cost', 15, 2)->default(0);
            $table->decimal('grand_total', 15, 2);
            $table->decimal('new_unit_price', 15, 2);
            $table->text('remarks')->nullable();
            $table->timestamps();

            $table->foreign('quotation_comparison_id')->references('id')->on('quotation_comparisons')->onDelete('cascade');
            $table->foreign('pre_purchase_order_detail_id')->references('id')->on('pre_purchase_order_details')->onDelete('cascade');
            $table->foreign('item_uom_id')->references('id')->on('item_uoms');

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
