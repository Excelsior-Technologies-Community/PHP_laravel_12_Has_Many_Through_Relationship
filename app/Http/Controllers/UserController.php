<?php

namespace App\Http\Controllers;

use App\Models\Country; 

class UserController extends Controller
{
    public function index()
    {
        // Find country record with ID = 1 from countries table
        $country = Country::find(1);

        // Get all posts related to this country
        // (Country → Users → Posts using hasManyThrough)
        dd($country->posts);
    }
}
