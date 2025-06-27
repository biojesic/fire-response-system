<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Mail;
use App\Mail\TestEmail;
// use Illuminate\Support\Facades\Mail;

class MailController extends Controller
{
   public function index(Request $request) {
    try {
        Mail::to("test@example.com")->send(new TestEmail());
        dd("done");
    } catch (\Exception $e) {
        dd('Mail Error:', $e->getMessage());
    }
}

}
