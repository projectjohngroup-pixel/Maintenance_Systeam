<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

#[Fillable([
    'name',
    'email',
    'password',
    'role',
    'bagian',
    'foto',
    'status',
    'last_login_at',
    'last_activity_at',
])]

#[Hidden([
    'password',
    'remember_token',
])]

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;


    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'last_login_at' => 'datetime',
            'last_activity_at' => 'datetime',
        ];
    }


    /*
    |--------------------------------------------------------------------------
    | API TOKEN (REACT NATIVE)
    |--------------------------------------------------------------------------
    |
    | Membuat token API baru untuk user ini.
    | Token disimpan dalam bentuk hash SHA-256,
    | nilai plaintext hanya dikembalikan sekali.
    |
    */

    public function issueApiToken(string $name = 'react-native'): string
    {
        $plain = bin2hex(random_bytes(32));

        \App\Models\ApiToken::query()->create([
            'user_id' => $this->id,
            'name' => $name,
            'token' => hash('sha256', $plain),
            'last_used_at' => now(),
        ]);

        return $plain;
    }
}