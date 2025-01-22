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
        Schema::create('customer_orders', function (Blueprint $table) {
            $table->bigIncrements('id'); // Primary Key
            $table->string('order_number', 50)->unique(); // Unique order number
            $table->unsignedBigInteger('customer_id'); // Reference to customers table
            $table->unsignedBigInteger('job_category_id')->nullable(); // Reference to job_categories table
            $table->string('project_name', 255)->nullable(); // Project name
            $table->text('description')->nullable(); // Order description
            $table->date('order_date'); // Date of the order
            $table->string('order_status', 50); // Status of the order
            $table->decimal('dpp', 30, 2)->default(0); // Dasar Pengenaan Pajak
            $table->decimal('total_amount', 30, 2)->default(0.00); // Total amount
            $table->boolean('is_active')->default(true); // Active status
            $table->timestamps(); // created_at and updated_at

            // Foreign Key Constraints
            $table->foreign('customer_id')->references('id')->on('customers')->onDelete('cascade');
            $table->foreign('job_category_id')->references('id')->on('job_categories')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('customer_orders');
    }
};
