<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

class Country extends Model
{
    protected $fillable = [
        'name',
    ];

    /**
     * Country has many users.
     */
    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    /**
     * Country has many posts through users.
     *
     * Country → Users → Posts
     */
    public function posts(): HasManyThrough
    {
        return $this->hasManyThrough(
            Post::class,
            User::class,
            'country_id', // Foreign key on users table
            'user_id',    // Foreign key on posts table
            'id',         // Local key on countries table
            'id'          // Local key on users table
        );
    }
}