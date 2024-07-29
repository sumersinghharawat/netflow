<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CreateLevelCommissionRegisterPacksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('level_commission_register_packs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('package_id')->constrained('packages')->onDelete('cascade');
            $table->integer('level');
            $table->timestamps();
        });
        $prefix = config('database.connections.mysql.prefix');
        DB::statement("ALTER TABLE `{$prefix}level_commission_register_packs` ADD `commission` FLOAT DEFAULT 0 AFTER `level`");
        DB::statement("ALTER TABLE `{$prefix}level_commission_register_packs` ADD `percentage` FLOAT DEFAULT 0 COMMENT '%' AFTER `commission`");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('level_commission_register_packs');
    }
}
