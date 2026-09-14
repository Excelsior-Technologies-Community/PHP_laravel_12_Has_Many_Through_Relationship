<?php

namespace App\Http\Controllers;

use App\Models\Country;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class CountryController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');

        $countries = Country::withCount(['users', 'posts'])
            ->when($search, function ($q, $search) {
                $q->where('name', 'like', '%'.$search.'%');
            })
            ->orderBy('name')
            ->paginate(10)
            ->withQueryString();

        return view('countries.index', compact('countries', 'search'));
    }

    public function create()
    {
        return view('countries.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:countries,name'],
        ]);

        $country = Country::create($validated);

        return redirect()
            ->route('countries.index')
            ->with('success', 'Country "'.$country->name.'" created successfully.');
    }

    public function show(Country $country)
    {
        return redirect()->route('country.posts.details', $country->id);
    }

    public function edit(Country $country)
    {
        return view('countries.edit', compact('country'));
    }

    public function update(Request $request, Country $country)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255', Rule::unique('countries', 'name')->ignore($country->id)],
        ]);

        $country->update($validated);

        return redirect()
            ->route('countries.index')
            ->with('success', 'Country "'.$country->name.'" updated successfully.');
    }

    public function destroy(Country $country)
    {
        $countryName = $country->name;

        $country->delete();

        return redirect()
            ->route('countries.index')
            ->with('success', 'Country "'.$countryName.'" deleted successfully.');
    }
}
