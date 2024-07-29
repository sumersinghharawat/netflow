<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class TestCron extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'run:test-cron';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'For testing cron job';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $myFile = '/var/www/admin/public/crontest.txt';
        $myFileContents = file_get_contents($myFile);
        $myDate = "\n". now();
        file_put_contents($myFile,$myDate, FILE_APPEND);
        return Command::SUCCESS;
    }
}
