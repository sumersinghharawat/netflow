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
        Schema::table('level_commission_register_packs', function (Blueprint $table) {
            $table->foreignId('package_id')->nullable()->change();
            $table->unsignedBigInteger('oc_product_id')->nullable()->after('package_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('level_commission_register_packs', function (Blueprint $table) {
            $table->foreignId('package_id')->nullable()->change();
            $table->unsignedBigInteger('oc_product_id')->nullable();
        });
    }
};
