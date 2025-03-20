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
        Schema::create('quotation_comparisons', function (Blueprint $table) {

            $table->id();
            $table->unsignedBigInteger('pre_purchase_order_id');
            $table->unsignedBigInteger('supplier_id');
            $table->string('currency', 10)->default('idr');
            $table->decimal('subtotal_before_tax', 15, 2)->default(0);
            $table->enum('tax_type', ['percentage', 'fixed'])->default('percentage');
            $table->decimal('tax_value', 15, 2)->default(0);
            $table->decimal('tax_amount', 15, 2)->default(0);
            $table->decimal('total_amount', 15, 2)->default(0);
            $table->text('remarks')->nullable();
            $table->boolean('is_selected')->default(false);
            $table->timestamps();

            $table->foreign('pre_purchase_order_id')->references('id')->on('pre_purchase_orders')->onDelete('cascade');
            $table->foreign('supplier_id')->references('id')->on('suppliers')->onDelete('cascade');

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('quotation_comparisons');
    }
};
