<?php

namespace App;

enum ActivityAction: string
{
    case UserRegistered = 'user_registered';
    case UserLogin = 'user_login';
    case UserLogout = 'user_logout';
    case EmailVerified = 'email_verified';
    case PasswordReset = 'password_reset';
    case ProjectCreated = 'project_created';
    case ProjectDeleted = 'project_deleted';

    public function label(): string
    {
        return match ($this) {
            self::UserRegistered => 'User Registered',
            self::UserLogin => 'User Login',
            self::UserLogout => 'User Logout',
            self::EmailVerified => 'Email Verified',
            self::PasswordReset => 'Password Reset',
            self::ProjectCreated => 'Project Created',
            self::ProjectDeleted => 'Project Deleted',

        };
    }
}
