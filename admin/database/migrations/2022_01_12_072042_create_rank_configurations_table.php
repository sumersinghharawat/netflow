<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRankConfigurationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('rank_configurations', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug');
            $table->string('calculation')->default('instant');
            $table->tinyInteger('status')->comment('0: disabled, 1: enabled')->default(0);
            // $table->string('rank_calculation');
            // $table->integer('default_rank_id')->nullable();
            // $table->integer('referral_count');
            // $table->integer('personal_pv');
            // $table->integer('group_pv');
            // $table->integer('joinee_package');
            // $table->integer('downline_member_count');
            // $table->integer('downline_purchase_count');
            // $table->integer('downline_rank');
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
        Schema::dropIfExists('rank_configurations');
    }
}
