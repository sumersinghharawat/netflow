<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePasswordPoliciesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('password_policies', function (Blueprint $table) {
            $table->id();
            $table->tinyInteger('enable_policy');
            $table->tinyInteger('mixed_case')->comment('0:no, 1:yes');
            $table->tinyInteger('number')->comment('0:no, 1:yes');
            $table->tinyInteger('sp_char')->comment('0:no, 1:yes');
            $table->tinyInteger('min_length')->comment('0:no, 1:yes');
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
        Schema::dropIfExists('password_policies');
    }
}
