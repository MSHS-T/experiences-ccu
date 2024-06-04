<?php

namespace App\Http\Controllers;

use App\Http\Requests\ContactRequest;
use App\Mail\Contact;
use App\Models\User;
use Illuminate\Support\Facades\Mail;

class ContactController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(ContactRequest $request)
    {
        $name = $request->input('name');
        $email = $request->input('email');
        $message = $request->input('message');

        $mailNotification = new Contact($name, $email, $message);

        User::role('administrator')->get()->each(fn ($user) => Mail::to($user->email)->send($mailNotification));

        return redirect()->route('contact')->with('success', __('public.messages.contact_sent'));
    }
}
