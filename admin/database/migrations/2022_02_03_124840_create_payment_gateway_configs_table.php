<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePaymentGatewayConfigsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('payment_gateway_configs', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->tinyInteger('status')->default(0)->comment('0 for disable 1 for enable');
            $table->string('logo')->nullable();
            $table->integer('sort_order');
            $table->string('mode')->default('test');
            $table->tinyInteger('payout_status')->default(0)->comment('0 for disable 1 for enable');
            $table->tinyInteger('payout_sort_order')->default(0);
            $table->tinyInteger('registration')->default(0)->comment('0 for disable 1 for enable');
            $table->tinyInteger('repurchase')->default(0)->comment('0 for disable 1 for enable');
            $table->tinyInteger('membership_renewal')->default(0)->comment('0 for disable 1 for enable');
            $table->tinyInteger('admin_only')->default(0)->comment('0 for disable 1 for enable');
            $table->tinyInteger('gate_way')->default(0)->comment('0 for disable 1 for enable');
            $table->tinyInteger('payment_only')->default(0)->comment('0 for disable 1 for enable');
            $table->tinyInteger('upgradation')->default(0)->comment('0 for disable 1 for enable');
            $table->tinyInteger('reg_pending_status')->default(0)->comment('0 for disable 1 for enable'); // registration complets only approve pending
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
        Schema::dropIfExists('payment_gateway_configs');
    }
}
