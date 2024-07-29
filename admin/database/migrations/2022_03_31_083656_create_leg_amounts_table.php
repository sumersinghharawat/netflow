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
        Schema::create('leg_amounts', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('from_id')->nullable();
            $table->decimal('total_amount', 14,4)->default(0);
            $table->decimal('amount_payable',14,4)->default(0);
            $table->decimal('purchase_wallet',14,4)->default(0);
            $table->string('amount_type');
            $table->dateTime('date_of_submission')->default(DB::raw('CURRENT_TIMESTAMP'))->nullable();
            $table->dateTime('released_date')->nullable();
            $table->integer('user_level')->default(0);
            $table->unsignedBigInteger('product_id')->nullable();
            $table->timestamps();
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('from_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('product_id')->references('id')->on('packages')->onDelete('cascade');
            $table->index(['amount_type']);
        });

        $prefix = config('database.connections.mysql.prefix');
        DB::statement("ALTER TABLE `{$prefix}leg_amounts` ADD `total_leg` FLOAT DEFAULT 0 AFTER `from_id`");
        DB::statement("ALTER TABLE `{$prefix}leg_amounts` ADD `left_leg` FLOAT DEFAULT 0 AFTER `total_leg`");
        DB::statement("ALTER TABLE `{$prefix}leg_amounts` ADD `right_leg` FLOAT DEFAULT 0 AFTER `left_leg`");
        DB::statement("ALTER TABLE `{$prefix}leg_amounts` ADD `tds` FLOAT DEFAULT 0 AFTER `amount_type`");
        DB::statement("ALTER TABLE `{$prefix}leg_amounts` ADD `service_charge` FLOAT DEFAULT 0 AFTER `tds`");
        DB::statement("ALTER TABLE `{$prefix}leg_amounts` ADD `pair_value` FLOAT DEFAULT 0 AFTER `product_id`");
        DB::statement("ALTER TABLE `{$prefix}leg_amounts` ADD `product_value` FLOAT DEFAULT 0 AFTER `pair_value`");

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('leg_amounts');
    }
};
