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
        Schema::create('customers', function (Blueprint $table) {
            $table->id();
            $table->string('customer_code', 50)->unique(); // Unique customer code
            $table->string('customer_name', 255); // Customer name
            $table->text('customer_address')->nullable(); // Customer address
            $table->string('customer_tax_number', 50)->nullable(); // Tax number (optional)
            $table->string('customer_contact', 100)->nullable(); // Contact person name
            $table->string('customer_phone_number', 20)->nullable(); // Phone number
            $table->string('customer_email', 100)->nullable(); // Email address
            $table->boolean('is_active')->default(true); // Customer status
            $table->timestamps(); // created_at and updated_at
            $table->softDeletes(); // deleted_at for soft deletes
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('customers');
    }
};
