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
        Schema::create('job_categories', function (Blueprint $table) {
            $table->id(); // Primary key
            $table->string('category_code', 50)->unique(); // Kode kategori pekerjaan
            $table->string('category_name', 255); // Nama kategori pekerjaan (contoh: HVAC, CIV)
            $table->text('description')->nullable(); // Deskripsi kategori pekerjaan
            $table->boolean('is_active')->default(true); // Status aktif/tidak
            $table->timestamps(); // created_at dan updated_at
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('job_categories');
    }
};
