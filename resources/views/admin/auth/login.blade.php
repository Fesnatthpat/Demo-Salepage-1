@extends('layouts.admin')

@section('title', 'Admin Login | Salepage Demo')

@section('content')
<div class="container mx-auto px-4">
    <div class="flex justify-center items-center min-h-screen py-10">
        <div class="w-full max-w-sm bg-white p-8 rounded-xl shadow-lg border border-gray-100 text-center">
            <div class="mb-6">
                <h2 class="text-2xl font-bold text-gray-800">Admin Login</h2>
                <p class="text-gray-500 text-sm mt-2">Please log in to continue.</p>
            </div>

            <form method="POST" action="{{ route('admin.login') }}">
                @csrf
                <div class="mb-4">
                    <input type="text" name="username" id="username" placeholder="Username"
                        class="w-full px-4 py-3 rounded-lg bg-gray-100 border border-gray-200 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                        required autofocus>
                    @error('username')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="mb-6">
                    <input type="password" name="password" id="password" placeholder="Password"
                        class="w-full px-4 py-3 rounded-lg bg-gray-100 border border-gray-200 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                        required>
                </div>

                <button type="submit"
                    class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 px-4 rounded-lg transition duration-200 shadow-md hover:shadow-lg">
                    Login
                </button>
            </form>
        </div>
    </div>
</div>
@endsection
