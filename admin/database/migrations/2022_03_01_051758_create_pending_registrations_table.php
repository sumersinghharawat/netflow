<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePendingRegistrationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pending_registrations', function (Blueprint $table) {
            $table->id();
            $table->string('username');
            $table->string('email')->nullable();
            $table->unsignedBigInteger('updated_id')->nullable();
            $table->unsignedBigInteger('package_id')->nullable();
            $table->unsignedBigInteger('sponsor_id');
            $table->foreignId('payment_method')->constrained('payment_gateway_configs')->onDelete('cascade')->onUpdate('cascade');
            $table->longText('data');
            $table->string('status')->default('pending')->comment("pending, active, rejected");
            $table->dateTime('date_added');
            $table->dateTime('date_modified')->nullable();
            $table->string('email_verification_status');
            $table->string('default_currency')->nullable();
            $table->foreign('updated_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('sponsor_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('package_id')->references('id')->on('packages')->onDelete('cascade');
            $table->longText('user_tokens')->nullable();
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
        Schema::dropIfExists('pending_registrations');
    }
}
