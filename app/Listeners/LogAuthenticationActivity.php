<?php

namespace App\Listeners;

use App\ActivityAction;
use App\Services\ActivityLogService;
use Illuminate\Auth\Events\Login;
use Illuminate\Auth\Events\Logout;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Events\Verified;

class LogAuthenticationActivity
{
    public function __construct(protected ActivityLogService $activityLog)
    {
    }

    public function handleRegistered(Registered $event): void
    {
        $this->activityLog->log(
            ActivityAction::UserRegistered,
            $event->user,
            'New user account was registered.'
        );
    }

    public function handleLogin(Login $event): void
    {
        $this->activityLog->log(
            ActivityAction::UserLogin,
            $event->user,
            'User successfully logged in.'
        );
    }

    public function handleLogout(Logout $event): void
    {
        $this->activityLog->log(
            ActivityAction::UserLogout,
            $event->user,
            'User logged out.'
        );
    }

    public function handleVerified(Verified $event): void
    {
        $this->activityLog->log(
            ActivityAction::EmailVerified,
            $event->user,
            'Email address was verified.'
        );
    }

    public function handlePasswordReset(PasswordReset $event): void
    {
        $this->activityLog->log(
            ActivityAction::PasswordReset,
            $event->user,
            'Password was reset.'
        );
    }
}