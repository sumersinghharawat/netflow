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
        Schema::create('paypal_payout_batch_failure_histories', function (Blueprint $table) {
            $table->id();
            $table->string('sender_batch_id')->nullable();
            $table->longText('paypal_data')->nullable();
            $table->longText('webhook_data')->nullable();
            $table->longText('exception_message')->nullable();
            $table->longText('data')->nullable();
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
        Schema::dropIfExists('paypal_payout_batch_failure_histories');
    }
};
