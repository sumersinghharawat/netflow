<?php

namespace Database\Seeders;

use App\Models\Compensation;
use App\Models\ModuleStatus;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $this->call([
            ModuleStatusSeeder::class,
            ConfigurationSeeder::class,
            UsernameConfigSeeder::class,
            CompanyProfileSeeder::class,
            AmountTypeSeeder::class,
            BankTransferSettingSeeder::class,
            CommonMailSettingSeeder::class,
            CommonSettingsSeeder::class,
            LanguageSeeder::class,
            LetterConfigSeeder::class,
            LevelCommissionSeeder::class,
            LevelCommRegPckSeeder::class,
            MailSettingsSeeder::class,
            PasswordPolicySeeder::class,
            SignupSettingsSeeder::class,
            SignupFieldSeeder::class,
            TermsAndConditionsSeeder::class,
            TooltipConfigSeeder::class,
            UploadCategorySeeder::class,
            UserDashboardSeeder::class,
            PayoutConfigurationSeeder::class,
            PaymentGatewayConfigSeeder::class,
            PinAmountDetailsSeeder::class,
            PinConfigSeeder::class,
            PaymentGatewayDetailSeeder::class,
            CountrySeeder::class,
            StateSeeder::class,
            PackageSeeder::class,
            CurrencyDetailsSeeder::class,
            CompensationSeeder::class,
            UserAddonSeeder::class,
            Placeholderseeder::class,
        ]);

        $moduleStatus = ModuleStatus::first();
        $compensation = Compensation::first();
        $this->call([
            AdminSeeder::class,
            TreepathSeeder::class,
        ]);
        if ($moduleStatus->mlm_plan == 'Binary') {
            $this->call([
                BinarySeeder::class,
                LegDetailSeeder::class,
                UserPlacementSeeder::class
            ]);
        } elseif ($moduleStatus->mlm_plan == 'Donation') {
            $this->call([
                DonationSeeder::class,
            ]);
        } elseif ($moduleStatus->mlm_plan == 'Stair_Step') {
            $this->call([
                StairstepSeeder::class,
            ]);
        } elseif ($moduleStatus->mlm_plan == 'Party') {
            $this->call([
                PartyPlanSeeder::class,
            ]);
        }
        $this->call([
            MenuSeeder::class,
            MenuPermissionSeeder::class,
        ]);

        if ($compensation->matching_bonus) {
            $this->call([
                MatchingCommissionSeeder::class,
                MatchingLevelCommissionSeeder::class,
            ]);
        }
        if ($compensation->sales_Commission) {
            $this->call([
                RepurchaseCategorySeeder::class,
                RepurchaseSeeder::class,
                SalesRankCommissionSeeder::class,
                SalesLevelCommissionSeeder::class,
            ]);
        }
        if ($moduleStatus->sms_status) {
            $this->call([
                SMSTypeSeeder::class
            ]);
        }
        if ($moduleStatus->replicated_site_status) {
            $this->call([
                ReplicaSeeder::class,
            ]);
        }
        if ($moduleStatus->ticket_system_status) {
            $this->call([
                TicketPrioritySeeder::class,
                TicketStatusSeeder::class,
            ]);
        }

        if ($moduleStatus->subscription_status) {
            $this->call([
                SubscriptionConfigSeeder::class,
            ]);
        }

        if ($moduleStatus->employee_status) {
            $this->call([
                EmployeeDashboardItemSeeder::class,
            ]);
        }

        if ($compensation->fast_start_bonus) {
            $this->call([
                FastStartBonusSeeder::class,
            ]);
        }
    }
}
