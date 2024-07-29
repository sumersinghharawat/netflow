<?php

namespace App\Console\Commands;

use DB;
use App\Models\User;
use App\Models\OCProduct;
use App\Models\ModuleStatus;
use Illuminate\Console\Command;

class Setup extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'setup:project';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Migrate all tables for the projects';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->call('migrate:fresh');
        $this->info("*********** Table migration completed ***********************\n");
        $this->info("*********** Table data seeder started ***********************\n");
        $this->call('db:seed');
        $this->info("*********** Table data seeder completed *********************\n");
        $moduleStatus = ModuleStatus::first();
        if ($moduleStatus->ecom_status) {
            $this->info("*********** Store Table migration started. It may take few moments *********************\n");
            $storeDb     = file_get_contents(config('mlm.ecom_database_url'));
            $prefix      = config('database.connections.mysql.prefix');
            $prefixDb    = preg_replace('/oc_/', "{$prefix}oc_", $storeDb);
            DB::statement($prefixDb);
            $user        = User::GetAdmin();
            $user->oc_product_id = OCProduct::where('package_type', 'registration')->first()->product_id;
            $user->ecom_customer_ref_id = 1;
            $user->save();
            $this->call('db:seed', [
                '--class' => 'RankSeeder'
            ]);
            $this->call('db:seed', [
                '--class' => 'RankConfigDownlineRankCountSeeder'
            ]);
            $this->call('db:seed', [
                '--class' => 'RankConfigSeeder'
            ]);
            $this->call('db:seed', [
                '--class' => 'RankDetailsSeeder'
            ]);
        }
        $this->info("*********** Migration Completed. *********************\n");

        return Command::SUCCESS;
    }
}
