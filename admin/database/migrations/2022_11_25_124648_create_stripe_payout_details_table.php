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
        Schema::create('stripe_payout_details', function (Blueprint $table) {
            $table->id();
            $table->string('account_id');
            $table->text('transaction_id');
            $table->foreignId('user_id')->constrained('users');
            $table->longText('response_data')->nullable();
            $table->longText('payout_data');
            $table->longText('webhook_data')->nullable();
            $table->longText('stripe_data')->nullable();
            $table->tinyInteger('status')->comment('0 for false 1 for true');
            $table->longText('data')->nullable();
            $table->foreignId('reference_id')->constrained('amount_paids')->onDelete('cascade')->comments('amount_paids table id');
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
        Schema::dropIfExists('stripe_payout_details');
    }
};
