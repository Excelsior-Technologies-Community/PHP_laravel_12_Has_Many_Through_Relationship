<?php

namespace App\Http\Controllers;

use App\Models\Country;
use App\Models\Post;
use App\Models\User;
use Illuminate\Http\Request;

class PostController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');
        $countryId = $request->input('country_id');
        $userId = $request->input('user_id');
        $sortBy = $request->input('sort_by', 'created_at');
        $sortDir = $request->input('sort_dir', 'desc');

        $postsQuery = Post::with('user.country');

        if (! empty($search)) {
            $postsQuery->where('name', 'like', '%'.$search.'%');
        }

        if (! empty($countryId)) {
            $postsQuery->whereHas('user', function ($q) use ($countryId) {
                $q->where('country_id', $countryId);
            });
        }

        if (! empty($userId)) {
            $postsQuery->where('user_id', $userId);
        }

        if ($sortBy === 'name') {
            $postsQuery->orderBy('name', $sortDir === 'asc' ? 'asc' : 'desc');
        } else {
            $postsQuery->orderBy('created_at', $sortDir === 'asc' ? 'asc' : 'desc');
        }

        $posts = $postsQuery->paginate(10)->withQueryString();

        $countries = Country::orderBy('name')->get();
        $users = User::orderBy('name')->get();

        return view('posts.index', compact(
            'posts',
            'countries',
            'users',
            'search',
            'countryId',
            'userId',
            'sortBy',
            'sortDir'
        ));
    }

    public function create()
    {
        $countries = Country::orderBy('name')->get();
        $users = User::orderBy('name')->get();

        return view('posts.create', compact('countries', 'users'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'user_id' => ['required', 'exists:users,id'],
        ]);

        $post = Post::create($validated);

        return redirect()
            ->route('posts.index')
            ->with('success', 'Post "'.$post->name.'" created successfully.');
    }

    public function show(Post $post)
    {
        $post->load(['user.country']);

        return view('posts.show', compact('post'));
    }

    public function edit(Post $post)
    {
        $countries = Country::orderBy('name')->get();
        $users = User::orderBy('name')->get();

        return view('posts.edit', compact('post', 'countries', 'users'));
    }

    public function update(Request $request, Post $post)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'user_id' => ['required', 'exists:users,id'],
        ]);

        $post->update($validated);

        return redirect()
            ->route('posts.index')
            ->with('success', 'Post "'.$post->name.'" updated successfully.');
    }

    public function destroy(Post $post)
    {
        $postName = $post->name;

        $post->delete();

        return redirect()
            ->route('posts.index')
            ->with('success', 'Post "'.$postName.'" deleted successfully.');
    }
}
