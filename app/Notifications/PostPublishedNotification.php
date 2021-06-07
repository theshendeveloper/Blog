<?php

namespace App\Notifications;

use App\Models\Post;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class PostPublishedNotification extends Notification implements ShouldQueue
{
    use Queueable;
    private $post;
    /**
     * Create a new notification instance.
     *
     * @param $post
     */
    public function __construct(Post $post)
    {
        $this->post = $post ;

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
            ->subject('یک پست جدید منتشر شد')
            ->view('emails.Template1',
                [
                    'BigTitle' => 'یک پست جدید منتشر شد',
                    'SmallTitle' => "کاربر عزیز",
                    'Message' => " یک پست جدید با عنوان {$this->post->title} در وبلاگ یلوادوایز منتشر شده است.",
                    'link' => $this->post->path(),
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
