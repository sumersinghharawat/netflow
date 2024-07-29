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
        Schema::create('ewallet_commission_histories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('cascade');
            $table->foreignId('from_id')->nullable()->constrained('users')->onDelete('cascade');
            $table->foreignId('leg_amount_id')->constrained('leg_amounts')->onDelete('cascade')->onUpdate('cascade')->comment('leg_amount');
            $table->decimal('amount', 14,4)->default(0);
            $table->decimal('purchase_wallet', 14,4)->default(0);
            $table->decimal('balance', 14,4)->default(0);
            $table->string('amount_type')->index('amount_type');
            $table->dateTime('date_added')->default(DB::raw('CURRENT_TIMESTAMP'))->nullable();
            $table->timestamps();
            $table->index(['date_added', 'user_id', 'amount_type'], 'idx_commission_histories_date_added_user_id_amount_type');
            $table->index(['purchase_wallet', 'amount', 'amount_type'], 'idx_amount_type_total');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('ewallet_commission_histories');
    }
};
