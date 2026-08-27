<?php

namespace App\Filament\Pages;

use Filament\Auth\Http\Responses\Contracts\PasswordResetResponse;
use App\Models\User;
use Illuminate\Contracts\Support\Htmlable;

class ResetPassword extends \Filament\Auth\Pages\PasswordReset\ResetPassword
{
    public function resetPassword(): ?PasswordResetResponse
    {
        $parent = parent::resetPassword();
        if ($parent !== null) {
            $user = User::where('email', $this->email)->first();
            if ($user !== null && ! $user->hasVerifiedEmail()) {
                /** @var User $user */
                $user->markEmailAsVerified();
            }

            return $parent;
        }

        return null;
    }

    public function getTitle(): string | Htmlable
    {
        $user = User::where('email', $this->email)->first();
        if ($user !== null && $user->created_at->timestamp === $user->updated_at->timestamp) {
            return __('admin.init_password');
        }

        return parent::getTitle();
    }

    public function getHeading(): string | Htmlable
    {
        $user = User::where('email', $this->email)->first();
        if ($user !== null && $user->created_at->timestamp === $user->updated_at->timestamp) {
            return __('admin.init_password');
        }

        return parent::getHeading();
    }
}
