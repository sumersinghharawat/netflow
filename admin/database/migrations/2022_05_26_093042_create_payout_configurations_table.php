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
        Schema::create('payout_configurations', function (Blueprint $table) {
            $table->id();
            $table->string('release_type');
            $table->integer('min_payout')->default(0);
            $table->integer('request_validity')->comment('in days');
            $table->integer('max_payout');
            $table->tinyInteger('mail_status')->comment('0: no 1 : yes')->default(0);
            $table->integer('fee_amount');
            $table->enum('fee_mode', ['flat', 'percentage']);
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
        Schema::dropIfExists('payout_configurations');
    }
};
