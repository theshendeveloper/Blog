<?php

namespace App\Http\Controllers;

class CaptchaController extends Controller
{
    public function refreshCaptcha()
    {
        return response()->json(['captcha'=> captcha_src('math')]);
    }


}
