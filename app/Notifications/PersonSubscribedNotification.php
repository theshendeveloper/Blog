<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class PersonSubscribedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
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
                ->subject('عضویت در خبرنامه')
                ->view('emails.Template1',
                    [
                        'BigTitle' => 'شما در خبرنامه عضو شدید',
                        'SmallTitle' => "کاربر عزیز ",
                        'Message' => "عضویت شما در خبرنامه‌ی وبلاگ یلوادوایز با موفقیت انجام شد. شما در جریان پست‌های جدید قرار خواهید گرفت.",
                    ]);
    }
}
