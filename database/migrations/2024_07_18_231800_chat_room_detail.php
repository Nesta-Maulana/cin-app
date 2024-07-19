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
        Schema::create('chat_room_details', function (Blueprint $table) {
            $table->id();
            $table->string('message_id')->nullable();
            $table->foreignId('chat_room_id')->constrained('chat_rooms');
            $table->enum('type', ['chat', 'image', 'video', 'document']);
            $table->string('mimetype')->nullable();
            $table->text('message')->nullable();
            $table->boolean('is_from_me')->nullable()->default(false);
            $table->dateTime('message_time')->nullable();
            $table->json('data')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('chat_room_details');
    }
};
