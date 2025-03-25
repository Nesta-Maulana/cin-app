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
        Schema::table('quotation_comparison_details', function (Blueprint $table) {
            // Make item_uom_id nullable
            $table->unsignedBigInteger('item_uom_id')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('quotation_comparison_details', function (Blueprint $table) {
            // Revert to non-nullable
            $table->unsignedBigInteger('item_uom_id')->nullable(false)->change();
        });
    }
};
