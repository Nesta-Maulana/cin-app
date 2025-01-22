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
        Schema::create('item_requests', function (Blueprint $table) {
            $table->bigIncrements('id'); // Primary Key
            $table->unsignedBigInteger('customer_order_id'); // Relasi ke Customer Orders
            $table->string('request_number', 100)->unique(); // Nomor unik item request
            $table->date('request_date'); // Tanggal permintaan
            $table->text('remarks')->nullable(); // Catatan tambahan
            $table->boolean('is_active')->default(true); // Status aktif
            $table->timestamps(); // Timestamps untuk created_at dan updated_at

            // Foreign Key Constraints
            $table->foreign('customer_order_id')
                ->references('id')
                ->on('customer_orders')
                ->onDelete('cascade'); // Hapus item request jika customer order dihapus
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('item_requests');
    }
};
