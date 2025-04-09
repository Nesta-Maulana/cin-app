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
        Schema::create('purchase_order_new_attachments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('purchase_order_id')->constrained('purchase_order_news')->onDelete('cascade');
            $table->string('file_name', 255);
            $table->text('file_path');
            $table->string('file_type', 100)->nullable();
            $table->bigInteger('file_size')->nullable();
            $table->text('description')->nullable();
            $table->timestamp('uploaded_at');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('purchase_order_new_attachments');
    }
};
