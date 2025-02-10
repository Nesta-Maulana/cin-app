<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('stock_entries', function (Blueprint $table) {
            $table->id(); // Auto-increment primary key
            $table->foreignId('warehouse_id')->constrained('warehouses')->onDelete('cascade'); // Warehouse ID
            $table->foreignId('section_id')->constrained('warehouse_sections')->onDelete('cascade'); // Warehouse Section ID
            $table->foreignId('item_id')->constrained('items')->onDelete('cascade'); // Item ID
            $table->foreignId('supplier_id')->nullable()->constrained('suppliers')->onDelete('set null'); // Supplier ID (optional)
            $table->foreignId('warehouse_destination_id')->nullable()->constrained('warehouses')->onDelete('cascade'); // Warehouse ID

            $table->enum('type', ['in', 'out'])->default('in'); // 'in' = incoming stock, 'out' = outgoing stock
            $table->enum('stock_source', ['purchase', 'transfer', 'adjustment', 'sale'])->default('purchase'); // Source of stock entry
            $table->decimal('quantity', 15, 3)->default(0.000); // Stock level with 3 decimal places
            $table->foreignId('item_uom_id')->constrained('item_uoms')->onDelete('cascade'); // Item Uoms ID

            $table->string('reference_number')->nullable(); // Unique transaction number (PO, invoice, etc.)

            $table->date('date'); // Transaction date
            $table->text('notes')->nullable(); // Additional notes
            $table->foreignId('created_by')->constrained('users')->onDelete('cascade'); // User who created entry
            $table->foreignId('updated_by')->nullable()->constrained('users')->onDelete('set null'); // Last user to update
            $table->timestamps(); // Created_at & Updated_at

            $table->index(['warehouse_id', 'item_id', 'item_uom_id', 'section_id', 'date']);

        });
    }

    public function down()
    {
        Schema::dropIfExists('stock_entries');
    }
};
