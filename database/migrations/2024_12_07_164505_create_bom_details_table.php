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
        Schema::create('bom_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('bom_id')->constrained('boms')->onDelete('cascade'); // Foreign key ke tabel materials
            $table->foreignId('material_id')->constrained('materials')->onDelete('cascade'); // Foreign key ke tabel materials
            $table->foreignId('material_price_id')->constrained('material_prices')->onDelete('cascade'); // Foreign key ke tabel materials
            $table->text('description')->nullable(); // Deskripsi material
            $table->json('additional')->nullable();
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
        Schema::dropIfExists('bom_details');
    }
};
