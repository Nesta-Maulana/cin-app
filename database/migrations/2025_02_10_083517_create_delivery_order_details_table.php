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
        Schema::create('delivery_order_details', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('header_id'); // References the header table
            $table->unsignedBigInteger('item_request_detail_id')->nullable(); // Optional: one DO can process many item requests
            $table->unsignedBigInteger('item_id');
            $table->decimal('quantity', 15, 3)->default(0);
            $table->unsignedBigInteger('item_uom_id');
            $table->unsignedBigInteger('warehouse_id'); // From warehouse
            $table->unsignedBigInteger('section_id');   // From section in the warehouse
            $table->text('remarks')->nullable();

            $table->timestamps();

            // Foreign key constraints
            $table->foreign('header_id')
                ->references('id')
                ->on('delivery_orders')
                ->onDelete('cascade');

            $table->foreign('item_request_detail_id')
                ->references('id')
                ->on('item_request_details')
                ->onDelete('cascade');

            $table->foreign('item_id')
                ->references('id')
                ->on('items')
                ->onDelete('cascade');

            $table->foreign('item_uom_id')
                ->references('id')
                ->on('item_uoms')
                ->onDelete('cascade');

            $table->foreign('warehouse_id')
                ->references('id')
                ->on('warehouses')
                ->onDelete('cascade');

            $table->foreign('section_id')
                ->references('id')
                ->on('warehouse_sections')
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
        Schema::dropIfExists('delivery_order_details');
    }
};
