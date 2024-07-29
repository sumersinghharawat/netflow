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
        Schema::create('contacts', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email');
            $table->text('address');
            $table->string('phone');
            $table->text('contact_info');
            $table->foreignId('owner_id')->constrained('users');
            $table->tinyInteger('status')->comment('0:no and 1:yes')->default(1);
            $table->string('mail_added_date');
            $table->tinyInteger('read_msg')->comment('0:no and 1:yes')->default(0);
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
        Schema::dropIfExists('contacts');
    }
};
