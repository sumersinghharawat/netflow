<?php

namespace App\Http\Controllers;
use Throwable;
use App\Services\RevampService;
use App\Models\DemoUser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use App\Models\User;
use App\Models\OCProduct;
use App\Models\MonolineConfig;
use App\Models\UserDetail;
use App\Models\Addon;
use App\Models\Compensation;
use App\Models\Package;
use App\Models\ModuleStatus;
use Illuminate\Support\Facades\Http;
use App\Services\CustomdemoService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Auth\Events\Registered;
use Carbon\Carbon;


class RevampFixController extends CoreInfController
{
    protected $serviceClass;

    public function __construct(RevampService $serviceClass){
        $this->serviceClass = $serviceClass;
    }

    public function ReplicaDbFix(Request $request)
    {
        $prefixes = DemoUser::select('prefix')->where('is_preset', 1)->get();

        foreach ($prefixes as $value) {
            $prefix = $value['prefix'];

            $q = '


OUR PLAN


software is integrated with Replicating Website






Plan header 1


The software is an entire solution for all type of business plan like Binary,Matrix, Unilevel and many other compensation plans. This is developed by a





Plan header 2


The software is an entire solution for all type of business plan like Binary,Matrix, Unilevel and many other compensation plans. This is developed by a





Plan header 3


The software is an entire solution for all type of business plan like Binary,Matrix, Unilevel and many other compensation plans. This is developed by a



            ';

            $p = '


ABOUT US

software is integrated with Replicating Website





about title and some description about title and some description about title and some.


The software is an entire solution for all type of business plan like Binary,Matrix, Unilevel and many other compensation plans. This is developed by a leading MLM software development company Company name. More over these we are keen to construct MLM software as per the business plan suggested by the clients.This MLM software is featured of with integrated with SMS, E-Wallet, Replicating Website, E-Pin, E-Commerce Shopping Cart,Web Design





';
            if (Schema::hasTable("{$prefix}_replica_contents")) {
                $one = DB::table("{$prefix}_replica_contents")->where('key', 'plan')->update([
                    'value' => $q
                ]);
                $two = DB::table("{$prefix}_replica_contents")->where('key', 'about')->update([
                    'value' => $p
                ]);
            }
            if (Schema::hasTable("{$prefix}_replica_banners")) {
                $three = DB::table("{$prefix}_replica_banners")->where('id', 1)->update([
                    'image' => env('APP_URL') . 'assets/images/banners/default_banner.jpg'
                ]);
            }
        }

        die('success');
    }

    // package/product table tree_icon image path

    public function changeProductTreeIcon()
    {
        $prefixes = DemoUser::select('prefix')->where('is_preset', 1)->get();
        foreach ($prefixes as $key => $value) {
            $prefix = $value->prefix;
            if (Schema::hasTable("{$prefix}_packages")) {
                DB::table("{$prefix}_packages")->where('product_id', 'product-1')->update([
                    'tree_icon' => config('app.url') . '/assets/images/reg_packs/package-1.png'
                ]);
                DB::table("{$prefix}_packages")->where('product_id', 'product-2')->update([
                    'tree_icon' => config('app.url') . '/assets/images/reg_packs/package-2.png'
                ]);
                DB::table("{$prefix}_packages")->where('product_id', 'product-3')->update([
                    'tree_icon' => config('app.url') . '/assets/images/reg_packs/package-3.png'
                ]);
            }
        }
        dd('successfully updated');
    }

    public function addNewTables()
    {
        $prefixes = DB::table('demo_users')->select('prefix')->where('deleted_date', '!=', null)->get();

        foreach ($prefixes as $key => $value) {
            $prefix = $value->prefix;

            if (Schema::hasTable("{$prefix}_users") && !Schema::hasTable("{$prefix}_manual_pv_update_histories")) {
                config(['database.connections.mysql.prefix' => "{$value->prefix}_"]);
                DB::purge('mysql');
                DB::connection('mysql');

                Artisan::call('migrate', [
                    '--path' => '/database/migrations/2023_01_27_152426_create_manual_pv_update_histories_table.php',
                    '--force' => true
                ]);
            }
        }

        dd('successfully added');
    }

    public function alterTreepaths()
    {
        $prefixes = DB::table('demo_users')->select('prefix')->whereNull('deleted_date')->get();
        foreach ($prefixes as $key => $value) {
            $prefix = $value->prefix;
            if (Schema::hasTable("{$prefix}_treepaths") && Schema::hasColumn("{$prefix}_treepaths" ,'id')) {
                DB::statement("ALTER TABLE {$prefix}_treepaths DROP COLUMN id, CHANGE COLUMN level depth BIGINT, ADD PRIMARY KEY (ancestor, descendant)");
            } else {
                dump("{$prefix}_treepaths");
            }
        }
        dd('done');
    }
    public function alterSponsorTreepaths()
    {
        $prefixes = DB::table('demo_users')->select('prefix')->whereNull('deleted_date')->get();
        foreach ($prefixes as $key => $value) {
            $prefix = $value->prefix;
            if (Schema::hasTable("{$prefix}_sponsor_treepaths") && Schema::hasColumn("{$prefix}_sponsor_treepaths" ,'id')) {
                DB::statement("ALTER TABLE {$prefix}_sponsor_treepaths DROP COLUMN id, ADD COLUMN depth BIGINT DEFAULT 0, ADD PRIMARY KEY (ancestor, descendant)");
            } else {
                dump("{$prefix}_sponsor_treepaths");
            }
        }
        dd('done');
    }
    public function addindexToTreepaths()
    {
        $prefixes = DB::table('demo_users')->select('prefix')->whereNull('deleted_date')->get();
        foreach ($prefixes as $key => $value) {
            $prefix = $value->prefix;
            if (Schema::hasTable("{$prefix}_treepaths") && Schema::hasColumn("{$prefix}_treepaths" ,'depth')) {
                DB::statement("ALTER TABLE {$prefix}_treepaths ADD INDEX depth_index (depth)");
            }
            if (Schema::hasTable("{$prefix}_sponsor_treepaths") && Schema::hasColumn("{$prefix}_sponsor_treepaths" ,'depth')) {
                DB::statement("ALTER TABLE {$prefix}_sponsor_treepaths ADD INDEX depth_index (depth)");
            }
        }
        dd('done');
    }
    public function updateSponsorTreepathDepth()
    {
        $prefixes = DB::table('demo_users')->select('prefix')->whereNull('deleted_date')->get();
        foreach ($prefixes as $key => $value) {
            $prefix = $value->prefix;

            if (Schema::hasTable("{$prefix}_sponsor_treepaths") && Schema::hasColumn("{$prefix}_sponsor_treepaths" ,'depth')) {
                DB::statement("UPDATE {$prefix}_sponsor_treepaths st
                                SET st.depth = (SELECT descendant.sponsor_level - ancestor.sponsor_level
                                                FROM {$prefix}_users ancestor
                                                JOIN {$prefix}_users descendant ON st.descendant = descendant.id
                                                WHERE st.ancestor = ancestor.id)"
                                );
            }
        }
        dd('done');
    }
    public function updateTreepathDepth()
    {
        $prefixes = DB::table('demo_users')->select('prefix')->whereNull('deleted_date')->get();
        foreach ($prefixes as $key => $value) {
            $prefix = $value->prefix;

            if (Schema::hasTable("{$prefix}_treepaths") && Schema::hasColumn("{$prefix}_treepaths" ,'depth')) {
                DB::statement("UPDATE {$prefix}_treepaths st
                                SET st.depth = (SELECT descendant.user_level - ancestor.user_level
                                                FROM {$prefix}_users ancestor
                                                JOIN {$prefix}_users descendant ON st.descendant = descendant.id
                                                WHERE st.ancestor = ancestor.id)"
                                );
            }
        }
        dd('done');
    }
    public function createRegView()
    {
        $prefixes = DB::table('demo_users')->select('prefix')->whereNull('deleted_date')->get();
        foreach ($prefixes as $key => $value) {
            $prefix = $value->prefix;
            if(Schema::hasTable("{$prefix}_users_registrations")) {
                DB::statement("DROP VIEW IF EXISTS {$prefix}_user_registration_views");
                $sql = "CREATE VIEW {$prefix}_user_registration_views AS
                    SELECT
                    SUM(CAST(reg_amount AS DECIMAL(14,4))) AS regAmount,
                    SUM(CAST(product_amount AS DECIMAL(14,4))) AS productAmount,
                    SUM(CAST(total_amount AS DECIMAL(14,4))) AS totalAmount
                    FROM {$prefix}_users_registrations";
                DB::statement($sql);

            } else {
                dump("{$prefix}_users_registrations");
            }
        }
        dd('done');
    }
    public function createTotalCommission()
    {
        $prefixes = DB::table('demo_users')->select('prefix')->whereNull('deleted_date')->get();
        foreach ($prefixes as $key => $value) {
            $prefix = $value->prefix;
            if (Schema::hasTable("{$prefix}_leg_amounts")) {
                DB::statement("DROP TABLE IF EXISTS {$prefix}_total_commissions_and_income");
                $sql = "CREATE TABLE {$prefix}_total_commissions_and_income (
                    id INT PRIMARY KEY AUTO_INCREMENT,
                    amount_type VARCHAR(100),
                    total_amount DECIMAL(14,4),
                    amount_payable DECIMAL(14,4),
                    purchase_wallet DECIMAL(14,4),
                    service_charge FLOAT
                );

                CREATE INDEX idx_amount_type ON {$prefix}_total_commissions_and_income(amount_type);
                ";
                DB::statement($sql);
                $results = DB::table("{$prefix}_leg_amounts")
                                ->select(DB::raw('amount_type, SUM(total_amount) as total_amount, SUM(amount_payable) as amount_payable, SUM(purchase_wallet) as purchase_wallet, SUM(service_charge) as service_charge'))
                                ->groupBy('amount_type')
                                ->get();

                $values = [];
                foreach ($results as $key => $value) {
                    $values['amount_type'] = $value->amount_type;
                    $values['total_amount'] = $value->total_amount;
                    $values['amount_payable'] = $value->amount_payable;
                    $values['purchase_wallet'] = $value->purchase_wallet;
                    $values['service_charge'] = $value->service_charge;
                }
                DB::table("{$prefix}_total_commissions_and_income")
                        ->insert($values);
            }
        }
        dd('ok');
    }

    public function createTreesizeColumn()
    {
        $prefixes = DB::table('demo_users')->select('prefix')->whereNull('deleted_date')->get();
        foreach ($prefixes as $key => $value) {
            $prefix = $value->prefix;
            if (
                Schema::hasTable("{$prefix}_configurations")
                && !Schema::hasColumn("{$prefix}_configurations" ,'tree_depth')
                && !Schema::hasColumn("{$prefix}_configurations" ,'tree_width')
            ) {
                $sql = "ALTER TABLE {$prefix}_configurations
                ADD COLUMN tree_width INTEGER DEFAULT 4 AFTER rank_calculation,
                ADD COLUMN tree_depth INTEGER DEFAULT 4 AFTER tree_width";
                DB::statement($sql);
            }
        }
        dd('ok');
    }
    public function updateTreeSize()
    {
        $prefixes = DB::table('demo_users')->select('prefix')->whereNull('deleted_date')->get();
        foreach ($prefixes as $key => $value) {
            $prefix = $value->prefix;
            if (
                Schema::hasTable("{$prefix}_configurations")
                && !Schema::hasColumn("{$prefix}_configurations" ,'tree_depth')
                && !Schema::hasColumn("{$prefix}_configurations" ,'tree_width')
            ) {
                $sql = "UPDATE `{$prefix}_configuration` SET tree_width = 4, tree_depth = 4 WHERE id = 1";
                DB::statement($sql);
            }
        }
        dd('ok');
    }

    public function changeColumnSignupField()
    {
        $prefixes = DB::table('demo_users')->select('prefix')->whereNull('deleted_date')->get();
        foreach ($prefixes as $key => $value) {
            $prefix = $value->prefix;
            if (
                Schema::hasTable("{$prefix}_signup_fields")
                && Schema::hasColumn("{$prefix}_signup_fields", 'deafult_value')
            ) {
                $sql = "ALTER TABLE `{$prefix}_signup_fields` CHANGE deafult_value default_value VARCHAR(225)";

                DB::statement($sql);
            }
            dd('ok');
        }
    }

    public function changeColumnUserDetails()
    {
        $prefixes = DB::table('demo_users')->select('prefix')->whereNull('deleted_date')->get();
        foreach ($prefixes as $key => $value) {
            $prefix = $value->prefix;
            if (
                Schema::hasTable("{$prefix}_user_details")
                && Schema::hasColumn("{$prefix}_user_details", 'nacct_holder')
            ) {
                $sql = "ALTER TABLE `{$prefix}_user_details` CHANGE nacct_holder acc_holder VARCHAR(225)";

                DB::statement($sql);
            }
            dd('ok');
        }
    }
    public function alterSalesorder()
    {
        $prefixes = DB::table('demo_users')->select('prefix')->whereNull('deleted_date')->get();
        foreach ($prefixes as $key => $value) {
            $prefix = $value->prefix;
            if (Schema::hasTable("{$prefix}_sales_orders") && !Schema::hasColumn("{$prefix}_sales_orders", 'oc_product_id')) {
                DB::statement("ALTER TABLE {$prefix}_sales_orders ADD COLUMN oc_product_id INTEGER, ADD CONSTRAINT fk_sales_orders_oc_product FOREIGN KEY (oc_product_id) REFERENCES oc_product(id) ON DELETE CASCADE");
            }
        }
        dd('done');
    }

    public function alterPemdingRegistrationsEmailValue()
    {
        $prefixes = DB::table('demo_users')->select('prefix')->get();
        foreach ($prefixes as $key => $value) {
            $prefix = $value->prefix;
            if (Schema::hasTable("{$prefix}_pending_registrations") && Schema::hasColumn("{$prefix}_pending_registrations", 'email')) {
                DB::statement("UPDATE {$prefix}_pending_registrations SET email = NULL");
                DB::statement("ALTER TABLE {$prefix}_pending_registrations ADD CONSTRAINT unique_email UNIQUE (email)");
            }
        }
        dd('done');
    }
    public function alterPemdingRegistrations()
    {
        $prefixes = DB::table('demo_users')->select('prefix')->get();
        foreach ($prefixes as $key => $value) {
            $prefix = $value->prefix;
            if (Schema::hasTable("{$prefix}_pending_registrations") && !Schema::hasColumn("{$prefix}_pending_registrations", 'email')) {
                DB::statement("ALTER TABLE {$prefix}_pending_registrations ADD COLUMN email VARCHAR(255) NULL AFTER username, ADD CONSTRAINT unique_email UNIQUE (email)");
            }
        }
        dd('done');
    }
    public function alterPemdingRegistrationsEmailRemoveDefault()
    {
        $prefixes = DB::table('demo_users')->select('prefix')->get();
        foreach ($prefixes as $key => $value) {
            $prefix = $value->prefix;
            if (Schema::hasTable("{$prefix}_pending_registrations") && Schema::hasColumn("{$prefix}_pending_registrations", 'email')) {
                DB::statement("ALTER TABLE {$prefix}_pending_registrations ALTER COLUMN email SET DEFAULT NULL");
            }
        }
        dd('done');
    }
    public function menuIocnUpdate(){
        $prefixes = DB::table('demo_users')->select('prefix')->get();
        foreach ($prefixes as $key => $value) {
            $prefix = $value->prefix;
            if (Schema::hasTable("{$prefix}_menus")) {
                DB::table("{$prefix}_menus")
                ->whereIn('slug', ['dashboard', 'networks', 'tools' , 'e-wallet' , 'payout' , 'mail-box' , 'e-pin' , 'shopping-cart' , 'support-center' , 'crm' , 'donation' , 'register' , 'shopping' , 'parties' , 'auto-responder'])
                ->update([
                    'user_icon' => DB::raw("CASE
                        WHEN slug = 'dashboard' THEN 'dashboard_ico.svg'
                        WHEN slug = 'networks' THEN 'network_ico.svg'
                        WHEN slug = 'tools' THEN 'tool.svg'
                        WHEN slug = 'e-wallet' THEN 'wallet_ico.svg'
                        WHEN slug = 'payout' THEN 'payout_ico.svg'
                        WHEN slug = 'mail-box' THEN 'mail.svg'
                        WHEN slug = 'e-pin' THEN 'e-pin.svg'
                        WHEN slug = 'shopping-cart' THEN 'ShoppingBasketOutlinedIcon'
                        WHEN slug = 'support-center' THEN 'customer-support.svg'
                        WHEN slug = 'crm' THEN 'crm.svg'
                        WHEN slug = 'donation' THEN 'VolunteerActivismOutlinedIcon'
                        WHEN slug = 'register' THEN 'user_ico.svg'
                        WHEN slug = 'shopping' THEN 'shopping-cart-white.svg'
                        WHEN slug = 'parties' THEN 'LiquorOutlinedIcon'
                        WHEN slug = 'auto-responder' THEN 'DraftsOutlinedIcon'
                    END")
                ]);
            }

        }
        dd('done');
    }

    public function menuPermission(){
        // $prefixes = DB::table('demo_users')->select('prefix')
        // ->where(function ($query) {
        //     $query->whereNull('access_expiry')
        //         ->orWhere('access_expiry', '>=', now()->subDays(1));
        // })
        // ->get();
        $prefixes = DB::table('demo_users')->select('prefix')->where('is_preset',1)->get();
        foreach ($prefixes as $key => $value){
            $prefix = $value->prefix;
            if (Schema::hasTable("{$prefix}_menus")) {
                $store = DB::table("{$prefix}_menus")->where('slug','store')->first();
                if($store){
                   DB::table("{$prefix}_menu_permissions")
                   ->where('menu_id',$store->id)
                   ->update(['user_permission' => 0]);
                }
                $store = DB::table("{$prefix}_menus")->where('slug','upload-material')->first();
                if($store){
                   DB::table("{$prefix}_menu_permissions")
                   ->where('menu_id',$store->id)
                   ->update(['user_permission' => 0]);
                }
            }
        }
        dd('done');
    }

    public function refferalPermission(){
        $prefixes = DB::table('demo_users')->select('prefix')
        ->where(function ($query) {
            $query->whereNull('access_expiry')
                ->orWhere('access_expiry', '>=', now()->subDays(1));
        })
        ->get();
        foreach ($prefixes as $key => $value){
            try{
                $prefix = $value->prefix;
                if (Schema::hasTable("{$prefix}_menus")) {
                    $plan = DB::table("{$prefix}_module_statuses")->first();
                    if($plan->mlm_plan == 'Unilevel'){
                    $store = DB::table("{$prefix}_menus")->where('slug','referral-members')->first();
                    if($store){
                       DB::table("{$prefix}_menu_permissions")
                       ->where('menu_id',$store->id)
                       ->update(['user_permission' => 0 , 'admin_permission' => 0]);
                    }
                    }
                }

            } catch(Throwable $th) {
                continue;
            }
        }
        dd('done');
    }

    public function tockenTableCreation(){
        $prefixes = DB::table('demo_users')->select('prefix')
        ->where(function ($query) {
            $query->whereNull('access_expiry')
                ->orWhere('access_expiry', '>=', now()->subDays(1));
        })
        ->get();
        foreach ($prefixes as $key => $value){
            $prefix = $value->prefix;
            if (!Schema::hasTable("{$prefix}_blacklist_tockens")) {
                Schema::create("{$prefix}_blacklist_tockens", function ($table) {
                    $table->string('tocken',300)->primary();
                });
            }
        }
        dd('done');
    }

    public function menuIocnReverse(){
        $prefixes = DB::table('demo_users')->select('prefix')
        ->where(function ($query) {
            $query->whereNull('access_expiry')
                ->orWhere('access_expiry', '>=', now()->subDays(1));
        })
        ->get();
        foreach ($prefixes as $key => $value) {
            $prefix = $value->prefix;
            if (Schema::hasTable("{$prefix}_menus")) {
                DB::table("{$prefix}_menus")
                ->whereIn('slug', ['dashboard', 'networks', 'tools' , 'e-wallet' , 'payout' , 'mail-box' , 'e-pin' , 'shopping-cart' , 'support-center' , 'crm' , 'donation' , 'register' , 'shopping' , 'parties' , 'auto-responder'])
                ->update([
                    'user_icon' => DB::raw("CASE
                        WHEN slug = 'dashboard' THEN 'dashboard_ico.svg'
                        WHEN slug = 'networks' THEN 'network_ico.svg'
                        WHEN slug = 'tools' THEN 'SettingsOutlinedIcon'
                        WHEN slug = 'e-wallet' THEN 'wallet_ico.svg'
                        WHEN slug = 'payout' THEN 'payout_ico.svg'
                        WHEN slug = 'mail-box' THEN 'DraftsOutlinedIcon'
                        WHEN slug = 'e-pin' THEN 'PersonAddAlt1OutlinedIcon'
                        WHEN slug = 'shopping-cart' THEN 'ShoppingBasketOutlinedIcon'
                        WHEN slug = 'support-center' THEN 'SmsOutlinedIcon'
                        WHEN slug = 'crm' THEN 'GroupsOutlinedIcon'
                        WHEN slug = 'donation' THEN 'VolunteerActivismOutlinedIcon'
                        WHEN slug = 'register' THEN 'user_ico.svg'
                        WHEN slug = 'shopping' THEN 'ShoppingBasketOutlinedIcon'
                        WHEN slug = 'parties' THEN 'LiquorOutlinedIcon'
                        WHEN slug = 'auto-responder' THEN 'DraftsOutlinedIcon'
                    END")
                ]);
            }

        }
        dd('done');
    }
    public function menuPermissionReverse(){
        $prefixes = DB::table('demo_users')->select('prefix')
        ->where(function ($query) {
            $query->whereNull('access_expiry')
                ->orWhere('access_expiry', '>=', now()->subDays(1));
        })
        ->get();
        foreach ($prefixes as $key => $value){
            $prefix = $value->prefix;
            if (Schema::hasTable("{$prefix}_menus")) {
                $store = DB::table("{$prefix}_menus")->where('slug','store')->first();
                if($store){
                   DB::table("{$prefix}_menu_permissions")
                   ->where('menu_id',$store->id)
                   ->update(['user_permission' => 1]);
                }
                $store = DB::table("{$prefix}_menus")->where('slug','upload-material')->first();
                if($store){
                   DB::table("{$prefix}_menu_permissions")
                   ->where('menu_id',$store->id)
                   ->update(['user_permission' => 1]);
                }
            }
        }
        dd('done');
    }

    public function cleanUp(){
        $moduleStatus = ModuleStatus::first();
        $demoStatus = config('mlm.demo_status');
        try{
            if($demoStatus == 'yes'){
                $admin = User::first();
                $tables = DB::connection()->getDoctrineSchemaManager()->listTableNames();
                $prefix      = config('database.connections.mysql.prefix');
                foreach ($tables as $table){
                    if (strpos($table, $prefix) !== false) {
                        $tablelist[] = $table;
                    }
                }
                foreach ($tablelist as $dbtable) {
                    if (strpos($dbtable, $prefix) === 0) {
                        $dbtable = substr($dbtable, strlen($prefix));
                    }
                    Schema::disableForeignKeyConstraints();
                    Schema::dropIfExists($dbtable);
                    Schema::enableForeignKeyConstraints();
                }
                $prefix = str_replace('_', '', $prefix);
                config(['database.connections.mysql.prefix' => "{$prefix}_"]);
                        DB::purge('mysql');
                        DB::connection('mysql');
                        $sql = "DROP VIEW IF EXISTS {$prefix}_user_registration_views; ";
                        DB::statement($sql);
                        Artisan::call('migrate');
                        Artisan::call('db:seed', ['--class' => 'CountrySeeder']);
                        Artisan::call('db:seed', ['--class' => 'StateSeeder']);
                        Artisan::call('db:seed', ['--class' => 'PackageSeeder']);
                        Artisan::call('db:seed', ['--class' => 'CurrencyDetailsSeeder']);
                        Artisan::call('db:seed', ['--class' => 'CompensationSeeder']);
                        Artisan::call('db:seed', ['--class' => 'UserAddonSeeder']);
                        Artisan::call('db:seed', ['--class' => 'LanguageSeeder']);

                        $user = $this->serviceClass->updateModuleStatus($admin->username, $moduleStatus->mlm_plan, $prefix);
                        $moduleStatus = ModuleStatus::first();

                        Artisan::call('db:seed');

                        if ($moduleStatus->mlm_plan == 'Binary') {
                            Artisan::call('migrate', [
                                '--path' => '/database/migrations/binary/',
                            ]);
                            Artisan::call('db:seed', [
                                '--class' => 'BinarySeeder',
                            ]);
                            Artisan::call('db:seed', [
                                '--class' => 'LegDetailSeeder',
                            ]);
                        } elseif ($moduleStatus->mlm_plan == 'Donation') {
                            Artisan::call('migrate', [
                                '--path' => '/database/migrations/donation/',
                            ]);

                            Artisan::call('db:seed', [
                                '--class' => 'DonationSeeder',
                            ]);
                        } elseif ($moduleStatus->mlm_plan == 'Stair_Step') {
                            Artisan::call('migrate', [
                                '--path' => '/database/migrations/stairstep/',
                            ]);

                            Artisan::call('db:seed', [
                                '--class' => 'StairstepSeeder',
                            ]);
                        } elseif ($moduleStatus->mlm_plan == 'Party') {
                            Artisan::call('migrate', [
                                '--path' => '/database/migrations/party/',
                            ]);

                            Artisan::call('db:seed', [
                                '--class' => 'PartyPlanSeeder',
                            ]);
                        } elseif ($moduleStatus->mlm_plan == 'Monoline') {
                            Artisan::call('migrate', [
                                '--path' => '/database/migrations/monoline/',
                            ]);

                            Artisan::call('db:seed', [
                                '--class' => 'MonolineConfigSeeder',
                            ]);

                DB::statement("INSERT INTO {$prefix}_aggregate_user_commission_and_incomes (user_id, amount_type, total_amount, amount_payable, purchase_wallet, tds, service_charge)
                SELECT u.id, 'la.amount_type', la.total_amount, la.amount_payable, la.purchase_wallet, la.tds, la.service_charge
                FROM {$prefix}_users AS u
                INNER JOIN {$prefix}_leg_amounts AS la ON u.id = la.user_id");

            }
                        Artisan::call('db:seed', ['--class' => 'MenuSeeder']);
                        Artisan::call('db:seed', ['--class' => 'MenuPermissionSeeder']);

                        if ($moduleStatus->ecom_status) {
                            try {
                                $storeDb     = file_get_contents(config('mlm.ecom_revamp_database_url') . "databaserevamp.sql");
                                $database    = preg_replace('/oc_/', "{$prefix}_oc_", $storeDb);
                                $primaryKeys = file_get_contents(config('mlm.ecom_revamp_database_url') . "addkey.sql");
                                $prefixDbKey = preg_replace('/oc_/', "{$prefix}_oc_", $primaryKeys);
                                config(['database.connections.mysql.prefix' => "{$prefix}_"]);
                                DB::purge('mysql');
                                DB::connection('mysql');
                                DB::statement($database);
                                DB::statement($prefixDbKey);
                                $user = User::GetAdmin();
                                $user->oc_product_id = OCProduct::where('package_type', 'registration')->first()->product_id;
                                $user->ecom_customer_ref_id = 1;
                                $user->save();

                            } catch (\Illuminate\Database\QueryException $ex) {
                                dd($ex->getMessage());
                                // Note any method of class PDOException can be called on $ex.
                            }
                        }
                        if ($moduleStatus->mlm_plan == "Monoline") {
                            $user->nextReentry()->create([
                                'user_id'   => $user->id,
                                'next_reentry' => MonolineConfig::first()->downline_count
                            ]);
                        }
                        if ($moduleStatus->rank_status) {
                            Artisan::call('db:seed', ['--class' => 'RankSeeder']);
                            Artisan::call('db:seed', ['--class' => 'PresetRankConfigSeeder']);
                            Artisan::call('db:seed', ['--class' => 'RankDetailsSeeder']);
                            Artisan::call('db:seed', ['--class' => 'RankConfigDownlineRankCountSeeder']);
                            Artisan::call('db:seed', ['--class' => 'PurchaseRankSeeder']);
                        }
                        // $this->info("***********{$user->username} completed ************\n******************************");
                        dd('complete');
                        return true;
                    }
                    else{
                        Artisan::call('setup:project');
                    }
                } catch (\Throwable $th) {
                    dd($th);
                }
            }


    public function aggregateTable($prefixes = null)
    {
        if ($prefixes == null) {
            $prefixes = DB::table('demo_users')->select('prefix')
                ->where(function ($query) {
                    $query->whereNull('access_expiry')
                        ->orWhere('access_expiry', '>=', now()->subDays(1));
                })->get();

            foreach ($prefixes as $key => $value) {
                $prefix = $value->prefix;
                if (!Schema::hasTable("{$prefix}_aggregate_user_commission_and_incomes")&& Schema::hasTable("{$prefix}_leg_amounts")) {
                    $sql = "CREATE TABLE {$prefix}_aggregate_user_commission_and_incomes (
                        id INT PRIMARY KEY AUTO_INCREMENT,
                        user_id INT(100),
                        amount_type VARCHAR(255),
                        total_amount DECIMAL(14,4),
                        amount_payable DECIMAL(14,4),
                        purchase_wallet DECIMAL(14,4) DEFAULT 0.0000,
                        tds DECIMAL(14,4) DEFAULT 0.0000,
                        service_charge DECIMAL(14,4) DEFAULT 0.0000,
                        created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
                        updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
                    )";
                    DB::statement($sql);

                    $query = DB::table("{$prefix}_leg_amounts")
                    ->select('user_id', 'amount_type', DB::raw("SUM(total_amount) as total_amount"),
                        DB::raw("SUM(amount_payable) as amount_payable"),
                        DB::raw("SUM(purchase_wallet) as purchase_wallet"),
                        DB::raw("SUM(tds) as tds"),
                        DB::raw("SUM(service_charge) as service_charge")
                    )
                    ->groupBy('user_id', 'amount_type')
                    ->get();

                    foreach ($query as $row) {
                        DB::table("{$prefix}_aggregate_user_commission_and_incomes")->insert([
                            'user_id' => $row->user_id,
                            'amount_type' => $row->amount_type,
                            'total_amount' => $row->total_amount,
                            'amount_payable' => $row->amount_payable,
                            'purchase_wallet' => $row->purchase_wallet,
                            'tds' => $row->tds,
                            'service_charge' => $row->service_charge,
                            'created_at' => now(),
                            'updated_at' => now(),
                        ]);
                    }
                }
            }
            dd("done");
        }

        $prefix = $prefixes;
            if (!Schema::hasTable("{$prefix}_aggregate_user_commission_and_incomes") && Schema::hasTable("{$prefix}_leg_amounts")) {
                $sql = "CREATE TABLE {$prefix}_aggregate_user_commission_and_incomes (
                    id INT PRIMARY KEY AUTO_INCREMENT,
                    user_id INT(100),
                    amount_type VARCHAR(255),
                    total_amount DECIMAL(14,4),
                    amount_payable DECIMAL(14,4),
                    purchase_wallet DECIMAL(14,4),
                    tds DECIMAL(14,4),
                    service_charge DECIMAL(14,4),
                    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
                    updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
                )";
                DB::statement($sql);

                $query = DB::table("{$prefix}_leg_amounts")
                ->select('user_id', 'amount_type', DB::raw("SUM(total_amount) as total_amount"),
                    DB::raw("SUM(amount_payable) as amount_payable"),
                    DB::raw("SUM(purchase_wallet) as purchase_wallet"),
                    DB::raw("SUM(tds) as tds"),
                    DB::raw("SUM(service_charge) as service_charge")
                )
                ->groupBy('user_id', 'amount_type')
                ->get();

                foreach ($query as $row) {
                    DB::table("{$prefix}_aggregate_user_commission_and_incomes")->insert([
                        'user_id' => $row->user_id,
                        'amount_type' => $row->amount_type,
                        'total_amount' => $row->total_amount,
                        'amount_payable' => $row->amount_payable,
                        'purchase_wallet' => $row->purchase_wallet,
                        'tds' => $row->tds,
                        'service_charge' => $row->service_charge,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
            }

        dd("done");
    }

    public function orderDetailPermission(){
        $prefixes = DB::table('demo_users')->select('prefix')
        ->where(function ($query) {
            $query->whereNull('access_expiry')
                ->orWhere('access_expiry', '>=', now()->subDays(1));
        })
        ->get();
        foreach ($prefixes as $key => $value){
            $prefix = $value->prefix;
            if (Schema::hasTable("{$prefix}_menus")) {
                $store = DB::table("{$prefix}_menus")->where('slug','order-details')->first();
                if($store){
                   $permission = DB::table("{$prefix}_menu_permissions")
                   ->where('menu_id',$store->id)->first();
                   if($permission->user_permission){
                        DB::table("{$prefix}_menu_permissions")
                        ->where('menu_id',$store->id)
                        ->update(['user_permission' => 0]);
                   }else{
                        DB::table("{$prefix}_menu_permissions")
                        ->where('menu_id',$store->id)
                        ->update(['user_permission' => 1]);
                   }
                }
            }
        }
        dd('done');
    }
    public function menuIocnUpdateproduct(){
            if (Schema::hasTable("menus")) {
                DB::table("menus")
                ->whereIn('slug', ['dashboard', 'networks', 'tools' , 'e-wallet' , 'payout' , 'mail-box' , 'e-pin' , 'shopping-cart' , 'support-center' , 'crm' , 'donation' , 'register' , 'shopping' , 'parties' , 'auto-responder'])
                ->update([
                    'user_icon' => DB::raw("CASE
                        WHEN slug = 'dashboard' THEN 'dashboard_ico.svg'
                        WHEN slug = 'networks' THEN 'network_ico.svg'
                        WHEN slug = 'tools' THEN 'tool.svg'
                        WHEN slug = 'e-wallet' THEN 'wallet_ico.svg'
                        WHEN slug = 'payout' THEN 'payout_ico.svg'
                        WHEN slug = 'mail-box' THEN 'mail.svg'
                        WHEN slug = 'e-pin' THEN 'e-pin.svg'
                        WHEN slug = 'shopping-cart' THEN 'ShoppingBasketOutlinedIcon'
                        WHEN slug = 'support-center' THEN 'customer-support.svg'
                        WHEN slug = 'crm' THEN 'crm.svg'
                        WHEN slug = 'donation' THEN 'VolunteerActivismOutlinedIcon'
                        WHEN slug = 'register' THEN 'user_ico.svg'
                        WHEN slug = 'shopping' THEN 'shopping-cart-white.svg'
                        WHEN slug = 'parties' THEN 'LiquorOutlinedIcon'
                        WHEN slug = 'auto-responder' THEN 'DraftsOutlinedIcon'
                    END")
                ]);
            }
        dd('done');
    }

    public function menuPermissionproduct(){
            if (Schema::hasTable("menus")) {
                $store = DB::table("menus")->where('slug','store')->first();
                if($store){
                   DB::table("menu_permissions")
                   ->where('menu_id',$store->id)
                   ->update(['user_permission' => 0]);
                }
                $store = DB::table("menus")->where('slug','upload-material')->first();
                if($store){
                   DB::table("menu_permissions")
                   ->where('menu_id',$store->id)
                   ->update(['user_permission' => 0]);
                }
            }
        dd('done');
    }

    public function addSponserIndexColumn()
    {
        $prefixes = DB::table('demo_users')->select('prefix')->whereNull('deleted_date')->get();
        foreach($prefixes as $key => $value) {
            $prefix = $value->prefix;
            if (Schema::hasTable("{$prefix}_users") && !Schema::hasColumn("{$prefix}_users", 'sponsor_index')) {
                DB::statement("ALTER TABLE {$prefix}_users ADD COLUMN sponsor_index integer AFTER delete_status");
            }
        }
        dd('done');
    }

    public function replicacontent(){
        // $prefixes = DB::table('demo_users')->select('prefix')->whereNull('deleted_date')->get();
        $prefixes = [];
        foreach($prefixes as $key => $value) {
            $prefix = $value->prefix;
            if (Schema::hasTable("{$prefix}_replica_contents")){
                DB::table("{$prefix}_replica_contents")->delete();

            $language = DB::table("{$prefix}_languages")->where('code', 'en')->orWhere('default', 1)->first();
            $data = [
                [
                    'key' => 'home_title1',
                    'value' => 'software name v1.1',
                    'lang_id' => $language->id,
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'key' => 'home_title2',
                    'value' => 'software title and some heading content',
                    'lang_id' => $language->id,
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'key' => 'plan',
                    'value' => '








Plan header 1

The software is an entire solution for all type of business plan like Binary,Matrix, Unilevel and many other compensation plans.












Plan header 2

The software is an entire solution for all type of business plan like Binary,Matrix, Unilevel and many other compensation plans.












Plan header 3

The software is an entire solution for all type of business plan like Binary,Matrix, Unilevel and many other compensation plans.












Plan header 4

The software is an entire solution for all type of business plan like Binary,Matrix, Unilevel and many other compensation plans.





',
                    'lang_id' => $language->id,
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'key' => 'contact_phone',
                    'value' => '999999999',
                    'lang_id' => $language->id,
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'key' => 'contact_mail',
                    'value' => 'companyname@mail.in',
                    'lang_id' => $language->id,
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'key' => 'contact_address',
                    'value' => 'address',
                    'lang_id' => $language->id,
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'key' => 'policy',
                    'value' => '
All subscribers of MLM services agree to be bound by the terms of this service. The MLM software is an entire solution for all type of business plan like Binary, Matrix, Unilevel and many other compensation plans. This is developed by a leading MLM software development company COMPANY NAME. More over these we are keen to construct MLM software as per the business plan suggested by the clients.This MLM software is featured of with integrated with SMS, E-Wallet,Replicating Website,E-Pin,E-Commerce, Shopping Cart,Web Design and more

                    ',
                    'lang_id' => $language->id,
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'key' => 'terms',
                    'value' => '
All subscribers of MLM services agree to be bound by the terms of this service. The MLM software is an entire solution for all type of business plan like Binary, Matrix, Unilevel and many other compensation plans. This is developed by a leading MLM software development company COMPANY NAME. More over these we are keen to construct MLM software as per the business plan suggested by the clients.This MLM software is featured of with integrated with SMS, E-Wallet,Replicating Website,E-Pin,E-Commerce, Shopping Cart,Web Design and more

',
                    'lang_id' => $language->id,
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'key' => 'about',
                    'value' => '





                                        about-image


                                        about-image








About Us

Company title and some description about title and some description about title and some.


The software is an entire solution for all type of business plan like Binary,Matrix, Unilevel and many other compensation plans. This is developed by a leading MLM software development company Company name. More over these we are keen to construct MLM software as per the business plan suggested by the clients.
This MLM software is featured of with integrated with SMS, E-Wallet, Replicating Website, E-Pin, E-Commerce Shopping Cart,Web Design.






',
                    'lang_id' => $language->id,
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'key' => 'why_choose_us',
                    'value' => '




                                    benefits-image









Why Choose Us

Our track record speaks for itself. We have built a strong reputation for reliability, integrity, and exceptional service within the industry.





we have the knowledge and expertise to deliver exceptional results.


Our track record speaks for itself. We have built a strong reputation for reliability.




                                We understand the importance of deadlines. You can rely on us to deliver your product or service on time, every time.



                                We are constantly evolving and adapting to the changing landscape of our industry.





',
                    'lang_id' => $language->id,
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'key' => 'features',
                    'value' => '








Feature 1

The software is an entire solution for all type of business plan like Binary,Matrix, Unilevel and many other compensation plans.









Feature 2

The software is an entire solution for all type of business plan like Binary,Matrix, Unilevel and many other compensation plans.









Feature 3

The software is an entire solution for all type of business plan like Binary,Matrix, Unilevel and many other compensation plans.






',
                    'lang_id' => $language->id,
                    'created_at' => now(),
                    'updated_at' => now(),
                ],

            ];
            DB::table("{$prefix}_replica_contents")->insert($data);

            }
        }
        dd('done');
    }

    public function mailContentPlaceholder(){
        $prefixes = DB::table('demo_users')->select('prefix')->where('is_preset',1)->get();
        foreach ($prefixes as $key => $value) {
            $prefix = $value->prefix;
            if(!Schema::hasTable("{$prefix}_placeholders")){
                Schema::create("{$prefix}_placeholders", function ($table) {
                    $table->id();
                    $table->text('placeholder');
                    $table->text('name');
                    $table->timestamps();
                });
            $data = [
                [
                    'placeholder' => 'first_name',
                    'name' => 'name',
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'placeholder' => 'password',
                    'name' => 'password',
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'placeholder' => 'username',
                    'name' => 'username',
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'placeholder' => 'admin_user_name',
                    'name' => 'adminUserName',
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'placeholder' => 'payout_amount',
                    'name' => 'payoutAmount',
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'placeholder' => 'fullname',
                    'name' => 'fullName',
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'placeholder' => 'company_name',
                    'name' => 'companyName',
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'placeholder' => 'full_name',
                    'name' => 'full_Name',
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'placeholder' => 'new_password',
                    'name' => 'newPassword',
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'placeholder' => 'links',
                    'name' => 'links',
                    'created_at' => now(),
                    'updated_at' => now(),
                ],

            ];
            DB::table("{$prefix}_placeholders")->insert($data);
        }
        }
        dd('yes');
    }
        public function replicabanner()
    {
        $prefixes = DB::table('demo_users')->select('prefix')->whereNull('deleted_date')->get();
        foreach($prefixes as $key => $value) {
            $prefix = $value->prefix;
            if (Schema::hasTable("{$prefix}_replica_banners")){
                $existingImages = DB::table("{$prefix}_replica_banners")->whereNull('user_id')->where('is_default', 1)->get();
                if(count($existingImages) < 2){
                    $delete = DB::table("{$prefix}_replica_banners")->whereNull('user_id')->where('is_default', 1)->delete();
                    DB::table("{$prefix}_replica_banners")->insert([
                        [
                            'image' => asset('assets/replica/img/banner/banner-2.jpg'),
                            'is_default' => 1,
                            'created_at' => now(),
                            'updated_at' => now(),
                        ],
                        [
                            'image' => asset('assets/replica/img/banner/banner-1.jpg'),
                            'is_default' => 1,
                            'created_at' => now(),
                            'updated_at' => now(),
                        ]
                    ]);
                }
            }
        }
        dd('done');
    }

    public function replicabannerpreset()
    {
        $prefixes = DB::table('demo_users')->select('prefix')->where('is_preset',1)->get();
        foreach($prefixes as $key => $value) {
            $prefix = $value->prefix;
            if (Schema::hasTable("{$prefix}_replica_banners")){
                $existingImages = DB::table("{$prefix}_replica_banners")->whereNull('user_id')->where('is_default', 1)->get();
                $delete = DB::table("{$prefix}_replica_banners")->delete();
                DB::table("{$prefix}_replica_banners")->insert([
                    [
                        'image' => asset('assets/replica/img/banner/banner-2.jpg'),
                        'is_default' => 1,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ],
                    [
                        'image' => asset('assets/replica/img/banner/banner-1.jpg'),
                        'is_default' => 1,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]
                ]);
            }
        }
        dd('done');
    }

    public function createuserplacements(){
        $prefixes = DB::table('demo_users')->select('prefix')->where('is_preset',1)->get();
        foreach ($prefixes as $key => $value) {
            $prefix = $value->prefix;
            if(!Schema::hasTable("{$prefix}_user_placements")){
                Schema::create("{$prefix}_user_placements", function (Blueprint $table) use ($prefix) {
                    $table->id();
                    $table->foreignId('user_id')->constrained("{$prefix}_users")->onDelete('cascade');
                    $table->foreignId('branch_parent')->nullable()->constrained("{$prefix}_users")->onDelete('cascade');
                    $table->foreignId('left_most')->nullable()->constrained("{$prefix}_users")->onDelete('cascade');
                    $table->foreignId('right_most')->nullable()->constrained("{$prefix}_users")->onDelete('cascade');
                });
                $user = DB::table("{$prefix}_users")->where('user_type', 'admin')->first();
                DB::table("{$prefix}_user_placements")->insert([
                    'user_id' => $user->id,
                ]);
            }
            }
            dd("Done");
    }

    public function newMailContent(){
       $prefixes = DB::table('demo_users')->select('prefix')->where('is_preset',1)->get();
        foreach ($prefixes as $key => $value) {
            $prefix = $value->prefix;
            if(Schema::hasTable("{$prefix}_common_mail_settings")){
                DB::table("{$prefix}_common_mail_settings")->truncate();
                $data = [
                        [
                            'mail_type' => 'send_tranpass',
                            'subject' => 'Change Transaction Password ',
                            'mail_content' => '
Dear {{first_name}},

Your new Transaction Password is {{password}}

',
                            'status' => 1,
                            'created_at' => now(),
                        ],
                        [
                            'mail_type' => 'payout_request',
                            'subject' => 'Payout Request',
                            'mail_content' => '
Dear {{admin_user_name}},

{{username}} requested payout of {{payout_amount}}

',
                            'status' => 1,
                            'created_at' => now(),
                        ],
                        [
                            'mail_type' => 'registration_email_verification',
                            'subject' => 'Email Verification',
                            'mail_content' => '
Hi {{full_name}},

Thanks for creating {{company_name}} account. To continue, Please confirm your email address by clicking the link:

                                Click Here
',
                            'status' => 1,
                            'created_at' => now(),
                        ],
                        [
                            'mail_type' => 'forgot_password',
                            'subject' => 'Forgot Password',
                            'mail_content' => '
Dear Customer,

you are recently requested reset password for that please follow the below link:

                                Reset Password
',
                            'status' => 1,
                            'created_at' => now(),
                        ],
                        [
                            'mail_type' => 'reset_googleAuth',
                            'subject' => 'Reset Google Authentication',
                            'mail_content' => '
Dear Customer,

you are recently requested reset Google Authentication for that please follow the below link:

                                Click Here
',
                            'status' => 1,
                            'created_at' => now(),
                        ],
                        [
                            'mail_type' => 'forgot_transaction_password',
                            'subject' => 'Forgot Transaction Password',
                            'mail_content' => '
Dear Customer,

You have recently requested to change your Transaction password. Follow the link below to reset the Transaction password:

                                Click Here
',
                            'status' => 1,
                            'created_at' => now(),
                        ],
                        [
                            'mail_type' => 'external_mail',
                            'subject' => '',
                            'mail_content' => '
Subject:{{subject}},

Message:            {{content}}

',
                            'status' => 1,
                            'created_at' => now(),
                        ],
                        [
                            'mail_type' => 'change_password',
                            'subject' => 'Change Password ',
                            'mail_content' => '
Dear {{full_name}},

Your password has been sucessfully changed, Your new password is {{new_password}}

',
                            'status' => 1,
                            'created_at' => now(),
                        ],
                        [
                            'mail_type' => 'registration',
                            'subject' => 'Welcome to ',
                            'mail_content' => '
Congratulations!!! You have been registered successfully!,

Dear {{fullname}}


Your MLM software is now active. Please save this message, so you will have a permanent record of your MLM Software. I trust that this mail finds you mutually excited about your new opportunity with {{company_name}}. Each of us will play a role to ensure your successful integration into the company.

',
                            'status' => 1,
                            'created_at' => now(),
                        ],
                        [
                            'mail_type' => 'payout_release',
                            'subject' => 'Payout Release Mail ',
                            'mail_content' => '
Dear {{fullname}},

Your payout has been released successfully

',
                            'status' => 1,
                            'created_at' => now(),
                        ],

                    ];
                    DB::table("{$prefix}_common_mail_settings")->insert($data);
            }
        }
            dd("ok");
    }

    public function sponsorindexcolumn()
    {
        $prefixes = DB::table('demo_users')->select('prefix')->where('is_preset',1)->get();

        foreach ($prefixes as $key => $value) {
            $prefix = $value->prefix;

            if (!empty($prefix) && Schema::hasTable("{$prefix}_users") && !Schema::hasColumn("{$prefix}_users", 'sponsor_index')) {
                $sql = "ALTER TABLE {$prefix}_users ADD COLUMN sponsor_index INTEGER DEFAULT 0, ADD INDEX idx_sponsor_index (sponsor_index)";
                DB::statement($sql);
            }
        }
        print_r('done');
    }

    public function createuserplacementscustomdemo(){
        $prefixes = DB::table('demo_users')->select('prefix')->where('is_preset',0)->where('deleted_date',null)->get();
        foreach ($prefixes as $key => $value) {
            $prefix = $value->prefix;
            if(!Schema::hasTable("{$prefix}_user_placements") && Schema::hasTable("{$prefix}_users")){
                Schema::create("{$prefix}_user_placements", function (Blueprint $table) use ($prefix) {
                    $table->id();
                    $table->foreignId('user_id')->constrained("{$prefix}_users")->onDelete('cascade');
                    $table->foreignId('branch_parent')->nullable()->constrained("{$prefix}_users")->onDelete('cascade');
                    $table->foreignId('left_most')->nullable()->constrained("{$prefix}_users")->onDelete('cascade');
                    $table->foreignId('right_most')->nullable()->constrained("{$prefix}_users")->onDelete('cascade');
                });
                $user = DB::table("{$prefix}_users")->where('user_type', 'admin')->first();
                if($user){
                    DB::table("{$prefix}_user_placements")->insert([
                        'user_id' => $user->id,
                    ]);
                }else{
                }
            }
            }
            dd("Done");
    }

    public function newMailContentcustomdemo(){
       $prefixes = DB::table('demo_users')->select('prefix')->where('is_preset',0)->where('deleted_date',null)->get();
        foreach ($prefixes as $key => $value) {
            $prefix = $value->prefix;
            if(Schema::hasTable("{$prefix}_common_mail_settings")){
                DB::table("{$prefix}_common_mail_settings")->truncate();
                $data = [
                        [
                            'mail_type' => 'send_tranpass',
                            'subject' => 'Change Transaction Password ',
                            'mail_content' => '
Dear {{first_name}},

Your new Transaction Password is {{password}}

',
                            'status' => 1,
                            'created_at' => now(),
                        ],
                        [
                            'mail_type' => 'payout_request',
                            'subject' => 'Payout Request',
                            'mail_content' => '
Dear {{admin_user_name}},

{{username}} requested payout of {{payout_amount}}

',
                            'status' => 1,
                            'created_at' => now(),
                        ],
                        [
                            'mail_type' => 'registration_email_verification',
                            'subject' => 'Email Verification',
                            'mail_content' => '
Hi {{full_name}},

Thanks for creating {{company_name}} account. To continue, Please confirm your email address by clicking the link:

                                Click Here
',
                            'status' => 1,
                            'created_at' => now(),
                        ],
                        [
                            'mail_type' => 'forgot_password',
                            'subject' => 'Forgot Password',
                            'mail_content' => '
Dear Customer,

you are recently requested reset password for that please follow the below link:

                                Reset Password
',
                            'status' => 1,
                            'created_at' => now(),
                        ],
                        [
                            'mail_type' => 'reset_googleAuth',
                            'subject' => 'Reset Google Authentication',
                            'mail_content' => '
Dear Customer,

you are recently requested reset Google Authentication for that please follow the below link:

                                Click Here
',
                            'status' => 1,
                            'created_at' => now(),
                        ],
                        [
                            'mail_type' => 'forgot_transaction_password',
                            'subject' => 'Forgot Transaction Password',
                            'mail_content' => '
Dear Customer,

You have recently requested to change your Transaction password. Follow the link below to reset the Transaction password:

                                Click Here
',
                            'status' => 1,
                            'created_at' => now(),
                        ],
                        [
                            'mail_type' => 'external_mail',
                            'subject' => '',
                            'mail_content' => '
Subject:{{subject}},

Message:            {{content}}

',
                            'status' => 1,
                            'created_at' => now(),
                        ],
                        [
                            'mail_type' => 'change_password',
                            'subject' => 'Change Password ',
                            'mail_content' => '
Dear {{full_name}},

Your password has been sucessfully changed, Your new password is {{new_password}}

',
                            'status' => 1,
                            'created_at' => now(),
                        ],
                        [
                            'mail_type' => 'registration',
                            'subject' => 'Welcome to ',
                            'mail_content' => '
Congratulations!!! You have been registered successfully!,

Dear {{fullname}}


Your MLM software is now active. Please save this message, so you will have a permanent record of your MLM Software. I trust that this mail finds you mutually excited about your new opportunity with {{company_name}}. Each of us will play a role to ensure your successful integration into the company.

',
                            'status' => 1,
                            'created_at' => now(),
                        ],
                        [
                            'mail_type' => 'payout_release',
                            'subject' => 'Payout Release Mail ',
                            'mail_content' => '
Dear {{fullname}},

Your payout has been released successfully

',
                            'status' => 1,
                            'created_at' => now(),
                        ],

                    ];
                    DB::table("{$prefix}_common_mail_settings")->insert($data);
            }
        }
            dd("ok");
    }

    public function sponsorindexcolumncustomdemo()
    {
        $prefixes = DB::table('demo_users')->select('prefix')->where('is_preset',0)->where('deleted_date',null)->get();
        foreach ($prefixes as $key => $value) {
            $prefix = $value->prefix;

            if (!empty($prefix) && Schema::hasTable("{$prefix}_users") && !Schema::hasColumn("{$prefix}_users", 'sponsor_index')) {
                $sql = "ALTER TABLE {$prefix}_users ADD COLUMN sponsor_index INTEGER DEFAULT 0, ADD INDEX idx_sponsor_index (sponsor_index)";
                DB::statement($sql);
            }
        }
        print_r('done');
    }

    public function mailContentPlaceholdercustomdemo(){
        $prefixes = DB::table('demo_users')->select('prefix')->where('is_preset',0)->where('deleted_date',null)->get();
        foreach ($prefixes as $key => $value) {
            $prefix = $value->prefix;
            if(!Schema::hasTable("{$prefix}_placeholders")){
                Schema::create("{$prefix}_placeholders", function ($table) {
                    $table->id();
                    $table->text('placeholder');
                    $table->text('name');
                    $table->timestamps();
                });
            $data = [
                [
                    'placeholder' => 'first_name',
                    'name' => 'name',
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'placeholder' => 'password',
                    'name' => 'password',
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'placeholder' => 'username',
                    'name' => 'username',
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'placeholder' => 'admin_user_name',
                    'name' => 'adminUserName',
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'placeholder' => 'payout_amount',
                    'name' => 'payoutAmount',
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'placeholder' => 'fullname',
                    'name' => 'fullName',
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'placeholder' => 'company_name',
                    'name' => 'companyName',
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'placeholder' => 'full_name',
                    'name' => 'full_Name',
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'placeholder' => 'new_password',
                    'name' => 'newPassword',
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'placeholder' => 'links',
                    'name' => 'links',
                    'created_at' => now(),
                    'updated_at' => now(),
                ],

            ];
            DB::table("{$prefix}_placeholders")->insert($data);
        }
        }
        dd('yes');
    }

     public function fromtreecolumncustomdemo()
    {
        $prefixes = DB::table('demo_users')->select('prefix')->where('is_preset',0)->where('deleted_date',null)->get();
        foreach ($prefixes as $key => $value) {
            $prefix = $value->prefix;

            if (Schema::hasTable("{$prefix}_users") && !Schema::hasColumn("{$prefix}_users", 'from_tree')) {
                DB::statement("ALTER TABLE {$prefix}_users ADD COLUMN from_tree INT DEFAULT 0 COMMENT '1: true, 0: false'");
            }
        }
        dd("done");
    }

    public function fromtreecolumn()
    {
        $prefixes = DB::table('demo_users')->select('prefix')->where('is_preset',1)->get();
        foreach ($prefixes as $key => $value) {
            $prefix = $value->prefix;

            if (Schema::hasTable("{$prefix}_users") && !Schema::hasColumn("{$prefix}_users", 'from_tree')) {
                DB::statement("ALTER TABLE {$prefix}_users ADD COLUMN from_tree INT DEFAULT 0 COMMENT '1: true, 0: false'");
            }
        }
        dd("done");
    }
    public function insertPresetDemo() {
        $prefixes = DB::table('demo_users')->where('is_preset',1)->get();

        foreach ($prefixes as $key => $value) {
            config(['database.connections.mysql.prefix' => "{$value->prefix}_"]);
            DB::purge('mysql');
            DB::connection('mysql');

            $sql = "DROP VIEW IF EXISTS {$value->prefix}_user_registration_views; ";
            DB::statement($sql);
            Artisan::call('migrate', ['--force' => true ]);
            Artisan::call('db:seed', ['--class' => 'CountrySeeder', '--force' => true ]);
            Artisan::call('db:seed', ['--class' => 'StateSeeder', '--force' => true ]);
            Artisan::call('db:seed', ['--class' => 'PackageSeeder' , '--force' => true ]);
            Artisan::call('db:seed', ['--class' => 'CurrencyDetailsSeeder','--force' => true ]);
            Artisan::call('db:seed', ['--class' => 'CompensationSeeder','--force' => true ]);
            Artisan::call('db:seed', ['--class' => 'UserAddonSeeder','--force' => true ]);
            Artisan::call('db:seed', ['--class' => 'LanguageSeeder','--force' => true ]);

            $user = $this->serviceClass->updateModuleStatus($value->username, $value->mlm_plan, $value->prefix);
            $moduleStatus = ModuleStatus::first();

            Artisan::call('db:seed', []);

            if ($value->mlm_plan == 'Binary') {
                Artisan::call('migrate', [
                    '--path' => '/database/migrations/binary/',
                ]);
                Artisan::call('db:seed', [
                    '--class' => 'BinarySeeder',
                ]);
                Artisan::call('db:seed', [
                    '--class' => 'LegDetailSeeder',
                ]);
            } elseif ($value->mlm_plan == 'Donation') {
                Artisan::call('migrate', [
                    '--path' => '/database/migrations/donation/','--force' => true
                ]);

                Artisan::call('db:seed', [
                    '--class' => 'DonationSeeder','--force' => true
                ]);
            } elseif ($value->mlm_plan == 'Stair_Step') {
                Artisan::call('migrate', [
                    '--path' => '/database/migrations/stairstep/',
                ]);

                Artisan::call('db:seed', [
                    '--class' => 'StairstepSeeder','--force' => true
                ]);
            } elseif ($value->mlm_plan == 'Party') {
                Artisan::call('migrate', [
                    '--path' => '/database/migrations/party/','--force' => true
                ]);

                Artisan::call('db:seed', [
                    '--class' => 'PartyPlanSeeder',
                ]);
            } elseif ($value->mlm_plan == 'Monoline') {
                Artisan::call('migrate', [
                    '--path' => '/database/migrations/monoline/','--force' => true
                ]);

                Artisan::call('db:seed', [
                    '--class' => 'MonolineConfigSeeder','--force' => true
                ]);
            }
            Artisan::call('db:seed', ['--class' => 'MenuSeeder','--force' => true ]);
            Artisan::call('db:seed', ['--class' => 'MenuPermissionSeeder','--force' => true ]);

            if ($moduleStatus->ecom_status) {
                try {
                    $storeDb     = file_get_contents(config('mlm.ecom_revamp_database_url') . "databaserevamp.sql");
                    $database    = preg_replace('/oc_/', "{$value->prefix}_oc_", $storeDb);
                    $primaryKeys = file_get_contents(config('mlm.ecom_revamp_database_url') . "addkey.sql");
                    $prefixDbKey = preg_replace('/oc_/', "{$value->prefix}_oc_", $primaryKeys);
                    config(['database.connections.mysql.prefix' => "{$value->prefix}_"]);
                    DB::purge('mysql');
                    DB::connection('mysql');
                    DB::statement($database);
                    DB::statement($prefixDbKey);
                    $user = User::GetAdmin();
                    $user->oc_product_id = OCProduct::where('package_type', 'registration')->first()->product_id;
                    $user->ecom_customer_ref_id = 1;
                    $user->save();

                } catch (\Illuminate\Database\QueryException $ex) {
                    dd($ex->getMessage());
                    // Note any method of class PDOException can be called on $ex.
                }
            }
            if ($value->mlm_plan == "Monoline") {
                $user->nextReentry()->create([
                    'user_id'   => $user->id,
                    'next_reentry' => MonolineConfig::first()->downline_count
                ]);
            }
            if ($moduleStatus->rank_status) {
                Artisan::call('db:seed', ['--class' => 'RankSeeder']);
                Artisan::call('db:seed', ['--class' => 'PresetRankConfigSeeder']);
                Artisan::call('db:seed', ['--class' => 'RankDetailsSeeder']);
                Artisan::call('db:seed', ['--class' => 'RankConfigDownlineRankCountSeeder']);
                Artisan::call('db:seed', ['--class' => 'PurchaseRankSeeder']);
            }
            $count  = 20;
            $URL    = config('services.commission.url');
            $secret = config('services.commission.secret');
            // Http::timeout(60 * 60)->withHeaders([
            //     'prefix' => $value->prefix,
            //     'SECRET_KEY' => encryptData($secret),
            // ])->asForm()->post("{$URL}insert-Dummy", [
            //     'count' => $count,
            // ]);
        }
        dd('done');
    }
    public function deletePresetDemo() {
        $datas = DemoUser::where('is_preset', 1)->get();
        foreach ($datas as $data) {
            $prefix = $data['prefix'];
            $newData = DemoUser::find($data['id']);
            $this->dropAllTables($prefix);

        }
        dd("Done");
    }
    public function dropAllTables($prefix)
    {
        $tables = DB::select('SELECT TABLE_NAME, TABLE_TYPE FROM INFORMATION_SCHEMA.TABLES WHERE (TABLE_NAME LIKE "' . $prefix . '_%" AND TABLE_TYPE = "BASE TABLE") OR (TABLE_NAME LIKE "' . $prefix . '_%" AND TABLE_TYPE = "VIEW")');
        $db = config('database.connections.mysql.database');
        DB::statement('SET FOREIGN_KEY_CHECKS = 0');
        foreach ($tables as $table) {
            try {

                if ($table->TABLE_TYPE == 'BASE TABLE' && Schema::hasTable($table->TABLE_NAME)) {
                    DB::statement('DROP TABLE `' . $table->TABLE_NAME . '`');
                } elseif ($table->TABLE_TYPE == 'VIEW' && DB::select("SELECT COUNT(*) as count FROM information_schema.views WHERE table_schema = '{$db}' AND table_name = '" . $table->TABLE_NAME . "'")[0]->count > 0) {
                    DB::statement('DROP VIEW `' . $table->TABLE_NAME . '`');
                }
            } catch (\Illuminate\Database\QueryException $ex ) {
                continue;
            }

        }
        DB::statement('SET FOREIGN_KEY_CHECKS = 1');
    }

    public function InserPlacementData(Request $request)
    {
        $prefix = $request->prefix;
        if (Schema::hasTable("{$prefix}_user_placements")) {
            if (!DB::table("{$prefix}_user_placements")->where('id', 1)->exists()) {
                DB::table("{$prefix}_user_placements")->insert([
                    'user_id' => 1,
                ]);
            }
        }
        dd("DONE");
    }

    public function insertDemoUser(Request $request)
    {
        try {
            $randomNumber = mt_rand(10000, 99999);
            $insertData = [
                'username' => $request->username,
                'prefix' => $randomNumber,
                'api_key' => $randomNumber,
                'password' => Hash::make($request->username. '123'),
                'mlm_plan' => $request->plan,
                'is_preset' => 1,
                'account_status' => 'active',
                'company_name' => 'Demo',
                'full_name' => $request->full_name,
                'email' => $request->email,
                'phone' => $request->phone,
                'country' => 'India',
                'registration_date' => now(),
                'created_at' => now(),
                'updated_at' => now(),
            ];
            DB::table("demo_users")->insert($insertData);

            $prefix = $insertData['prefix'];

            $this->setupMigration($request, $prefix);

            $this->setupDbSeed($request);

            $this ->setupAddonSeeder($request);

            $this->setupUser($request);

            $this->finalSetup($request);

            dd("DONE");
        } catch (\Throwable $th) {
            dd($th);
        }

    }

    public function setupMigration($request, $prefix)
    {
        config(['database.connections.mysql.prefix' => "{$prefix}_"]);
        DB::purge('mysql');
        DB::connection('mysql');
        $request->session()->put('prefix', $prefix);
        Artisan::call('migrate', [
            '--force' => true
        ]);

        return response()->json([
            'status' => true,
            'message' => 'Migration completed successfully',
        ]);
    }

    public function addLead($data)
    {
        DB::purge('mysql');
        config(['database.connections.mysql.prefix' => ""]);
        DB::connection('mysql');

        $leadConfig = DB::table('infinite_mlm_lead_configs')->first();
        if (!$data) return true;
        // $mail_otp = substr(str_shuffle("0123456790123456789"), 1, 4);

        if (strtolower($data->country) == 'india') {
            $access_expiry = now()->addHours($leadConfig->indian_custom_demo_timeout);
        } else {
            $access_expiry = now()->addHours($leadConfig->other_custom_demo_timeout);
        }
        $leadDetails = [
            "name" => $data->full_name,
            "email" => $data->email,
            "phone" => $data->phone,
            "ip_address" => request()->ip(),
            // "country" => $data->country,
            // "country" => Location::get(request()->ip())->countryName ?? $data->country,
            "country" => $data->country,
            "demo_type" => 'custom',
            "demo_ref_id" => $data->id,
            "status" => 'verified',
            "added_date" => now(),
            "access_expiry" => $access_expiry,
            "email_otp" => null,
            "phone_otp" => null,
            "otp_expiry" => null,
        ];
        DB::table('infinite_mlm_leads')->insert($leadDetails);

        return true;
    }

    public function setupDbSeed($request)
    {
        try {
            DB::purge('mysql');
            config(['database.connections.mysql.prefix' => ""]);
            DB::connection('mysql');
            $prefix = DemoUser::where('username', $request->username)->first();
            $prefix->phone = $request->phone;
            $prefix->save();
            if (!$prefix) {
                return response()->json([
                    'status'    => false,
                    'message'   => 'User Not Found',
                ], 404);
            }
            $this->addLead($prefix);

            $data = [
                'name' => $prefix->full_name,
                'password' => base64_decode($prefix->temp_password),
                'email' => $prefix->email,
                'phone' => $request->phone ?? 0,
                'country' => $prefix->country,
                'username' => $prefix->username,
                'mlm_plan' => $prefix->mlm_plan,
            ];

            DB::purge('mysql');
            config(['database.connections.mysql.prefix' => "{$prefix->prefix}_"]);
            DB::connection('mysql');
            session()->forget('prefix');
            $request->session()->put('prefix', $prefix->prefix);
            Artisan::call('db:seed', [
                '--class' => 'CountrySeeder',
                '--force' => true
            ]);
            Artisan::call('db:seed', [
                '--class' => 'StateSeeder',
                '--force' => true
            ]);
            Artisan::call('db:seed', [
                '--class' => 'LanguageSeeder',
                '--force' => true
            ]);
            Artisan::call('db:seed', [
                '--class' => 'PackageSeeder',
                '--force' => true
            ]);

            Artisan::call('db:seed', [
                '--class' => 'CurrencyDetailsSeeder',
                '--force' => true
            ]);
            Artisan::call('db:seed', [
                '--class' => 'CompensationSeeder',
                '--force' => true
            ]);
            Artisan::call('db:seed', [
                '--class' => 'UserAddonSeeder',
                '--force' => true
            ]);

            Artisan::call('db:seed', [
                '--class' => 'TermsAndConditionsSeeder',
                '--force' => true
            ]);

            return true;
        } catch (\Throwable $th) {
            dd($th);
        }
    }

    public function setupAddonSeeder($request)
    {
        try {
            $requestData = [];
            $requestData = $request;
            $prefix = session()->get('prefix');
            DB::purge('mysql');
            config(['database.connections.mysql.prefix' => "{$prefix}_"]);
            DB::connection('mysql');
            $customdemoService = new CustomdemoService;
            $module = $customdemoService->setModuleCompensation($requestData);
            if($module->ecom_status) {
                $storeDb     = file_get_contents(config('mlm.ecom_revamp_database_url') . "databaserevamp.sql");
                $prefix      = session()->get('prefix');
                $prefixDb    = preg_replace('/oc_/', "{$prefix}_oc_", $storeDb);
                $primaryKeys = file_get_contents(config('mlm.ecom_revamp_database_url') . "addkey.sql");
                $prefixDbKey = preg_replace('/oc_/', "{$prefix}_oc_", $primaryKeys);
                DB::statement($prefixDb);
                DB::statement($prefixDbKey);
            }
            $customdemoService->setupAddon($requestData);
            if ($request->plan == 'Binary') {
                Artisan::call('migrate', [
                    '--path' => '/database/migrations/binary/',
                    '--force' => true
                ]);

                Artisan::call('db:seed', [
                    '--class' => 'BinarySeeder',
                    '--force' => 'true'
                ]);
            } elseif ($request->plan == 'Donation') {
                Artisan::call('migrate', [
                    '--path' => '/database/migrations/donation/',
                    '--force' => true
                ]);

                Artisan::call('db:seed', [
                    '--class' => 'DonationSeeder',
                    '--force' => true
                ]);
            } elseif ($request->plan == 'Stair_Step') {
                Artisan::call('migrate', [
                    '--path' => '/database/migrations/stairstep/',
                    '--force' => true
                ]);

                Artisan::call('db:seed', [
                    '--class' => 'StairstepSeeder',
                    '--force' => true
                ]);
            } elseif ($request->plan == 'Party') {
                Artisan::call('migrate', [
                    '--path' => '/database/migrations/party/',
                    '--force' => true
                ]);

                Artisan::call('db:seed', [
                    '--class' => 'PartyPlanSeeder',
                    '--force' => true
                ]);
            }

            return true;
        } catch (\Throwable $th) {
            dd($th);
        }
    }

    public function setupUser($request)
    {
        try {
            DB::purge('mysql');
            config(['database.connections.mysql.prefix' => ""]);
            DB::connection('mysql');
            $prefix = DemoUser::where('username', $request->username)->first();
            if (!$prefix) {
                return response()->json([
                    'status'    => false,
                    'message'   => 'User Not Found',
                ], 404);
            }
            DB::purge('mysql');
            config(['database.connections.mysql.prefix' => "{$prefix->prefix}_"]);
            DB::connection('mysql');
            $moduleStatus = ModuleStatus::first();
            $customdemoService = new CustomdemoService;
            $customdemoService->addAdmin($request, $prefix);
            $customdemoService->addAddon($request);
            $customdemoService->setConfigurationSeeder($request, $prefix->prefix);
            if ($request->plan == 'Matrix') {
                $customdemoService->setConfigurationSeederUpdate($request, $prefix->prefix);
            }
            Artisan::call('db:seed', [
                '--class' => 'CustomDemoConfigurationSeeder',
                '--force' => true
            ]);
            if ($moduleStatus->ecom_status) {
                $admin = User::GetAdmin();
                $product = DB::table('oc_product')->where('package_type', 'registration')->first();
                $admin->oc_product_id = $product->product_id;
                $admin->ecom_customer_ref_id = 1;
                $admin->push();
            }
            return true;
        } catch (\Throwable $th) {
            dd($th);
        }
    }

    public function finalSetup($request)
    {
        try {
            $prefix = session()->get('prefix');
            DB::purge('mysql');
            config(['database.connections.mysql.prefix' => "{$prefix}_"]);
            DB::connection('mysql');
            Artisan::call('db:seed', [
                '--class' => 'MenuSeeder',
                '--force' => true
            ]);
            Artisan::call('db:seed', [
                '--class' => 'CustomDemoMenuPermissionSeeder',
                '--force' => true
            ]);
            $user = User::where('username', $request->username)->first();
            $demoUser = collect(DB::select('select * from demo_users where username = ? LIMIT 1', [$request->username]))->first();
            // $access_expiry = $this->setAccessExpiry($request->username);
            if ($user) {
                Auth::login($user);
                event(new Registered($user));
                // $this->sendCustomDemoOtp($request->username); commented for new version signup with google

                session()->forget('is_preset');

                session()->put('is_preset', 0);

                $prefix = session()->get('prefix');

                // $this->sendCustomDemoWelcomeMail($demoUser, $request, $prefix, $access_expiry);

                DB::purge('mysql');
                config(['database.connections.mysql.prefix' => "{$prefix}_"]);
                return route('dashboard');
            }
            return false;
        } catch (\Throwable $th) {
            dd($th);
        }
    }

    public function customdemodelete(){
        $prefixes = DB::table('demo_users')->select('prefix')->where('is_preset',0)->where('deleted_date',null)->get();
        foreach ($prefixes as $value){
            $prefix = $value->prefix;
            $tables = DB::select('SELECT TABLE_NAME, TABLE_TYPE FROM INFORMATION_SCHEMA.TABLES WHERE (TABLE_NAME LIKE "' . $prefix . '_%" AND TABLE_TYPE = "BASE TABLE") OR (TABLE_NAME LIKE "' . $prefix . '_%" AND TABLE_TYPE = "VIEW")');
            foreach ($tables as $table) {
                try {
                    $db = config('database.connections.mysql.database');
                    DB::statement('SET FOREIGN_KEY_CHECKS = 0');
                    if ($table->TABLE_TYPE == 'BASE TABLE' && Schema::hasTable($table->TABLE_NAME)) {
                        DB::statement('DROP TABLE `' . $table->TABLE_NAME . '`');
                    } elseif ($table->TABLE_TYPE == 'VIEW' && DB::select("SELECT COUNT(*) as count FROM information_schema.views WHERE table_schema = '{$db}' AND table_name = '" . $table->TABLE_NAME . "'")[0]->count > 0) {
                        DB::statement('DROP VIEW `' . $table->TABLE_NAME . '`');
                    }
                } catch (\Illuminate\Database\QueryException $ex ) {
                    continue;
                }
                DB::statement('SET FOREIGN_KEY_CHECKS = 1');
            }
        }
        dd("Done");
    }

    public function userplacementdata(){
        $prefixes = DB::table('demo_users')->select('prefix')->where('is_preset',1)->where('mlm_plan','Binary')->get();
        foreach ($prefixes as $key => $value) {
            $prefix = $value->prefix;
            if(Schema::hasTable("{$prefix}_users")){
                if (!Schema::hasTable("{$prefix}_user_placements")){
                    $this->serviceClass->createuserplacementtable($prefix);
                }
                DB::table("{$prefix}_user_placements")->truncate();
                DB::table("{$prefix}_user_placements")->insert([
                    'user_id' => 1,
                ]);
                $users = DB::table("{$prefix}_users")->where('user_type','!=','admin')->get();
                if (count($users) > 0){
                    foreach ($users as $user){
                        $sponsor    = DB::table("{$prefix}_users")->where('id',$user->sponsor_id)->first();
                        $position   = $user->position;
                        $this->serviceClass->updatePlacementTable($sponsor , $user , $position , $prefix);
                    }
                }
            }
        }
        dd("ok");
    }

    public function userplacementdatacustomdemo(){
        $prefixes = DB::table('demo_users')->select('prefix')->where('is_preset',0)->where('deleted_date',null)->where('mlm_plan','Binary')->get();
        foreach ($prefixes as $key => $value) {
            $prefix = $value->prefix;
            $user = DB::table("{$prefix}_users")->get();
            if($user->count() > 0){   
                if(Schema::hasTable("{$prefix}_users")){
                    if (!Schema::hasTable("{$prefix}_user_placements")){
                        $this->serviceClass->createuserplacementtable($prefix);
                    }
                    DB::table("{$prefix}_user_placements")->truncate();
                    DB::table("{$prefix}_user_placements")->insert([
                        'user_id' => 1,
                    ]);
                    $users = DB::table("{$prefix}_users")->where('user_type','!=','admin')->get();
                    if (count($users) > 0){
                        foreach ($users as $user){
                            $sponsor    = DB::table("{$prefix}_users")->where('id',$user->sponsor_id)->first();
                            $position   = $user->position;
                            $this->serviceClass->updatePlacementTable($sponsor , $user , $position , $prefix);
                        }
                    }
                }
            }
        }
        dd("ok");
    }

    public function insertSponsorIndex(Request $request)
    {
        $prefix = $request->prefix;
        $user = DB::table("{$prefix}_users as u")
            ->join(DB::raw("(SELECT id, sponsor_id, ROW_NUMBER() OVER (PARTITION BY sponsor_id ORDER BY id) - 1 AS sponsor_index FROM `{$prefix}_users`) numbered_users"), 'u.id', '=', 'numbered_users.id')
            ->update(['u.sponsor_index' => DB::raw('numbered_users.sponsor_index')]);

            dd("DONE");
    }

    public function customSponsorIndex()
    {
        $prefixes = DB::table('demo_users')->select('prefix')->where('is_preset', 0)->where('deleted_date', null)->get();
        foreach ($prefixes as $key => $value) {
            $prefix = $value->prefix;

            if (Schema::hasTable("{$prefix}_users") && Schema::hasColumn("{$prefix}_users" ,'sponsor_index')) {
                $user = DB::table("{$prefix}_users as u")
                    ->join(DB::raw("(SELECT id, sponsor_id, ROW_NUMBER() OVER (PARTITION BY sponsor_id ORDER BY id) AS sponsor_index FROM `{$prefix}_users`) numbered_users"), 'u.id', '=', 'numbered_users.id')
                    ->update(['u.sponsor_index' => DB::raw('numbered_users.sponsor_index')]);
            }

        }
        dd("DONE");
    }

    public function presetSponsorIndex()
    {
        $prefixes = DB::table('demo_users')->select('prefix')->where('is_preset', 1)->get();
        foreach ($prefixes as $key => $value) {
            $prefix = $value->prefix;

            if (Schema::hasTable("{$prefix}_users") && Schema::hasColumn("{$prefix}_users" ,'sponsor_index')) {
                $user = DB::table("{$prefix}_users as u")
                    ->join(DB::raw("(SELECT id, sponsor_id, ROW_NUMBER() OVER (PARTITION BY sponsor_id ORDER BY id)  AS sponsor_index FROM `{$prefix}_users`) numbered_users"), 'u.id', '=', 'numbered_users.id')
                    ->update(['u.sponsor_index' => DB::raw('numbered_users.sponsor_index')]);
            }

        }
        dd("DONE");
    }

    public function aggregateUserCommissionSum()
    {
        $prefixes = DB::table('demo_users')->select('prefix')->where('is_preset',1)->get();

        foreach ($prefixes as $key => $value) {
            $prefix = $value->prefix;

            $viewName = "{$prefix}_aggregateUserCommissionSum";

            $viewExists = DB::select("SHOW TABLES LIKE '{$viewName}'");

            if (empty($viewExists)) {
                $viewQuery = "CREATE VIEW {$prefix}_aggregateUserCommissionSum AS
                SELECT user_id, SUM(amount_payable) AS total_amount_payable
                FROM {$prefix}_aggregate_user_commission_and_incomes
                GROUP BY user_id";

                DB::statement($viewQuery);

            }
        }

        dd("DONE");

    }

    public function aggregateUserCommissionSumcustomdemo()
    {
        $prefixes = DB::table('demo_users')->select('prefix')->where('is_preset', 0)->where('deleted_date', null)->get();

        foreach ($prefixes as $key => $value) {
            $prefix = $value->prefix;

            $viewName = "{$prefix}_aggregateUserCommissionSum";

            $viewExists = DB::select("SHOW TABLES LIKE '{$viewName}'");

            if (empty($viewExists) && Schema::hasTable("{$prefix}_aggregate_user_commission_and_incomes")) {
                $viewQuery = "CREATE VIEW {$prefix}_aggregateUserCommissionSum AS
                SELECT user_id, SUM(amount_payable) AS total_amount_payable
                FROM {$prefix}_aggregate_user_commission_and_incomes
                GROUP BY user_id";

                DB::statement($viewQuery);

            }
        }

        dd("DONE");

    }


    public function approvalmenu(){
        $prefixes = DB::table('demo_users')->select('prefix')->where('is_preset',1)->get();
        foreach ($prefixes as $key => $value){
            $prefix = $value->prefix;
            if(Schema::hasTable("{$prefix}_menus")){
                $approval = DB::table("{$prefix}_menus")->where('slug','allapproval')->where('parent_id',null)->first();
                if(!$approval){
                    $data = [
                            'title' => 'Approval',
                            'slug' => 'allapproval',
                            'parent_id' => null,
                            'order' => 4,
                            'react' => 0,
                            'admin_only' => 1,
                            'react_only' => 0,
                            'is_heading' => 1,
                            'has_children' => 1,
                            'is_child' => 0,
                            'admin_icon' => 'bx bx-user-check',
                            'user_icon' => 'DraftsOutlinedIcon',
                            'route_name' => 'responder.index',
                            'created_at' => now(),
                            'updated_at' => now(),
                        ];
                    $mainmenu = DB::table("{$prefix}_menus")->insert($data);
                    $moduleStatus = DB::table("{$prefix}_module_statuses")->first();
                    $mainmenuid = DB::table("{$prefix}_menus")->where('slug','allapproval')->first();
                    $permissiondata = [
                                'menu_id' => $mainmenuid->id,
                                'admin_permission' => 1,
                                'user_permission' => 0,
                                'created_at' => now(),
                                'updated_at' => now(),
                            ];
                    DB::table("{$prefix}_menu_permissions")->insert($permissiondata);
                    if($mainmenu){
                        $subMenus = [];
                        if (!$moduleStatus->ecom_status) {
                            $subMenus[] = [
                                'title' => 'Registration Approval',
                                'slug' => Str::slug('Registration Approval'),
                                'parent_id' => $mainmenuid->id,
                                'child_order' => 1,
                                'is_heading' => 0,
                                'has_children' => 0,
                                'is_child' => 1,
                                'admin_only' => 1,
                                'react_only' => 0,
                                'route_name' => 'approval.index',
                                'created_at' => now(),
                                'updated_at' => now(),
                            ];
                            $signup = DB::table("{$prefix}_menus")->where('slug','sign-up')->where('parent_id',null)->first();
                            DB::table("{$prefix}_menus")->where('parent_id',$signup->id)->where('slug','approve')->delete();
                        }
                        if(!$moduleStatus->ecom_status && $moduleStatus->package_upgrade){
                            $subMenus[] = [
                                'title' => 'Upgrade Approval',
                                'slug' => Str::slug('Upgrade Approval'),
                                'parent_id' => $mainmenuid->id,
                                'child_order' => 2,
                                'is_heading' => 0,
                                'has_children' => 0,
                                'is_child' => 1,
                                'admin_only' => 1,
                                'react_only' => 0,
                                'route_name' => 'package-upgrade.view',
                                'created_at' => now(),
                                'updated_at' => now(),
                            ];
                        }
                        if (!$moduleStatus->ecom_status && $moduleStatus->subscription_status){
                            $subMenus[] = [
                                'title' => 'Renewal Approval',
                                'slug' => Str::slug('Renewal Approval'),
                                'parent_id' => $mainmenuid->id,
                                'child_order' => 3,
                                'is_heading' => 0,
                                'has_children' => 0,
                                'is_child' => 1,
                                'admin_only' => 1,
                                'react_only' => 0,
                                'route_name' => 'package-renewal.view',
                                'created_at' => now(),
                                'updated_at' => now(),
                            ];
                        }
                        if ($moduleStatus->ecom_status) {
                            $subMenus[] = [
                                'title' => 'Order Approval',
                                'slug' => Str::slug('Order Approval'),
                                'parent_id' => $mainmenuid->id,
                                'child_order' => 2,
                                'is_heading' => 0,
                                'has_children' => 0,
                                'is_child' => 1,
                                'admin_only' => 1,
                                'react_only' => 0,
                                'route_name' => 'ecom.order.approval',
                                'created_at' => now(),
                                'updated_at' => now(),
                            ];
                            $orderapproval = DB::table("{$prefix}_menus")->where('slug','order-details')->where('parent_id',null)->first();
                            DB::table("{$prefix}_menus")->where('parent_id',$orderapproval->id)->where('slug','order-approval')->delete();
                        }
                        DB::table("{$prefix}_menus")->insert($subMenus);
                        foreach ($subMenus as $menu){
                            $submenu = DB::table("{$prefix}_menus")->where('slug',$menu['slug'])->where('parent_id',$mainmenuid->id)->first();
                            $permissiondata = [
                                'menu_id' => $submenu->id,
                                'admin_permission' => 1,
                                'user_permission' => 0,
                                'created_at' => now(),
                                'updated_at' => now(),
                            ];
                            DB::table("{$prefix}_menu_permissions")->insert($permissiondata);
                        }
                    }
                }
            }
        }
        dd('ok');
    }
    public function approvalmenucustomdemo(){
        $prefixes = DB::table('demo_users')->select('prefix')->where('is_preset',0)->where('deleted_date',null)->get();
        foreach ($prefixes as $key => $value){
            $prefix = $value->prefix;
            if(Schema::hasTable("{$prefix}_menus")){
                $approval = DB::table("{$prefix}_menus")->where('slug','allapproval')->where('parent_id',null)->first();
                if(!$approval){
                    $data = [
                            'title' => 'Approval',
                            'slug' => 'allapproval',
                            'parent_id' => null,
                            'order' => 4,
                            'react' => 0,
                            'admin_only' => 1,
                            'react_only' => 0,
                            'is_heading' => 1,
                            'has_children' => 1,
                            'is_child' => 0,
                            'admin_icon' => 'bx bx-envelope',
                            'user_icon' => 'DraftsOutlinedIcon',
                            'route_name' => 'responder.index',
                            'created_at' => now(),
                            'updated_at' => now(),
                        ];
                    $mainmenu = DB::table("{$prefix}_menus")->insert($data);
                    $moduleStatus = DB::table("{$prefix}_module_statuses")->first();
                    $mainmenuid = DB::table("{$prefix}_menus")->where('slug','allapproval')->where('parent_id',null)->first();
                    $permissiondata = [
                                'menu_id' => $mainmenuid->id,
                                'admin_permission' => 1,
                                'user_permission' => 0,
                                'created_at' => now(),
                                'updated_at' => now(),
                            ];
                    DB::table("{$prefix}_menu_permissions")->insert($permissiondata);
                    if($mainmenu && $moduleStatus){
                        $subMenus = [];
                        if (!$moduleStatus->ecom_status) {
                            $subMenus[] = [
                                'title' => 'Registration Approval',
                                'slug' => Str::slug('Registration Approval'),
                                'parent_id' => $mainmenuid->id,
                                'child_order' => 1,
                                'is_heading' => 0,
                                'has_children' => 0,
                                'is_child' => 1,
                                'admin_only' => 1,
                                'react_only' => 0,
                                'route_name' => 'approval.index',
                                'created_at' => now(),
                                'updated_at' => now(),
                            ];
                            $signup = DB::table("{$prefix}_menus")->where('slug','sign-up')->where('parent_id',null)->first();
                            if($signup){
                                DB::table("{$prefix}_menus")->where('slug','approve')->where('parent_id',$signup->id)->delete();
                            }
                        }
                        if(!$moduleStatus->ecom_status && $moduleStatus->package_upgrade){
                            $subMenus[] = [
                                'title' => 'Upgrade Approval',
                                'slug' => Str::slug('Upgrade Approval'),
                                'parent_id' => $mainmenuid->id,
                                'child_order' => 2,
                                'is_heading' => 0,
                                'has_children' => 0,
                                'is_child' => 1,
                                'admin_only' => 1,
                                'react_only' => 0,
                                'route_name' => 'package-upgrade.view',
                                'created_at' => now(),
                                'updated_at' => now(),
                            ];
                        }
                        if (!$moduleStatus->ecom_status && $moduleStatus->subscription_status){
                            $subMenus[] = [
                                'title' => 'Renewal Approval',
                                'slug' => Str::slug('Renewal Approval'),
                                'parent_id' => $mainmenuid->id,
                                'child_order' => 3,
                                'is_heading' => 0,
                                'has_children' => 0,
                                'is_child' => 1,
                                'admin_only' => 1,
                                'react_only' => 0,
                                'route_name' => 'package-renewal.view',
                                'created_at' => now(),
                                'updated_at' => now(),
                            ];
                        }
                        if ($moduleStatus->ecom_status) {
                            $subMenus[] = [
                                'title' => 'Order Approval',
                                'slug' => Str::slug('Order Approval'),
                                'parent_id' => $mainmenuid->id,
                                'child_order' => 2,
                                'is_heading' => 0,
                                'has_children' => 0,
                                'is_child' => 1,
                                'admin_only' => 1,
                                'react_only' => 0,
                                'route_name' => 'ecom.order.approval',
                                'created_at' => now(),
                                'updated_at' => now(),
                            ];
                           $orderapproval = DB::table("{$prefix}_menus")->where('slug','order-details')->where('parent_id',null)->first();
                           if($orderapproval){
                                DB::table("{$prefix}_menus")->where('parent_id',$orderapproval->id)->where('slug','order-approval')->delete();
                           }
                        }
                        DB::table("{$prefix}_menus")->insert($subMenus);
                        foreach ($subMenus as $menu){
                            $submenu = DB::table("{$prefix}_menus")->where('slug',$menu['slug'])->where('parent_id',$mainmenuid->id)->first();
                            $permissiondata = [
                                'menu_id' => $submenu->id,
                                'admin_permission' => 1,
                                'user_permission' => 0,
                                'created_at' => now(),
                                'updated_at' => now(),
                            ];
                            DB::table("{$prefix}_menu_permissions")->insert($permissiondata);
                        }
                    }
                }
            }
        }
        dd('ok');
    }

    public function commonmailsettingslang(){
        $prefixes = DB::table('demo_users')->select('prefix')->where('is_preset',1)->get();
        foreach($prefixes as $key => $value){
            $prefix = $value->prefix;
            if (Schema::hasTable("{$prefix}_common_mail_settings") && !Schema::hasColumn("{$prefix}_common_mail_settings", 'lang_id')){
                Schema::table("{$prefix}_common_mail_settings", function (Blueprint $table) {
                    $table->integer('lang_id')->default(1);
                });
            }
        }
        dd("Done");
    }
    public function commonmailsettingslangcustomdemo(){
        $prefixes = DB::table('demo_users')->select('prefix')->where('is_preset',0)->where('deleted_date',null)->get();
        foreach($prefixes as $key => $value){
            $prefix = $value->prefix;
            if (Schema::hasTable("{$prefix}_common_mail_settings") && !Schema::hasColumn("{$prefix}_common_mail_settings", 'lang_id')){
                Schema::table("{$prefix}_common_mail_settings", function (Blueprint $table) {
                    $table->integer('lang_id')->default(1);
                });
            }
        }
        dd("Done");
    }

    public function packageupgardehistory()
    {
        $prefixes = DB::table('demo_users')->select('prefix')->where('is_preset',1)->get();
        foreach($prefixes as $key => $value){
            $prefix = $value->prefix;
            if(Schema::hasTable("{$prefix}_package_upgrade_histories") && Schema::hasColumn("{$prefix}_package_upgrade_histories", 'payment_receipt')){
                Schema::table("{$prefix}_package_upgrade_histories", function (Blueprint $table) use ($prefix) {
                    $table->dropColumn('payment_receipt');
                });
                DB::statement("ALTER TABLE {$prefix}_package_upgrade_histories ADD COLUMN payment_receipt BIGINT UNSIGNED AFTER status, ADD CONSTRAINT {$prefix}_package_upgrade_histories_payment_forienkey FOREIGN KEY (payment_receipt) REFERENCES {$prefix}_payment_receipts(id) ON DELETE CASCADE");
            }
        }
        dd("Done");
    }

    public function packageupgardehistorycustomdemo()
    {
        $prefixes = DB::table('demo_users')->select('prefix')->where('is_preset',0)->where('deleted_date',null)->get();
        foreach($prefixes as $key => $value){
            $prefix = $value->prefix;
            if(Schema::hasTable("{$prefix}_package_upgrade_histories") && Schema::hasColumn("{$prefix}_package_upgrade_histories", 'payment_receipt')){
                Schema::table("{$prefix}_package_upgrade_histories", function (Blueprint $table) use ($prefix) {
                    $table->dropColumn('payment_receipt');
                });
                DB::statement("ALTER TABLE {$prefix}_package_upgrade_histories ADD COLUMN payment_receipt BIGINT UNSIGNED AFTER status, ADD CONSTRAINT {$prefix}_package_upgrade_histories_payment_forienkey FOREIGN KEY (payment_receipt) REFERENCES {$prefix}_payment_receipts(id) ON DELETE CASCADE");
            }
        }
        dd("Done");
    }

    public function ticketstatusnewcolumn()
    {
        $prefixes = DB::table('demo_users')->select('prefix')->where('is_preset',1)->get();
        foreach ($prefixes as $key => $value) {
            $prefix = $value->prefix;
            if(Schema::hasTable("{$prefix}_ticket_status") && !Schema::hasColumn("{$prefix}_ticket_status" ,'slug') && !Schema::hasColumn("{$prefix}_ticket_status" ,'default')){
                Schema::table("{$prefix}_ticket_status", function ($table) {
                    $table->string('slug')->after('status');
                    $table->tinyInteger('default')->comment('0: blocked 1 : active')->default(0)->after('slug');
                });
                DB::statement('SET FOREIGN_KEY_CHECKS = 0');
                DB::table("{$prefix}_ticket_status")->truncate();
                $data = [
                    [
                        'ticket_status' => 'New',
                        'status' => 1,
                        'slug' => Str::slug('New'),
                        'default' => 1,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ],
                    [
                        'ticket_status' => 'In Progress',
                        'status' => 1,
                        'slug' => Str::slug('In Progress'),
                        'default' => 1,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ],
                    [
                        'ticket_status' => 'Resolved',
                        'status' => 1,
                        'slug' => Str::slug('Resolved'),
                        'default' => 1,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ],
                    [
                        'ticket_status' => 'On Hold',
                        'status' => 1,
                        'slug' => Str::slug('On Hold'),
                        'default' => 1,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ],
                    [
                        'ticket_status' => 'Re open',
                        'status' => 1,
                        'slug' => Str::slug('Re open'),
                        'default' => 1,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ],
                ];
                DB::table("{$prefix}_ticket_status")->insert($data);
                DB::statement('SET FOREIGN_KEY_CHECKS = 1');
            }
        }
        dd("done");
    }

    public function ticketstatusnewcolumncustomdemo()
    {
        $prefixes = DB::table('demo_users')->select('prefix')->where('is_preset',0)->where('deleted_date',null)->get();
        foreach ($prefixes as $key => $value) {
            $prefix = $value->prefix;
            if(Schema::hasTable("{$prefix}_ticket_status") && !Schema::hasColumn("{$prefix}_ticket_status" ,'slug') && !Schema::hasColumn("{$prefix}_ticket_status" ,'default')){
                Schema::table("{$prefix}_ticket_status", function ($table) {
                    $table->string('slug')->after('status');
                    $table->tinyInteger('default')->comment('0: blocked 1 : active')->default(0)->after('slug');
                });
                DB::statement('SET FOREIGN_KEY_CHECKS = 0');
                DB::table("{$prefix}_ticket_status")->truncate();
                $data = [
                    [
                        'ticket_status' => 'New',
                        'status' => 1,
                        'slug' => Str::slug('New'),
                        'default' => 1,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ],
                    [
                        'ticket_status' => 'In Progress',
                        'status' => 1,
                        'slug' => Str::slug('In Progress'),
                        'default' => 1,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ],
                    [
                        'ticket_status' => 'Resolved',
                        'status' => 1,
                        'slug' => Str::slug('Resolved'),
                        'default' => 1,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ],
                    [
                        'ticket_status' => 'On Hold',
                        'status' => 1,
                        'slug' => Str::slug('On Hold'),
                        'default' => 1,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ],
                    [
                        'ticket_status' => 'Re open',
                        'status' => 1,
                        'slug' => Str::slug('Re open'),
                        'default' => 1,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ],
                ];
                DB::table("{$prefix}_ticket_status")->insert($data);
                DB::statement('SET FOREIGN_KEY_CHECKS = 1');
            }
        }
        dd("done");
    }

    public function yearMonthColumncustomdemo()
    {
        $prefixes = DB::table('demo_users')->select('prefix')->where('is_preset',0)->where('deleted_date',null)->get();
        foreach ($prefixes as $key => $value) {
            $prefix = $value->prefix;

            if (Schema::hasTable("{$prefix}_users") && !Schema::hasColumns("{$prefix}_users", ['year', 'year_month'])) {
                DB::statement("ALTER TABLE {$prefix}_users ADD COLUMN `year` VARCHAR(255) DEFAULT NULL");
                DB::statement("ALTER TABLE {$prefix}_users ADD COLUMN `year_month` VARCHAR(255) DEFAULT NULL");
            }
        }
        dd("done");
    }

    public function yearMonthColumn()
    {
        $prefixes = DB::table('demo_users')->select('prefix')->where('is_preset',1)->get();
        foreach ($prefixes as $key => $value) {
            $prefix = $value->prefix;

            if (Schema::hasTable("{$prefix}_users") && !Schema::hasColumns("{$prefix}_users", ['year', 'year_month'])) {
                DB::statement("ALTER TABLE {$prefix}_users ADD COLUMN `year` VARCHAR(255) DEFAULT NULL");
                DB::statement("ALTER TABLE {$prefix}_users ADD COLUMN `year_month` VARCHAR(255) DEFAULT NULL");
            }
        }
        dd("done");
    }

    public function insertDataToYearMonthColumn()
    {
        $prefixes = DB::table('demo_users')->select('prefix')->where('is_preset',1)->get();

        foreach ($prefixes as $key => $value) {
            $prefix = $value->prefix;

            if (Schema::hasTable("{$prefix}_users") && Schema::hasColumns("{$prefix}_users", ['year', 'year_month'])) {
                $users = DB::table("{$prefix}_users")->get();

                foreach ($users as $user) {
                    $dateOfJoining = Carbon::parse($user->date_of_joining);

                    DB::table("{$prefix}_users")->where('id', $user->id)->update([
                        'year' => $dateOfJoining->year,
                        'year_month' => $dateOfJoining->format('Y-m')
                    ]);
                }
            }
        }
        dd("done");
    }

    public function insertDataToYearMonthColumncustomdemo()
    {
        $prefixes = DB::table('demo_users')->select('prefix')->where('is_preset',0)->where('deleted_date',null)->get();

        foreach ($prefixes as $key => $value) {
            $prefix = $value->prefix;

            if (Schema::hasTable("{$prefix}_users") && Schema::hasColumns("{$prefix}_users", ['year', 'year_month'])) {
                $users = DB::table("{$prefix}_users")->get();

                foreach ($users as $user) {
                    $dateOfJoining = Carbon::parse($user->date_of_joining);

                    DB::table("{$prefix}_users")->where('id', $user->id)->update([
                        'year' => $dateOfJoining->year,
                        'year_month' => $dateOfJoining->format('Y-m')
                    ]);
                }
            }
        }
        dd("done");
    }

    public function yearMonthColumnuserregistration()
    {
        $prefixes = DB::table('demo_users')->select('prefix')->where('is_preset',1)->get();
        foreach ($prefixes as $key => $value) {
            $prefix = $value->prefix;

            if (Schema::hasTable("{$prefix}_users_registrations") && !Schema::hasColumns("{$prefix}_users_registrations", ['year', 'year_month'])) {
                DB::statement("ALTER TABLE {$prefix}_users_registrations ADD COLUMN `year` VARCHAR(255) DEFAULT NULL");
                DB::statement("ALTER TABLE {$prefix}_users_registrations ADD COLUMN `year_month` VARCHAR(255) DEFAULT NULL");
            }
        }
        dd("done");
    }

    public function yearMonthColumnuserregistrationcustomdemo()
    {
        $prefixes = DB::table('demo_users')->select('prefix')->where('is_preset',0)->where('deleted_date',null)->get();
        foreach ($prefixes as $key => $value) {
            $prefix = $value->prefix;

            if (Schema::hasTable("{$prefix}_users_registrations") && !Schema::hasColumns("{$prefix}_users_registrations", ['year', 'year_month'])) {
                DB::statement("ALTER TABLE {$prefix}_users_registrations ADD COLUMN `year` VARCHAR(255) DEFAULT NULL");
                DB::statement("ALTER TABLE {$prefix}_users_registrations ADD COLUMN `year_month` VARCHAR(255) DEFAULT NULL");
            }
        }
        dd("done");
    }


    public function insertDataToYearMonthColumnuserregistration()
    {
        $prefixes = DB::table('demo_users')->select('prefix')->where('is_preset',1)->get();

        foreach ($prefixes as $key => $value) {
            $prefix = $value->prefix;

            if (Schema::hasTable("{$prefix}_users_registrations") && Schema::hasColumns("{$prefix}_users_registrations", ['year', 'year_month'])) {
                $users = DB::table("{$prefix}_users")->get();

                foreach ($users as $user) {
                    $dateOfJoining = Carbon::parse($user->date_of_joining);

                    DB::table("{$prefix}_users_registrations")->where('user_id', $user->id)->update([
                        'year' => $dateOfJoining->year,
                        'year_month' => $dateOfJoining->format('Y-m')
                    ]);
                }
            }
        }
        dd("done");
    }

    public function insertDataToYearMonthColumnuserregistrationcustomdemo()
    {
        $prefixes = DB::table('demo_users')->select('prefix')->where('is_preset',0)->where('deleted_date',null)->get();

        foreach ($prefixes as $key => $value) {
            $prefix = $value->prefix;

            if (Schema::hasTable("{$prefix}_users_registrations") && Schema::hasColumns("{$prefix}_users_registrations", ['year', 'year_month'])) {
                $users = DB::table("{$prefix}_users")->get();

                foreach ($users as $user) {
                    $dateOfJoining = Carbon::parse($user->date_of_joining);

                    DB::table("{$prefix}_users_registrations")->where('user_id', $user->id)->update([
                        'year' => $dateOfJoining->year,
                        'year_month' => $dateOfJoining->format('Y-m')
                    ]);
                }
            }
        }
        dd("done");
    }

    public function ranktablesortorder()
    {
        $prefixes = DB::table('demo_users')->select('prefix')->where('is_preset',1)->get();
        foreach ($prefixes as $key => $value) {
            $prefix = $value->prefix;
            if (Schema::hasTable("{$prefix}_ranks") && !Schema::hasColumn("{$prefix}_ranks" ,'rank_order')){
                Schema::table("{$prefix}_ranks", function ($table) {
                    $table->tinyInteger('rank_order')->nullable()->unique();
                });
                $ranks = DB::table("{$prefix}_ranks")->get();
                foreach ($ranks as $rank){
                    DB::table("{$prefix}_ranks")->where('id',$rank->id)->update(['rank_order' => $rank->id]);
                }
            }
        }
        dd("done");
    }

    public function ranktablesortordercustomdemo()
    {
        $prefixes = DB::table('demo_users')->select('prefix')->where('is_preset',0)->where('deleted_date',null)->get();
        foreach ($prefixes as $key => $value) {
            $prefix = $value->prefix;
            if (Schema::hasTable("{$prefix}_ranks") && !Schema::hasColumn("{$prefix}_ranks" ,'rank_order')){
                Schema::table("{$prefix}_ranks", function ($table) {
                    $table->tinyInteger('rank_order')->nullable()->unique();
                });
                $ranks = DB::table("{$prefix}_ranks")->get();
                foreach ($ranks as $rank){
                    DB::table("{$prefix}_ranks")->where('id',$rank->id)->update(['rank_order' => $rank->id]);
                }
            }
        }
        dd("done");
    }

}
