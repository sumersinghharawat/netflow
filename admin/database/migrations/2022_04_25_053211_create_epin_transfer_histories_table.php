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
        Schema::create('epin_transfer_histories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('to_user')->constrained('users')->onDelete('cascade');
            $table->foreignId('from_user')->constrained('users')->onDelete('cascade');
            $table->foreignId('epin_id')->constrained('pin_numbers')->onDelete('cascade');
            $table->string('ip');
            $table->foreignId('done_by')->constrained('users')->onDelete('cascade');
            $table->dateTime('date');
            $table->text('activity');
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
        Schema::dropIfExists('epin_transfer_histories');
    }
};
