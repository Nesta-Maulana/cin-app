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
        Schema::create('item_price_histories', function (Blueprint $table) {
            $table->id(); // Primary key
            $table->foreignId('item_uom_id')->constrained('item_uoms')->onDelete('cascade'); // Relasi ke tabel item_uoms
            $table->decimal('price', 15, 2); // Harga jual
            $table->decimal('cost', 15, 2); // Harga pokok
            $table->string('currency', 10)->default('IDR'); // Mata uang harga
            $table->boolean('is_active')->default(true); // Status aktif atau tidak
            $table->timestamps(); // Kolom created_at dan updated_at
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('item_price_histories');
    }
};
