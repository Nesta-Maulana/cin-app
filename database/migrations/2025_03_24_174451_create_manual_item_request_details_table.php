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
        Schema::create('manual_item_request_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('manual_item_request_id')->constrained('manual_item_requests')->onDelete('cascade');
            $table->string('item_name')->comment('NAMA BARANG - name of the item');
            $table->text('description')->nullable()->comment('DESCRIPTION - description of the item');
            $table->text('specification')->nullable()->comment('SPESIFICATION - specifications of the item');
            $table->string('unit')->comment('UNIT - unit of measurement');
            $table->decimal('quantity', 10, 2)->comment('QTY - quantity requested');
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
        Schema::dropIfExists('manual_item_request_details');
    }
};
