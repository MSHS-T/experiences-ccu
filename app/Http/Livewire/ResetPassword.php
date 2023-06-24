<?php

namespace App\Http\Livewire;

use Filament\Forms;
use Filament\Notifications\Actions\Action as NotificationAction;
use Filament\Notifications\Notification;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use JeffGreco13\FilamentBreezy\FilamentBreezy;
use Livewire\Component;
use JeffGreco13\FilamentBreezy\Http\Livewire\Auth\ResetPassword as AuthResetPassword;

class ResetPassword extends AuthResetPassword
{
    public function submit()
    {
        $data = $this->form->getState();

        if ($this->isResetting) {
            $response = Password::broker(config('filament-breezy.reset_broker', config('auth.defaults.passwords')))->reset([
                'token' => $this->token,
                'email' => $this->email,
                'password' => $data['password'],
            ], function ($user, $password) {
                $user->password = Hash::make($password);
                $user->email_verified_at = now();
                $user->setRememberToken(Str::random(60));
                $user->save();
                event(new PasswordReset($user));
            });

            if ($response == Password::PASSWORD_RESET) {
                return redirect(route('filament.auth.login', ['email' => $this->email, 'reset' => true]));
            } else {
                Notification::make()->title(__("filament-breezy::default.reset_password.notification_error"))->persistent()->actions([NotificationAction::make('resetAgain')->label(__("filament-breezy::default.reset_password.notification_error_link_text"))->url(route(config('filament-breezy.route_group_prefix') . 'password.request'))])->danger()->send();
            }
        } else {
            $response = Password::broker(config('filament-breezy.reset_broker', config('auth.defaults.passwords')))->sendResetLink(['email' => $this->email]);
            if ($response == Password::RESET_LINK_SENT) {
                Notification::make()->title(__("filament-breezy::default.reset_password.notification_success"))->success()->send();

                $this->hasBeenSent = true;
            } else {
                Notification::make()->title(match ($response) {
                    "passwords.throttled" => __("passwords.throttled"),
                    "passwords.user" => __("passwords.user")
                })->danger()->send();
            }
        }
    }
}
