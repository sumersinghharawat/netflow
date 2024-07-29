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
        Schema::create('rank_downline_rank', function (Blueprint $table) {
            $table->id();
            $table->foreignId('rank_id')->constrained('ranks')->onDelete('cascade')->onUpdate('cascade');
            $table->foreignId('downline_rank_id')->constrained('ranks')->onDelete('cascade')->onUpdate('cascade');
            $table->integer('count');
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
        //
    }
};
