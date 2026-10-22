<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasFactory, Notifiable;

    protected $fillable = ['name','email','password','role','status','google_id','google_token','google_refresh_token','email_verified_at',];

    protected $hidden = ['password','remember_token','google_token','google_refresh_token',];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function activityLogs()
    {
        return $this->hasMany(ActivityLog::class);
    }
}