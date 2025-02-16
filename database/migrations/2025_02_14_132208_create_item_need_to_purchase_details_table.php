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
        Schema::create('item_need_to_purchase_details', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('header_id'); // References the header table
            $table->unsignedBigInteger('item_request_detail_id')->nullable(); // Optional: one DO can process many item requests
            $table->unsignedBigInteger('warehouse_id')->nullable(); // From warehouse
            $table->foreign('warehouse_id')
                ->references('id')
                ->on('warehouses')
                ->onDelete('cascade');
            $table->timestamps();
            // Foreign key constraints
            $table->foreign('header_id')
                ->references('id')
                ->on('item_need_to_purchases')
                ->onDelete('cascade');
            $table->foreign('item_request_detail_id')
                ->references('id')
                ->on('item_request_details')
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
        Schema::dropIfExists('item_need_to_purchase_details');
    }
};
