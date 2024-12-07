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
        Schema::create('material_prices', function (Blueprint $table) {
            $table->id(); // Primary key
            $table->foreignId('material_id')->constrained('materials')->onDelete('cascade'); // Foreign key ke tabel materials
            $table->decimal('price', 18, 2); // Harga material
            $table->boolean('is_active')->nullable()->default(true);
            $table->timestamp('effective_date')->default(DB::raw('CURRENT_TIMESTAMP')); // Tanggal berlaku harga
            $table->timestamps(); // created_at dan updated_at
        });
    }

    public function down()
    {
        Schema::dropIfExists('material_prices');
    }

};
