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
        Schema::table('module_statuses', function (Blueprint $table) {
            $table->tinyInteger('crm_status')->after('lead_capture_status')->default(0)->comment('0 : disabled, 1: enabled');
        });
    }
    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('module_statuses', function (Blueprint $table) {
            //
        });
    }
};
