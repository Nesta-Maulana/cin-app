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
        Schema::table('users', function (Blueprint $table) {
            // Add the new column with default value
            $table->boolean('is_active')->default(true)->after('status');
        });

        // Copy data from old column to new column
        DB::statement('UPDATE users SET is_active = status');

        Schema::table('users', function (Blueprint $table) {
            // Drop the old column
            $table->dropColumn('status');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            // Add the old column back
            $table->boolean('status')->default(true)->after('is_active');
        });

        // Copy data back from new column to old column
        DB::statement('UPDATE users SET status = is_active');

        Schema::table('users', function (Blueprint $table) {
            // Drop the new column
            $table->dropColumn('is_active');
        });
    }

};
