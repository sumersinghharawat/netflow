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
        Schema::table('leg_amounts', function (Blueprint $table) {
            $table->unsignedBigInteger('oc_order_id')->nullable();
            $table->unsignedBigInteger('oc_product_id')->nullable();

            $table->index(['oc_order_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('leg_amounts', function (Blueprint $table) {
            $table->unsignedBigInteger('oc_order_id')->nullable();
            $table->unsignedBigInteger('oc_product_id')->nullable();
        });
    }
};
