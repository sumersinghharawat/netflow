<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class CustomDemoConfigurationSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $this->call([
            // common table data
            //ConfigurationSeeder::class,
            UsernameConfigSeeder::class,
            CompanyProfileSeeder::class,
            AmountTypeSeeder::class,
            BankTransferSettingSeeder::class,
            CommonMailSettingSeeder::class,
            CommonSettingsSeeder::class,
            LetterConfigSeeder::class,
            LevelCommissionSeeder::class,
            LevelCommRegPckSeeder::class,
            MatchingCommissionSeeder::class,
            MatchingLevelCommissionSeeder::class,
            MailSettingsSeeder::class,
            PasswordPolicySeeder::class,
            RepurchaseCategorySeeder::class,
            RepurchaseSeeder::class,
            SalesRankCommissionSeeder::class,
            SalesLevelCommissionSeeder::class,
            SignupSettingsSeeder::class,
            SignupFieldSeeder::class,
            SMSTypeSeeder::class,
            TermsAndConditionsSeeder::class,
            TooltipConfigSeeder::class,
            UploadCategorySeeder::class,
            UserDashboardSeeder::class,
            PayoutConfigurationSeeder::class,
            PaymentGatewayConfigSeeder::class,
            TreepathSeeder::class,
            PinAmountDetailsSeeder::class,
            PinConfigSeeder::class,
            PaymentGatewayDetailSeeder::class,
            Placeholderseeder::class,
        ]);
    }
}
