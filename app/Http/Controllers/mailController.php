<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Mail\MyCustomMail;
use Illuminate\Support\Facades\Mail;

class mailController extends Controller
{
    //
    public function sendEmail()
    {
        $data = [
            // Data to be passed to the email view
            'name'=> 'john'
        ];

        Mail::to('gab4@mailinator.com')->send(new MyCustomMail($data));

        dd('mail sent');
    }
}


