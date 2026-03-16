@extends('layout')

@section('content')
    <div class="container mx-auto px-4 py-12 min-h-screen">

        {{-- Header --}}
        <div class="text-center mb-12">
            <h1 class="text-3xl md:text-4xl font-bold text-gray-900">ติดต่อเรา</h1>
            <p class="text-gray-500 mt-3 text-lg">มีคำถามหรือข้อสงสัยเกี่ยวกับสินค้า? เราพร้อมช่วยเหลือคุณ</p>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 lg:gap-16 items-start">

            {{-- ================= LEFT COLUMN: Contact Info ================= --}}
            <div class="flex flex-col gap-8">

                {{-- Info Cards --}}
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-1 gap-6">

                    {{-- Address --}}
                    <div
                        class="flex items-start gap-4 p-4 rounded-lg hover:bg-white hover:shadow-sm transition border border-transparent hover:border-gray-100">
                        <div
                            class="w-12 h-12 bg-emerald-100 rounded-full flex items-center justify-center flex-shrink-0 text-emerald-600">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z">
                                </path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                            </svg>
                        </div>
                        <div>
                            <h3 class="font-bold text-gray-900 text-lg">ที่อยู่ร้าน</h3>
                            <p class="text-gray-600 mt-1 leading-relaxed">
                                H&M-R Store Thailand<br>
                                99/9 ชั้น 1 ห้างสรรพสินค้าเซ็นทรัลเวิลด์<br>
                                ปทุมวัน, กรุงเทพฯ 10330
                            </p>
                        </div>
                    </div>

                    {{-- Phone & Email --}}
                    <div
                        class="flex items-start gap-4 p-4 rounded-lg hover:bg-white hover:shadow-sm transition border border-transparent hover:border-gray-100">
                        <div
                            class="w-12 h-12 bg-blue-100 rounded-full flex items-center justify-center flex-shrink-0 text-blue-600">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z">
                                </path>
                            </svg>
                        </div>
                        <div>
                            <h3 class="font-bold text-gray-900 text-lg">ช่องทางติดต่อ</h3>
                            <p class="text-gray-600 mt-1">โทร: <a href="tel:021234567"
                                    class="hover:text-emerald-600">02-123-4567</a></p>
                            <p class="text-gray-600">อีเมล: <a href="mailto:support@hm-r.com"
                                    class="hover:text-emerald-600">support@hm-r.com</a></p>
                            <p class="text-gray-500 text-sm mt-1">เวลาทำการ: จันทร์ - อาทิตย์ (10:00 - 22:00)</p>
                        </div>
                    </div>

                    {{-- Social Media --}}
                    <div
                        class="flex items-start gap-4 p-4 rounded-lg hover:bg-white hover:shadow-sm transition border border-transparent hover:border-gray-100">
                        <div
                            class="w-12 h-12 bg-purple-100 rounded-full flex items-center justify-center flex-shrink-0 text-purple-600">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M17 8h2a2 2 0 012 2v6a2 2 0 01-2 2h-2v4l-4-4H9a1.994 1.994 0 01-1.414-.586m0 0L11 14h4a2 2 0 002-2V6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2v4l.586-.586z">
                                </path>
                            </svg>
                        </div>
                        <div>
                            <h3 class="font-bold text-gray-900 text-lg">ติดตามเรา</h3>
                            <div class="flex gap-4 mt-3">
                                <a href="#"
                                    class="btn btn-circle btn-sm bg-[#1877F2] text-white border-none hover:bg-blue-700">
                                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                                        <path
                                            d="M9 8h-3v4h3v12h5v-12h3.642l.358-4h-4v-1.667c0-.955.192-1.333 1.115-1.333h2.885v-5h-3.808c-3.596 0-5.192 1.583-5.192 4.615v3.385z" />
                                    </svg>
                                </a>
                                <a href="#"
                                    class="btn btn-circle btn-sm bg-[#06C755] text-white border-none hover:bg-green-600">
                                    <svg class="w-4 h-4" viewBox="0 0 24 24" fill="currentColor">
                                        <path
                                            d="M12 2C6.48 2 2 5.92 2 10.75c0 4.3 3.52 7.9 8.35 8.63.38.08.9.23.68 1.05-.18.66-.4 1.76-1.5 2.45 2.14.22 5.86-1.6 7.6-3.76 3.1-1.83 4.87-4.66 4.87-8.37C22 5.92 17.52 2 12 2z" />
                                    </svg>
                                </a>
                                <a href="#"
                                    class="btn btn-circle btn-sm bg-gradient-to-tr from-[#f09433] via-[#dc2743] to-[#bc1888] text-white border-none">
                                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                                        <path
                                            d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163c0-3.403-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z" />
                                    </svg>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Map --}}
                <div class="w-full h-64 bg-gray-200 rounded-xl overflow-hidden shadow-sm border border-gray-200 mt-4">
                    {{-- Google Maps Embed Placeholder --}}
                    <iframe
                        src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3875.617143926618!2d100.5376573141427!3d13.74161700123516!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x30e29ed806655555%3A0x8673322f77868060!2sCentralWorld!5e0!3m2!1sen!2sth!4v1638258287518!5m2!1sen!2sth"
                        width="100%" height="100%" style="border:0;" allowfullscreen="" loading="lazy"></iframe>
                </div>

            </div>

            {{-- ================= RIGHT COLUMN: Contact Form ================= --}}
            <div class="bg-white p-6 md:p-8 rounded-2xl shadow-lg border border-gray-100" x-data="{
                submitted: false,
                loading: false,
                submitForm() {
                    this.loading = true;
                    // จำลองการส่งข้อมูล
                    setTimeout(() => {
                        this.loading = false;
                        this.submitted = true;
                    }, 1500);
                }
            }">

                <h2 class="text-2xl font-bold text-gray-900 mb-6">ส่งข้อความถึงเรา</h2>

                {{-- Form Content --}}
                <form @submit.prevent="submitForm()" x-show="!submitted">
                    <div class="space-y-5">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                            <div class="form-control w-full">
                                <label class="label"><span class="label-text font-medium">ชื่อ-นามสกุล</span></label>
                                <input type="text" placeholder="สมชาย ใจดี"
                                    class="input input-bordered w-full focus:ring-emerald-500 focus:border-emerald-500"
                                    required />
                            </div>
                            <div class="form-control w-full">
                                <label class="label"><span class="label-text font-medium">อีเมล</span></label>
                                <input type="email" placeholder="example@gmail.com"
                                    class="input input-bordered w-full focus:ring-emerald-500 focus:border-emerald-500"
                                    required />
                            </div>
                        </div>

                        <div class="form-control w-full">
                            <label class="label"><span class="label-text font-medium">เบอร์โทรศัพท์
                                    (ไม่บังคับ)</span></label>
                            <input type="tel" placeholder="08x-xxx-xxxx"
                                class="input input-bordered w-full focus:ring-emerald-500 focus:border-emerald-500" />
                        </div>

                        <div class="form-control w-full">
                            <label class="label"><span class="label-text font-medium">หัวข้อเรื่อง</span></label>
                            <select class="select select-bordered w-full focus:ring-emerald-500 focus:border-emerald-500">
                                <option disabled selected>เลือกหัวข้อเรื่อง</option>
                                <option>สอบถามข้อมูลสินค้า</option>
                                <option>ติดตามสถานะคำสั่งซื้อ</option>
                                <option>แจ้งปัญหาการชำระเงิน</option>
                                <option>ขอคืนสินค้า/คืนเงิน</option>
                                <option>อื่นๆ</option>
                            </select>
                        </div>

                        <div class="form-control w-full">
                            <label class="label"><span class="label-text font-medium">ข้อความ</span></label>
                            <textarea class="textarea textarea-bordered h-32 focus:ring-emerald-500 focus:border-emerald-500"
                                placeholder="พิมพ์รายละเอียดที่คุณต้องการสอบถาม..."></textarea>
                        </div>

                        <div class="pt-2">
                            <button type="submit"
                                class="btn bg-emerald-600 hover:bg-emerald-700 text-white w-full text-lg shadow-md"
                                :class="loading ? 'loading' : ''">
                                <span x-show="!loading">ส่งข้อความ</span>
                                <span x-show="loading">กำลังส่ง...</span>
                            </button>
                        </div>
                    </div>
                </form>

                {{-- Success Message (จะโชว์เมื่อส่งฟอร์มสำเร็จ) --}}
                <div x-show="submitted" x-transition:enter="transition ease-out duration-300"
                    x-transition:enter-start="opacity-0 scale-90" x-transition:enter-end="opacity-100 scale-100"
                    class="flex flex-col items-center justify-center py-10 text-center space-y-4">

                    <div
                        class="w-20 h-20 bg-emerald-100 rounded-full flex items-center justify-center text-emerald-600 mb-2">
                        <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7">
                            </path>
                        </svg>
                    </div>
                    <h3 class="text-2xl font-bold text-gray-900">ส่งข้อความเรียบร้อยแล้ว!</h3>
                    <p class="text-gray-500 max-w-sm">ขอบคุณที่ติดต่อเรา
                        เจ้าหน้าที่จะรีบดำเนินการตรวจสอบและตอบกลับทางอีเมลภายใน 24 ชั่วโมง</p>
                    <button @click="submitted = false" class="btn btn-outline btn-sm mt-4">ส่งข้อความอื่น</button>
                </div>

            </div>

        </div>
    </div>
@endsection
