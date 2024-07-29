<?php

namespace App\Jobs;

use App\Mail\CustomDemoExpireMail;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

class CustomDemoMailNotification implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $demousers = DB::table('infinite_mlm_leads as l')
            ->leftJoin('demo_users as u', 'l.demo_ref_id', 'u.id')
            ->where('l.demo_type', 'custom')
            ->where('u.account_status', '!=', 'deleted')
            ->get();

        foreach ($demousers as $key => $user) {
            if ($user->warning_mail_sent) {
                continue;
            }
            $demo = [];
            //If the date of expire is after deletion period, the message should be
            if ($this->checkExpireIsAfterDeletionPeriod($user->added_date, $user->access_expiry, $user->deleted_at)) {
                $hour = $this->getDeletedHour($user->deleted_at);
                $mail_subject = "IMPORTANT!";
                $demo['title'] = "Important!";
                $demo['message'] = "Your Infinite MLM Software demo has been removed,";
                $demo['message1'] = "a recovery won't be possible please contact our support team to extend your access time.";
            }

            //Mail should be sent to demo emails which is going to expire withing 24 hours
            elseif ($this->demoIsExpireWithIn24Hours($user->deleted_at, $user->access_expiry)) {
                $to_time = strtotime($user->access_expiry);
                $from_time = strtotime(date('Y-m-d H:i:s'));
                $hour =  round(abs($to_time - $from_time) / 60 / 60, 0);
                $mail_subject = "IMPORTANT!";
                $demo['title'] = "Important!";
                $demo['message'] = "Your Infinite MLM Software demo will be blocked within $hour hours.";
                $demo['message1'] = "If you would like to further use your demo,";
            }
            if (isset($demo['message']) and !empty($demo['message'])) {
                DB::table('infinite_mlm_leads')->where('demo_ref_id', $user->id)->update([
                    'warning_mail_sent' => true
                ]);

                $mailData = [
                    'username' => $user->username,
                    'title' => $demo['title'],
                    'message' => $demo['message'],
                    'message1' => $demo['message1']
                ];
                Mail::to($user->email)->send(new CustomDemoExpireMail($mail_subject, $mailData));
            }
        }
    }

    function checkExpireIsAfterDeletionPeriod($created_at, $expire_at, $deleted_at)
    {
        if ($deleted_at) {
            return false;
        }
        $delete_at = date('Y-m-d H:i:s', strtotime($created_at . ' + 30 days'));
        if ($delete_at > $expire_at) {
            return false;
        }
        return true;
    }

    function getDeletedHour($deleted_at)
    {
        if ($deleted_at != NULL) {
            $to_time = strtotime(date('Y-m-d H:i:s'));
            $from_time = strtotime($deleted_at);
            return round(abs($to_time - $from_time) / 60 / 60, 0);
        }
        return 0;
    }

    function demoIsExpireWithIn24Hours($delete_at, $expiry)
    {
        if ($delete_at) {
            return false;
        }

        $from_time = strtotime(date('Y-m-d H:i:s'));
        $hours = round(abs(strtotime($expiry) - $from_time) / 60 / 60);
        if ($hours > 24 || $hours < 0) {
            return false;
        }
        return true;
    }
}
