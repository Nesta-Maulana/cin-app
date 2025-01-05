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
        Schema::create('items', function (Blueprint $table) {
            $table->id(); // Primary key
            $table->string('name'); // Nama produk
            $table->string('sku', 100)->unique(); // SKU unik
            $table->string('barcode', 100)->nullable(); // Barcode (opsional)
            $table->foreignId('item_type_id')->constrained('item_types')->onDelete('cascade'); // Relasi ke item_types
            $table->foreignId('category_id')->nullable()->constrained('item_categories')->onDelete('set null'); // Relasi ke categories
            $table->foreignId('unit_of_measurement_id')->constrained('unit_of_measurements')->onDelete('cascade'); // UOM utama
            $table->text('description')->nullable(); // Deskripsi produk
            $table->boolean('is_active')->default(true); // Status produk
            $table->timestamps(); // Waktu pembuatan dan pembaruan
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('items');
    }
};
