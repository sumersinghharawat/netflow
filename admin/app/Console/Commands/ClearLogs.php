<?php

namespace App\Console\Commands;

use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class ClearLogs extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'clear:logs';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Deletes logs that are older than 10 days';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $logDirectory = storage_path('logs');

        if (!File::exists($logDirectory)) {
            return;
        }

        $files = File::allFiles($logDirectory);
        foreach ($files as $file) {
            $fileCreationTime = Carbon::createFromTimestamp(File::lastModified($file));

            if ($fileCreationTime->diffInDays(Carbon::now()) > 10) {
                File::delete($file);
            }
        }
    }
}
