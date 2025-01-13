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
        Schema::create('approval_requests', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('approval_id'); // Relasi ke tabel approvals
            $table->string('class_name'); // relation to reference  model class
            $table->unsignedBigInteger('reference_id'); // ID referensi (misal ID dari data yang sedang di-approve)
            $table->unsignedBigInteger('current_level_id')->nullable(); // Level approval yang sedang aktif
            $table->string('status')->default('pending'); // Status permintaan (pending, approved, rejected)
            $table->text('remarks')->nullable(); // Catatan terkait permintaan approval
            $table->timestamps();

            // Foreign key constraints
            $table->foreign('approval_id')->references('id')->on('approvals')->onDelete('cascade');
            $table->foreign('current_level_id')->references('id')->on('approval_levels')->onDelete('set null');
            $table->index(['class_name', 'reference_id']);

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('approval_requests');
    }
};
