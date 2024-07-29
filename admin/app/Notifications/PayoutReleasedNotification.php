<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class PayoutReleasedNotification extends Notification
{
    // use Queueable;
    private $payoutData;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($payoutData)
    {
        $this->payoutData = $payoutData;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['Database'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        // return (new MailMessage)
        //             ->line('The introduction to the notification.')
        //             ->action('Notification Action', url('/'))
        //             ->line('Thank you for using our application!');
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        //
    }

    public function toDatabase($notifiable)
    {
        return [
            "type"      =>"payout_released",
            "title"     =>"payout_released_title",
            "request_id"=> $this->payoutData['requestId'],
            "amount"    =>$this->payoutData['amount'],
            "url"       =>$this->payoutData['url'],
            "user_id"   =>$this->payoutData['userId'],
            "username"  =>$this->payoutData['username'],
            "icon"      =>'<i class="fa-solid fa-wallet"></i>',
        ];
    }

}
