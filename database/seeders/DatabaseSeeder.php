<?php

namespace Database\Seeders;

use App\Models\Country;
use App\Models\User;
use App\Models\Post;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        /*
        |--------------------------------------------------------------------------
        | Countries
        |--------------------------------------------------------------------------
        */

        $india = Country::create([
            'name' => 'India',
        ]);

        $usa = Country::create([
            'name' => 'USA',
        ]);

        $canada = Country::create([
            'name' => 'Canada',
        ]);


        /*
        |--------------------------------------------------------------------------
        | India Users
        |--------------------------------------------------------------------------
        */

        $harry = User::create([
            'name' => 'Harry',
            'email' => 'harry@example.com',
            'password' => Hash::make('123456'),
            'country_id' => $india->id,
        ]);

        $rahul = User::create([
            'name' => 'Rahul Sharma',
            'email' => 'rahul@example.com',
            'password' => Hash::make('123456'),
            'country_id' => $india->id,
        ]);

        $priya = User::create([
            'name' => 'Priya Patel',
            'email' => 'priya@example.com',
            'password' => Hash::make('123456'),
            'country_id' => $india->id,
        ]);


        /*
        |--------------------------------------------------------------------------
        | USA Users
        |--------------------------------------------------------------------------
        */

        $john = User::create([
            'name' => 'John Smith',
            'email' => 'john@example.com',
            'password' => Hash::make('123456'),
            'country_id' => $usa->id,
        ]);

        $michael = User::create([
            'name' => 'Michael Brown',
            'email' => 'michael@example.com',
            'password' => Hash::make('123456'),
            'country_id' => $usa->id,
        ]);


        /*
        |--------------------------------------------------------------------------
        | Canada Users
        |--------------------------------------------------------------------------
        */

        $alex = User::create([
            'name' => 'Alex Wilson',
            'email' => 'alex@example.com',
            'password' => Hash::make('123456'),
            'country_id' => $canada->id,
        ]);

        $sarah = User::create([
            'name' => 'Sarah Taylor',
            'email' => 'sarah@example.com',
            'password' => Hash::make('123456'),
            'country_id' => $canada->id,
        ]);


        /*
        |--------------------------------------------------------------------------
        | India Posts
        |--------------------------------------------------------------------------
        |
        | 12 posts are intentionally created for India
        | so that pagination can be tested.
        |
        */

        $indiaPosts = [
            'Laravel 12 Introduction',
            'Laravel Has Many Through Relationship',
            'Understanding Eloquent ORM',
            'Laravel Relationships Tutorial',
            'Building REST API with Laravel',
            'Laravel Authentication Guide',
            'Laravel Migration Tutorial',
            'Laravel Seeder Explained',
            'Laravel Controller Best Practices',
            'Laravel Model Relationships',
            'Laravel Pagination Tutorial',
            'Laravel Search and Filtering',
        ];

        foreach ($indiaPosts as $index => $postName) {
            Post::create([
                'name' => $postName,
                'user_id' => match ($index % 3) {
                    0 => $harry->id,
                    1 => $rahul->id,
                    default => $priya->id,
                },
            ]);
        }


        /*
        |--------------------------------------------------------------------------
        | USA Posts
        |--------------------------------------------------------------------------
        */

        $usaPosts = [
            'USA Technology News',
            'Eloquent ORM in Laravel',
            'Laravel API Development',
            'PHP 8.2 Features',
            'Laravel Database Relationships',
            'Building Modern Web Applications',
        ];

        foreach ($usaPosts as $index => $postName) {
            Post::create([
                'name' => $postName,
                'user_id' => $index % 2 === 0
                    ? $john->id
                    : $michael->id,
            ]);
        }


        /*
        |--------------------------------------------------------------------------
        | Canada Posts
        |--------------------------------------------------------------------------
        */

        $canadaPosts = [
            'Canada Web Development',
            'Laravel 12 Best Practices',
            'PHP Development Guide',
            'Eloquent HasMany Relationship',
            'Laravel Performance Optimization',
        ];

        foreach ($canadaPosts as $index => $postName) {
            Post::create([
                'name' => $postName,
                'user_id' => $index % 2 === 0
                    ? $alex->id
                    : $sarah->id,
            ]);
        }


        /*
        |--------------------------------------------------------------------------
        | Success Message
        |--------------------------------------------------------------------------
        */

        $this->command->info('Has Many Through test data created successfully!');
        $this->command->info('Countries: 3');
        $this->command->info('Users: 7');
        $this->command->info('Posts: 23');
    }
}