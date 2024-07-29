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
        Schema::create('total_commissions_and_income', function (Blueprint $table) {
            $table->id();
            $table->string('amount_type');
            $table->decimal('total_amount', 14, 4)->default(0);
            $table->decimal('amount_payable', 14, 4)->default(0);
            $table->decimal('purchase_wallet', 14, 4)->default(0);
            $table->index('amount_type');
        });
        $prefix = config('database.connections.mysql.prefix');
        DB::statement("ALTER TABLE `{$prefix}total_commissions_and_income` ADD `service_charge` FLOAT DEFAULT 0 AFTER `purchase_wallet`");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('total_commissions_and_income');
    }
};
