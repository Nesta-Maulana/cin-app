<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('warehouse_section_stocks', function (Blueprint $table) {
            $table->id(); // Auto-increment primary key
            $table->foreignId('warehouse_id')->constrained('warehouses')->onDelete('cascade'); // Warehouse ID
            $table->foreignId('section_id')->constrained('warehouse_sections')->onDelete('cascade'); // Section ID
            $table->foreignId('item_id')->constrained('items')->onDelete('cascade'); // Product ID
            $table->decimal('current_stock', 15, 3)->default(0.000); // Stock level with 3 decimal places
            $table->timestamps(); // Created_at & Updated_at
        });
    }

    public function down()
    {
        Schema::dropIfExists('warehouse_section_stocks');
    }
};
