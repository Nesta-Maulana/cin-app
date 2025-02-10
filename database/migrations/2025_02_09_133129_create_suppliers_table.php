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
        Schema::create('suppliers', function (Blueprint $table) {
            $table->id(); // Primary Key
            $table->string('name', 255); // Nama Perusahaan 供应商名称
            $table->text('address')->nullable(); // Alamat 地址
            $table->string('phone', 20)->nullable(); // Nomor Telepon 电话号码
            $table->string('email', 100)->nullable(); // Email 电子邮件
            $table->string('website', 255)->nullable(); // Website 网站
            $table->string('business_type', 255)->nullable(); // Jenis Usaha 业务类型
            $table->string('contact_name', 255)->nullable(); // Nama Kontak Utama 主要联系人姓名
            $table->string('contact_position', 255)->nullable(); // Jabatan Kontak 主要联系人职位
            $table->string('contact_phone', 20)->nullable(); // Nomor Telepon Kontak 联系人电话
            $table->string('tax_number', 50)->nullable(); // Nomor Pajak 税号
            $table->string('bank', 100)->nullable(); // Nama Bank 银行名称
            $table->string('account_number', 50)->nullable(); // Nomor Rekening 银行账户号码
            $table->string('account_name', 255)->nullable(); // Nama Pemilik Rekening 银行账户名称
            $table->string('currency', 10)->nullable(); // Mata Uang 货币
            $table->string('payment_terms', 50)->nullable(); // Termin Pembayaran 付款条款
            $table->boolean('is_active')->default(true); // Status 状态
            $table->timestamps(); // created_at & updated_at 时间戳
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('suppliers');
    }
};
