<?php

namespace App\Jobs;

use App\Models\TicketActivity;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;

class TicketActivityJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $timeout = 900;

    public function __construct(
        protected $ticket,
        protected $user,
        protected $activity,
        protected $prefix,
        protected $reply,
        protected $comment
    ){
        $this->onQueue('ticketActivity');
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $ticketActivity = new TicketActivity();
        $ticketActivity->ticket_id = $this->ticket;
        $ticketActivity->doneby = $this->user;
        $ticketActivity->activity = $this->activity;
        $ticketActivity->if_comment = $this->comment;
        $ticketActivity->if_reply = $this->reply;
        $ticketActivity->save();
    }
}
