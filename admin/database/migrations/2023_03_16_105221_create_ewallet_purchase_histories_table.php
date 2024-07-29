<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ewallet_purchase_histories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('cascade');
            $table->bigInteger('reference_id')->nullable()->unsigned()->index('refernece')->comment('amount_paids, payout_release_request, ewallet_payment_details etc');
            $table->string('ewallet_type');
            $table->decimal('amount', 14,4)->default(0);
            $table->decimal('balance', 14,4)->default(0);
            $table->string('amount_type');
            $table->string('type')->index('type');
            $table->dateTime('date_added')->default(DB::raw('CURRENT_TIMESTAMP'))->nullable();
            $table->timestamps();
            $table->index(['date_added', 'user_id', 'amount_type'], 'idx_purchase_histories_date_added_user_id_amount_type');
        });
        $prefix = config('database.connections.mysql.prefix');
        DB::statement("ALTER TABLE `{$prefix}ewallet_purchase_histories` ADD `transaction_fee` FLOAT DEFAULT 0 AFTER `type`");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('ewallet_purchase_histories');
    }
};
