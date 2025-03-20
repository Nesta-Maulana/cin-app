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
        Schema::create('quotation_comparison_additional_costs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('quotation_comparison_id');
            $table->string('description');
            $table->decimal('amount', 15, 2);
            $table->string('type'); // shipping, handling, insurance, fee, discount, other
            $table->enum('category', ['before_tax', 'after_tax']);
            $table->timestamps();
            $table->foreign('quotation_comparison_id')->references('id')->on('quotation_comparisons')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('quotation_comparison_additional_costs');
    }
};
