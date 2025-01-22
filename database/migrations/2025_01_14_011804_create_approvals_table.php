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
        Schema::create('approvals', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // Nama approval process
            $table->text('description')->nullable(); // Deskripsi approval
            $table->string('class_name'); // relation to reference  model class
            $table->string('event'); // relation to action model class like create read update or delete data
            $table->string('column_update')->nullable(); // relation to column model class
            $table->unsignedInteger('levels'); // Jumlah level approval
            $table->boolean('is_active')->default(true); // Status aktif
            $table->timestamps(); // Created_at dan updated_at

            $table->index(['class_name', 'event']);

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('approvals');
    }
};
