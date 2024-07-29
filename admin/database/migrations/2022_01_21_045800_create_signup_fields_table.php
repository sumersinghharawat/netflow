<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSignupFieldsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('signup_fields', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('type');
            $table->string('deafult_value')->nullable();
            $table->tinyInteger('required')->default(0)->comment('1:yes, 0:no');
            $table->integer('sort_order');
            $table->tinyInteger('status')->default(1)->comment('1: active, 0:disabled');
            $table->tinyInteger('editable')->default(0)->comment('1:active, 0:disabled');
            $table->tinyInteger('is_custom')->default(0)->comment('1:yes, 0:no');
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
        Schema::dropIfExists('signup_fields');
    }
}
