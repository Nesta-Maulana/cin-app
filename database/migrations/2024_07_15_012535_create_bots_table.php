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
        Schema::create('bots', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('tenant_id')->nullable()->default('0')->comment('connected to tenant if not 0');
            $table->string('session_name')->nullable();
            $table->string('token')->nullable();
            $table->string('status')->nullable();
            $table->string('phone_number')->nullable();
            $table->timestamps();
            $table->fullText('session_name');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('bots');
    }
};
