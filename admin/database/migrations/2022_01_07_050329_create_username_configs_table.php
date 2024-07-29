<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsernameConfigsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('username_configs', function (Blueprint $table) {
            $table->id();
            $table->string('length')->default('6,8');
            $table->tinyInteger('prefix_status')->default(1)->comment('0:no, 1:yes');
            $table->string('prefix')->default('INF');
            $table->string('user_name_type')->default('dynamic');
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
        Schema::dropIfExists('username_configs');
    }
}
