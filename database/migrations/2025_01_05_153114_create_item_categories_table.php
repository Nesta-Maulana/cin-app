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
        Schema::create('item_categories', function (Blueprint $table) {
            $table->id(); // Primary key
            $table->string('name'); // Nama kategori
            $table->text('description')->nullable(); // Deskripsi kategori
            $table->foreignId('item_type_id')->constrained('item_types')->onDelete('cascade'); // Relasi ke tabel item_types
            $table->foreignId('parent_id')->nullable()->constrained('item_categories')->onDelete('cascade'); // Relasi ke kategori induk
            $table->boolean('is_active')->default(true); // Status aktif
            $table->timestamps(); // Kolom created_at dan updated_at
        });
    }

    public function down()
    {
        Schema::dropIfExists('item_categories');
    }
};
