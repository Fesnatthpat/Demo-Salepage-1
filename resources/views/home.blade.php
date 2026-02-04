@extends('layouts.main')

@section('content')
    <div class="container mx-auto py-20 text-center">

        <h1 class="font-bold text-gray-800 hero-title" data-customize-id="hero-title-text">
            {{ $settings['hero_title'] ?? 'Welcome to Shop' }}
        </h1>

        <p class="mt-4 text-gray-500" data-customize-id="hero-desc-text">
            {{ $settings['hero_desc'] ?? 'Best products here' }}
        </p>

        <button class="mt-8 px-6 py-3 text-white rounded bg-primary">
            Shop Now
        </button>

    </div>
@endsection
