<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Mail\ForgotPassword;
//use App\Models\;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class MailController extends Controller
{
    /**
     * Send reset password mail
     *
     * @param  \Illuminate\Http\Request  $request
     */
    public function sendEmail(Request $request)
    {

        // Send the mail
        $data = array(
            'to' => "xyz@qwe.com",
          );
          

        Mail::to('xyz@qwe.com')->send(new ForgotPassword());
    }
}
