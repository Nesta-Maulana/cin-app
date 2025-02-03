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
        Schema::table('approval_levels', function (Blueprint $table) {
            $table->dropForeign(['department_id']);
            $table->unsignedBigInteger('department_id')->nullable()->change();

            // Tambahkan kembali foreign key dengan ON DELETE SET NULL
            $table->foreign('department_id')
                ->references('id')
                ->on('departments')
                ->onDelete('set null');

        });
        DB::table('departments')->insertOrIgnore([
            'id' => 0,
            'name' => 'All Departments',
            'slug' => 'all-departments',
            'is_active' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('approval_levels', function (Blueprint $table) {
            // Hapus foreign key yang baru
            $table->dropForeign(['department_id']);

            // Ubah kembali agar department_id tidak boleh NULL
            $table->unsignedBigInteger('department_id')->nullable(false)->change();

            // Tambahkan kembali foreign key dengan aturan sebelumnya
            $table->foreign('department_id')
                ->references('id')
                ->on('departments')
                ->onDelete('set null');
        });

        // Hapus department id = 0 jika tidak diperlukan lagi
        DB::table('departments')->where('id', 0)->delete();
    }
};
