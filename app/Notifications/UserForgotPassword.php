<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class UserForgotPassword extends Notification
{
    use Queueable;

    public $resetCode;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($resetCode)
    {
        $this->resetCode = $resetCode;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        return (new MailMessage)
                ->from('noreply@Yuup.com', 'Yuup')
                ->subject('Forgotten password reset')
                ->markdown('mails.UserForgotPassword', [
                    'user' => $notifiable,
                    'url' => url('/auth/reset-password/' . $this->resetCode)
                ]);
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return [
            //
        ];
    }
}
