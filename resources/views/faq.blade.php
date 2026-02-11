@extends('layout')

@section('title', 'คำถามที่พบบ่อย | Salepage Demo')

@section('content')
<div class="container mx-auto px-4 py-8 md:py-12 min-h-screen">
    <div class="bg-white rounded-lg shadow-lg p-6 md:p-8 max-w-3xl mx-auto mt-8">
        <h1 class="text-3xl md:text-4xl font-bold text-gray-800 text-center mb-8">คำถามที่พบบ่อย</h1>

        <div class="space-y-4">
            {{-- FAQ Item 1 --}}
            <div class="collapse collapse-plus bg-base-200">
                <input type="radio" name="my-accordion-1" checked="checked" />
                <div class="collapse-title text-xl font-medium text-red-600">
                    ฉันจะสั่งซื้อสินค้าได้อย่างไร?
                </div>
                <div class="collapse-content">
                    <p class="text-gray-700">คุณสามารถสั่งซื้อสินค้าได้ง่ายๆ โดยเลือกสินค้าที่ต้องการเพิ่มลงในตะกร้าสินค้า จากนั้นดำเนินการชำระเงินตามขั้นตอนที่ระบุไว้</p>
                </div>
            </div>

            {{-- FAQ Item 2 --}}
            <div class="collapse collapse-plus bg-base-200">
                <input type="radio" name="my-accordion-1" />
                <div class="collapse-title text-xl font-medium text-red-600">
                    มีวิธีการชำระเงินแบบใดบ้าง?
                </div>
                <div class="collapse-content">
                    <p class="text-gray-700">เรารับชำระเงินผ่านการโอนเงินธนาคาร (PromptPay) โดยคุณจะได้รับ QR Code สำหรับการชำระเงินหลังยืนยันคำสั่งซื้อ</p>
                </div>
            </div>

            {{-- FAQ Item 3 --}}
            <div class="collapse collapse-plus bg-base-200">
                <input type="radio" name="my-accordion-1" />
                <div class="collapse-title text-xl font-medium text-red-600">
                    ฉันจะติดตามสถานะคำสั่งซื้อได้อย่างไร?
                </div>
                <div class="collapse-content">
                    <p class="text-gray-700">คุณสามารถติดตามสถานะคำสั่งซื้อได้ที่หน้า "ประวัติการสั่งซื้อ" สำหรับสมาชิก หรือใช้เลขที่คำสั่งซื้อในการติดตามที่หน้า "ติดตามสถานะคำสั่งซื้อ" สำหรับบุคคลทั่วไป</p>
                </div>
            </div>

            {{-- FAQ Item 4 --}}
            <div class="collapse collapse-plus bg-base-200">
                <input type="radio" name="my-accordion-1" />
                <div class="collapse-title text-xl font-medium text-red-600">
                    หากสินค้ามีปัญหา ฉันควรทำอย่างไร?
                </div>
                <div class="collapse-content">
                    <p class="text-gray-700">หากสินค้ามีปัญหา กรุณาติดต่อทีมงานของเราทันทีผ่านช่องทาง "ติดต่อเรา" พร้อมแจ้งรายละเอียดปัญหาและเลขที่คำสั่งซื้อ ทีมงานจะรีบช่วยเหลือคุณโดยเร็วที่สุด</p>
                </div>
            </div>

            {{-- FAQ Item 5 --}}
            <div class="collapse collapse-plus bg-base-200">
                <input type="radio" name="my-accordion-1" />
                <div class="collapse-title text-xl font-medium text-red-600">
                    สามารถเปลี่ยนหรือคืนสินค้าได้หรือไม่?
                </div>
                <div class="collapse-content">
                    <p class="text-gray-700">นโยบายการเปลี่ยนหรือคืนสินค้าของเราจะขึ้นอยู่กับเงื่อนไขที่ระบุไว้ กรุณาตรวจสอบรายละเอียดเพิ่มเติมในส่วน "การคืนสินค้าและการคืนเงิน" หรือติดต่อสอบถามเจ้าหน้าที่</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection