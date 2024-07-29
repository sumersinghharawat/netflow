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
        Schema::create('paypal_orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->text('order_id');
            $table->foreignId('package_id')->constrained('packages')->onDelete('cascade');
            $table->double('amount');
            $table->string('currency');
            $table->text('type');
            $table->tinyInteger('status')->default(0)->comment('0 for pending 1 for success 2 for failed');
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
        Schema::dropIfExists('paypal_orders');
    }
};
