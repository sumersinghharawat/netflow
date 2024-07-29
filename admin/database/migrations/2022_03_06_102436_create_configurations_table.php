<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CreateConfigurationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('configurations', function (Blueprint $table) {
            $table->id();
            $table->string('pair_ceiling_type');
            $table->string('start_date');
            $table->string('end_date');
            $table->tinyInteger('sms_status')->default(0)->comment('0:no, yes:1');
            $table->bigInteger('max_pin_count')->comment('E-Pin count');
            $table->string('pair_commission_type');
            $table->bigInteger('depth_ceiling');
            $table->bigInteger('width_ceiling');
            $table->string('level_commission_type');
            $table->string('profile_updation_history');
            $table->bigInteger('xup_level');
            $table->bigInteger('upload_config');
            $table->bigInteger('pair_ceiling_monthly');
            $table->tinyInteger('pool_bonus_percent')->default(0);
            $table->string('sponsor_commission_type');
            $table->string('commission_criteria');
            $table->string('referral_commission_type');
            $table->bigInteger('commission_upto_level')->default(3);
            $table->string('roi_period');
            $table->text('roi_days_skip')->nullable();
            $table->string('roi_criteria');
            $table->tinyInteger('skip_blocked_users_commission')->default(1)->comment('0:no, 1:yes');
            $table->string('pool_bonus_period');
            $table->string('pool_bonus_criteria');
            $table->string('pool_distribution_criteria');
            $table->string('matching_criteria');
            $table->bigInteger('matching_upto_level')->default(3);
            $table->string('sales_criteria');
            $table->string('sales_type');
            $table->bigInteger('sales_level')->default(30);
            $table->text('api_key')->nullable();
            $table->string('tree_icon_based');
            $table->string('active_tree_icon');
            $table->string('inactive_tree_icon');
            $table->string('default_package_tree_icon');
            $table->string('default_rank_tree_icon');
            $table->foreignId('default_rank')->nullable()->constrained('ranks')->onDelete('cascade')->onUpdate('cascade');
            $table->string('rank_calculation')->default('instant')->comment('instant, daily, weekly, monthly');
            $table->integer('tree_depth')->default(4)->comment('< tree_depth');
            $table->integer('tree_width')->default(3)->comment('== tree_width');
            $table->timestamps();
        });

        $prefix = config('database.connections.mysql.prefix');
        DB::statement("ALTER TABLE `{$prefix}configurations` ADD `tds` FLOAT DEFAULT 0 AFTER `id`");
        DB::statement("ALTER TABLE `{$prefix}configurations` ADD `service_charge` FLOAT DEFAULT 0 AFTER `tds`");
        DB::statement("ALTER TABLE `{$prefix}configurations` ADD `pair_price` FLOAT DEFAULT 0 AFTER `service_charge`");
        DB::statement("ALTER TABLE `{$prefix}configurations` ADD `pair_ceiling` FLOAT DEFAULT 0 AFTER `pair_price`");
        DB::statement("ALTER TABLE `{$prefix}configurations` ADD `product_point_value` FLOAT DEFAULT 0 AFTER `pair_ceiling_type`");
        DB::statement("ALTER TABLE `{$prefix}configurations` ADD `pair_value` FLOAT DEFAULT 0 AFTER `product_point_value`");
        DB::statement("ALTER TABLE `{$prefix}configurations` ADD `reg_amount` FLOAT DEFAULT 0 AFTER `sms_status`");
        DB::statement("ALTER TABLE `{$prefix}configurations` ADD `referral_amount` FLOAT DEFAULT 0 AFTER `reg_amount`");
        DB::statement("ALTER TABLE `{$prefix}configurations` ADD `trans_fee` FLOAT DEFAULT 0 AFTER `level_commission_type`");
        DB::statement("ALTER TABLE `{$prefix}configurations` ADD `override_commission` FLOAT DEFAULT 0 AFTER `trans_fee`");
        DB::statement("ALTER TABLE `{$prefix}configurations` ADD `purchase_income_perc` FLOAT DEFAULT 0 AFTER `sponsor_commission_type`");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('configurations');
    }
}
