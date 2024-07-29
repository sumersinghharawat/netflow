<?php

namespace App\Console\Commands;

use App\Jobs\NotifyIncompleteMailDemoUsers;
use Illuminate\Console\Command;

class IncompletDemoMail extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'notify:incomplete-users';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'One Time Notification Mail Send to Custom Demo Users';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        NotifyIncompleteMailDemoUsers::dispatch();
        return Command::SUCCESS;
    }
}
