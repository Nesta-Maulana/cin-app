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
        Schema::create('approval_logs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('approval_request_id'); // Relasi ke tabel approval_requests
            $table->unsignedBigInteger('approval_level_id'); // Relasi ke tabel approval_levels
            $table->unsignedBigInteger('approver_id'); // ID user menyetujui/menolak
            $table->string('action'); // Action yang dilakukan (approve/reject)
            $table->text('remarks')->nullable(); // Catatan terkait action
            $table->timestamps();

            // Foreign key constraints
            $table->foreign('approval_request_id')->references('id')->on('approval_requests')->onDelete('cascade');
            $table->foreign('approval_level_id')->references('id')->on('approval_levels')->onDelete('cascade');
            $table->foreign('approver_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('approval_logs');
    }
};
