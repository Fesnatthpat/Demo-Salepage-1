@extends('layout')

@section('content')
    <div x-data="reviewSystem()" class="min-h-screen bg-gray-50 font-['Noto_Sans_Thai'] p-4 lg:p-8">

        {{-- Header Section --}}
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-8 gap-4">
            <div>
                <h1 class="text-2xl font-bold text-gray-800 flex items-center gap-2">
                    <svg class="w-8 h-8 text-yellow-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z">
                        </path>
                    </svg>
                    จัดการรีวิว (Reviews & Ratings)
                </h1>
                <p class="text-gray-500 text-sm mt-1 ml-10">ตรวจสอบ อนุมัติ และตอบกลับความคิดเห็นของลูกค้า</p>
            </div>

            {{-- AI Moderation Toggle --}}
            <div class="flex items-center gap-3 bg-white px-4 py-2 rounded-lg border border-gray-200 shadow-sm">
                <div class="flex flex-col items-end">
                    <span class="text-sm font-bold text-gray-700">AI กรองคำหยาบ</span>
                    <span class="text-[10px] text-emerald-600">ทำงานอยู่ (Active)</span>
                </div>
                <input type="checkbox" class="toggle toggle-success" checked />
            </div>
        </div>

        {{-- Dashboard Stats --}}
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-8">
            <div class="bg-white p-4 rounded-xl shadow-sm border border-gray-200">
                <div class="text-gray-500 text-xs">คะแนนเฉลี่ยร้านค้า</div>
                <div class="flex items-center gap-2 mt-1">
                    <span class="text-3xl font-bold text-gray-800">4.8</span>
                    <div class="flex text-yellow-400 text-sm">★★★★★</div>
                </div>
            </div>
            <div class="bg-white p-4 rounded-xl shadow-sm border border-yellow-200 bg-yellow-50">
                <div class="text-yellow-700 text-xs font-bold">รอการตรวจสอบ (Pending)</div>
                <div class="text-2xl font-bold text-gray-800 mt-1">15 รายการ</div>
            </div>
            <div class="bg-white p-4 rounded-xl shadow-sm border border-red-200 bg-red-50">
                <div class="text-red-700 text-xs font-bold">ถูกรายงาน/คำหยาบ (Reported)</div>
                <div class="text-2xl font-bold text-gray-800 mt-1">3 รายการ</div>
            </div>
            <div class="bg-white p-4 rounded-xl shadow-sm border border-gray-200">
                <div class="text-gray-500 text-xs">รีวิวทั้งหมด</div>
                <div class="text-2xl font-bold text-gray-800 mt-1">1,240</div>
            </div>
        </div>

        {{-- Content Area --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 min-h-[500px]">

            {{-- Tabs & Filters --}}
            <div class="border-b border-gray-200 p-4 flex flex-col md:flex-row justify-between items-center gap-4">
                <div class="tabs tabs-boxed bg-transparent p-0">
                    <a class="tab" :class="activeTab === 'all' ? 'tab-active bg-gray-800 text-white' : ''"
                        @click="activeTab = 'all'">ทั้งหมด</a>
                    <a class="tab relative" :class="activeTab === 'pending' ? 'tab-active bg-yellow-500 text-white' : ''"
                        @click="activeTab = 'pending'">
                        รอตรวจสอบ
                        <span class="w-2 h-2 bg-red-500 rounded-full absolute top-1 right-1"></span>
                    </a>
                    <a class="tab" :class="activeTab === 'published' ? 'tab-active bg-emerald-600 text-white' : ''"
                        @click="activeTab = 'published'">เผยแพร่แล้ว</a>
                    <a class="tab" :class="activeTab === 'hidden' ? 'tab-active bg-red-500 text-white' : ''"
                        @click="activeTab = 'hidden'">ถูกซ่อน/รายงาน</a>
                </div>

                <div class="flex gap-2 w-full md:w-auto">
                    <select class="select select-bordered select-sm w-full md:w-auto">
                        <option>ล่าสุด</option>
                        <option>คะแนนน้อยไปมาก</option>
                        <option>คะแนนมากไปน้อย</option>
                    </select>
                    <input type="text" placeholder="ค้นหารีวิว..."
                        class="input input-bordered input-sm w-full md:w-auto" />
                </div>
            </div>

            {{-- Review List --}}
            <div class="divide-y divide-gray-100">
                <template x-for="review in filteredReviews" :key="review.id">
                    <div class="p-6 hover:bg-gray-50 transition duration-150 ease-in-out"
                        :class="{ 'bg-red-50 hover:bg-red-100': review.status === 'hidden' }">

                        <div class="flex flex-col md:flex-row gap-4">
                            {{-- Review Content --}}
                            <div class="flex-1">
                                {{-- Header: User & Rating --}}
                                <div class="flex justify-between items-start mb-2">
                                    <div class="flex items-center gap-3">
                                        <div class="avatar">
                                            <div class="w-10 h-10 rounded-full bg-gray-200">
                                                <img :src="review.avatar" />
                                            </div>
                                        </div>
                                        <div>
                                            <div class="font-bold text-gray-900 text-sm" x-text="review.user"></div>
                                            <div class="text-xs text-gray-500" x-text="review.date"></div>
                                        </div>
                                    </div>

                                    <div class="flex flex-col items-end">
                                        <div class="flex text-yellow-400 text-sm">
                                            <template x-for="i in 5">
                                                <span x-text="i <= review.rating ? '★' : '☆'"
                                                    :class="i <= review.rating ? 'text-yellow-400' : 'text-gray-300'"></span>
                                            </template>
                                        </div>

                                        {{-- Status Badge --}}
                                        <div class="mt-1">
                                            <span class="badge badge-sm border-none text-white"
                                                :class="{
                                                    'bg-emerald-500': review.status === 'published',
                                                    'bg-yellow-500': review.status === 'pending',
                                                    'bg-red-500': review.status === 'hidden'
                                                }"
                                                x-text="getStatusLabel(review.status)">
                                            </span>
                                        </div>
                                    </div>
                                </div>

                                {{-- Product Link --}}
                                <div class="mb-3">
                                    <span class="text-xs text-gray-500 bg-gray-100 px-2 py-1 rounded">
                                        รีวิวสินค้า: <span class="font-bold text-gray-700"
                                            x-text="review.productName"></span>
                                    </span>
                                </div>

                                {{-- Review Text --}}
                                <p class="text-gray-700 text-sm leading-relaxed mb-4">
                                    <span x-html="highlightBadWords(review.text)"></span>
                                </p>

                                {{-- Media Gallery (Images/Video) --}}
                                <div class="flex gap-2 mb-4" x-show="review.media.length > 0">
                                    <template x-for="(media, index) in review.media">
                                        <div
                                            class="relative w-20 h-20 rounded-lg overflow-hidden border border-gray-200 cursor-pointer hover:opacity-80 transition">
                                            <img :src="media.type === 'video' ? '/images/video-placeholder.png' : media.url"
                                                class="w-full h-full object-cover">
                                            <div x-show="media.type === 'video'"
                                                class="absolute inset-0 flex items-center justify-center bg-black/30">
                                                <svg class="w-8 h-8 text-white" fill="currentColor" viewBox="0 0 24 24">
                                                    <path d="M8 5v14l11-7z" />
                                                </svg>
                                            </div>
                                        </div>
                                    </template>
                                </div>

                                {{-- Admin Reply (ถ้ามี) --}}
                                <div x-show="review.reply"
                                    class="bg-gray-100 p-3 rounded-lg border-l-4 border-emerald-500 mt-2">
                                    <p class="text-xs font-bold text-emerald-700 mb-1">ตอบกลับจากร้านค้า:</p>
                                    <p class="text-xs text-gray-600" x-text="review.reply"></p>
                                </div>
                            </div>

                            {{-- Actions Buttons --}}
                            <div
                                class="flex flex-row md:flex-col gap-2 md:w-32 md:border-l md:border-gray-100 md:pl-4 justify-center md:justify-start">

                                {{-- Actions for Pending --}}
                                <template x-if="review.status === 'pending'">
                                    <div class="w-full space-y-2">
                                        <button @click="changeStatus(review.id, 'published')"
                                            class="btn btn-sm bg-emerald-600 hover:bg-emerald-700 text-white w-full border-none">
                                            อนุมัติ
                                        </button>
                                        <button @click="changeStatus(review.id, 'hidden')"
                                            class="btn btn-sm btn-outline btn-error w-full">
                                            ไม่อนุมัติ
                                        </button>
                                    </div>
                                </template>

                                {{-- Actions for Published --}}
                                <template x-if="review.status === 'published'">
                                    <div class="w-full space-y-2">
                                        <button @click="openReplyModal(review)" class="btn btn-sm btn-outline w-full"
                                            :disabled="review.reply">
                                            <span x-text="review.reply ? 'ตอบแล้ว' : 'ตอบกลับ'"></span>
                                        </button>
                                        <button @click="changeStatus(review.id, 'hidden')"
                                            class="btn btn-sm btn-ghost text-red-500 w-full hover:bg-red-50">
                                            ซ่อนรีวิว
                                        </button>
                                    </div>
                                </template>

                                {{-- Actions for Hidden --}}
                                <template x-if="review.status === 'hidden'">
                                    <div class="w-full space-y-2">
                                        <button @click="changeStatus(review.id, 'published')"
                                            class="btn btn-sm btn-outline btn-success w-full">
                                            แสดงใหม่
                                        </button>
                                        <button @click="deleteReview(review.id)"
                                            class="btn btn-sm bg-red-600 hover:bg-red-700 text-white w-full border-none">
                                            ลบทิ้ง
                                        </button>
                                    </div>
                                </template>

                            </div>
                        </div>
                    </div>
                </template>
            </div>
        </div>

        {{-- Reply Modal --}}
        <dialog class="modal" :class="showReplyModal ? 'modal-open' : ''">
            <div class="modal-box bg-white">
                <h3 class="font-bold text-lg mb-2">ตอบกลับรีวิว</h3>
                <p class="text-sm text-gray-500 mb-4">
                    ตอบกลับคุณ: <span class="font-bold text-gray-800" x-text="selectedReview?.user"></span>
                </p>
                <div class="bg-gray-50 p-3 rounded mb-4 text-sm text-gray-600 italic border-l-2 border-gray-300">
                    "<span x-text="selectedReview?.text"></span>"
                </div>

                <textarea x-model="replyText" class="textarea textarea-bordered w-full h-32" placeholder="พิมพ์ข้อความตอบกลับ..."></textarea>

                <div class="modal-action">
                    <button @click="showReplyModal = false" class="btn btn-ghost text-gray-500">ยกเลิก</button>
                    <button @click="submitReply()"
                        class="btn bg-emerald-600 hover:bg-emerald-700 text-white">ส่งข้อความ</button>
                </div>
            </div>
        </dialog>

    </div>

    <script>
        function reviewSystem() {
            return {
                activeTab: 'all',
                showReplyModal: false,
                selectedReview: null,
                replyText: '',

                // Mock Data
                reviews: [{
                        id: 1,
                        user: 'คุณบาส',
                        avatar: 'https://ui-avatars.com/api/?name=Bas&background=0D9488&color=fff',
                        date: '10 นาทีที่แล้ว',
                        rating: 5,
                        productName: 'เสื้อยืด Oversize Cotton 100%',
                        text: 'สินค้าดีมากครับ ผ้านุ่ม ใส่สบาย จัดส่งไวมาก แนะนำเลยครับร้านนี้',
                        media: [{
                                type: 'image',
                                url: '/images/T-Shirt-1.png'
                            },
                            {
                                type: 'image',
                                url: '/images/T-Shirt-B.png'
                            }
                        ],
                        status: 'published',
                        reply: 'ขอบคุณมากครับคุณบาส ดีใจที่คุณชอบสินค้านะครับ'
                    },
                    {
                        id: 2,
                        user: 'คุณสมชาย',
                        avatar: 'https://ui-avatars.com/api/?name=Somchai&background=random',
                        date: '1 ชั่วโมงที่แล้ว',
                        rating: 4,
                        productName: 'กางเกงยีนส์ Slim Fit',
                        text: 'เนื้อผ้าดี แต่ทรงอาจจะเล็กไปนิดนึง ใครสั่งแนะนำเผื่อไซซ์ครับ',
                        media: [],
                        status: 'pending', // รออนุมัติ
                        reply: null
                    },
                    {
                        id: 3,
                        user: 'Unknown User',
                        avatar: 'https://ui-avatars.com/api/?name=Unknown&background=red&color=fff',
                        date: '2 ชั่วโมงที่แล้ว',
                        rating: 1,
                        productName: 'หมวกแก๊ป',
                        text: 'ร้านนี้บริการแย่มาก ส่งของช้า ของก็ห่วยแตก อย่าไปซื้อ', // มีคำหยาบ (Mockup)
                        media: [],
                        status: 'hidden', // ถูก AI ซ่อน
                        reply: null
                    }
                ],

                get filteredReviews() {
                    if (this.activeTab === 'all') return this.reviews;
                    return this.reviews.filter(r => r.status === this.activeTab);
                },

                getStatusLabel(status) {
                    const labels = {
                        'pending': 'รอตรวจสอบ',
                        'published': 'เผยแพร่',
                        'hidden': 'ถูกซ่อน/รายงาน'
                    };
                    return labels[status];
                },

                // ฟังก์ชันจำลองการไฮไลท์คำหยาบ (Mockup Logic)
                highlightBadWords(text) {
                    const badWords = ['ห่วย', 'แย่', 'เลว', 'โกง'];
                    let highlightedText = text;
                    badWords.forEach(word => {
                        const regex = new RegExp(word, 'gi');
                        highlightedText = highlightedText.replace(regex,
                            `<span class="bg-red-100 text-red-600 px-1 rounded font-bold">${word}</span>`);
                    });
                    return highlightedText;
                },

                changeStatus(id, newStatus) {
                    const review = this.reviews.find(r => r.id === id);
                    if (review) {
                        review.status = newStatus;
                        // ถ้าอนุมัติ -> ให้ Alert
                        if (newStatus === 'published') alert('อนุมัติรีวิวเรียบร้อย');
                        if (newStatus === 'hidden') alert('ซ่อนรีวิวแล้ว');
                    }
                },

                deleteReview(id) {
                    if (confirm('ยืนยันที่จะลบุรีวิวนี้ถาวร?')) {
                        this.reviews = this.reviews.filter(r => r.id !== id);
                    }
                },

                openReplyModal(review) {
                    this.selectedReview = review;
                    this.replyText = '';
                    this.showReplyModal = true;
                },

                submitReply() {
                    if (this.selectedReview) {
                        this.selectedReview.reply = this.replyText;
                        this.showReplyModal = false;
                        alert('ตอบกลับรีวิวสำเร็จ!');
                    }
                }
            }
        }
    </script>
@endsection
