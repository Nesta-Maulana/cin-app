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
        Schema::create('materials', function (Blueprint $table) {
            $table->id();
            $table->string('name', 255); // Nama material
            $table->string('name_mandarin', 255)->nullable(); // Nama material dalam Mandarin (opsional)
            $table->string('code', 50)->unique(); // Kode unik material
            $table->foreignId('unit_id')->nullable()->constrained('permissions');
            $table->text('description')->nullable(); // Deskripsi material
            $table->json('additional')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('materials');
    }
};
