<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::table('settings', function (Blueprint $table) {
            $table->dropColumn('app_name');
            $table->dropColumn('app_version');
            $table->dropColumn('address');
            $table->dropColumn('logo');
            if (!Schema::hasColumn('settings', 'name')) {
                $table->string('name')->nullable();
                $table->index('name');
            }

            if (!Schema::hasColumn('settings', 'category')) {
                $table->string('category')->nullable();
                $table->fullText('category');
            }
            if (!Schema::hasColumn('settings', 'data')) {
                $table->json('data')->nullable();
            }
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('settings', function (Blueprint $table) {
            if (Schema::hasColumn('settings', 'category')) {
                $table->dropColumn('category');
            }
            if (Schema::hasColumn('settings', 'data')) {
                $table->dropColumn('data');
            }
            $table->string('app_name')->nullable();
            $table->string('app_version')->nullable();
            if (!Schema::hasColumn('settings', 'name')) {
                $table->string('name')->nullable();
                $table->index('name');
            }
            $table->text('address')->nullable();
            $table->string('logo')->nullable();
        });
    }
};
