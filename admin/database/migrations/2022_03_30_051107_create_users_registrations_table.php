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
        Schema::create('users_registrations', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->unique();
            $table->string('username')->unique();
            $table->string('name');
            $table->string('second_name')->nullable();
            $table->text('address')->nullable();
            $table->text('address2')->nullable();
            $table->foreignId('country_id')->nullable()->constrained();
            $table->string('country_name')->nullable();
            $table->foreignId('state_id')->nullable()->constarined();
            $table->string('state_name')->nullable();
            $table->string('city')->nullable();
            $table->string('email')->nullable();
            $table->unsignedBigInteger('product_id')->nullable();
            $table->foreign('product_id')->references('id')->on('packages')->onDelete('cascade');
            $table->double('product_pv')->nullable();
            $table->double('product_amount')->nullable();
            $table->double('reg_amount')->nullable();
            $table->double('total_amount')->nullable();
            $table->foreignId('payment_method')->nullable()->constrained('payment_gateway_configs')->onDelete('cascade');
            $table->string('year')->nullable();
            $table->string('year_month')->nullable();
            $table->unsignedBigInteger('oc_product_id')->nullable();
            $table->timestamps();
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('users_registrations');
    }
};
