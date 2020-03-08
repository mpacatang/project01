<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class NewUserActivation extends Notification
{
    use Queueable;

    public $activationCode;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($activationCode)
    {
        $this->activationCode = $activationCode;
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
                ->subject('Activate your account')
                ->markdown('mails.NewUserActivation', [
                    'url' => url('/activation/' . $this->activationCode)
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
