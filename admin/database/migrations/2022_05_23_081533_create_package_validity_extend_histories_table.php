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
        Schema::create('package_validity_extend_histories', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->unsignedBigInteger('package_id')->nullable();
            $table->foreign('package_id')->references('id')->on('packages')->nullable()->onDelete('cascade');
            $table->string('invoice_id');
            $table->decimal('total_amount', 14,4)->default(0);
            $table->string('payment_type');
            $table->string('pay_type');
            $table->longText('renewal_details')->nullable();
            $table->tinyInteger('renewal_status')->comment('0: pending 1 : active')->default(1);
            $table->text('receipt')->nullable();
            $table->timestamps();
        });
        $prefix = config('database.connections.mysql.prefix');
        DB::statement("ALTER TABLE `{$prefix}package_validity_extend_histories` ADD `product_pv` FLOAT DEFAULT 0 AFTER `total_amount`");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('package_validity_extend_histories');
    }
};
