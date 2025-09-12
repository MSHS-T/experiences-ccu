<?php

namespace App\Filament\Pages;

use App\Models\User;
use Filament\Http\Responses\Auth\Contracts\PasswordResetResponse;
use Filament\Pages\Auth\PasswordReset\ResetPassword as BasePage;
use Illuminate\Contracts\Support\Htmlable;

class ResetPassword extends BasePage
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
