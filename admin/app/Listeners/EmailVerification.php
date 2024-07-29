<?php

namespace App\Listeners;

use App\Events\UserRegistered;
use App\Services\SendMailService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use App\Models\SignupSetting;

class EmailVerification
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  \App\Events\UserRegistered  $event
     * @return void
     */
    public function handle(UserRegistered $event)
    {
        $verifyStatus = SignupSetting::pluck('email_verification')->first();
        if($verifyStatus){
            $serviceClass = new SendMailService;
            $sendMail = $serviceClass->sendAllEmails("registration_email_verification",$event->userId, $event->regData);
        }
    }
}
