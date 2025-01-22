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
        Schema::create('item_uoms', function (Blueprint $table) {
            $table->id(); // Primary key
            $table->foreignId('item_id')->constrained('items')->onDelete('cascade'); // Relasi ke tabel items
            $table->foreignId('unit_of_measurement')->constrained('unit_of_measurements')->onDelete('cascade'); // Relasi ke tabel uoms
            $table->decimal('conversion', 10, 2)->nullable(); // Rasio konversi dari UOM utama
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
        Schema::dropIfExists('item_uoms');
    }
};
