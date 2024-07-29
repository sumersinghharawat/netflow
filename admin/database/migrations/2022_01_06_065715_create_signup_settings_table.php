<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSignupSettingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('signup_settings', function (Blueprint $table) {
            $table->id();
            $table->tinyInteger('registration_allowed')->default(1)->comment('0:no, 1:yes');
            $table->tinyInteger('sponsor_required')->default(1)->comment('0:no, 1:yes');
            $table->tinyInteger('mail_notification')->default(0)->comment('0:no, 1:yes');
            $table->string('binary_leg')->default('any');
            $table->integer('age_limit')->default(18)->comment('0:no, 1:yes');
            $table->tinyInteger('bank_info_required')->default(0)->comment('0:no, 1:yes');
            $table->tinyInteger('compression_commission')->default(0)->comment('0:no, 1:yes');
            $table->integer('default_country')->default('99'); //TODO default is 99
            $table->tinyInteger('email_verification')->default(0)->comment('0:no, 1:yes');
            $table->tinyInteger('login_unapproved')->default(0)->comment('0:no, 1:yes');
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
        Schema::dropIfExists('signup_settings');
    }
}
