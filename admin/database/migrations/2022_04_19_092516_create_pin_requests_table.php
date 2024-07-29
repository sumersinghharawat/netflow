<?php

use Carbon\Carbon;
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
        Schema::create('pin_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->integer('requested_pin_count')->default(0);
            $table->integer('allotted_pin_count')->default(0);
            $table->dateTime('requested_date')->default(DB::raw('CURRENT_TIMESTAMP'))->nullable();
            $table->dateTime('expiry_date');
            $table->integer('status')->default(1)->comment('0 for inactive 1 for active 2 for delete');
            $table->string('remarks')->default('NA');
            $table->integer('pin_amount');
            $table->integer('read_status')->default(0)->comment('0 for false 1 for true');
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
        Schema::dropIfExists('pin_requests');
    }
};
