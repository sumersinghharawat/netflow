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
        Schema::create('payout_release_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->decimal('amount', 14,4)->default('0');
            $table->decimal('balance_amount', 14,4)->default('0')->comment('requested balance amount');
            $table->tinyInteger('status')->default('0')->comment('0 : pending, 1: released, 2: cancelled/deleted');
            $table->tinyInteger('read_status')->default('0')->comment('0: no , 1: yes');
            $table->decimal('payout_fee', 14,4)->default('0')->comment('fee amount,after calculations');
            $table->foreignId('payment_method')->constrained('payment_gateway_configs')->onDelete('cascade');
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
        Schema::dropIfExists('payout_release_requests');
    }
};
