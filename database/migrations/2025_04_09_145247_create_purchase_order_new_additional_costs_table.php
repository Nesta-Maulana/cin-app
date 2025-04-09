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
        Schema::create('purchase_order_new_additional_costs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('purchase_order_id')->constrained('purchase_order_news')->onDelete('cascade');
            $table->string('description', 255);
            $table->decimal('amount', 15, 2);
            $table->string('type', 50)->default('other');
            $table->enum('category', ['before_tax', 'after_tax']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('purchase_order_new_additional_costs');
    }
};
