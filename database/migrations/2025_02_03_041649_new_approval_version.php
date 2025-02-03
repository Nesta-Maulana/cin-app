<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::table('approvals', function (Blueprint $table) {
            $table->dropColumn('column_update');
        });

        Schema::table('approval_levels', function (Blueprint $table) {
            $table->dropColumn(['updated_value_on_approve', 'updated_value_on_reject']);

            $table->jsonb('updated_values_on_approve')->default(json_encode([]));
            $table->jsonb('updated_values_on_reject')->default(json_encode([]));
            $table->unsignedBigInteger('department_id')->nullable();
            $table->foreign('department_id')->references('id')->on('departments')->onDelete('set null');
        });
    }

    public function down()
    {
        Schema::table('approval_levels', function (Blueprint $table) {
            $table->dropColumn(['updated_values_on_approve', 'updated_values_on_reject', 'department_id']);
        });

        Schema::table('approvals', function (Blueprint $table) {
            $table->string('column_update')->nullable();
        });
    }

};
