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
        Schema::create('customfield_values', function (Blueprint $table) {
            $table->id();
            $table->foreignId('customfield_id')->constrained('signup_fields')->onDelete('cascade')->onUpdate('cascade');
            $table->foreignId('user_id')->constrained('users');
            $table->longText('value')->nullable();
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
        Schema::dropIfExists('customfield_values');
    }
};
