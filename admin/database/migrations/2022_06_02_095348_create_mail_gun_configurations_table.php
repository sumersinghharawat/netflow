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
        Schema::create('mail_gun_configurations', function (Blueprint $table) {
            $table->id();
            $table->string('fromName');
            $table->string('fromEmail');
            $table->string('replyTo');
            $table->string('domain');
            $table->string('apiKey');
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
        Schema::dropIfExists('mail_gun_configurations');
    }
};
