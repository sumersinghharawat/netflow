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
        Schema::create('register_jobs', function (Blueprint $table) {
            $table->id();
            $table->longText('data');
            $table->foreignId('payment_method')->constrained('payment_gateway_configs')->onDelete('cascade')->onUpdate('cascade');
            $table->tinyInteger('status')->comment("0:initial, 1:processing, 2:completed, 3:failed")->default(0);
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
        Schema::dropIfExists('register_jobs');
    }
};
