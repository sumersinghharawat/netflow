<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class AllUserMailNotification extends Notification
{
    // use Queueable;
    private $allMailData;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($allMailData)
    {
        $this->allMailData = $allMailData;
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
            "type"      =>"all_mail",
            "title"     =>"all_mail_title",
            "url"       =>$this->allMailData['url'],
            "icon"      =>"<i class='bx bx-envelope'></i>"
        ];
    }

}
