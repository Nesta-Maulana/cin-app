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
        Schema::table('purchase_order_supplier_offer_details', function (Blueprint $table) {
            $table->decimal('shipping_cost', 15, 2)->default(0);

            // Option 1: Use direct expressions instead of referencing another generated column
            $table->decimal('grand_total', 15, 2)->default(0);
            $table->decimal('new_price_per_unit', 15, 2)->default(0 );
            // Option 2 (alternative): Don't use generated columns and handle it in application logic
            // $table->decimal('grand_total', 15, 2)->default(0);
            // $table->decimal('new_price_per_unit', 15, 2)->default(0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('purchase_order_supplier_offer_details', function (Blueprint $table) {
            $table->dropColumn(['shipping_price', 'grand_total', 'new_price_per_unit']);
        });
    }
};
