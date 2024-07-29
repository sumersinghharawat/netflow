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
        Schema::create('mlm_curl_history', function (Blueprint $table) {
            // $table->unsignedBigInteger('curl_id')->autoIncrement();
            $table->id('curl_id');
            $table->bigInteger('customer_id');
            $table->string('curl_url');
            $table->string('curl_type');
            $table->longText('curl_data');
            $table->longText('curl_result');
            $table->dateTime('curl_date');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('mlm_curl_history');
    }
};
