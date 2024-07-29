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
        Schema::table('rank_configurations', function (Blueprint $table) {
            $table->tinyInteger('isProduct_dependent')->default(0)->comment('0 : No, 1 : Yes');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('rank_configurations', function (Blueprint $table) {
            $table->tinyInteger('isProduct_dependent')->default(0)->comment('0 : No, 1 : Yes');
        });
    }
};
