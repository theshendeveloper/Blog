<?php

namespace App\Http\Controllers;

use App\Models\Subscriber;
use App\Notifications\PersonSubscribedNotification;
use Illuminate\Http\Request;
use Illuminate\Notifications\Messages\MailMessage;

class SubscriberController extends Controller
{

    public function __invoke(Request $request)
    {
        $attributes = $request->validate([
            'email'=> ['required','string','email','unique:subscribers']
        ]);
        $subscriber = Subscriber::create($attributes);
        $subscriber->notify(new PersonSubscribedNotification());
        session()->flash('status', [
            'type' => 'success',
            'message' => 'شما با موفقیت در خبرنامه عضو شدید.'
        ]);
        return back();
    }
}
