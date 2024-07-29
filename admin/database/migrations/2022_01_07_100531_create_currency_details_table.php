<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CreateCurrencyDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('currency_details', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('code');
            $table->string('symbol_left')->nullable();
            $table->string('symbol_right')->nullable();
            $table->tinyInteger('status')->default(0);
            $table->integer('default');
            $table->string('delete_status')->default('yes');
            $table->timestamps();
        });

        $prefix = config('database.connections.mysql.prefix');
        DB::statement("ALTER TABLE `{$prefix}currency_details` ADD `value` FLOAT DEFAULT 0 AFTER `title`");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('currency_details');
    }
}
