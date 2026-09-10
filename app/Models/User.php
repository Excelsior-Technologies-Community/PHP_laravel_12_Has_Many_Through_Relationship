<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\HasMany;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'country_id',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * User belongs to country.
     */
    public function country()
    {
        return $this->belongsTo(Country::class);
    }

    /**
     * User has many posts.
     */
    public function posts(): HasMany
    {
        return $this->hasMany(Post::class);
    }
}