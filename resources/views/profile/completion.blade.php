@extends('layout')

@section('content')
    <div class="min-h-[80vh] bg-gray-50 flex flex-col justify-center py-12 sm:px-6 lg:px-8">
        {{-- Header Section --}}
        <div class="sm:mx-auto sm:w-full sm:max-w-md text-center mb-6">
            <h2 class="text-3xl font-extrabold text-gray-900">
                Complete Your Profile
            </h2>
            <p class="mt-2 text-sm text-gray-600">
                กรุณากรอกข้อมูลเพิ่มเติมเพื่อดำเนินการต่อ
            </p>
        </div>

        {{-- Card Section --}}
        <div class="sm:mx-auto sm:w-full sm:max-w-md">
            <div class="bg-white py-8 px-4 shadow-xl rounded-xl sm:px-10 border border-gray-100">
                <form class="space-y-6" action="{{ route('profile.store') }}" method="POST">
                    @csrf

                    {{-- 1. Date of Birth --}}
                    <div>
                        <label for="date_of_birth" class="block text-sm font-medium text-gray-700 mb-1">
                            วันเดือนปีเกิด (Date of Birth)
                        </label>
                        <div class="relative">
                            <input type="date" name="date_of_birth" id="date_of_birth" required
                                class="block w-full px-4 py-3 rounded-lg border border-gray-300 shadow-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm transition duration-200 ease-in-out"
                                placeholder="Select date">
                        </div>
                    </div>

                    {{-- 2. Gender --}}
                    <div>
                        <label for="gender" class="block text-sm font-medium text-gray-700 mb-1">
                            เพศ (Gender)
                        </label>
                        <select id="gender" name="gender" required
                            class="block w-full px-4 py-3 rounded-lg border border-gray-300 shadow-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm bg-white transition duration-200 ease-in-out">
                            <option value="" disabled selected>กรุณาเลือกเพศ</option>
                            <option value="male">ชาย (Male)</option>
                            <option value="female">หญิง (Female)</option>
                            <option value="other">อื่นๆ (Other)</option>
                        </select>
                    </div>

                    {{-- 3. Age (Auto Calculated) --}}
                    <div>
                        <label for="age" class="block text-sm font-medium text-gray-700 mb-1">
                            อายุ (Age)
                        </label>
                        <div class="relative rounded-md shadow-sm">
                            <input type="number" name="age" id="age" readonly
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

            dobInput.addEventListener('change', function() {
                if (!this.value) return;

                const dob = new Date(this.value);
                const today = new Date();

                if (dob > today) {
                    alert("วันเกิดไม่สามารถเป็นอนาคตได้");
                    this.value = '';
                    ageInput.value = '';
                    return;
                }

                let age = today.getFullYear() - dob.getFullYear();
                const monthDiff = today.getMonth() - dob.getMonth();

                if (monthDiff < 0 || (monthDiff === 0 && today.getDate() < dob.getDate())) {
                    age--;
                }

                ageInput.value = age;
            });
        });
    </script>
@endsection
