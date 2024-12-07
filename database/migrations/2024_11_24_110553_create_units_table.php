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
        Schema::create('units', function (Blueprint $table) {
            $table->id(); // Primary key
            $table->string('name', 100)->unique(); // Nama unit, misalnya "Kilogram", "Meter"
            $table->string('name_mandarin', 100)->unique(); // Nama unit, misalnya "Kilogram", "Meter"
            $table->string('abbreviation', 10)->nullable(); // Singkatan, misalnya "kg", "m"
            $table->string('abbreviation_mandarin', 10)->nullable(); // Singkatan, misalnya "kg", "m"
            $table->text('description')->nullable(); // Deskripsi tambahan tentang unit
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
        Schema::dropIfExists('units');
    }
};
