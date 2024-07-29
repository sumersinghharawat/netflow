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
use App\Models\User;
use App\Models\AutoResponderSettings;
use Carbon\Carbon;
use App\Models\CompanyProfile;
use Illuminate\Support\Facades\DB;
use App\Models\ModuleStatus;


class AutoResponderJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    public $timeout = 900;


    public function __construct()
    {
        $this->onQueue('autoResponder');
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $module_status = ModuleStatus::get()->toArray();
        if ($module_status[0]['autoresponder_status']) {
            $mail_details = AutoResponderSettings::where('status', 1)->where('date_to_send', Carbon::now()->format('d'))->get();
            foreach ($mail_details as $data) {
                // $users = User::select('id')->whereRelation('userDetail', 'email', '!=', '')->where('active', 1)->where('user_type', 'user')->with('userDetail:id,user_id,email')->get();
                $users = User::select('id,email')->where('email', '!=', '')->where('active', 1)->where('user_type', 'user')->with('userDetail:id,user_id')->get();
                foreach ($users->chunk(25) as $user) {
                    $user_email = [];
                    foreach ($user as $user_data) {
                        array_push($user_email, $user_data->userDetail->email);
                    }
                    $mail = DB::table('mail_settings')->first();
                    Config::set('mail', $this->mailConfig($mail));
                    $email  = new MailSendMail($this->mailData($data, $mail));
                    Mail::to($user_email)->send($email);
                }
            }
        }
    }
    public function mailData($data, $mail)
    {
        $companyProfile = CompanyProfile::first();
        $footerText = $companyProfile['name'] . ', ' . $companyProfile['address'] . '<br/> Ph: ' . $companyProfile['phone'];
        $companyLogo = $companyProfile['login_logo'] ? 'storage/' . $companyProfile['login_logo'] : "logo-dark.png";
        return [
            "from"    => ['email' => $mail->from_email, 'name' => $companyProfile['name']],
            // "to"      => $mailTo,
            "subject" => $data->subject,
            "content" => $data->content,
            "footer"  => $footerText,
            "logo"    => $companyLogo,
        ];
    }
    public function mailConfig($mail)
    {
        return [
            'driver'     => 'smtp',
            'host'       => $mail->smtp_host,
            'port'       => $mail->smtp_port,
            'from'       => array('address' => $mail->from_email, 'name' => $mail->from_name),
            'encryption' => $mail->smtp_protocol,
            'username'   => $mail->smtp_username,
            'password'   => $mail->smtp_password,
            'timeout'    => $mail->smtp_timeout
        ];
    }
}
