<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRankDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('rank_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('rank_id')->constrained('ranks')->onDelete('cascade')->onUpdate('cascade');
            $table->integer('referral_count')->default(0);
            $table->integer('party_comm')->default(0);
            $table->integer('personal_pv')->default(0);
            $table->integer('group_pv')->default(0);
            $table->string('downline_count')->default(0);
            $table->double('referral_commission')->default(0);
            $table->integer('team_member_count')->default(0);
            $table->tinyInteger('pool_status')->default('0');
            $table->bigInteger('pool_percentage')->default(0);
            $table->tinyInteger('status')->default('1');
            $table->string('delete_status')->default('no');
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
        Schema::dropIfExists('rank_details');
    }
}
