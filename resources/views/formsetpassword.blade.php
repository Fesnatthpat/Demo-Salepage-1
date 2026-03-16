@extends('layout')

@section('content')
    {{-- Login Page Content --}}
    <div class="container mx-auto px-2 flex justify-center items-center min-h-screen">
        <div class="w-96 mx-auto mt-10 bg-white p-8 border border-gray-200 rounded-lg shadow-lg">
            <h2 class="text-2xl font-bold mb-6 text-center">ยืนยันรหัสผ่าน</h2>
            <form action="/" method="" class="space-y-6">
                
                <div>
                    <label for="password" class="block text-sm font-medium text-gray-700">รหัสผ่าน</label>
                    <input type="password" name="password" id="password" required
                        class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                </div>
                <div>
                    <button type="submit"
                        class="w-full bg-blue-600 text-white py-2 px-4 rounded-md hover:bg-blue-700 transition duration-300">
                        ยืนยัน
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection
