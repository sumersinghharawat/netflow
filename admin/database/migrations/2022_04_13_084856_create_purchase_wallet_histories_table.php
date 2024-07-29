<?php

use Carbon\Carbon;
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
        Schema::create('purchase_wallet_histories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade')->onUpdate('cascade');
            $table->foreignId('from_user_id')->nullable()->constrained('users')->onDelete('cascade')->onUpdate('cascade');
            $table->integer('transaction_id');
            $table->decimal('amount', 14,4);
            $table->decimal('purchase_wallet', 14,4);
            $table->decimal('balance', 14,4)->default(0);
            $table->string('amount_type');
            $table->date('date')->nullable();
            $table->string('type')->nullable();
            $table->timestamps();
            $table->index(['amount_type']);
        });
        $prefix = config('database.connections.mysql.prefix');
        DB::statement("ALTER TABLE `{$prefix}purchase_wallet_histories` ADD `tds` FLOAT DEFAULT 0 AFTER `date`");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('purchase_wallet_histories');
    }
};
