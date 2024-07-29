<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
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
        $prefix = config('database.connections.mysql.prefix');
        if($prefix){
            DB::statement("DROP VIEW IF EXISTS {$prefix}user_registration_views; ");
            $sql = "CREATE VIEW {$prefix}user_registration_views AS
                SELECT
                SUM(CAST(reg_amount AS DECIMAL(14,4))) AS regAmount,
                SUM(CAST(product_amount AS DECIMAL(14,4))) AS productAmount,
                SUM(CAST(total_amount AS DECIMAL(14,4))) AS totalAmount
                FROM {$prefix}users_registrations";
            DB::statement($sql);
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('user_registration_views');
    }
};
