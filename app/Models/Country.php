<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

class Country extends Model
{
    public function posts(): HasManyThrough
    {
        return $this->hasManyThrough(
            Post::class,
            User::class,
            'country_id', // users table FK
            'user_id',    // posts table FK
            'id',          // countries PK
            'id'           // users PK
        );
    }
}
