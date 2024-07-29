<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\DB;
use Config;
use Illuminate\Support\Facades\Auth;

class MailServiceProvide extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {

            // $mail = DB::table('mail_settings')->first();
            // if ($mail) //checking if table is not empty
            // {
            //     $config = array(
            //         'driver'     => 'smtp',
            //         'host'       => $mail->smtp_host,
            //         'port'       => $mail->smtp_port,
            //         'from'       => array('address' => $mail->from_email, 'name' => $mail->from_name),
            //         'encryption' => $mail->smtp_protocol,
            //         'username'   => $mail->smtp_username,
            //         'password'   => $mail->smtp_password,
            //     );
            //     Config::set('mail', $config);
            // }
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}
