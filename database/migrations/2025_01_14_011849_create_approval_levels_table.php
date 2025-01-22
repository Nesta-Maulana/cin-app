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
        Schema::create('approval_levels', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('approval_id'); // Relasi ke tabel approvals
            $table->unsignedInteger('hierarchy_order '); // Nomor level (1, 2, 3, dst.)
            $table->string('class_name_approver_type'); // relation to reference  model class to role or user table
            $table->string('approver_reference_id'); // id reference of spesific user / role
            $table->string('updated_value_on_approve'); // update value on approve
            $table->string('updated_value_on_reject'); // update value on reject
            $table->text('description')->nullable(); // Deskripsi level
            $table->boolean('required')->default(true); // Status diperlukan atau tidak
            $table->timestamps();
            // Foreign key constraint
            $table->foreign('approval_id')->references('id')->on('approvals')->onDelete('cascade');
            $table->index(['class_name_approver_type', 'approver_reference_id']);

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('approval_levels');
    }
};
