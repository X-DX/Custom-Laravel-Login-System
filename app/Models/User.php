<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\DB;


class User extends Authenticatable
{
    use HasFactory, Notifiable;
    protected $fillable = ['username', 'email', 'password'];

    // protected $hidden = ['password', 'remember_token'];
    protected static function booted()
    {
        static::deleting(function ($user) {
            // Delete user’s active session from DB
            if ($user->session_id) {
                DB::table('sessions')->where('id', $user->session_id)->delete();
            }
        });
    }
}
