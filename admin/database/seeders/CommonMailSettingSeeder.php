<?php

namespace Database\Seeders;

use App\Models\CommonMailSetting;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CommonMailSettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('common_mail_settings')->delete();
        $data = [
            [
                'mail_type' => 'send_tranpass',
                'subject' => 'Change Transaction Password ',
                'mail_content' => '<p>Dear {{first_name}},</p><p>Your new Transaction Password is {{password}}</p>',
                'status' => 1,
                'created_at' => now(),
            ],
            [
                'mail_type' => 'payout_request',
                'subject' => 'Payout Request',
                'mail_content' => '<p>Dear {{admin_user_name}},</p><p>{{username}} requested payout of {{payout_amount}}</p>',
                'status' => 1,
                'created_at' => now(),
            ],
            [
                'mail_type' => 'registration_email_verification',
                'subject' => 'Email Verification',
                'mail_content' => '<p>Hi {{full_name}},</p><p>Thanks for creating {{company_name}} account. To continue, Please confirm your email address by clicking the link:</p>
                    <button style="background: #954CEA;border: 0px;color: #fff;padding: 12px 25px;font-size: 15px;border-radius: 30px;display: block;margin: 20px auto;"><a href="{{link}}" style="text-decoration: none;color: #fff;">Click Here</a></button>',
                'status' => 1,
                'created_at' => now(),
            ],
            [
                'mail_type' => 'forgot_password',
                'subject' => 'Forgot Password',
                'mail_content' => '<p>Dear Customer,</p><p>you are recently requested reset password for that please follow the below link:</p>
                    <button style="background: #954CEA;border: 0px;color: #fff;padding: 12px 25px;font-size: 15px;border-radius: 30px;display: block;margin: 20px auto;"><a href="{{link}}" style="text-decoration: none;color: #fff;">Reset Password</a></button>',
                'status' => 1,
                'created_at' => now(),
            ],
            [
                'mail_type' => 'reset_googleAuth',
                'subject' => 'Reset Google Authentication',
                'mail_content' => '<p>Dear Customer,</p><p>you are recently requested reset Google Authentication for that please follow the below link:</p>
                    <button style="background: #954CEA;border: 0px;color: #fff;padding: 12px 25px;font-size: 15px;border-radius: 30px;display: block;margin: 20px auto;"><a href="{{link}}" style="text-decoration: none;color: #fff;">Click Here</a></button>',
                'status' => 1,
                'created_at' => now(),
            ],
            [
                'mail_type' => 'forgot_transaction_password',
                'subject' => 'Forgot Transaction Password',
                'mail_content' => '<p>Dear Customer,</p><p>You have recently requested to change your Transaction password. Follow the link below to reset the Transaction password:</p>
                    <button style="background: #954CEA;border: 0px;color: #fff;padding: 12px 25px;font-size: 15px;border-radius: 30px;display: block;margin: 20px auto;"><a href="{{link}}" style="text-decoration: none;color: #fff;">Click Here</a></button>',
                'status' => 1,
                'created_at' => now(),
            ],
            [
                'mail_type' => 'external_mail',
                'subject' => '',
                'mail_content' => '<p>Subject:{{subject}},</p><p>Message:&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;{{content}}</p>',
                'status' => 1,
                'created_at' => now(),
            ],
            [
                'mail_type' => 'change_password',
                'subject' => 'Change Password ',
                'mail_content' => '<p>Dear {{full_name}},</p><p>Your password has been sucessfully changed, Your new password is {{new_password}}</p>',
                'status' => 1,
                'created_at' => now(),
            ],
            [
                'mail_type' => 'registration',
                'subject' => 'Welcome to ',
                'mail_content' => '<p><p>Congratulations!!! You have been registered successfully!,</p><p>Dear {{fullname}}</p>
                    <p> Your MLM software is now active. Please save this message, so you will have a permanent record of your MLM Software. I trust that this mail finds you mutually excited about your new opportunity with {{company_name}}. Each of us will play a role to ensure your successful integration into the company. </p>',
                'status' => 1,
                'created_at' => now(),
            ],
            [
                'mail_type' => 'payout_release',
                'subject' => 'Payout Release Mail ',
                'mail_content' => '<p>Dear {{fullname}},</p><p> Your payout has been released successfully</p>',
                'status' => 1,
                'created_at' => now(),
            ],

        ];

        CommonMailSetting::insert($data);
    }
}
