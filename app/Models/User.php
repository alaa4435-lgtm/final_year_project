<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasFactory, Notifiable;

    protected $fillable = ['name', 'email', 'password', 'avatar'];

    protected $hidden = ['password', 'remember_token'];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function isLearner(): bool
    {
        return $this->role === 'learner';
    }

    /**
     * The home dashboard URL for this user's role.
     */
    public function dashboardUrl(): string
    {
        return $this->isAdmin() ? route('admin.dashboard') : route('learner.dashboard');
    }

    public function isSuspended(): bool
    {
        return $this->status === 'suspended';
    }

    public function activityLogs()
    {
        return $this->hasMany(ActivityLog::class);
    }

    public function projects()
    {
        return $this->hasMany(Project::class);
    }

    protected function avatarUrl(): Attribute
    {
        return Attribute::get(
            fn () => $this->avatar ? asset('storage/'.$this->avatar) : null
        );
    }

    protected function initials(): Attribute
    {
        return Attribute::get(
            fn () => mb_strtoupper(mb_substr(trim($this->name), 0, 1))
        );
    }
}
