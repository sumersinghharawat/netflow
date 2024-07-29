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
        Schema::create('ewallet_transfer_histories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('cascade');
            $table->foreignId('fund_transfer_id')->nullable()->constrained('fund_transfer_details')->onDelete('cascade')->onUpdate('cascade');
            $table->decimal('amount', 14,4)->default(0);
            $table->string('amount_type');
            $table->decimal('balance', 14,4)->default(0);
            $table->string('type')->index('type');
            $table->dateTime('date_added')->default(DB::raw('CURRENT_TIMESTAMP'))->nullable();
            $table->text('transaction_id')->nullable();
            $table->text('transaction_note')->nullable();
            $table->timestamps();
            $table->index(['date_added', 'user_id', 'amount_type'], 'idx_transfer_histories_date_added_user_id_amount_type');

        });
        $prefix = config('database.connections.mysql.prefix');
        DB::statement("ALTER TABLE `{$prefix}ewallet_transfer_histories` ADD `transaction_fee` FLOAT DEFAULT 0 AFTER `transaction_note`");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('ewallet_transfer_histories');
    }
};
