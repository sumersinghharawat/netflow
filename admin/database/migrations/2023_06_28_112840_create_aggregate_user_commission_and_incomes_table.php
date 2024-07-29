<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('aggregate_user_commission_and_incomes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade')->onUpdate('cascade');
            $table->string('amount_type');
            $table->decimal('total_amount', 14, 4)->default(0);
            $table->decimal('amount_payable', 14, 4)->default(0);
            $table->decimal('purchase_wallet', 14, 4)->default(0);
            $table->decimal('tds', 14, 4)->default(0);
            $table->decimal('service_charge', 14, 4)->default(0);
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
        Schema::dropIfExists('aggregate_user_commission_and_incomes');
    }
};
