@extends('layout')

@section('title', 'แก้ไขข้อมูลส่วนตัว')

@section('content')
    <div class="min-h-[80vh] bg-gray-50 flex flex-col justify-center py-12 sm:px-6 lg:px-8">
        {{-- Header Section --}}
        <div class="sm:mx-auto sm:w-full sm:max-w-md text-center mb-6">
            <h2 class="text-3xl font-extrabold text-gray-900">
                แก้ไขข้อมูลส่วนตัว
            </h2>
            <p class="mt-2 text-sm text-gray-600">
                อัปเดตข้อมูลส่วนตัวของคุณ
            </p>
        </div>

        {{-- Card Section --}}
        <div class="sm:mx-auto sm:w-full sm:max-w-md">
            <div class="bg-white py-8 px-4 shadow-xl rounded-xl sm:px-10 border border-gray-100">

                @if (session('success'))
                    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
                        <span class="block sm:inline">{{ session('success') }}</span>
                    </div>
                @endif
                
                <form class="space-y-6" action="{{ route('profile.update') }}" method="POST">
                    @csrf
                    @method('PUT')

                    {{-- 0. Name --}}
                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-700 mb-1">
                            ชื่อ-นามสกุล (Full Name)
                        </label>
                        <div class="relative">
                            <input type="text" name="name" id="name" required
                                value="{{ old('name', $user->name) }}"
                                class="block w-full px-4 py-3 rounded-lg border border-gray-300 shadow-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm transition duration-200 ease-in-out"
                                placeholder="กรอกชื่อ-นามสกุล">
                        </div>
                    </div>

                    {{-- 1. Date of Birth --}}
                    <div>
                        <label for="date_of_birth" class="block text-sm font-medium text-gray-700 mb-1">
                            วันเดือนปีเกิด (Date of Birth)
                        </label>
                        <div class="relative">
                            <input type="date" name="date_of_birth" id="date_of_birth"
                                value="{{ old('date_of_birth', optional($user->date_of_birth)->format('Y-m-d')) }}"
                                class="block w-full px-4 py-3 rounded-lg border border-gray-300 shadow-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm transition duration-200 ease-in-out"
                                placeholder="Select date">
                        </div>
                    </div>

                    {{-- 2. Gender --}}
                    <div>
                        <label for="gender" class="block text-sm font-medium text-gray-700 mb-1">
                            เพศ (Gender)
                        </label>
                        <select id="gender" name="gender"
                            class="block w-full px-4 py-3 rounded-lg border border-gray-300 shadow-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm bg-white transition duration-200 ease-in-out">
                            <option value="" disabled>กรุณาเลือกเพศ</option>
                            <option value="male" @if(old('gender', $user->gender) == 'male') selected @endif>ชาย (Male)</option>
                            <option value="female" @if(old('gender', $user->gender) == 'female') selected @endif>หญิง (Female)</option>
                            <option value="other" @if(old('gender', $user->gender) == 'other') selected @endif>อื่นๆ (Other)</option>
                        </select>
                    </div>

                    {{-- 3. Age (Auto Calculated) --}}
                    <div>
                        <label for="age" class="block text-sm font-medium text-gray-700 mb-1">
                            อายุ (Age)
                        </label>
                        <div class="relative rounded-md shadow-sm">
                            <input type="number" name="age" id="age" readonly
                                value="{{ old('age', $user->age) }}"
                                class="block w-full px-4 py-3 rounded-lg border border-gray-200 bg-gray-100 text-gray-500 sm:text-sm cursor-not-allowed focus:outline-none"
                                placeholder="ระบบคำนวณอัตโนมัติ">
                        </div>
                        <p class="mt-1 text-xs text-gray-500">*อายุจะถูกคำนวณอัตโนมัติจากวันเกิด</p>
                    </div>

                    {{-- 4. Phone Number --}}
                    <div>
                        <label for="phone" class="block text-sm font-medium text-gray-700 mb-1">
                            เบอร์โทรศัพท์ (Phone Number)
                        </label>
                        <div class="relative">
                            <input type="tel" name="phone" id="phone" required
                                value="{{ old('phone', $user->phone) }}"
                                class="block w-full px-4 py-3 rounded-lg border border-gray-300 shadow-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm transition duration-200 ease-in-out"
                                placeholder="กรอกเบอร์โทรศัพท์">
                        </div>
                    </div>

                    {{-- Submit Button --}}
                    <div class="pt-2">
                        <button type="submit"
                            class="w-full flex justify-center py-3 px-4 border border-transparent rounded-lg shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition duration-300 transform hover:-translate-y-0.5">
                            บันทึกข้อมูล (Save Profile)
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Script คำนวณอายุ --}}
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const dobInput = document.getElementById('date_of_birth');
            const ageInput = document.getElementById('age');

            const calculateAge = () => {
                if (!dobInput.value) {
                    ageInput.value = '';
                    return;
                };

                const dob = new Date(dobInput.value);
                const today = new Date();

                if (dob > today) {
                    // alert("วันเกิดไม่สามารถเป็นอนาคตได้");
                    dobInput.value = '';
                    ageInput.value = '';
                    return;
                }

                let age = today.getFullYear() - dob.getFullYear();
                const monthDiff = today.getMonth() - dob.getMonth();

                if (monthDiff < 0 || (monthDiff === 0 && today.getDate() < dob.getDate())) {
                    age--;
                }

                ageInput.value = age > 0 ? age : 0;
            }

            dobInput.addEventListener('change', calculateAge);
            
            // Initial calculation on page load
            calculateAge();
        });
    </script>
@endsection
