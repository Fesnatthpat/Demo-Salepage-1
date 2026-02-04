@extends('layouts.guest-admin')

@section('title', 'Admin Login | Salepage Demo')

@section('content')
    <div class="container mx-auto px-4 bg-gray-900 min-h-screen">
        <div class="flex justify-center items-center min-h-screen py-10">
            <div class="w-full max-w-sm bg-gray-800 p-8 rounded-2xl shadow-2xl border border-gray-700 text-center">
                <div class="mb-8">
                    <h2 class="text-3xl font-extrabold text-white">Admin Login</h2>
                    <p class="text-gray-400 text-sm mt-2">Sign in to manage your dashboard</p>
                </div>

                <form method="POST" action="{{ route('admin.login') }}">
                    @csrf
                    <div class="mb-4 text-left">
                        <label class="text-gray-300 text-xs mb-1 ml-1 block">Username</label>
                        <input type="text" name="username" id="username" placeholder="Enter username"
                            class="w-full px-4 py-3 rounded-xl bg-gray-700 border border-gray-600 text-white placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-all"
                            required autofocus>
                        @error('username')
                            <p class="text-red-400 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="mb-6 text-left">
                        <label class="text-gray-300 text-xs mb-1 ml-1 block">Password</label>
                        <input type="password" name="password" id="password" placeholder="••••••••"
                            class="w-full px-4 py-3 rounded-xl bg-gray-700 border border-gray-600 text-white placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-all"
                            required>
                    </div>

                    <button type="submit"
                        class="w-full bg-indigo-600 hover:bg-indigo-500 text-white font-bold py-3 px-4 rounded-xl transition duration-300 shadow-lg shadow-indigo-500/20">
                        Sign In
                    </button>
                </form>

                <div class="mt-8">
                    <p class="text-gray-500 text-xs">© {{ date('Y') }} Salepage Demo System</p>
                </div>
            </div>
        </div>
    </div>
@endsection
