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
        Schema::table('pre_purchase_order_details', function (Blueprint $table) {
            // Make existing column nullable
            $table->unsignedBigInteger('item_request_detail_id')->nullable()->change();
            $table->unsignedBigInteger('item_uom_id')->nullable()->change();

            // Add new columns
            $table->unsignedBigInteger('manual_item_request_detail_id')->nullable()->after('item_request_detail_id');
            $table->string('item_name')->nullable()->after('manual_item_request_detail_id');
            $table->text('specification')->nullable()->after('item_name');
            $table->string('unit')->nullable()->after('specification');

            // Add foreign key
            $table->foreign('manual_item_request_detail_id')
                ->references('id')
                ->on('manual_item_request_details')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('pre_purchase_order_details', function (Blueprint $table) {
            // Remove the foreign key constraint first
            $table->dropForeign(['manual_item_request_detail_id']);

            // Drop new columns
            $table->dropColumn([
                'manual_item_request_detail_id',
                'item_name',
                'specification',
                'unit'
            ]);

            // Make columns required again
            $table->unsignedBigInteger('item_request_detail_id')->nullable(false)->change();
            $table->unsignedBigInteger('item_uom_id')->nullable(false)->change();
        });
    }
};
