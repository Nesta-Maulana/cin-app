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
        Schema::create('boms', function (Blueprint $table) {
            $table->id(); // Primary key
            $table->string('request_number')->unique(); // Nomor permintaan unik
            $table->enum('type', ['material', 'other'])->default('material'); // Jenis permintaan
            $table->string('project_name'); // Nama proyek
            $table->date('request_date'); // Tanggal permintaan
            $table->string('requested_by'); // Nama pemohon
            $table->text('description')->nullable(); // Deskripsi tambahan
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
        Schema::dropIfExists('boms');
    }
};
