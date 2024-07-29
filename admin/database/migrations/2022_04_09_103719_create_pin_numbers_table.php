<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
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
        Schema::create('pin_numbers', function (Blueprint $table) {
            $table->id();
            $table->string('numbers');
            $table->dateTime('alloc_date');
            $table->string('status')->default('active')->comment('active, blocked, expiired, deleted');
            $table->tinyInteger('purchase_status')->default(0)->comment('0 false 1 : true');
            // $table->foreignId('used_user')->nullable()->constrained('users');
            $table->foreignId('generated_user')->constrained('users');
            $table->foreignId('allocated_user')->constrained('users');
            $table->dateTime('uploaded_date');
            $table->dateTime('expiry_date');
            $table->double('amount')->default(0);
            $table->double('balance_amount')->default(0);
            $table->text('transaction_id')->nullable();
            // $table->tinyInteger('is_used')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('pin_numbers');
    }
};
