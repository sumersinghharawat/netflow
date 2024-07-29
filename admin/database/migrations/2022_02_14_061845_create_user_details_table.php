<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUserDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users');
            $table->foreignId('sponsor_id')->nullable()->constrained('users');
            $table->foreignId('country_id')->nullable()->constrained('countries');
            $table->foreignId('state_id')->nullable()->constrained('states');
            $table->string('name');
            $table->string('second_name')->nullable();
            $table->text('address')->nullable();
            $table->text('address2')->nullable();
            $table->string('city')->nullable();
            $table->string('pin')->nullable();
            $table->string('mobile')->nullable();
            $table->string('land_phone')->nullable();
            // $table->string('email');
            $table->date('dob')->nullable();
            $table->string('gender');
            $table->text('bitcoin_address')->nullable();
            $table->string('account_number')->nullable();
            $table->string('ifsc')->nullable();
            $table->string('bank')->nullable();
            $table->string('nacct_holder')->default('NA');
            $table->string('branch')->nullable();
            $table->string('pan')->nullable();
            $table->dateTime('join_date');
            $table->string('image')->nullable();
            $table->text('facebbok')->nullable();
            $table->text('twitter')->nullable();
            $table->tinyInteger('bank_info_required')->default(1)->comment('0 for false 1 for true');
            $table->text('paypal')->nullable();
            $table->text('blockchain')->nullable();
            $table->text('bitgo_wallet')->nullable();
            $table->integer('upload_count')->default(0);
            $table->tinyInteger('kyc_status')->default(0)->comment('0 for false 1 for true');
            $table->foreignId('payout_type')->nullable()->constrained('payment_gateway_configs')->onDelete('cascade')->onUpdate('cascade');
            $table->string('banner')->default('banner.jpg');
            $table->integer('read_doc_count')->default(0);
            $table->integer('read_news_count')->default(0);
            $table->string('apiKey')->nullable();
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
        Schema::dropIfExists('user_details');
    }
}
