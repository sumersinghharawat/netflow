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
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string('invoice_no')->nullable();
            $table->unsignedBigInteger('user_id');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->unsignedBigInteger('order_address_id');
            $table->foreign('order_address_id')->references('id')->on('addresses')->onDelete('cascade');
            $table->dateTime('order_date');
            $table->double('total_amount');
            $table->double('total_pv');
            $table->enum('order_status', ['0', '1'])->default('1')->comment('0 for pending 1 for confirmed');
            $table->foreignId('payment_method')->constrained('payment_gateway_configs')->onDelete('cascade')->onUpdate('cascade');
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
        Schema::dropIfExists('orders');
    }
};
