<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;
use Laravel\Sanctum\HasApiTokens;

use LdapRecord\Laravel\Auth\LdapAuthenticatable;
use LdapRecord\Laravel\Auth\AuthenticatesWithLdap;

class User extends Authenticatable implements LdapAuthenticatable
{
    use Notifiable, AuthenticatesWithLdap, HasRoles;


    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'guid',     // Добавляем поле для LDAP GUID
        'domain',   // Добавляем поле для домена (если нужно)
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }



    public function scopeSearchByName($query, $name)
    {
        $driver = config('database.default');

        if ($driver === 'pgsql') {
            return $query->where('name', 'ilike', '%' . $name . '%');
        } elseif ($driver === 'sqlite') {
            return $query->whereRaw('name LIKE ? COLLATE NOCASE', ['%' . $name . '%']);
        } else {
            return $query->whereRaw('LOWER(name) LIKE ?', ['%' . strtolower($name) . '%']);
        }
    }
}
