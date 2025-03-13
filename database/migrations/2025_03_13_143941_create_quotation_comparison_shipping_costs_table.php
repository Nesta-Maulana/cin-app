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
        Schema::create('quotation_comparison_shipping_costs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('offer_id'); // Referensi ke header supplier offer
            $table->json('item_request_detail_ids')->nullable();
            $table->decimal('shipping_cost', 15, 2)->default(0); // Ongkos kirim per supplier
            $table->timestamps();
            $table->foreign('offer_id')->references('id')->on('quotation_comparisons')->onDelete('cascade');

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('quotation_comparison_shipping_costs');
    }
};
