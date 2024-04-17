<?php

namespace App\Livewire;

use App\Actions\SubjectLoginAction;
use App\Mail\LoginLink;
use App\Models\Subject;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Session;
use Livewire\Attributes\Layout;
use Livewire\Component;
use MagicLink\Actions\LoginAction;
use MagicLink\MagicLink;

class Login extends Component
{
    public string $email;
    public string $error   = '';
    public string $success = '';

    public function mount()
    {
        if (filled(Session::get('subject_email', null))) {
            return redirect()->route('my_bookings');
        }
        if (filled(session('success'))) {
            $this->success = session('success');
        }
    }

    public function submit()
    {
        $exists = DB::table('bookings')->where('email', $this->email)->exists()
            || DB::table('booking_histories')->where('hashed_email', md5($this->email))->exists();
        if (!$exists) {
            $this->error = 'Cette adresse email n\'existe pas dans notre base de données de participants.';
        } else {
            $action = new SubjectLoginAction($this->email);

            if (App::isLocal()) {
                // return $action->run();
            }

            $url = MagicLink::create($action, 60, 1)->url; // 60 minutes, single use
            Mail::to($this->email)->send(new LoginLink($url));

            $this->error = '';
            $this->success = 'Un lien de connexion vient de vous être envoyé à l\'adresse indiquée.';
            $this->email = '';
        }
    }

    #[Layout('layouts.public')]
    public function render()
    {
        return view('livewire.login');
    }
}
