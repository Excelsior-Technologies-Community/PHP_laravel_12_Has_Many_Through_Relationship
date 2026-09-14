@extends('layouts.app')

@section('header')
    <h2 class="font-semibold text-xl text-gray-800 leading-tight">
        Post: {{ $post->name }}
    </h2>
@endsection

@section('content')
    <div class="py-6">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 text-center">
                        <div>
                            <p class="text-2xl font-bold text-gray-900">{{ $post->name }}</p>
                            <p class="text-sm text-gray-500">Post Name</p>
                        </div>
                        <div>
                            <p class="text-2xl font-bold text-gray-900">{{ $post->user->name ?? 'N/A' }}</p>
                            <p class="text-sm text-gray-500">Author</p>
                        </div>
                        <div>
                            <p class="text-2xl font-bold text-gray-900">{{ $post->user->country->name ?? 'N/A' }}</p>
                            <p class="text-sm text-gray-500">Country</p>
                        </div>
                    </div>

                    <div class="mt-6 pt-6 border-t">
                        <p class="text-sm text-gray-500">
                            Created at: {{ $post->created_at?->format('d M Y H:i') ?? 'N/A' }}
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
