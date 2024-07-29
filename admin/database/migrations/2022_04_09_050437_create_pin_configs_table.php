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
        Schema::create('pin_configs', function (Blueprint $table) {
            $table->id();
            $table->integer('amount');
            $table->integer('length');
            $table->string('type')->nullable();
            $table->string('character_set');
            $table->integer('max_count');
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
        Schema::dropIfExists('pin_configs');
    }
};
