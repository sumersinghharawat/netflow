<?php

namespace App\Services;

use App\Jobs\SendMail;
use App\Models\Mailsetting;
use Illuminate\Support\Str;
use App\Models\SignupSetting;
use App\Models\CompanyProfile;
use App\Models\CommonMailSetting;
use Illuminate\Support\Facades\DB;
use App\Models\PayoutConfiguration;
use App\Services\UserApproveService;
use Illuminate\Support\Facades\Mail;
use Config;
use App\Models\User;

class SendMailService
{
    public function sendAllEmails($type = 'notification', $user = '' ,$regData = '', $attachments = array())
    {
        $companyProfile = CompanyProfile::first();
        $commonMailSettings = Mailsetting::first();


        $mailTo = array("email" => $regData['email'], "name" => $regData['first_name'] );
        if ($type == "ext_mail") {
            // $mailFrom = array("email" => $regData['email_from'], "name" => $regData['full_name']);
        } else {
            $mailFrom = array("email" => $companyProfile['email'], "name" => $companyProfile['name']);
        }
        $mailReplyTo = $mailFrom;
        $mailSubject = "Notification";
        $mailBodyDetails = " ";

        if ($type == "registration") {
            $SignupSettings = SignupSetting::first();
            if ($SignupSettings['mail_notification'] == 0) {
                return;
            }
            $content = CommonMailSetting::where('mail_type',$type)->first();
            $mailSubject = $mailAltbody = $content['subject'] ?? "Registration";
            $mailBodyDetails = $content['mail_content'];

            $mailBodyDetails = Str::replace("{{fullname}}", $regData['first_name'], $mailBodyDetails);
            $mailBodyDetails = Str::replace("{{username}}", $regData['username'], $mailBodyDetails);
            $mailBodyDetails = Str::replace("{{company_name}}", $companyProfile['name'], $mailBodyDetails);
            $mailBodyDetails = Str::replace("{{company_address}}", $companyProfile['address'], $mailBodyDetails);
            $mailBodyDetails = Str::replace("{{sponsor_username}}", $regData['sponsorName'], $mailBodyDetails);
            $mailBodyDetails = Str::replace("{{payment_type}}", $regData['payment_method'], $mailBodyDetails);

        } elseif($type == 'payout_release'){
            $SignupSettings = PayoutConfiguration::first();
            if ($SignupSettings['mail_status'] == 0) {
                return;
            }
            $content = CommonMailSetting::where('mail_type',$type)->first();
            $mailSubject = $mailAltbody = $content['subject'] ?? "Payout Release";
            $mailBodyDetails = $content['mail_content'];

            $mailBodyDetails = Str::replace("{{fullname}}", $regData['first_name'], $mailBodyDetails);
            // $mailBodyDetails = Str::replace("{{username}}", $regData['username'], $mailBodyDetails);
            $mailBodyDetails = Str::replace("{{company_name}}", $companyProfile['name'], $mailBodyDetails);
            $mailBodyDetails = Str::replace("{{company_address}}", $companyProfile['address'], $mailBodyDetails);

        } elseif($type == "autoresponder"){
            // $content = CommonMailSetting::where('mail_type',$type)->first();
            $mailContent = $regData['mail_content'];
            $mailSubject = $content['subject'] ?? "Auto Responder";
            $mailBodyDetails = $mailContent;

            $mailBodyDetails = Str::replace("{{visitor_name}}", $regData['user_name'], $mailBodyDetails);
            $mailBodyDetails = Str::replace("{{member_name}}", $regData['sponser_name'], $mailBodyDetails);
            $mailBodyDetails = Str::replace("{{member_email}}", $regData['sponser_email'], $mailBodyDetails);

        } elseif($type == "change_password"){
            $content = CommonMailSetting::where('mail_type',$type)->first();
            $mailSubject = $mailAltbody = $content['subject'] ?? "Change Password";
            $mailBodyDetails = $content['mail_content'];

            $mailBodyDetails = Str::replace("{{full_name}}", $regData['full_name'], $mailBodyDetails);
            $mailBodyDetails = Str::replace("{{new_password}}", $regData['new_password'], $mailBodyDetails);

        } elseif($type == "send_tranpass"){
            $content = CommonMailSetting::where('mail_type',$type)->first();
            $mailSubject = $mailAltbody = $content['subject'] ?? "Transaction Password";
            $mailBodyDetails = $content['mail_content'];

            $mailBodyDetails = Str::replace("{{first_name}}", $regData['first_name'], $mailBodyDetails);
            $mailBodyDetails = Str::replace("{{password}}", $regData['tranpass'], $mailBodyDetails);

        } elseif($type == "payout_request"){
            $content = CommonMailSetting::where('mail_type',$type)->first();
            $mailSubject = $mailAltbody = $content['subject'] ?? "Payout Request";
            $mailBodyDetails = $content['mail_content'];

            $mailBodyDetails = Str::replace("{{admin_user_name}}", $this->ADMIN_USER_NAME, $mailBodyDetails);
            $mailBodyDetails = Str::replace("{{username}}", $regData['username'], $mailBodyDetails);
            $mailBodyDetails = Str::replace("{{payout_amount}}", $regData['payout_amount'], $mailBodyDetails);

        } elseif($type == "invaite_mail"){
            // $content = CommonMailSetting::where('mail_type',$type)->first();
            $mailSubject = $mailAltbody = "Invite Email";
            $mailBodyDetails = $regData['mail_content'];

        } elseif($type == "forgot_password"){
            $content = CommonMailSetting::where('mail_type',$type)->first();
            $mailSubject = $mailAltbody = $content['subject'] ?? "Forgot Password";
            $mailBodyDetails = $content['mail_content'];

            // variables
            $mailBodyDetails = Str::replace("{{fullname}}", $regData['full_name'], $mailBodyDetails);
            $mailBodyDetails = Str::replace("{{company_name}}", $companyProfile['name'], $mailBodyDetails);
            $mailBodyDetails = Str::replace("{{company_address}}", $companyProfile['address'], $mailBodyDetails);

            // Link
            // $keyword = $this->login_model->getKeyWord($regr['user_id']);
            // $keyword_encode = $this->encryption->encrypt($keyword);
            // $keyword_encode = str_replace(["=", "/", "+"], ["-", "~", "."], $keyword_encode);
            // $link = base_url() . "login/reset_password/$keyword_encode";
            // $mailBodyDetails = str_replace("{link}", $link, $mailBodyDetails);

        } elseif($type == "reset_googleAuth"){
            $content = CommonMailSetting::where('mail_type',$type)->first();
            $mailSubject = $content['subject'] ?? "Reset Google Authentication";
            $mailBodyDetails = $content['mail_content'];
            $mailBodyDetails = Str::replace("{{link}}", $regData['reset_url'],$mailBodyDetails);
        } elseif($type == 'lcp_reply'){
            // $content = CommonMailSetting::where('mail_type',$type)->first();
            // $mailSubject = $mailAltbody = $content['subject'] ?? "Forgot Password";
            // $mailBodyDetails = $content['mail_content'];

            //todo
        } elseif($type == 'forgot_transaction_password'){
            $content = CommonMailSetting::where('mail_type',$type)->first();
            $mailSubject = $mailAltbody = $content['subject'] ?? "Forgot Transaction Password";
            $mailBodyDetails = $content['mail_content'];

            // // Link
            // $this->load->model('tran_pass_model');
            // $admin_username = $this->validation_model->getAdminUsername();
            // $keyword = $this->tran_pass_model->getKeyWord($regr['user_id']);
            // $keyword_encode = $this->encryption->encrypt($keyword);
            // $keyword_encode = str_replace(["=", "/", "+"], ["-", "~", "."], $keyword_encode);
            // $link = base_url() . "login/reset_tran_password/$keyword_encode/$admin_username";

            // $mailBodyDetails = str_replace("{link}", $link, $mailBodyDetails);

        } elseif($type == "external_mail"){
            $content = CommonMailSetting::where('mail_type',$type)->first();
            $mailSubject = $mailAltbody = $content['subject'] ?? "External Mail";
            $mailBodyDetails = $content['mail_content'];

            $mailBodyDetails = Str::replace("{{subject}}", $regData['subject'], $mailBodyDetails);
            $mailBodyDetails = Str::replace("{{content}}", $regData['content'], $mailBodyDetails);

        } elseif ($type == 'registration_email_verification') {
            $content = CommonMailSetting::where('mail_type',$type)->first();
            $mailSubject = $content['subject'] ?? "Registration Email Verification";
            $mailBodyDetails = $content['mail_content'];
            $mailBodyDetails = Str::replace("{{full_name}}", $regData['first_name'], $mailBodyDetails);
            $mailBodyDetails = Str::replace("{{company_name}}", $companyProfile['name'], $mailBodyDetails);
            $redirect_link   = Config('mlm.user_url').'/confirm_email'.'/'.base64_encode($regData['username']).'/'.base64_encode(User::getAdmin()->username);
            $mailBodyDetails = Str::replace("{{link}}", $redirect_link,$mailBodyDetails);
        }else{
            return;
        }

        $footerText = $companyProfile['name']. ', ' . $companyProfile['address']. '<br/> Ph: '. $companyProfile['phone'];
        // $companyLogo = 'storage/'.$companyProfile['login_logo'] ?? "logo-dark.png";
        $companyLogo = 'logo-dark.png';

        $mailData = [
            "from"    => $mailFrom,
            "to"      => $mailTo,
            "subject" => $mailSubject,
            "content" => $mailBodyDetails,
            "footer"  => $footerText,
            "logo"    => $companyLogo,
        ];
        $mail = DB::table('mail_settings')->first();
        if ($mail) //checking if table is not empty
        {
            $config = array(
                'driver'     => 'smtp',
                'host'       => $mail->smtp_host,
                'port'       => $mail->smtp_port,
                'from'       => array('address' => $mail->from_email, 'name' => $mail->from_name),
                'encryption' => $mail->smtp_protocol,
                'username'   => $mail->smtp_username,
                'password'   => $mail->smtp_password,
                'timeout'    => $mail->smtp_timeout,
                'auth_mode'  => null,
                'verify_peer' => false,
            );
        }
        SendMail::dispatch($mailData, $config);
    }
}
