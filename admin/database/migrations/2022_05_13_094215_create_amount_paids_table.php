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
        Schema::create('amount_paids', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->decimal('amount', 14,4)->default('0');
            $table->datetime('date');
            $table->string('type');
            $table->unsignedBigInteger('transaction_id')->nullable();
            $table->tinyInteger('status')->default('1')->comment('0 for no 1 for yes');
            $table->foreignId('payment_method')->constrained('payment_gateway_configs')->onDelete('cascade')->onUpdate('cascade');
            $table->foreignId('request_id')->nullable()->constrained('payout_release_requests')->onDelete('cascade')->comment('payout releaes request id');
            $table->timestamps();
        });
        $prefix = config('database.connections.mysql.prefix');
        DB::statement("ALTER TABLE `{$prefix}amount_paids` ADD `payout_fee` FLOAT DEFAULT 0 AFTER `type`");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('amount_paids');
    }
};
