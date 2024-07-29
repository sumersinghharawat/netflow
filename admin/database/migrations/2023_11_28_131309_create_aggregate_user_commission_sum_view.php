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
        if($prefix) {
            DB::statement("DROP VIEW IF EXISTS {$prefix}aggregateUserCommissionSum;");
            $sql = "CREATE VIEW {$prefix}aggregateUserCommissionSum AS 
                SELECT user_id, SUM(amount_payable) AS total_amount_payable 
                FROM {$prefix}aggregate_user_commission_and_incomes 
                GROUP BY user_id";

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
        Schema::dropIfExists('aggregate_user_commission_sum_view');
    }
};
