<?php

namespace App\Http\Controllers;

use App\Models\Country;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');

        $users = User::with('country')
            ->withCount('posts')
            ->when($search, function ($q, $search) {
                $q->where('name', 'like', '%'.$search.'%')
                    ->orWhere('email', 'like', '%'.$search.'%');
            })
            ->orderBy('name')
            ->paginate(10)
            ->withQueryString();

        return view('users.index', compact('users', 'search'));
    }

    public function create()
    {
        $countries = Country::orderBy('name')->get();

        return view('users.create', compact('countries'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'country_id' => ['required', 'exists:countries,id'],
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'country_id' => $validated['country_id'],
        ]);

        return redirect()
            ->route('users.index')
            ->with('success', 'User "'.$user->name.'" created successfully.');
    }

    public function show(User $user)
    {
        $user->load(['country', 'posts']);
        $user->posts_count = $user->posts->count();

        return view('users.show', compact('user'));
    }

    public function edit(User $user)
    {
        $countries = Country::orderBy('name')->get();

        return view('users.edit', compact('user', 'countries'));
    }

    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users', 'email')->ignore($user->id)],
            'password' => ['nullable', 'string', 'min:8', 'confirmed'],
            'country_id' => ['required', 'exists:countries,id'],
        ]);

        $user->update([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'country_id' => $validated['country_id'],
        ]);

        if (! empty($validated['password'])) {
            $user->update(['password' => Hash::make($validated['password'])]);
        }

        return redirect()
            ->route('users.index')
            ->with('success', 'User "'.$user->name.'" updated successfully.');
    }

    public function destroy(User $user)
    {
        $userName = $user->name;

        $user->delete();

        return redirect()
            ->route('users.index')
            ->with('success', 'User "'.$userName.'" deleted successfully.');
    }
}
