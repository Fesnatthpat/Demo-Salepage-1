<?php $__env->startSection('title', 'ตะกร้าสินค้า | Salepage Demo'); ?>

<?php $__env->startSection('content'); ?>
    <div class="container px-4 mx-auto md:px-8 lg:p-12">
        <div class="p-6 bg-white shadow rounded-lg border-gray-200 md:p-8 lg:p-12">
            <form action="<?php echo e(route('payment.checkout')); ?>" method="GET" id="checkout-form">
                <div class="">
                    
                    <div class="mb-6 border-b border-gray-200 pb-4 flex items-center gap-3">
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(isset($items) && !$items->isEmpty()): ?>
                            <div class="flex items-center">
                                <input type="checkbox" id="select-all"
                                    class="w-5 h-5 text-red-600 rounded border-gray-300 focus:ring-red-500 cursor-pointer"
                                    onclick="toggleAll(this)">
                            </div>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        <h1 class="text-2xl font-bold text-gray-800">ตะกร้าสินค้า</h1>
                    </div>

                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(isset($items) && !$items->isEmpty()): ?>
                        <div id="cart-items-list">
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $items; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoop($loop->index); ?><?php endif; ?>
                                <?php
                                    $quantity = $item->quantity;
                                    $price = $item->price;
                                    $originalPrice = $item->attributes->has('original_price')
                                        ? $item->attributes->original_price
                                        : $price;
                                    $totalPrice = $price * $quantity;
                                    $isFree = $item->attributes->has('is_freebie') && $item->attributes->is_freebie;

                                    $calcOriginalPrice = $isFree ? 0 : $originalPrice;
                                    $totalOriginalPrice = $calcOriginalPrice * $quantity;

                                    $lineDiscount = $totalOriginalPrice - $totalPrice;
                                    $hasDiscount = $lineDiscount > 0;

                                    $displayImage =
                                        $item->attributes->image ?? 'https://via.placeholder.com/150?text=No+Image';
                                ?>

                            <div
                                class="flex flex-col md:flex-row md:items-start md:justify-between border-b border-gray-200 py-6 gap-4">
                                
                                <div class="flex flex-row gap-4 w-full md:w-auto items-start">
                                    <div class="mt-8 md:mt-10">
                                        <input type="checkbox" name="selected_items[]" value="<?php echo e($item->id); ?>" 
                                            data-price="<?php echo e($totalPrice); ?>" data-original-price="<?php echo e($totalOriginalPrice); ?>"
                                            class="item-checkbox w-5 h-5 text-red-600 rounded border-gray-300 focus:ring-red-500 cursor-pointer"
                                            onchange="onItemSelectionChange()">
                                    </div>
                                    <div class="flex-shrink-0">
                                        <img src="<?php echo e($displayImage); ?>" alt="<?php echo e($item->name); ?>"
                                            class="w-20 h-20 object-cover rounded-lg bg-gray-100 border border-gray-100">
                                    </div>
                                    <div class="flex-1 mt-1">
                                        <h1 class="font-bold text-gray-800 text-sm md:text-base break-words">
                                            <?php echo e($item->name); ?></h1>

                                        <p class="text-xs text-gray-500 mt-1">ราคาต่อชิ้น:
                                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($isFree): ?>
                                                <span class="font-bold text-red-600">ฟรี</span>
                                            <?php elseif($hasDiscount): ?>
                                                <s class="text-gray-400">฿<?php echo e(number_format($originalPrice)); ?></s>
                                                <span
                                                    class="font-semibold text-red-600 ml-1">฿<?php echo e(number_format($price)); ?></span>
                                            <?php else: ?>
                                                <span class="text-gray-800">฿<?php echo e(number_format($price)); ?></span>
                                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                        </p>
                                    </div>
                                </div>

                                
                                <div
                                    class="flex flex-row justify-between items-center md:flex-col md:items-end gap-4 w-full md:w-auto mt-2 md:mt-0 pl-9 md:pl-0">
                                    <div class="flex flex-col items-end">
                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($isFree): ?>
                                            <div class="text-2xl font-bold text-red-600">ฟรี</div>
                                        <?php else: ?>
                                            <div class="text-2xl font-bold text-red-600">
                                                ฿<?php echo e(number_format($totalPrice)); ?></div>
                                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                    </div>
                                    <div class="flex flex-col sm:flex-row items-end sm:items-center gap-3">
                                        <div class="flex items-center border border-gray-300 rounded h-10 md:h-12 bg-white">
                                            <button type="button"
                                                class="cart-action-btn px-3 text-gray-600 hover:bg-gray-100 h-full flex items-center"
                                                data-url="<?php echo e(route('cart.update', ['id' => $item->id, 'action' => 'decrease'])); ?>"
                                                data-method="PATCH">-</button>
                                            <span
                                                class="font-bold text-gray-700 text-sm md:text-base w-12 text-center"><?php echo e($quantity); ?></span>
                                            <button type="button"
                                                class="cart-action-btn px-3 text-gray-600 hover:bg-gray-100 h-full flex items-center"
                                                data-url="<?php echo e(route('cart.update', ['id' => $item->id, 'action' => 'increase'])); ?>"
                                                data-method="PATCH">+</button>
                                        </div>
                                        <button type="button"
                                            class="cart-action-btn text-red-500 hover:text-red-700 text-sm md:btn md:btn-ghost md:btn-sm"
                                            data-url="<?php echo e(route('cart.remove', $item->id)); ?>"
                                            data-method="DELETE">ลบ</button>
                                    </div>
                                </div>
                            </div>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>

                        
                        <div class="flex flex-col lg:flex-row justify-end gap-5 mt-10">
                            <div class="w-full lg:w-[400px]">
                                <div class="flex justify-between mt-5 text-base text-gray-600">
                                    <div>ยอดรวมสินค้า (<span id="selected-count"><?php echo e(count($items)); ?></span> รายการ)</div>
                                    <div class="font-medium">฿<span
                                            id="subtotal-display"><?php echo e(number_format($subTotal)); ?></span></div>
                                </div>
                                <div class="flex justify-between mt-2 text-base text-red-500 font-semibold">
                                    <div>ส่วนลดโปรโมชั่น</div>
                                    <div class="font-medium">-฿<span
                                            id="discount-display"><?php echo e(number_format($totalDiscount)); ?></span>
                                    </div>
                                </div>
                                <div class="border-t border-gray-200 my-4"></div>
                                <div class="flex justify-between items-center mb-6">
                                    <h1 class="font-bold text-xl text-gray-800">ยอดสุทธิ</h1>
                                    <h1 class="text-red-600 font-bold text-3xl">฿<span
                                            id="total-display"><?php echo e(number_format($total)); ?></span></h1>
                                </div>

                                
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(isset($freebieLimit) && $freebieLimit > 0 && isset($giftableProducts) && $giftableProducts->count() > 0): ?>
                                    <div class="mt-2 mb-6 p-5 bg-gradient-to-br from-pink-50 to-red-50 rounded-2xl border border-pink-100 shadow-sm" id="gift-selection-area">
                                        <div class="flex items-center justify-between mb-4">
                                            <div class="flex items-center gap-2">
                                                <span class="flex items-center justify-center w-6 h-6 rounded-full bg-pink-600 text-white shadow-sm">
                                                    <i class="fas fa-gift text-[10px]"></i>
                                                </span>
                                                <h3 class="font-bold text-pink-800 text-sm">เลือกของแถมของคุณ</h3>
                                            </div>
                                            <span class="text-[10px] font-bold bg-white text-pink-600 border border-pink-200 px-2.5 py-1 rounded-full shadow-sm">
                                                สิทธิ์คงเหลือ: <span id="gift-limit-display"><?php echo e($freebieLimit); ?></span> ชิ้น
                                            </span>
                                        </div>
                                        
                                        <div class="grid grid-cols-2 gap-3" id="gift-pool">
                                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $giftableProducts; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $gift): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoop($loop->index); ?><?php endif; ?>
                                                <div class="relative group">
                                                    <input type="checkbox" name="selected_freebies[]" value="<?php echo e($gift->pd_sp_id); ?>" 
                                                        class="gift-checkbox hidden" id="cart-gift-<?php echo e($gift->pd_sp_id); ?>"
                                                        onchange="validateGiftSelection(this)">
                                                    <label for="cart-gift-<?php echo e($gift->pd_sp_id); ?>" 
                                                        class="gift-label block p-2 bg-white border-2 border-gray-100 rounded-xl cursor-pointer transition-all hover:border-pink-300 hover:shadow-md">
                                                        <div class="aspect-square w-full rounded-lg overflow-hidden bg-gray-50 mb-2">
                                                            <img src="<?php echo e($gift->cover_image_url); ?>" class="w-full h-full object-cover">
                                                        </div>
                                                        <p class="text-[10px] font-bold text-gray-700 truncate px-1"><?php echo e($gift->pd_sp_name); ?></p>
                                                        
                                                        
                                                        <div class="selected-overlay absolute inset-0 bg-pink-600/10 rounded-xl border-2 border-pink-500 items-center justify-center hidden">
                                                            <div class="bg-pink-500 text-white rounded-full w-6 h-6 flex items-center justify-center shadow-lg transform scale-110">
                                                                <i class="fas fa-check text-xs"></i>
                                                            </div>
                                                        </div>
                                                    </label>
                                                </div>
                                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                                        </div>
                                        
                                        <div class="mt-4 pt-3 border-t border-pink-100 flex justify-center">
                                            <p class="text-[11px] font-bold text-pink-500 bg-white px-4 py-1 rounded-full shadow-sm border border-pink-50" id="gift-count-text">
                                                เลือกไปแล้ว 0 / <?php echo e($freebieLimit); ?> ชิ้น
                                            </p>
                                        </div>
                                    </div>
                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                                <button type="submit" id="checkout-btn"
                                    class="btn bg-red-600 hover:bg-red-700 border-none text-white w-full text-lg h-12 shadow-lg shadow-red-600/20 transition-all active:scale-95 font-black uppercase tracking-wider">
                                    ชำระเงิน <i class="fas fa-arrow-right ml-2"></i>
                                </button>
                            </div>
                        </div>
                    <?php else: ?>
                        <div class="text-center py-20 bg-gray-50 rounded-lg">
                            <h2 class="text-2xl font-bold text-gray-400 mb-2">ตะกร้าว่างเปล่า</h2>
                            <a href="<?php echo e(route('allproducts')); ?>"
                                class="btn bg-red-600 hover:bg-red-700 border-none text-white mt-4">ไปเลือกซื้อสินค้า</a>
                        </div>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </div>
            </form>
        </div>
    </div>

    <script>
        const STORAGE_KEY = 'cart_selected_items';

        function getSavedSelectedItems() {
            const saved = localStorage.getItem(STORAGE_KEY);
            return saved ? JSON.parse(saved) : null;
        }

        function saveSelectedItems() {
            const selectedIds = Array.from(document.querySelectorAll('.item-checkbox:checked'))
                .map(cb => cb.value);
            localStorage.setItem(STORAGE_KEY, JSON.stringify(selectedIds));
        }

        function numberWithCommas(x) {
            return x.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
        }

        function onItemSelectionChange() {
            // ดึงรายการที่เลือกทั้งหมด
            const selectedIds = Array.from(document.querySelectorAll('.item-checkbox:checked'))
                .map(cb => cb.value);
            
            // รีเฟรชหน้าเว็บพร้อมส่งรายการที่เลือกลูกค้าจะได้เห็นของแถมที่อัปเดตตามจริง
            const url = new URL(window.location.href);
            // ส่งแม้จะเป็นค่าว่าง (เช่น ?) เพื่อให้ Controller รู้ว่าเลือกเป็น 0
            url.searchParams.set('selected_items', selectedIds.join(','));
            window.location.href = url.toString();
        }

        function calculateTotal() {
            const checkboxes = document.querySelectorAll('.item-checkbox');
            const checkedBoxes = Array.from(checkboxes).filter(cb => cb.checked);
            const count = checkedBoxes.length;
            
            // บันทึกสถานะการเลือกลง localStorage
            saveSelectedItems();

            // อัปเดตตัวเลือก "เลือกทั้งหมด"
            const selectAll = document.getElementById('select-all');
            if (selectAll) {
                selectAll.checked = (checkboxes.length > 0 && checkboxes.length === count);
            }

            // อัปเดตจำนวนที่เลือกใน UI
            const selectedCountEl = document.getElementById('selected-count');
            if (selectedCountEl) selectedCountEl.innerText = count;

            // จัดการปุ่มชำระเงิน
            const btn = document.getElementById('checkout-btn');
            if (btn) {
                btn.disabled = (count === 0);
                if (count === 0) {
                    btn.classList.add('opacity-50', 'cursor-not-allowed');
                } else {
                    btn.classList.remove('opacity-50', 'cursor-not-allowed');
                }
            }
        }

        function validateGiftSelection(checkbox) {
            const limit = parseInt(document.getElementById('gift-limit-display')?.innerText || 0);
            const checkedGifts = document.querySelectorAll('.gift-checkbox:checked');
            
            // อัปเดต UI ของ Label (Overlay)
            const label = checkbox.nextElementSibling;
            const overlay = label.querySelector('.selected-overlay');
            
            if (checkbox.checked) {
                if (checkedGifts.length > limit) {
                    checkbox.checked = false;
                    Swal.fire({
                        icon: 'warning',
                        title: 'เลือกเกินจำนวน',
                        text: `คุณสามารถเลือกของแถมได้สูงสุด ${limit} ชิ้น`,
                        confirmButtonColor: '#ec4899',
                    });
                    return;
                }
                overlay.classList.remove('hidden');
                overlay.classList.add('flex');
                label.classList.add('border-pink-500', 'bg-pink-50');
            } else {
                overlay.classList.add('hidden');
                overlay.classList.remove('flex');
                label.classList.remove('border-pink-500', 'bg-pink-50');
            }

            // อัปเดตตัวเลขจำนวนที่เลือก
            const countText = document.getElementById('gift-count-text');
            if (countText) {
                countText.innerText = `เลือกไปแล้ว ${checkedGifts.length} / ${limit} ชิ้น`;
            }
        }

        function toggleAll(source) {
            document.querySelectorAll('.item-checkbox').forEach(cb => cb.checked = source.checked);
            onItemSelectionChange();
        }

        document.addEventListener("DOMContentLoaded", function() {
            const urlParams = new URLSearchParams(window.location.search);
            const selectedFromUrl = urlParams.get('selected_items');
            const savedIds = getSavedSelectedItems();
            const checkboxes = document.querySelectorAll('.item-checkbox');
            
            if (selectedFromUrl !== null) {
                // ถ้ามีใน URL (หลัง Refresh หรือมาจากการคลิก)
                const ids = selectedFromUrl ? selectedFromUrl.split(',').filter(id => id !== '') : [];
                checkboxes.forEach(cb => {
                    cb.checked = ids.includes(cb.value);
                });
            } else if (savedIds !== null) {
                // ถ้ามาหน้าตะกร้าใหม่แต่มีประวัติการเลือกในเครื่อง (กรณีมาจากหน้าอื่น)
                checkboxes.forEach(cb => {
                    cb.checked = savedIds.includes(cb.value);
                });
            } else {
                // ครั้งแรกสุดๆ (ไม่มีประวัติเลย): ให้ติ๊กถูกทั้งหมดตามพฤติกรรมมาตรฐาน
                checkboxes.forEach(cb => cb.checked = true);
            }

            // บันทึกสถานะล่าสุดและคำนวณเงิน
            calculateTotal();
            
            const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

            document.querySelectorAll('.cart-action-btn').forEach(btn => {
                btn.addEventListener('click', function(e) {
                    e.preventDefault();
                    const form = document.createElement('form');
                    form.method = 'POST';
                    form.action = this.dataset.url;
                    form.style.display = 'none';

                    const inputCsrf = document.createElement('input');
                    inputCsrf.type = 'hidden';
                    inputCsrf.name = '_token';
                    inputCsrf.value = csrfToken;
                    form.appendChild(inputCsrf);

                    if (this.dataset.method !== 'POST') {
                        const inputMethod = document.createElement('input');
                        inputMethod.type = 'hidden';
                        inputMethod.name = '_method';
                        inputMethod.value = this.dataset.method;
                        form.appendChild(inputMethod);
                    }
                    document.body.appendChild(form);
                    form.submit();
                });
            });
        });
    </script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layout', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\laravel\salepage-demo-1\resources\views/cart.blade.php ENDPATH**/ ?>