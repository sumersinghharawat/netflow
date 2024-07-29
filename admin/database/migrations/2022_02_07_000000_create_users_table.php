<?php

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('ecom_customer_ref_id')->nullable();
            $table->string('username')->unique();
            $table->string('user_type')->default('user')->index('user_type');
            $table->string('password');
            $table->string('email')->default('email@email.com');
            $table->unsignedBigInteger('user_rank_id')->nullable();
            $table->tinyInteger('active')->default(1)->comment('1: Active, 0: Blocked');
            $table->string('position')->nullable()->index('position');
            $table->integer('leg_position')->default(0);
            $table->integer('sponsor_index')->default(0)->nullable()->index('sponsor_index');
            $table->unsignedBigInteger('father_id')->nullable();
            $table->unsignedBigInteger('sponsor_id')->nullable();
            $table->integer('first_pair')->default(0);
            $table->integer('total_leg')->default(0)->comment('pairs');
            $table->unsignedBigInteger('product_id')->nullable();
            $table->dateTime('product_validity')->nullable();
            $table->dateTime('date_of_joining')->default(DB::raw('CURRENT_TIMESTAMP'))->nullable();
            $table->bigInteger('user_level')->default(0);
            $table->bigInteger('sponsor_level')->default(0);
            $table->string('register_by_using')->nullable();
            $table->text('api_key')->nullable();
            $table->tinyInteger('auto_renewal_status')->default(0)->comment('1: Active, 0: Inactive');
            $table->foreignId('default_lang')->nullable()->constrained('languages')->onDelete('cascade')->onUpdate('cascade');
            $table->foreignId('default_currency')->nullable()->constrained('currency_details')->onDelete('cascade')->onUpdate('cascade');
            $table->string('goc_key')->nullable();
            $table->bigInteger('personal_pv')->default(0);
            $table->bigInteger('group_pv')->default(0);
            $table->string('binary_leg')->default('any');
            $table->text('inf_token')->nullable();
            $table->unsignedBigInteger('oc_product_id')->nullable();
            $table->integer('force_logout')->default(0)->comment('0 for disable 1 for enable');
            $table->integer('google_auth_status')->default(0)->comment('0 for disable 1 for enable');
            $table->integer('delete_status')->default(1)->comment('0 for delete 1 for active');
            $table->foreign('user_rank_id')->references('id')->on('ranks')->onDelete('cascade');
            $table->foreign('father_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('sponsor_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('product_id')->references('id')->on('packages')->onDelete('cascade');
            $table->rememberToken();
            $table->unique(['email', 'user_type']);
            $table->integer('from_tree')->default(0)->comment('1: true, 0: false');
            $table->string('year')->nullable();
            $table->string('year_month')->nullable();
            $table->timestamps();
        });
        $prefix = config('database.connections.mysql.prefix');
        DB::statement("ALTER TABLE `{$prefix}users` ADD `total_left_carry` FLOAT DEFAULT 0 AFTER total_leg");
        DB::statement("ALTER TABLE `{$prefix}users` ADD `total_right_carry` FLOAT DEFAULT 0 AFTER total_left_carry");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('users');
    }
}
