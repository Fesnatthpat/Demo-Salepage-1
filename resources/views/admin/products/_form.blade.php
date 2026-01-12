<div class="card bg-base-100 shadow-xl border border-base-200 mb-6">
    <div class="card-body">
        <h2 class="card-title text-primary mb-4">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6 mr-2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z" />
            </svg>
            ข้อมูลสินค้า
        </h2>

        @if (isset($productSalepage) && $productSalepage->pd_code)
        <div class="form-control w-full mb-4">
            <label class="label" for="pd_code">
                <span class="label-text font-bold">รหัสสินค้า</span>
            </label>
            <input type="text" id="pd_code" class="input input-bordered w-full bg-gray-100" value="{{ $productSalepage->pd_code }}" readonly />
        </div>
        @endif

        <div class="form-control w-full mb-4">
            <label class="label" for="pd_sp_name">
                <span class="label-text font-bold">ชื่อสินค้า <span class="text-error">*</span></span>
            </label>
            <input type="text" name="pd_sp_name" id="pd_sp_name" 
                placeholder="ระบุชื่อสินค้า..." 
                class="input input-bordered w-full @error('pd_sp_name') input-error @enderror"
                value="{{ old('pd_sp_name', $productSalepage->pd_sp_name ?? '') }}" />
            @error('pd_sp_name')
                <div class="label">
                    <span class="label-text-alt text-error">{{ $message }}</span>
                </div>
            @enderror
        </div>

        <div class="form-control w-full">
            <label class="label" for="pd_sp_details">
                <span class="label-text font-bold">รายละเอียดสินค้า</span>
            </label>
            <textarea name="pd_sp_details" id="pd_sp_details" 
                class="textarea textarea-bordered h-32 w-full @error('pd_sp_details') textarea-error @enderror" 
                placeholder="อธิบายคุณสมบัติ จุดเด่น...">{{ old('pd_sp_details', $productSalepage->pd_sp_details ?? '') }}</textarea>
            @error('pd_sp_details')
                <div class="label">
                    <span class="label-text-alt text-error">{{ $message }}</span>
                </div>
            @enderror
        </div>
    </div>
</div>

<div class="card bg-base-100 shadow-xl border border-base-200">
    <div class="card-body">
        <h2 class="card-title text-primary mb-4">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6 mr-2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 15.75l5.159-5.159a2.25 2.25 0 013.182 0l5.159 5.159m-1.5-1.5l1.409-1.409a2.25 2.25 0 013.182 0l2.909 2.909m-18 3.75h16.5a1.5 1.5 0 001.5-1.5V6a1.5 1.5 0 00-1.5-1.5H3.75A1.5 1.5 0 002.25 6v12a1.5 1.5 0 001.5 1.5zm10.5-11.25h.008v.008h-.008V8.25zm.375 0a.375.375 0 11-.75 0 .375.375 0 01.75 0z" />
            </svg>
            รูปภาพสินค้า
        </h2>

        <div class="form-control w-full mb-6">
            <div class="relative group cursor-pointer">
                <div id="upload-zone" class="flex flex-col items-center justify-center w-full h-48 border-2 border-dashed border-base-300 rounded-2xl bg-base-50 hover:bg-base-100 hover:border-primary transition-all duration-300">
                    <div class="flex flex-col items-center justify-center pt-5 pb-6">
                        <svg class="w-10 h-10 mb-3 text-gray-400 group-hover:text-primary transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path></svg>
                        <p class="mb-2 text-sm text-gray-500"><span class="font-semibold">คลิกเพื่ออัปโหลด</span> หรือลากไฟล์มาวาง</p>
                        <p class="text-xs text-gray-500">PNG, JPG, JPEG (เลือกได้หลายรูป)</p>
                    </div>
                    <input type="file" name="images[]" id="images" multiple accept="image/*" class="absolute inset-0 w-full h-full opacity-0 cursor-pointer" />
                </div>
            </div>
            @error('images') <span class="text-error text-sm mt-2">{{ $message }}</span> @enderror
            @error('images.*') <span class="text-error text-sm mt-2">{{ $message }}</span> @enderror
        </div>

        <div id="new-image-preview" class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-6 gap-4 mb-6">
            </div>

        @if (isset($productSalepage) && $productSalepage->images->count() > 0)
            <div class="divider">รูปภาพปัจจุบัน</div>
            
            <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-5 gap-4">
                @foreach ($productSalepage->images as $image)
                <div class="card card-compact bg-base-100 shadow-md border border-base-200 group hover:shadow-lg transition-all" id="image-card-{{ $image->img_pd_id }}">
                    <figure class="relative pt-[75%] bg-gray-100 overflow-hidden">
                        <img src="{{ asset('storage/' . $image->image_path) }}" class="absolute top-0 left-0 w-full h-full object-cover transition-transform duration-500 group-hover:scale-110" alt="{{ $image->image_alt }}">
                        @if($image->is_primary)
                        <div class="absolute top-2 right-2 badge badge-primary shadow-sm">หลัก</div>
                        @endif
                    </figure>
                    <div class="card-body p-3">
                        <div class="form-control">
                            <label class="label cursor-pointer justify-start gap-2 p-0 mb-2">
                                <input type="radio" name="is_primary" value="{{ $image->img_pd_id }}" class="radio radio-primary radio-sm" {{ $image->is_primary ? 'checked' : '' }}>
                                <span class="label-text text-xs">ตั้งเป็นรูปหลัก</span>
                            </label>
                        </div>
                        <button type="button" class="btn btn-error btn-outline btn-xs w-full delete-image gap-2" data-image-id="{{ $image->img_pd_id }}">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-3 h-3">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0" />
                            </svg>
                            ลบ
                        </button>
                    </div>
                </div>
                @endforeach
            </div>
        @endif
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const uploadInput = document.getElementById('images');
        const previewContainer = document.getElementById('new-image-preview');
        const uploadZone = document.getElementById('upload-zone');

        // Drag & Drop Effects
        ['dragenter', 'dragover'].forEach(eventName => {
            uploadZone.addEventListener(eventName, (e) => {
                e.preventDefault();
                uploadZone.classList.add('border-primary', 'bg-blue-50');
            });
        });

        ['dragleave', 'drop'].forEach(eventName => {
            uploadZone.addEventListener(eventName, (e) => {
                e.preventDefault();
                uploadZone.classList.remove('border-primary', 'bg-blue-50');
            });
        });

        // Handle File Selection & Preview
        uploadInput.addEventListener('change', function() {
            previewContainer.innerHTML = ''; // Clear old previews
            const files = Array.from(this.files);

            files.forEach(file => {
                if (file.type.startsWith('image/')) {
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        const div = document.createElement('div');
                        // Tailwind Style สำหรับรูป Preview
                        div.className = 'relative aspect-square rounded-lg overflow-hidden border border-base-300 shadow-sm';
                        div.innerHTML = `
                            <img src="${e.target.result}" class="w-full h-full object-cover">
                            <div class="absolute bottom-0 left-0 right-0 bg-black bg-opacity-50 text-white text-xs p-1 truncate text-center">
                                ${file.name}
                            </div>
                        `;
                        previewContainer.appendChild(div);
                    }
                    reader.readAsDataURL(file);
                }
            });
        });

        // Handle Image Deletion
        document.querySelectorAll('.delete-image').forEach(button => {
            button.addEventListener('click', function() {
                if (confirm('คุณแน่ใจหรือไม่ว่าต้องการลบรูปภาพนี้?')) {
                    const imageId = this.dataset.imageId;
                    const cardElement = document.getElementById(`image-card-${imageId}`);
                    const originalBtnText = this.innerHTML;
                    
                    // Loading State
                    this.innerHTML = '<span class="loading loading-spinner loading-xs"></span> กำลังลบ...';
                    this.disabled = true;

                    fetch(`/admin/products/image/${imageId}`, {
                        method: 'DELETE',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Content-Type': 'application/json'
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            // Animation remove
                            cardElement.classList.add('scale-0', 'opacity-0');
                            setTimeout(() => {
                                cardElement.remove();
                                // ถ้าไม่มีรูปเหลือ ให้ซ่อน wrapper (Optional logic)
                            }, 300);
                        } else {
                            alert('Error: ' + data.message);
                            this.innerHTML = originalBtnText;
                            this.disabled = false;
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        alert('Connection Error');
                        this.innerHTML = originalBtnText;
                        this.disabled = false;
                    });
                }
            });
        });
    });
</script>
@endpush