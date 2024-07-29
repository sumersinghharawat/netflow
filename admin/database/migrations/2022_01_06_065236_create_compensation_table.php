<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCompensationTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('compensation', function (Blueprint $table) {
            $table->id();
            $table->tinyInteger('plan_commission')->comment('0 for inactive 1 for active');
            $table->tinyInteger('sponsor_commission')->comment('0 for inactive 1 for active');
            $table->tinyInteger('rank_commission')->comment('0 for inactive 1 for active');
            $table->tinyInteger('referral_commission')->comment('0 for inactive 1 for active');
            $table->tinyInteger('roi_commission')->comment('0 for inactive 1 for active');
            $table->tinyInteger('matching_bonus')->comment('0 for inactive 1 for active');
            $table->tinyInteger('pool_bonus')->comment('0 for inactive 1 for active');
            $table->tinyInteger('fast_start_bonus')->comment('0 for inactive 1 for active');
            $table->tinyInteger('performance_bonus')->comment('0 for inactive 1 for active');
            $table->tinyInteger('sales_Commission')->comment('0 for inactive 1 for active');
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
        Schema::dropIfExists('compensation');
    }
}
