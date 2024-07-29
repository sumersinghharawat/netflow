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
        Schema::create('sales_orders', function (Blueprint $table) {
            $table->id();
            $table->string('invoice_no');
            $table->foreignId('user_id')->nullable()->constrained('users');
            $table->foreignId('product_id')->nullable()->constrained('packages')->onDelete('cascade');
            $table->integer('oc_product_id')->nullable()->constrained('oc_product')->onDelete('cascade');
            $table->integer('amount')->default(0)->comment('product_price');
            $table->integer('product_pv')->default(0);
            $table->foreignId('payment_method')->constrained('payment_gateway_configs')->onDelete('cascade');
            $table->foreignId('pending_user_id')->nullable()->constrained('pending_registrations');
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
        Schema::dropIfExists('sales_orders');
    }
};
