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
        Schema::create('purchase_order_news', function (Blueprint $table) {
            $table->id();
            $table->string('po_number', 100)->unique();
            $table->foreignId('supplier_id')->constrained()->onDelete('restrict');
            $table->foreignId('customer_order_id')->constrained()->onDelete('restrict');
            $table->foreignId('pre_purchase_order_id')->nullable()->constrained()->onDelete('set null');
            $table->date('order_date');
            $table->date('expected_delivery_date');
            $table->string('currency', 10)->default('IDR');
            $table->decimal('subtotal_price', 15, 2)->default(0);
            $table->enum('tax_type', ['percentage', 'fixed'])->default('percentage');
            $table->decimal('tax_value', 15, 2)->default(0);
            $table->decimal('tax_amount', 15, 2)->default(0);
            $table->decimal('shipping_cost', 15, 2)->default(0);
            $table->decimal('other_cost', 15, 2)->default(0);
            $table->decimal('total_amount', 15, 2)->default(0);
            $table->string('payment_terms', 100)->nullable();
            $table->string('process_status', 50)->default('draft');
            $table->text('remarks')->nullable();
            $table->foreignId('created_by')->constrained('users')->onDelete('restrict');
            $table->foreignId('updated_by')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('approved_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('approved_at')->nullable();
            $table->timestamps();
        });

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('purchase_order_news');
    }
};
