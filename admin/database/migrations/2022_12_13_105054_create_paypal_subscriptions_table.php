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
        Schema::create('paypal_subscriptions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->nullable();
            $table->foreignId('product_id')->constrained('packages')->nullable();
            $table->string('plan_id',200)->nullable();
            $table->string('subscription_id',200)->nullable();
            $table->longText('subscription_data')->nullable();
            $table->tinyInteger('status')->comment('0:inactive, 1:active')->default(1);
            $table->decimal('amount', 14,4);
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
        Schema::dropIfExists('paypal_subscriptions');
    }
};
