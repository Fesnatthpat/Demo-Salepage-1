<?php $__env->startSection('title', 'ชำระเงินและที่อยู่จัดส่ง | Salepage Demo'); ?>

<?php $__env->startSection('content'); ?>
    <div class="container mx-auto p-4 lg:px-20 lg:py-10 max-w-7xl">

        
        <?php if(session('error')): ?>
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg relative mb-6 shadow-md"
                role="alert">
                <span class="block sm:inline"><?php echo e(session('error')); ?></span>
            </div>
        <?php endif; ?>

        
        <?php if($errors->any()): ?>
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg relative mb-6 shadow-md"
                role="alert">
                <strong class="font-bold">เกิดข้อผิดพลาด!</strong>
                <ul class="mt-2 list-disc list-inside">
                    <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <li><?php echo e($error); ?></li>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </ul>
            </div>
        <?php endif; ?>

        
        <?php
            $grandTotal = $totalAmount;
            $shippingCost = 0;
            $discount = $totalDiscount;
            $finalTotal = $grandTotal + $shippingCost;
        ?>

        
        <div class="bg-white border border-gray-200 rounded-lg p-6 mb-6 shadow-sm">
            <div class="flex justify-between items-center mb-6">
                <h2 class="text-xl font-bold text-gray-800">ที่อยู่ในการจัดส่งสินค้า</h2>
            </div>

            <div x-data="{
                activeAddress: null,
                init() {
                    let stored = localStorage.getItem('selected_address_id');
                    let defaultId = <?php echo e($addresses->count() > 0 ? $addresses->first()->id : 'null'); ?>;
                    this.activeAddress = stored ? parseInt(stored) : defaultId;
                },
                selectAddress(id) {
                    this.activeAddress = id;
                    localStorage.setItem('selected_address_id', id);
                }
            }" x-init="init()">

                <?php if($addresses->count() > 0): ?>
                    <?php $__currentLoopData = $addresses; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $address): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <?php $modalEditId = 'modal_edit_' . $address->id; ?>

                        
                        <div class="relative border rounded-lg p-6 mb-4 transition-all duration-200 cursor-pointer"
                            :class="activeAddress === <?php echo e($address->id); ?> ? 'border-emerald-500 bg-emerald-50/10' :
                                'border-gray-300 hover:border-emerald-300'"
                            @click="selectAddress(<?php echo e($address->id); ?>)">

                            <div class="flex justify-between items-start">
                                <div>
                                    <div class="flex items-center gap-3 mb-2">
                                        <h3 class="font-bold text-gray-800 text-base">บ้านของฉัน</h3>
                                        <?php if($index === 0): ?>
                                            <span
                                                class="text-[10px] text-emerald-600 border border-emerald-600 px-2 py-0.5 rounded">ค่าเริ่มต้น</span>
                                        <?php endif; ?>
                                        <span x-show="activeAddress === <?php echo e($address->id); ?>"
                                            class="text-[10px] bg-emerald-600 text-white px-2 py-0.5 rounded ml-2">เลือกอยู่</span>
                                    </div>

                                    <div class="text-gray-600 text-sm space-y-1">
                                        <p><span class="font-semibold text-gray-700">ชื่อ-นามสกุล:</span>
                                            <?php echo e($address->fullname); ?></p>
                                        <p>
                                            <span class="font-semibold text-gray-700">ที่อยู่:</span>
                                            <?php echo e($address->address_line1); ?>

                                            <?php echo e($address->address_line2 ? ' ' . $address->address_line2 : ''); ?>

                                            <?php echo e($address->district->name_th ?? ''); ?>

                                            <?php echo e($address->amphure->name_th ?? ''); ?>

                                            <?php echo e($address->province->name_th ?? ''); ?>

                                            <?php echo e($address->zipcode); ?>

                                        </p>
                                        <p><span class="font-semibold text-gray-700">เบอร์โทรศัพท์:</span>
                                            <?php echo e($address->phone); ?></p>

                                        <?php if($address->note): ?>
                                            <div class="divider my-2"></div>
                                            <p class="max-h-20 overflow-y-auto"><span
                                                    class="font-semibold text-gray-700">หมายเหตุ:</span>
                                                <?php echo e($address->note); ?></p>
                                        <?php endif; ?>
                                    </div>
                                </div>

                                
                                <div class="flex items-center gap-2">
                                    <button type="button" onclick="<?php echo e($modalEditId); ?>.showModal()" @click.stop
                                        class="btn btn-sm btn-outline border-gray-300 text-gray-600 hover:bg-gray-50 hover:text-gray-800 hover:border-gray-400 font-normal px-4">
                                        แก้ไขที่อยู่
                                    </button>

                                    <form id="delete-form-<?php echo e($address->id); ?>"
                                        action="<?php echo e(route('address.destroy', $address->id)); ?>" method="POST" @click.stop>
                                        <?php echo csrf_field(); ?>
                                        <?php echo method_field('DELETE'); ?>
                                        <button type="button" onclick="confirmDelete('delete-form-<?php echo e($address->id); ?>')"
                                            class="btn btn-sm btn-outline border-red-200 text-red-500 hover:bg-red-50 hover:border-red-300 hover:text-red-600 font-normal px-3">
                                            ลบ
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>

                        
                        <dialog id="<?php echo e($modalEditId); ?>" class="modal modal-middle" x-data="addressDropdown()"
                            x-init="loadEditData('<?php echo e($address->province_id); ?>', '<?php echo e($address->amphure_id); ?>', '<?php echo e($address->district_id); ?>')">
                            <div class="modal-box w-11/12 max-w-4xl p-0 bg-white rounded-lg shadow-xl overflow-hidden cursor-default"
                                @click.stop>
                                <div class="px-6 py-4 border-b border-gray-100 flex justify-between items-center">
                                    <h3 class="font-bold text-lg text-gray-800">แก้ไขที่อยู่จัดส่ง</h3>
                                    <form method="dialog"><button
                                            class="text-gray-400 hover:text-gray-600 text-2xl leading-none">&times;</button>
                                    </form>
                                </div>
                                <div class="p-6 max-h-[75vh] overflow-y-auto">
                                    <form action="<?php echo e(route('address.update', $address->id)); ?>" method="POST"
                                        id="form_edit_<?php echo e($address->id); ?>" onsubmit="showLoading()">
                                        <?php echo csrf_field(); ?> <?php echo method_field('PUT'); ?>
                                        <div class="mb-6">
                                            <h4 class="text-emerald-600 font-bold mb-4">ข้อมูลผู้รับ</h4>
                                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                                <div class="form-control">
                                                    <label class="label-text text-gray-500 mb-1">ชื่อ-นามสกุล</label>
                                                    <input type="text" name="fullname" value="<?php echo e($address->fullname); ?>"
                                                        class="input input-bordered w-full rounded focus:outline-emerald-500" />
                                                </div>
                                                <div class="form-control">
                                                    <label class="label-text text-gray-500 mb-1">เบอร์โทรศัพท์</label>
                                                    <input type="tel" name="phone" value="<?php echo e($address->phone); ?>"
                                                        class="input input-bordered w-full rounded focus:outline-emerald-500" />
                                                </div>
                                            </div>
                                        </div>
                                        <div class="mb-4">
                                            <h4 class="text-emerald-600 font-bold mb-4">ที่อยู่จัดส่ง</h4>
                                            <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-4">
                                                <div class="md:col-span-3 form-control">
                                                    <label class="label-text text-gray-500 mb-1">บ้านเลขที่ / อาคาร /
                                                        ถนน</label>
                                                    <input type="text" name="address_line1"
                                                        value="<?php echo e($address->address_line1); ?>"
                                                        class="input input-bordered w-full rounded focus:outline-emerald-500" />
                                                </div>
                                                <div class="md:col-span-1 form-control">
                                                    <label class="label-text text-gray-500 mb-1">หมู่ที่</label>
                                                    <input type="text" name="address_line2"
                                                        value="<?php echo e($address->address_line2); ?>"
                                                        class="input input-bordered w-full rounded focus:outline-emerald-500 text-center" />
                                                </div>
                                            </div>
                                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                                                <div class="form-control">
                                                    <label class="label-text text-gray-500 mb-1">จังหวัด</label>
                                                    <select name="province_id" x-model="selectedProvince"
                                                        @change="fetchAmphures()"
                                                        class="select select-bordered w-full rounded focus:outline-emerald-500">
                                                        <option value="">-- เลือกจังหวัด --</option>
                                                        <?php $__currentLoopData = $provinces; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $province): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                            <option value="<?php echo e($province->id); ?>">
                                                                <?php echo e($province->name_th); ?>

                                                            </option>
                                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                                    </select>
                                                </div>
                                                <div class="form-control">
                                                    <label class="label-text text-gray-500 mb-1">อำเภอ/เขต</label>
                                                    <select name="amphure_id" x-model="selectedAmphure"
                                                        @change="fetchDistricts()" :disabled="!selectedProvince"
                                                        class="select select-bordered w-full rounded focus:outline-emerald-500 disabled:bg-gray-100">
                                                        <option value="">-- เลือกอำเภอ --</option>
                                                        <template x-for="amphure in amphures" :key="amphure.id">
                                                            <option :value="amphure.id" x-text="amphure.name_th">
                                                            </option>
                                                        </template>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                                                <div class="form-control">
                                                    <label class="label-text text-gray-500 mb-1">ตำบล/แขวง</label>
                                                    <select name="district_id" x-model="selectedDistrict"
                                                        :disabled="!selectedAmphure"
                                                        class="select select-bordered w-full rounded focus:outline-emerald-500 disabled:bg-gray-100">
                                                        <option value="">-- เลือกตำบล --</option>
                                                        <template x-for="district in districts" :key="district.id">
                                                            <option :value="district.id" x-text="district.name_th">
                                                            </option>
                                                        </template>
                                                    </select>
                                                </div>
                                                <div class="form-control">
                                                    <label class="label-text text-gray-500 mb-1">รหัสไปรษณีย์</label>
                                                    <input type="text" name="zipcode" :value="getZipCode()" readonly
                                                        class="input input-bordered w-full rounded bg-gray-50 text-gray-700 font-semibold" />
                                                </div>
                                            </div>
                                            <div class="form-control mt-4">
                                                <label class="label-text text-gray-500 mb-1">หมายเหตุการจัดส่ง</label>
                                                <textarea name="note" class="textarea textarea-bordered w-full rounded focus:outline-emerald-500 h-24"><?php echo e($address->note); ?></textarea>
                                            </div>
                                        </div>
                                    </form>
                                    <div class="px-6 py-4 border-t border-gray-100 flex justify-end gap-3 bg-gray-50">
                                        <form method="dialog"><button
                                                class="btn btn-ghost text-gray-500 hover:bg-gray-200 font-normal">ยกเลิก</button>
                                        </form>
                                        <button
                                            onclick="document.getElementById('form_edit_<?php echo e($address->id); ?>').submit()"
                                            class="btn bg-[#00B900] hover:bg-[#009900] text-white border-none font-normal px-6">บันทึกข้อมูล</button>
                                    </div>
                                </div>
                            </div>
                        </dialog>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                <?php else: ?>
                    <div class="text-center py-10 bg-gray-50 rounded border-2 border-dashed border-gray-300">
                        <p class="text-gray-500 mb-4">ยังไม่มีข้อมูลที่อยู่จัดส่ง</p>
                        <button onclick="modal_add_new.showModal()"
                            class="btn btn-primary text-white">เพิ่มที่อยู่จัดส่ง</button>
                    </div>
                <?php endif; ?>
            </div>

            <?php if($addresses->count() > 0): ?>
                <div class="mt-4 pt-4 border-t border-gray-100">
                    <button onclick="modal_add_new.showModal()"
                        class="text-emerald-600 hover:text-emerald-700 text-sm font-semibold flex items-center gap-1">
                        + เพิ่มที่อยู่ใหม่
                    </button>
                </div>
            <?php endif; ?>
        </div>

        
        <div class="bg-white border border-gray-200 rounded-lg p-6 mb-6 shadow-sm">
            <h2 class="text-xl font-bold text-gray-800 mb-6">สั่งซื้อสินค้าแล้ว</h2>
            <div class="space-y-4">
                <?php if(isset($cartItems) && count($cartItems) > 0): ?>
                    <?php $__currentLoopData = $cartItems; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <?php
                            // แปลง Attributes เป็น Array
                            $attrs = $item->attributes;
                            if (!is_array($attrs) && is_object($attrs) && method_exists($attrs, 'toArray')) {
                                $attrs = $attrs->toArray();
                            }
                            $attrs = (array) $attrs;

                            $originalPrice = $attrs['original_price'] ?? $item->price;
                            $totalPrice = $item->price * $item->quantity;
                            
                            $productDb = $products[$item->id] ?? null;
                            $displayImage = $productDb ? $productDb->cover_image_url : 'https://via.placeholder.com/150?text=No+Image';
                        ?>

                        <div
                            class="flex justify-between items-start border-b border-gray-100 pb-4 last:border-0 last:pb-0">
                            <div class="flex items-center gap-4">
                                
                                <div
                                    class="w-16 h-16 bg-gray-100 rounded-md overflow-hidden border border-gray-200 flex-shrink-0 relative">
                                    <img src="<?php echo e($displayImage); ?>" class="w-full h-full object-cover"
                                        alt="<?php echo e($item->name); ?>"
                                        onerror="this.onerror=null;this.src='https://via.placeholder.com/150?text=Error';" />
                                </div>
                                
                                <div>
                                    <p class="font-bold text-gray-800 text-sm md:text-base line-clamp-1">
                                        <?php echo e($item->name); ?>

                                    </p>
                                    <p class="text-sm text-gray-500">จำนวน: <?php echo e($item->quantity); ?> ชิ้น</p>
                                </div>
                            </div>

                            
                            <div class="text-right">
                                <p class="font-bold text-emerald-600">฿<?php echo e(number_format($totalPrice)); ?></p>
                                <?php if($originalPrice > $item->price): ?>
                                    <p class="text-xs text-gray-400 line-through">
                                        ฿<?php echo e(number_format($originalPrice * $item->quantity)); ?>

                                    </p>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                <?php else: ?>
                    <p class="text-center text-gray-400 py-4">ไม่พบรายการสินค้า</p>
                <?php endif; ?>
            </div>
        </div>

        
        <div class="bg-white border border-gray-200 rounded-lg p-6 shadow-sm">
            <h2 class="text-xl font-bold text-gray-800 mb-6">วิธีชำระเงิน</h2>
            <div class="flex flex-col lg:flex-row gap-8 items-start">
                <div class="flex-1 w-full">
                    <div class="mb-4">
                        <select
                            class="select select-bordered w-full text-base rounded border-gray-300 focus:border-emerald-500 focus:outline-none">
                            <option disabled selected>เลือกวิธีชำระเงิน</option>
                            <option>ชำระเงินปลายทาง</option>
                            <option>บัตรเครดิต/เดบิต</option>
                            <option>โอนเงินธนาคาร</option>
                        </select>
                    </div>
                    <div class="border border-gray-300 rounded p-4 flex items-center gap-4">
                        <input type="checkbox" checked
                            class="checkbox checkbox-primary rounded-sm w-5 h-5 border-gray-400" />
                        <div class="border border-gray-200 rounded px-3 py-1 bg-white">
                            
                            <img src="<?php echo e(asset('images/ci-qrpayment-img-01.png')); ?>" alt="PromptPay" class="w-24">
                        </div>
                        <span class="text-gray-700">ชำระผ่านพร้อมเพย์</span>
                    </div>
                </div>

                <div class="w-full lg:w-[350px] bg-white lg:border-l lg:pl-8 border-gray-100">
                    <h3 class="font-bold text-gray-800 mb-4">สรุปยอดชำระ:</h3>
                    <div class="space-y-2 text-sm text-gray-600 mb-4">
                        <div class="flex justify-between">
                            <span>ราคารวมเต็มก่อนลดราคา</span>
                            <span class="font-medium text-gray-900">฿<?php echo e(number_format($totalOriginalAmount)); ?></span>
                        </div>
                        <div class="flex justify-between">
                            <span>รวมการสั่งซื้อ</span>
                            <span class="font-medium text-green-600">฿<?php echo e(number_format($grandTotal)); ?></span>
                        </div>
                        <div class="flex justify-between">
                            <span>การจัดส่ง</span>
                            <span class="font-medium text-gray-900">฿<?php echo e(number_format($shippingCost)); ?></span>
                        </div>
                        <?php if($discount > 0): ?>
                            <div class="flex justify-between text-red-600">
                                <span>ส่วนลด</span>
                                <span>-฿<?php echo e(number_format($discount)); ?></span>
                            </div>
                        <?php endif; ?>
                    </div>
                    <div class="flex justify-between items-center border-t border-gray-200 pt-4 mb-6">
                        <span class="font-bold text-gray-800">ยอดชำระทั้งหมด</span>
                        <span class="font-bold text-red-500 text-xl">฿<?php echo e(number_format($finalTotal)); ?></span>
                    </div>

                    <form action="<?php echo e(route('payment.process')); ?>" method="POST"
                        onsubmit="return handlePaymentSubmit()">
                        <?php echo csrf_field(); ?>
                        <?php if(isset($selectedItems)): ?>
                            <?php $__currentLoopData = $selectedItems; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $id): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <input type="hidden" name="selected_items[]" value="<?php echo e($id); ?>">
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        <?php endif; ?>
                        <?php if(isset($selectedFreebies)): ?>
                            <?php $__currentLoopData = $selectedFreebies; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $id): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <input type="hidden" name="selected_freebies[]" value="<?php echo e($id); ?>">
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        <?php endif; ?>
                        <input type="hidden" name="address_id" id="hidden_address_id">
                        <button type="submit"
                            class="btn bg-[#4F46E5] hover:bg-[#4338ca] text-white border-none w-full text-base font-normal h-11 rounded shadow-sm">
                            ชำระเงิน
                        </button>
                    </form>
                    <div class="text-center mt-3">
                        <a href="<?php echo e(route('cart.index')); ?>"
                            class="text-xs text-gray-500 hover:text-gray-700 underline">ยกเลิกคำสั่งซื้อ</a>
                    </div>
                </div>
            </div>
        </div>

        
        <dialog id="modal_add_new" class="modal modal-middle" x-data="addressDropdown()">
            <div class="modal-box w-11/12 max-w-4xl p-0 bg-white rounded-lg shadow-xl overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100 flex justify-between items-center">
                    <h3 class="font-bold text-lg text-gray-800">เพิ่มที่อยู่จัดส่งใหม่</h3>
                    <form method="dialog"><button
                            class="text-gray-400 hover:text-gray-600 text-2xl leading-none">&times;</button></form>
                </div>
                <div class="p-6 max-h-[75vh] overflow-y-auto">
                    <form action="<?php echo e(route('address.save')); ?>" method="POST" id="form_add_new"
                        onsubmit="showLoading()">
                        <?php echo csrf_field(); ?>
                        
                        <div class="mb-6">
                            <h4 class="text-emerald-600 font-bold mb-4">ข้อมูลผู้รับ</h4>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div class="form-control">
                                    <label class="label-text text-gray-500 mb-1">ชื่อ-นามสกุล</label>
                                    <input type="text" name="fullname"
                                        class="input input-bordered w-full rounded focus:outline-emerald-500" />
                                </div>
                                <div class="form-control">
                                    <label class="label-text text-gray-500 mb-1">เบอร์โทรศัพท์</label>
                                    <input type="tel" name="phone"
                                        class="input input-bordered w-full rounded focus:outline-emerald-500" />
                                </div>
                            </div>
                        </div>
                        <div class="mb-4">
                            <h4 class="text-emerald-600 font-bold mb-4">ที่อยู่จัดส่ง</h4>
                            <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-4">
                                <div class="md:col-span-3 form-control">
                                    <label class="label-text text-gray-500 mb-1">บ้านเลขที่ / อาคาร / ถนน</label>
                                    <input type="text" name="address_line1"
                                        class="input input-bordered w-full rounded focus:outline-emerald-500" />
                                </div>
                                <div class="md:col-span-1 form-control">
                                    <label class="label-text text-gray-500 mb-1">หมู่ที่</label>
                                    <input type="text" name="address_line2"
                                        class="input input-bordered w-full rounded focus:outline-emerald-500 text-center" />
                                </div>
                            </div>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                                <div class="form-control">
                                    <label class="label-text text-gray-500 mb-1">จังหวัด</label>
                                    <select name="province_id" x-model="selectedProvince" @change="fetchAmphures()"
                                        class="select select-bordered w-full rounded focus:outline-emerald-500">
                                        <option value="">-- เลือกจังหวัด --</option>
                                        <?php $__currentLoopData = $provinces; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $province): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <option value="<?php echo e($province->id); ?>"><?php echo e($province->name_th); ?></option>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </select>
                                </div>
                                <div class="form-control">
                                    <label class="label-text text-gray-500 mb-1">อำเภอ/เขต</label>
                                    <select name="amphure_id" x-model="selectedAmphure" @change="fetchDistricts()"
                                        :disabled="!selectedProvince"
                                        class="select select-bordered w-full rounded focus:outline-emerald-500 disabled:bg-gray-100">
                                        <option value="">-- เลือกอำเภอ --</option>
                                        <template x-for="amphure in amphures" :key="amphure.id">
                                            <option :value="amphure.id" x-text="amphure.name_th"></option>
                                        </template>
                                    </select>
                                </div>
                            </div>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                                <div class="form-control">
                                    <label class="label-text text-gray-500 mb-1">ตำบล/แขวง</label>
                                    <select name="district_id" x-model="selectedDistrict" :disabled="!selectedAmphure"
                                        class="select select-bordered w-full rounded focus:outline-emerald-500 disabled:bg-gray-100">
                                        <option value="">-- เลือกตำบล --</option>
                                        <template x-for="district in districts" :key="district.id">
                                            <option :value="district.id" x-text="district.name_th"></option>
                                        </template>
                                    </select>
                                </div>
                                <div class="form-control">
                                    <label class="label-text text-gray-500 mb-1">รหัสไปรษณีย์</label>
                                    <input type="text" name="zipcode" :value="getZipCode()" readonly
                                        class="input input-bordered w-full rounded bg-gray-50 text-gray-700 font-semibold" />
                                </div>
                            </div>
                            <div class="form-control mt-4">
                                <label class="label-text text-gray-500 mb-1">หมายเหตุการจัดส่ง</label>
                                <textarea name="note" class="textarea textarea-bordered w-full rounded focus:outline-emerald-500 h-24"
                                    placeholder="เช่น ฝากป้อมยาม, โทรหาพี่สาวแทน (ถ้ามี)"></textarea>
                            </div>
                        </div>
                    </form>
                    <div class="px-6 py-4 border-t border-gray-100 flex justify-end gap-3 bg-gray-50">
                        <form method="dialog"><button
                                class="btn btn-ghost text-gray-500 hover:bg-gray-200 font-normal">ยกเลิก</button></form>
                        <button onclick="document.getElementById('form_add_new').submit()"
                            class="btn bg-[#00B900] hover:bg-[#009900] text-white border-none font-normal px-6">บันทึกข้อมูล</button>
                    </div>
                </div>
            </div>
        </dialog>

        
        <div id="loading-overlay" class="fixed inset-0 z-[9999] bg-black/50 flex items-center justify-center hidden">
            <div class="bg-white p-6 rounded-2xl shadow-2xl flex flex-col items-center gap-4 animate-bounce-in">
                <span class="loading loading-spinner loading-lg text-emerald-500 scale-150"></span>
                <p class="text-gray-600 font-semibold text-lg animate-pulse">กำลังประมวลผล...</p>
            </div>
        </div>

    </div>

    <script>
        function showLoading() {
            const loader = document.getElementById('loading-overlay');
            if (loader) loader.classList.remove('hidden');
        }

        // ฟังก์ชัน Confirm Delete ด้วย SweetAlert2
        function confirmDelete(formId) {
            Swal.fire({
                title: 'ยืนยันการลบ?',
                text: "คุณต้องการลบที่อยู่นี้ใช่หรือไม่",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#ef4444', // สีแดง
                cancelButtonColor: '#6b7280', // สีเทา
                confirmButtonText: 'ใช่, ลบเลย!',
                cancelButtonText: 'ยกเลิก'
            }).then((result) => {
                if (result.isConfirmed) {
                    showLoading(); // แสดง Loading
                    document.getElementById(formId).submit(); // ส่ง Form
                }
            })
        }

        function handlePaymentSubmit() {
            const storedId = localStorage.getItem('selected_address_id');
            const defaultId = "<?php echo e($addresses->count() > 0 ? $addresses->first()->id : ''); ?>";
            const finalId = storedId ? storedId : defaultId;

            if (!finalId) {
                Swal.fire({
                    icon: 'error',
                    title: 'กรุณาเลือกที่อยู่',
                    text: 'โปรดเลือกหรือเพิ่มที่อยู่สำหรับจัดส่งสินค้าก่อนดำเนินการต่อ',
                    position: 'center',
                    confirmButtonColor: '#4F46E5'
                });
                return false; // Prevent form submission
            }

            document.getElementById('hidden_address_id').value = finalId;
            showLoading();
            return true;
        }

        function addressDropdown() {
            return {
                selectedProvince: '',
                selectedAmphure: '',
                selectedDistrict: '',
                amphures: [],
                districts: [],
                loadEditData(provinceId, amphureId, districtId) {
                    this.selectedProvince = provinceId;
                    fetch(`/api/amphures/${provinceId}`).then(r => r.json()).then(d => {
                        this.amphures = Array.isArray(d) ? d : (d.data || []);
                        this.selectedAmphure = amphureId;
                        if (amphureId) {
                            fetch(`/api/districts/${amphureId}`).then(r => r.json()).then(d => {
                                this.districts = Array.isArray(d) ? d : (d.data || []);
                                this.selectedDistrict = districtId;
                            });
                        }
                    });
                },
                fetchAmphures() {
                    this.selectedAmphure = '';
                    this.selectedDistrict = '';
                    this.amphures = [];
                    this.districts = [];
                    if (this.selectedProvince) {
                        fetch(`/api/amphures/${this.selectedProvince}`).then(r => r.json()).then(d => {
                            this.amphures = Array.isArray(d) ? d : (d.data || []);
                        });
                    }
                },
                fetchDistricts() {
                    this.selectedDistrict = '';
                    this.districts = [];
                    if (this.selectedAmphure) {
                        fetch(`/api/districts/${this.selectedAmphure}`).then(r => r.json()).then(d => {
                            this.districts = Array.isArray(d) ? d : (d.data || []);
                        });
                    }
                },
                getZipCode() {
                    if (!this.selectedDistrict || this.districts.length === 0) return '';
                    const district = this.districts.find(d => d.id == this.selectedDistrict);
                    return district ? (district.zip_code || district.zipcode) : '';
                }
            }
        }
    </script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layout', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\laravel\salepage-demo-1\resources\views/payment.blade.php ENDPATH**/ ?>