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
        Schema::create('unit_of_measurements', function (Blueprint $table) {
            $table->id(); // Primary key
            $table->string('name'); // Nama UOM
            $table->string('code', 50)->unique(); // Kode unik untuk UOM
            $table->text('description')->nullable(); // Deskripsi UOM (opsional)
            $table->enum('type', ['weight', 'volume', 'quantity', 'length', 'area', 'time', 'service'])->default('quantity'); // Jenis UOM
            $table->boolean('is_active')->default(true); // Status aktif
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
        Schema::dropIfExists('unit_of_measurements');
    }
};
