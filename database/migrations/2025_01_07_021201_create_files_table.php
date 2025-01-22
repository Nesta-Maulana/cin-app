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
        Schema::create('files', function (Blueprint $table) {
            $table->bigIncrements('id'); // Primary Key
            $table->string('class_name'); // relation to reference  model class
            $table->unsignedBigInteger('reference_id'); // relation to reference id in reference model class
            $table->string('file_name', 255); // Original file name
            $table->text('file_path'); // File storage path
            $table->string('file_type', 50)->nullable(); // File MIME type
            $table->bigInteger('file_size')->nullable(); // File size in bytes
            $table->text('description')->nullable(); // Optional file description
            $table->timestamp('uploaded_at')->useCurrent(); // Timestamp of file upload
            $table->boolean('is_active')->default(true); // Status of the file
            $table->timestamps(); // created_at and updated_at
            $table->softDeletes(); // deleted_at for soft deletes

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
        Schema::dropIfExists('files');
    }
};
