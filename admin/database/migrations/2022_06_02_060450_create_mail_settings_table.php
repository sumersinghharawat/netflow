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
        Schema::create('mail_settings', function (Blueprint $table) {
            $table->id();
            $table->string('from_name');
            $table->string('from_email');
            $table->string('smtp_host');
            $table->string('smtp_username');
            $table->string('smtp_password');
            $table->string('smtp_port');
            $table->string('smtp_timeout');
            $table->enum('reg_mailstatus', ['0', '1'])->default('0')->comment('0 for no 1 for yes');
            $table->text('reg_mailcontent');
            $table->string('reg_mailtype');
            $table->enum('smtp_authentication', ['0', '1'])->default('1')->comment('0 for disabled 1 for enabled');
            $table->string('smtp_protocol');
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
        Schema::dropIfExists('mail_settings');
    }
};
