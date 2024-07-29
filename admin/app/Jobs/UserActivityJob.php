<?php

namespace App\Jobs;

use App\Models\Activity;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;

class UserActivityJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(
        protected $user,
        protected $data,
        protected $activity,
        protected $description,
        protected $prefix,
        protected $user_type
    ){
        $this->onQueue('userActivity');
    }

    public function handle()
    {
        $userActivity = new Activity();
        $userActivity->user_id = $this->user;
        $userActivity->ip = request()->ip();
        $userActivity->activity = $this->activity;
        $userActivity->user_type = $this->user_type;
        $userActivity->description = $this->description;
        $userActivity->data = json_encode($this->data);
        $userActivity->save();
    }
}
