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
        Schema::create('bot_services', function (Blueprint $table) {
            $table->id();
            $table->char('name');
            $table->bigInteger('bot_id')->nullable()->default('0')->comment('connected to bot if not 0, 0 database master');
            $table->text('reference_message');
            $table->text('description');
            $table->timestamps();
            $table->fullText('reference_message');
            $table->fullText('description');
            $table->fullText('name');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('bot_services');
    }
};
