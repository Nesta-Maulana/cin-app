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
        Schema::create('manual_item_requests', function (Blueprint $table) {
            $table->id();
            $table->string('request_number')->unique()->comment('NO BOM - unique identifier for the request');
            $table->foreignId('customer_order_id')->constrained('customer_orders');
            $table->date('request_date')->comment('TGL - date of the request');
            $table->enum('status', ['pending', 'approved', 'rejected', 'completed'])->default('pending');
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('manual_item_requests');
    }
};
