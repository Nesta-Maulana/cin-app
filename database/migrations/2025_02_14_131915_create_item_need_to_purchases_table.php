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
        Schema::create('item_need_to_purchases', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('customer_order_id');
            $table->date('request_date');
            $table->string('process_status', 50)->default('pending'); // e.g., pending, approved, received, etc.
            $table->unsignedBigInteger('created_by');
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->timestamps();
            // Foreign key constraints
            $table->foreign('customer_order_id')
                ->references('id')
                ->on('customer_orders')
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
        Schema::dropIfExists('item_need_to_purchases');
    }
};
