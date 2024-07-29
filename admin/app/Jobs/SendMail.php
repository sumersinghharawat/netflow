<?php

namespace App\Jobs;

use App\Mail\SendMail as MailSendMail;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;
use Config;

class SendMail implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    public $timeout = 600;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(
        protected $mailData,
        protected $config
    ){
        $this->onQueue('mailer');
    }


    public function handle()
    {
        Config::set('mail', $this->config);
        $email  = new MailSendMail($this->mailData);
        Mail::to($this->mailData['to']['email'])->send($email);
    }
}
