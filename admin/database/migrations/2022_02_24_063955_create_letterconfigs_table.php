<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLetterconfigsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('letterconfigs', function (Blueprint $table) {
            $table->id();
            $table->string('company_name')->dafault('NA');
            $table->longText('company_address')->nullable();
            $table->longText('content')->nullable();
            $table->string('logo')->dafault('NA');
            $table->string('place')->dafault('NA');
            $table->unsignedBigInteger('language_id')->nullable();
            $table->foreign('language_id')->references('id')->on('languages')->onDelete('cascade');
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
        Schema::dropIfExists('letterconfigs');
    }
}
