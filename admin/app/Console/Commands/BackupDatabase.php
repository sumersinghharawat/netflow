<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

class BackupDatabase extends Command
{
    protected $signature = 'db:backup {--database= : The database connection to backup} {--path= : The path to save the backup} {--filename= : The filename for the backup} {--days=7 : The number of days to keep backups}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Backup the specified database';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        // Check the database connection
        try {
            $database = $this->option('database') ?? config('database.default');
            DB::connection($database)->getPdo();
        } catch (\Exception $e) {
            $this->error("Could not connect to the database. Please check your configuration. error: " . $e->getMessage());
            return;
        }
        $path = $this->option('path') ?? "/www/wwwroot/admin/backup";
        $filename = $this->option('filename') ?? date('Y-m-d_H-i-s').'_'.$database.'.sql.gz';
        // create the directory if not exists
        if (!File::exists($path)) {
            File::makeDirectory($path, 0775, true);
        }
        $command = sprintf(
            'mysqldump --user=%s --password=%s --host=%s --single-transaction %s | gzip > %s',
            "root",
            "da02344cbdfdf827",
            config("database.connections.{$database}.host"),
            config("database.connections.{$database}.database"),
            $path.'/'.$filename
        );
        $this->info(config("database.connections.{$database}.host"));
        exec($command);

        $this->info("The backup has been saved to {$path}/{$filename}");

        $days = $this->option('days') ?? 7;

        $files = File::files($path);

        $now = time();
        $cutoff = $now - ($days * 24 * 60 * 60);

        foreach ($files as $file) {
            if (File::lastModified($file) < $cutoff) {
                File::delete($file);
                $this->info("Deleted old backup: {$file}");
            }
        }

        exec("sudo -u ims rclone --config /home/ims/.config/rclone/rclone.conf sync $path ims:");
    }
}
