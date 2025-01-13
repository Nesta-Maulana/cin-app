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
        Schema::create('item_request_details', function (Blueprint $table) {
            $table->bigIncrements('id'); // Primary Key
            $table->unsignedBigInteger('item_request_id'); // Relasi ke Item Requests
            $table->unsignedBigInteger('item_price_history_id'); // Relasi ke Item Requests
            $table->integer('quantity'); // Jumlah item
            $table->text('remarks')->nullable(); // Catatan tambahan
            $table->boolean('is_active')->default(true); // Status aktif
            $table->json('additional')->nullable();
            $table->timestamps(); // Timestamps untuk created_at dan updated_at

            // Foreign Key Constraints
            $table->foreign('item_request_id')
                ->references('id')
                ->on('item_requests')
                ->onDelete('cascade'); // Hapus item detail jika item request dihapus
            $table->foreign('item_price_history_id')
                ->references('id')
                ->on('item_price_histories')
                ->onDelete('cascade'); // Hapus item detail jika item request dihapus
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('item_request_details');
    }
};
