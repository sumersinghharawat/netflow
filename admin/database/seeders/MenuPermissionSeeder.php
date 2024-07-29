<?php

namespace Database\Seeders;

use App\Models\Menu;
use App\Models\Addon;
use Illuminate\Support\Str;
use App\Models\ModuleStatus;
use App\Models\MenuPermission;
use Illuminate\Database\Seeder;

class MenuPermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $menus = Menu::all();
        $moduleStatus = ModuleStatus::first();
        $addons = Addon::where('status', 1)->pluck('slug')->toArray();
        $data = [];

        foreach ($menus as $menu) {
            $data['id'] = $menu->id;
            $data['admin'] = 0;
            $data['user'] = 0;
            if (
                $menu->slug == 'profile-management' ||
                $menu->slug == 'settings' ||
                $menu->slug == 'networks' ||
                // $menu->slug == 'business' ||
                $menu->slug == 'reports' ||
                $menu->slug == 'tools' ||
                $menu->slug == 'e-wallet' ||
                $menu->slug == 'payout' ||
                $menu->slug == 'mail-box' ||
                $menu->slug == 'sign-up' ||
                $menu->slug == 'register' ||
                // $menu->slug == 'bulk-register' ||
                $menu->slug == 'approve' ||
                $menu->slug == 'profile-view' ||
                $menu->slug == 'member-list' ||
                $menu->slug == 'commission-settings' ||
                // $menu->slug == 'advanced-settings' ||
                $menu->slug == 'company-profile' ||
                $menu->slug == 'content-management' ||
                $menu->slug == 'mail-content' ||
                $menu->slug == 'tree-view' ||
                $menu->slug == 'genealogy-tree' ||
                $menu->slug == 'downline-members' ||
                $menu->slug == 'profile' ||
                $menu->slug == 'activate-deactivate' ||
                $menu->slug == 'joining' ||
                $menu->slug == 'commission' ||
                $menu->slug == 'total-bonus' ||
                $menu->slug == 'top-earners' ||
                $menu->slug == 'news' ||
                $menu->slug == 'faqs' ||
                $menu->slug == 'membership' //||
                //$menu->slug == 'commission-history'
            ) {
                $data['admin'] = 1;
                $data['user'] = 1;
            }
            if($menu->slug == 'business'){
                $data['admin'] = 0;
                $data['user'] = 0;
            }
            if ($menu->slug == 'upload-material') {
                $data['admin'] = 1;
                $data['user'] = 0;
            }
            if ($menu->slug == 'download-materials') {
                $data['admin'] = 0;
                $data['user'] = 1;
            }
            if ($menu->route_name == 'dashboard') {
                $data['admin'] = 1;
                $data['user'] = 1;
            }

            if ($moduleStatus->replicated_site_status && $menu->slug == 'replica-site') {
                $data['admin'] = 1;
                $data['user'] = 1;
            }

            if ($moduleStatus->kyc_status && $menu->slug == 'kyc-details') {
                $data['admin'] = 1;
                $data['user'] = 1;
            }

            if ($moduleStatus->ecom_status && $menu->slug == 'register') {
                $data['admin'] = 0;
                $data['user'] = 1;
            }
            if ($moduleStatus->ecom_status && $menu->route_name == 'store.register') {
                $data['admin'] = 1;
                $data['user'] = 1;
            }
            if ($moduleStatus->ecom_status && $menu->slug == 'store-administration') {
                $data['admin'] = 1;
                $data['user'] = 0;
            }

            if ($moduleStatus->ecom_status && $menu->slug == 'store') {
                $data['admin'] = 1;
                $data['user'] = 0;
            }
            if ($moduleStatus->ecom_status && $menu->slug == 'store-administration') {
                $data['admin'] = 1;
                $data['user'] = 0;
            }

            if ($moduleStatus->ecom_status && $menu->slug == 'order-details') {
                $data['admin'] = 1;
                $data['user'] = 1;
            }
            if ($moduleStatus->ecom_status && $menu->slug == 'order-history') {
                $data['admin'] = 1;
                $data['user'] = 1;
            }
            if ($moduleStatus->ecom_status && $menu->slug == 'order-approval') {
                $data['admin'] = 1;
                $data['user'] = 0;
            }

            if ($menu->slug == 'package' && !$moduleStatus->ecom_status) {
                if ($moduleStatus->product_status) {
                    $data['admin'] = 1;
                    $data['user'] = 0;
                }
            }
            if ($menu->slug == 'e-pin') {
                if ($moduleStatus->pin_status) {
                    $data['admin'] = 1;
                    $data['user'] = 1;
                }
            }
            if ($menu->slug == 'privileged-user') {
                if ($moduleStatus->employee_status) {
                    $data['admin'] = 1;
                    $data['user'] = 0;
                }
            }
            if ($menu->slug == "replica-management") {
                if ($moduleStatus->replicated_site_status) {
                    $data['admin']  = 1;
                    $data['user']   = 0;
                }
            }
            if ($moduleStatus->rank_status) {
                if ($menu->slug == 'rank-achievers') {
                    $data['admin'] = 1;
                    $data['user'] = 0;
                }
                if ($menu->slug == 'rank-performance') {
                    $data['admin'] = 1;
                    $data['user'] = 0;
                }
                if ($menu->slug == 'rank') {
                    $data['admin'] = 1;
                    $data['user'] = 0;
                }
            }
            if ($menu->slug == 'package-upgrade') {
                if ($moduleStatus->package_upgrade_status) {
                    $data['admin'] = 1;
                    $data['user'] = 0;
                }
            }
            if ($menu->slug == 'shopping-cart') {
                if ($moduleStatus->repurchase_status && !$moduleStatus->ecom_status) {
                    $data['admin'] = 1;
                    $data['user'] = 0;
                }
            }
            if ($menu->slug == 'products') {
                if ($moduleStatus->repurchase_status) {
                    $data['admin'] = 1;
                    $data['user'] = 0;
                }
            }
            if ($menu->slug == 'approval') {
                if ($moduleStatus->repurchase_status) {
                    $data['admin'] = 1;
                    $data['user'] = 0;
                }
            }
            if ($moduleStatus->lead_capture_status && $moduleStatus->lcp_type == 'lcp_crm') {
                if ($menu->slug == 'crm') {
                    $data['admin'] = 1;
                    $data['user'] = 0;
                }
                if ($menu->route_name == 'crm.index') {
                    $data['admin'] = 1;
                    $data['user'] = 0;
                }
                if ($menu->slug == 'add-lead') {
                    $data['admin'] = 1;
                    $data['user'] = 0;
                }
                if ($menu->slug == 'view-lead') {
                    $data['admin'] = 1;
                    $data['user'] = 0;
                }
                if ($menu->slug == 'graph') {
                    $data['admin'] = 1;
                    $data['user'] = 0;
                }
            }
            if ($menu->slug == 'sms-content') {
                if ($moduleStatus->sms_status) {
                    $data['admin'] = 0;
                    $data['user'] = 0;
                }
            }

            if ($menu->slug == 'sponsor-tree') {
                if ($moduleStatus->sponsor_tree_status && $moduleStatus->mlm_plan != 'Unilevel') {
                    $data['admin'] = 1;
                    $data['user'] = 1;
                }
            }
            if ($menu->slug == Str::slug('Repurchase/Cart')) {
                if ($moduleStatus->repurchase_status) {
                    $data['admin'] = 1;
                    $data['user'] = 0;
                }
            }
            if ($menu->slug == 'support-center') {
                if ($moduleStatus->ticket_system_status) {
                    $data['admin'] = 1;
                    $data['user'] = 1;
                }
            }
            if ($menu->slug == 'donation') {
                if ($moduleStatus->mlm_plan == 'Donation') {
                    $data['admin'] = 1;
                    $data['user'] = 0;
                }
            }
            if ($menu->slug == 'hyiproi-cron') {
                if ($moduleStatus->hyip_status) {
                    $data['admin'] = 1;
                    $data['user'] = 0;
                }
            }
            if ($menu->slug == 'matching-bonus') {
                if (in_array('matching-bonus', $addons)) {
                    $data['admin'] = 1;
                    $data['user'] = 0;
                }
            }
            if ($menu->slug == 'pool-bonus') {
                if (in_array('pool-bonus', $addons)) {
                    $data['admin'] = 1;
                    $data['user'] = 0;
                }
            }
            if ($menu->slug == 'fast-start-bonus') {
                if (in_array('fast-start-bonus', $addons)) {
                    $data['admin'] = 1;
                    $data['user'] = 0;
                }
            }
            if ($menu->slug == 'performance-bonus') {
                if (in_array('performance-bonus', $addons)) {
                    $data['admin'] = 1;
                    $data['user'] = 0;
                }
            }
            if ($menu->slug == 'plan-settings') {
                if ($moduleStatus->mlm_plan == 'Donation' || $moduleStatus->mlm_plan == 'Stair_Step' || $moduleStatus->mlm_plan == 'Matrix') {
                    $data['admin'] = 1;
                    $data['user'] = 0;
                }
            }
            if ($moduleStatus->mlm_plan == 'Donation') {
                if ($menu->slug == 'recieved-donation') {
                    $data['admin'] = 1;
                    $data['user'] = 0;
                }
                if ($menu->slug == 'given-donation') {
                    $data['admin'] = 1;
                    $data['user'] = 0;
                }
                if ($menu->slug == 'missed-donation') {
                    $data['admin'] = 1;
                    $data['user'] = 0;
                }
                if ($menu->slug == 'manage-user-level') {
                    $data['admin'] = 1;
                    $data['user'] = 0;
                }
            }

            if ($moduleStatus->mlm_plan == 'Stair_Step') {
                if ($menu->slug == 'override-commission') {
                    $data['admin'] = 1;
                    $data['user'] = 0;
                }
                if ($menu->slug == 'step-view') {
                    $data['admin'] = 1;
                    $data['user'] = 0;
                }
            }

            if ($moduleStatus->mlm_plan == 'Binary' && $menu->slug == 'binary-configurations') {
                $data['admin'] = 1;
                $data['user'] = 0;
            }

            if ($moduleStatus->mlm_plan == 'Party') {
                if ($menu->slug == 'parties') {
                    $data['admin'] = 1;
                    $data['user'] = 0;
                }
                if ($menu->slug == 'setup-party') {
                    $data['admin'] = 1;
                    $data['user'] = 0;
                }
                if ($menu->slug == 'my-party-portal') {
                    $data['admin'] = 1;
                    $data['user'] = 0;
                }
                if ($menu->slug == 'host-management') {
                    $data['admin'] = 1;
                    $data['user'] = 0;
                }
                if ($menu->slug == 'guest-management') {
                    $data['admin'] = 1;
                    $data['user'] = 0;
                }
            }

            if ($moduleStatus->mlm_plan == 'Unilevel') {
                if ($menu->slug == 'referral-members') {
                    $data['admin'] = 0;
                    $data['user'] = 0;
                }
            }
            else{
                if ($menu->slug == 'referral-members') {
                    $data['admin'] = 1;
                    $data['user'] = 1;
                }
            }
            if ($menu->slug == 'leads') {
                if ($moduleStatus->lead_capture_status) {
                    $data['admin'] = 1;
                    $data['user'] = 0;
                }
            }

            if ($menu->slug == 'purchase' && $moduleStatus->repurchase_status) {
                $data['admin'] = 1;
                $data['user'] = 0;
            }
            if ($menu->slug == 'subscription-report' && $moduleStatus->subscription_status) {
                $data['admin'] = 1;
                $data['user'] = 0;
            }
            if ($menu->slug == 'auto-responder' && $moduleStatus->autoresponder_status) {
                $data['admin'] = 1;
                $data['user'] = 0;
            }
            if (!$moduleStatus->ecom_status && $moduleStatus->product_status && $menu->slug == 'shopping') {
                $data['admin'] = 0;
                $data['user'] = 1;
            }
            if ($menu->slug == 'promotion-tools' && $moduleStatus->promotion_status) {
                $data['admin'] = 1;
                $data['user'] = 1;
            }
            if ($menu->slug == 'epin-transfer' && $moduleStatus->pin_status) {
                $data['admin'] = 1;
                $data['user'] = 1;
            }
            if ($moduleStatus->ecom_status && $menu->slug == 'shopping') {
                $data['admin'] = 0;
                $data['user'] = 1;
            }
            if (!$moduleStatus->ecom_status && $moduleStatus->package_upgrade && $menu->slug == 'upgrade-approval'){
                $data['admin'] = 1;
                $data['user'] = 0;
            }
            if (!$moduleStatus->ecom_status && $moduleStatus->subscription_status && $menu->slug == 'renewal-approval') {
                $data['admin'] = 1;
                $data['user'] = 0;
            }
            if (!$moduleStatus->ecom_status && $menu->slug == 'registration-approval') {
                $data['admin'] = 1;
                $data['user'] = 0;
            }
            if($menu->slug == 'allapproval') {
                $data['admin'] = 1;
                $data['user'] = 0;
            }

            $this->insert($data);
        }
    }

    protected function insert($data)
    {
        MenuPermission::create([
            'menu_id' => $data['id'],
            'admin_permission' => $data['admin'],
            'user_permission' => $data['user'],
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}
