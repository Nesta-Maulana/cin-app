<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     * 运行迁移
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pre_purchase_order_item_selections', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('pre_purchase_order_id'); // Reference to pre-purchase order / 预采购单引用
            $table->string('item_key'); // Combined key of item_name and UOM / 物品名称和单位的组合键
            $table->unsignedBigInteger('quotation_id'); // Selected supplier quotation / 所选供应商报价
            $table->timestamps();

            // Foreign keys / 外键约束
            $table->foreign('pre_purchase_order_id')
                ->references('id')
                ->on('pre_purchase_orders')
                ->onDelete('cascade');

            $table->foreign('quotation_id')
                ->references('id')
                ->on('quotation_comparisons')
                ->onDelete('cascade');

            // Unique constraint to ensure only one supplier per item per PO / 确保每个预采购单的每个物品只有一个供应商
            $table->unique(['pre_purchase_order_id', 'item_key']);
        });
    }

    /**
     * Reverse the migrations.
     * 还原迁移
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('pre_purchase_order_item_selections');
    }
};
