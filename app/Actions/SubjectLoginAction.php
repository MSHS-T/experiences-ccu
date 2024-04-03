<?php

namespace App\Actions;

use Illuminate\Support\Facades\Session;
use MagicLink\Actions\ActionAbstract;

class SubjectLoginAction extends ActionAbstract
{
    public function __construct(public string $email)
    {
    }

    public function run()
    {
        // Do something
        Session::put('subject_email', $this->email);

        return response()->redirectToRoute('my_bookings');
    }
}
