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
        Schema::create('amount_types', function (Blueprint $table) {
            $table->id();
            $table->string('type');
            $table->string('view_type');
            $table->enum('status', ['0', '1'])->default('0')->comment('0 for no 1 for yes');
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
        Schema::dropIfExists('amount_types');
    }
};
