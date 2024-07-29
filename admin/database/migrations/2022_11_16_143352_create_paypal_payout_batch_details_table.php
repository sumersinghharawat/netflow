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
        Schema::create('paypal_payout_batch_details', function (Blueprint $table) {
            $table->id();
            $table->string('batch_id')->comment('given from paypal response');
            $table->foreignId('user_id')->constrained('users');
            $table->longText('response_data');
            $table->longText('payout_data');
            $table->longText('webhook_data')->nullable();
            $table->longText('paypal_data');
            $table->foreignId('reference_id')->constrained('amount_paids')->onDelete('cascade')->comments('amount_paids table id');
            $table->string('batch_status')->comment('[PENDING, SUCCESS, DENIED] which provided by paypal batch_status');
            $table->tinyInteger('status')->comment('0 for false , 1 for true');
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
        Schema::dropIfExists('paypal_payout_batch_details');
    }
};
