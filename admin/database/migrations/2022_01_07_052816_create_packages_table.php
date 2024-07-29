<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CreatePackagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('packages', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('type');
            $table->tinyInteger('active')->default(1)->comment('0: disabled 1: enabled');
            $table->string('product_id')->unique();
            $table->integer('quantity')->nullable();
            $table->string('image')->nullable();
            $table->text('description')->nullable();
            $table->integer('days')->default(0)->comment('roi in days');
            $table->integer('validity')->default(0)->comment('subscription period in months');
            $table->foreignId('category_id')->nullable()->constrained('repurchase_categories')->onDelete('cascade');
            $table->string('tree_icon')->nullable();
            $table->integer('reentry_limit')->default(0);
            $table->timestamps();
        });
        $prefix = config('database.connections.mysql.prefix');
        DB::statement("ALTER TABLE `{$prefix}packages` ADD `price` FLOAT DEFAULT 0 AFTER `product_id`");
        DB::statement("ALTER TABLE `{$prefix}packages` ADD `bv_value` FLOAT NULL AFTER `price`");
        DB::statement("ALTER TABLE `{$prefix}packages` ADD `pair_value` FLOAT DEFAULT '0' COMMENT 'business volume' AFTER `bv_value`");
        DB::statement("ALTER TABLE `{$prefix}packages` ADD `referral_commission` FLOAT NULL AFTER quantity");
        DB::statement("ALTER TABLE `{$prefix}packages` ADD `pair_price` FLOAT NULL AFTER `referral_commission`");
        DB::statement("ALTER TABLE `{$prefix}packages` ADD `roi` FLOAT DEFAULT 0 AFTER `image`");
        DB::statement("ALTER TABLE `{$prefix}packages` ADD `joinee_commission` FLOAT NULL AFTER `validity`");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('packages');
    }
}
