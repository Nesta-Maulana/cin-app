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
        Schema::table('purchase_order_supplier_offers', function (Blueprint $table) {
            $table->string('currency', 10)->default('IDR'); // Mata uang harga after id
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('purchase_order_supplier_offers', function (Blueprint $table) {
            $table->dropColumn('currency');
        });
    }
};
