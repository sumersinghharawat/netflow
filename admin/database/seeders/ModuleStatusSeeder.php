<?php

namespace Database\Seeders;

use App\Models\ModuleStatus;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ModuleStatusSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('module_statuses')->delete();
        $data = [
'mlm_plan' => 'Unilevel',
'first_pair' => '1:1',
'pin_status' => 0,
'product_status' => 1,
'sms_status' => 0,
'mailbox_status' => 1,
'referral_status' => 1,
'ewallet_status' => 1,
'employee_status' => 0,
'payout_release_status' => 'ewallet_request',
'upload_status' => 1,
'sponsor_tree_status' => 1,
'rank_status' => 0,
'rank_status_demo' => 0,
'lang_status' => 0,
'help_status' => 0,
'shuffle_status' => 0,
'statcounter_status' => 1,
'footer_demo_status' => 1,
'captcha_status' => 1,
'sponsor_commission_status' => 1,
'lead_capture_status' => 0,
'ticket_system_status' => 0,
'currency_conversion_status' => 0,
'ecom_status' => 0,
'live_chat_status' => 0,
'ecom_status_demo' => 0,
'lead_capture_status_demo' => 0,
'ticket_system_status_demo' => 0,
'autoresponder_status' => 0,
'autoresponder_status_demo' => 0,
'table_status' => 0,
'lcp_type' => 'lcp',
'payment_gateway_status' => 0,
'bitcoin_status' => 0,
'repurchase_status' => 0,
'repurchase_status_demo' => 0,
'google_auth_status' => 0,
'package_upgrade' => 0,
'package_upgrade_demo' => 0,
'maintenance_status_demo' => 1,
'maintenance_status' => 1,
'lang_status_demo' => 0,
'employee_status_demo' => 0,
'sms_status_demo' => 0,
'pin_status_demo' => 0,
'roi_status' => 0,
'basic_demo_status' => 0,
'xup_status' => 0,
'hyip_status' => 0,
'group_pv' => 0,
'personal_pv' => 0,
'kyc_status' => 0,
'signup_config' => 1,
'mail_gun_status' => 0,
'auto_ship_status' => 1,
'downline_count_rank' => 0,
'downline_purchase_rank' => 0,
'otp_modal' => 0,
'gdpr' => 0,
'purchase_wallet' => 0,
'crowd_fund' => 0,
'compression_status' => 0,
'promotion_status' => 0,
'multi_currency_status' => 0,
'multilang_status' => 0,
'crm_status' => 0,
'promotion_status_demo' => 0,
'subscription_status' => 0,
'subscription_status_demo' => 0,
'tree_updation' => 0,
'cache_status' => 0,
'replicated_site_status' => 1,
'created_at' => now(),
'updated_at' => now(),
];


        ModuleStatus::insert($data);
    }
}
