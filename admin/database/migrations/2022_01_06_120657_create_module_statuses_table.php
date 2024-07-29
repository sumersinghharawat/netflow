<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateModuleStatusesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('module_statuses', function (Blueprint $table) {
            $table->id();
            $table->string('mlm_plan');
            $table->string('first_pair')->nullable();
            $table->tinyInteger('pin_status')->default(0)->comment('0 : disabled, 1: enabled');
            $table->tinyInteger('product_status')->default(0)->comment('0 : disabled, 1: enabled');
            $table->tinyInteger('sms_status')->default(0)->comment('0 : disabled, 1: enabled');
            $table->tinyInteger('mailbox_status')->default(0)->comment('0 : disabled, 1: enabled');
            $table->tinyInteger('referral_status')->default(0)->comment('0 : disabled, 1: enabled');
            $table->tinyInteger('ewallet_status')->default(0)->comment('0 : disabled, 1: enabled');
            $table->tinyInteger('employee_status')->default(0)->comment('0 : disabled, 1: enabled');
            $table->string('payout_release_status')->default('ewallet_request');
            $table->tinyInteger('upload_status')->default(0)->comment('0 : disabled, 1: enabled');
            $table->tinyInteger('sponsor_tree_status')->default(0)->comment('0 : disabled, 1: enabled');
            $table->tinyInteger('rank_status')->default(0)->comment('0 : disabled, 1: enabled');
            $table->tinyInteger('rank_status_demo')->default(0)->comment('0 : disabled, 1: enabled');
            $table->tinyInteger('lang_status')->default(0)->comment('0 : disabled, 1: enabled');
            $table->tinyInteger('help_status')->default(0)->comment('0 : disabled, 1: enabled');
            $table->tinyInteger('shuffle_status')->default(0)->comment('0 : disabled, 1: enabled');
            $table->tinyInteger('statcounter_status')->default(0)->comment('0 : disabled, 1: enabled');
            $table->tinyInteger('footer_demo_status')->default(0)->comment('0 : disabled, 1: enabled');
            $table->tinyInteger('captcha_status')->default(0)->comment('0 : disabled, 1: enabled');
            $table->tinyInteger('sponsor_commission_status')->default(0)->comment('0 : disabled, 1: enabled');
            $table->tinyInteger('lead_capture_status')->default(0)->comment('0 : disabled, 1: enabled');
            $table->tinyInteger('ticket_system_status')->default(0)->comment('0 : disabled, 1: enabled');
            $table->tinyInteger('currency_conversion_status')->default(0)->comment('0 : disabled, 1: enabled');
            $table->tinyInteger('ecom_status')->comment('open cart changed into ecomerce')->default(0)->comment('0 : disabled, 1: enabled');
            $table->tinyInteger('live_chat_status')->default(0)->comment('0 : disabled, 1: enabled');
            $table->tinyInteger('ecom_status_demo')->comment('open cart changed into ecomerce')->default(0)->comment('0 : disabled, 1: enabled');
            $table->tinyInteger('lead_capture_status_demo')->default(0)->comment('0 : disabled, 1: enabled');
            $table->tinyInteger('ticket_system_status_demo')->default(0)->comment('0 : disabled, 1: enabled');
            $table->tinyInteger('autoresponder_status')->default(0)->comment('0 : disabled, 1: enabled');
            $table->tinyInteger('autoresponder_status_demo')->default(0)->comment('0 : disabled, 1: enabled');
            $table->tinyInteger('table_status')->default(0)->comment('0 : disabled, 1: enabled');
            $table->string('lcp_type')->default('lcp');
            $table->tinyInteger('payment_gateway_status')->default(0)->comment('0 : disabled, 1: enabled');
            $table->tinyInteger('bitcoin_status')->default(0)->comment('0 : disabled, 1: enabled');
            $table->tinyInteger('repurchase_status')->default(0)->comment('0 : disabled, 1: enabled');
            $table->tinyInteger('repurchase_status_demo')->default(0)->comment('0 : disabled, 1: enabled');
            $table->tinyInteger('google_auth_status')->default(0)->comment('0 : disabled, 1: enabled');
            $table->tinyInteger('package_upgrade')->default(1)->comment('0 : disabled, 1: enabled');
            $table->tinyInteger('package_upgrade_demo')->default(0)->comment('0 : disabled, 1: enabled');
            $table->tinyInteger('maintenance_status_demo')->default(0)->comment('0 : disabled, 1: enabled');
            $table->tinyInteger('maintenance_status')->default(0)->comment('0 : disabled, 1: enabled');
            $table->tinyInteger('lang_status_demo')->default(0)->comment('0 : disabled, 1: enabled');
            $table->tinyInteger('employee_status_demo')->default(0)->comment('0 : disabled, 1: enabled');
            $table->tinyInteger('sms_status_demo')->default(0)->comment('0 : disabled, 1: enabled');
            $table->tinyInteger('pin_status_demo')->default(0)->comment('0 : disabled, 1: enabled');
            $table->tinyInteger('roi_status')->default(0)->comment('0 : disabled, 1: enabled');
            $table->tinyInteger('basic_demo_status')->default(0)->comment('0 : disabled, 1: enabled');
            $table->tinyInteger('xup_status')->default(0)->comment('0 : disabled, 1: enabled');
            $table->tinyInteger('hyip_status')->default(0)->comment('0 : disabled, 1: enabled');
            $table->tinyInteger('group_pv')->default(0)->comment('0 : disabled, 1: enabled');
            $table->tinyInteger('personal_pv')->default(0)->comment('0 : disabled, 1: enabled');
            $table->tinyInteger('kyc_status')->default(0)->comment('0 : disabled, 1: enabled');
            $table->tinyInteger('signup_config')->default(0)->comment('0 : disabled, 1: enabled');
            $table->tinyInteger('mail_gun_status')->default(0)->comment('0 : disabled, 1: enabled');
            $table->tinyInteger('auto_ship_status')->default(0)->comment('0 : disabled, 1: enabled');
            $table->tinyInteger('downline_count_rank')->default(0)->comment('0 : disabled, 1: enabled');
            $table->tinyInteger('downline_purchase_rank')->default(0)->comment('0 : disabled, 1: enabled');
            $table->tinyInteger('otp_modal')->default(0)->comment('0 : disabled, 1: enabled');
            $table->tinyInteger('gdpr')->default(0)->comment('0 : disabled, 1: enabled');
            $table->tinyInteger('purchase_wallet')->default(0)->comment('0 : disabled, 1: enabled');
            $table->tinyInteger('crowd_fund')->default(0)->comment('0 : disabled, 1: enabled');
            $table->tinyInteger('compression_status')->default(0)->comment('0 : disabled, 1: enabled');
            $table->tinyInteger('promotion_status')->default(0)->comment('0 : disabled, 1: enabled');
            $table->tinyInteger('promotion_status_demo')->default(0)->comment('0 : disabled, 1: enabled');
            $table->tinyInteger('subscription_status')->default(0)->comment('0 : disabled, 1: enabled');
            $table->tinyInteger('subscription_status_demo')->default(0)->comment('0 : disabled, 1: enabled');
            $table->tinyInteger('tree_updation')->default(0)->comment('0 : disabled, 1: enabled');
            $table->tinyInteger('cache_status')->default(0)->comment('0 : disabled, 1: enabled');
            $table->tinyInteger('multilang_status')->default(0)->comment('0 for false 1 true');
            $table->string('default_lang_code')->default('en');
            $table->tinyInteger('multi_currency_status')->default(0)->comment('0 for false 1 true');
            $table->string('default_currency_code')->default('USD');
            $table->tinyInteger('replicated_site_status')->default(0)->comment('0 for false 1 true');
            $table->tinyInteger('replicated_site_status_demo')->default(0)->comment('0 for false 1 true');
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
        Schema::dropIfExists('module_statuses');
    }
}
