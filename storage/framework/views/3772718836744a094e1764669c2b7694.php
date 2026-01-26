<?php $__env->startSection('title', 'ตะกร้าสินค้า | Salepage Demo'); ?>

<?php $__env->startSection('content'); ?>
    <div class="container px-4 mx-auto md:px-8 lg:p-12">
        <div class="p-6 bg-white shadow rounded-lg border-gray-200 md:p-8 lg:p-12">
            <form action="<?php echo e(route('payment.checkout')); ?>" method="GET" id="checkout-form">
                <div class="">
                    
                    <div class="mb-6 border-b border-gray-200 pb-4 flex items-center gap-3">
                        <?php if(isset($items) && !$items->isEmpty()): ?>
                            <div class="flex items-center">
                                <input type="checkbox" id="select-all" checked
                                    class="w-5 h-5 text-emerald-600 rounded border-gray-300 focus:ring-emerald-500 cursor-pointer"
                                    onclick="toggleAll(this)">
                            </div>
                        <?php endif; ?>
                        <h1 class="text-2xl font-bold text-gray-800">ตะกร้าสินค้า</h1>
                    </div>

                    <?php if(isset($items) && !$items->isEmpty()): ?>
                        <?php
                            $summaryTotalPrice = 0;
                            $summaryTotalOriginal = 0;
                        ?>

                        <?php $__currentLoopData = $items; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <?php
                                $quantity = $item->quantity;
                                $price = $item->price;
                                $originalPrice = $item->attributes->has('original_price')
                                    ? $item->attributes->original_price
                                    : $price;
                                $totalPrice = $price * $quantity;
                                $isFree = $item->attributes->has('is_freebie') && $item->attributes->is_freebie;

                                // คิดราคาต้นเป็น 0 ถ้าเป็นของแถม เพื่อไม่ให้ยอดรวมเพี้ยน
                                $calcOriginalPrice = $isFree ? 0 : $originalPrice;
                                $totalOriginalPrice = $calcOriginalPrice * $quantity;

                                $lineDiscount = $totalOriginalPrice - $totalPrice;
                                $hasDiscount = $lineDiscount > 0;

                                $summaryTotalPrice += $totalPrice;
                                $summaryTotalOriginal += $totalOriginalPrice;

                                $productModel = $products[$item->id] ?? null;
                                $displayImage = $productModel
                                    ? $productModel->cover_image_url
                                    : 'https://via.placeholder.com/150?text=No+Image';
                            ?>

                            <div
                                class="flex flex-col md:flex-row md:items-start md:justify-between border-b border-gray-200 py-6 gap-4">
                                
                                <div class="flex flex-row gap-4 w-full md:w-auto items-start">
                                    <div class="mt-8 md:mt-10">
                                        <input type="checkbox" name="selected_items[]" value="<?php echo e($item->id); ?>" checked
                                            data-price="<?php echo e($totalPrice); ?>" data-original-price="<?php echo e($totalOriginalPrice); ?>"
                                            class="item-checkbox w-5 h-5 text-emerald-600 rounded border-gray-300 focus:ring-emerald-500 cursor-pointer"
                                            onchange="calculateTotal()">
                                    </div>
                                    <div class="flex-shrink-0">
                                        <img src="<?php echo e($displayImage); ?>" alt="<?php echo e($item->name); ?>"
                                            class="w-20 h-20 object-cover rounded-lg bg-gray-100 border border-gray-100">
                                    </div>
                                    <div class="flex-1 mt-1">
                                        <h1 class="font-bold text-gray-800 text-sm md:text-base"><?php echo e($item->name); ?></h1>
                                        <p class="text-xs text-gray-500">Code: <?php echo e($item->attributes->pd_code ?? '-'); ?></p>
                                        <p class="text-xs text-gray-500 mt-1">ราคาต่อชิ้น:
                                            <?php if($isFree): ?>
                                                <span class="font-bold text-red-600">ฟรี</span>
                                            <?php elseif($hasDiscount): ?>
                                                <s class="text-gray-400">฿<?php echo e(number_format($originalPrice)); ?></s>
                                                <span
                                                    class="font-semibold text-red-600 ml-1">฿<?php echo e(number_format($price)); ?></span>
                                            <?php else: ?>
                                                <span class="text-gray-800">฿<?php echo e(number_format($price)); ?></span>
                                            <?php endif; ?>
                                        </p>
                                    </div>
                                </div>

                                
                                <div
                                    class="flex flex-row justify-between items-center md:flex-col md:items-end gap-4 w-full md:w-auto mt-2 md:mt-0 pl-9 md:pl-0">
                                    <div class="flex flex-col items-end">
                                        <?php if($isFree): ?>
                                            <div class="text-2xl font-bold text-red-600">ฟรี</div>
                                        <?php else: ?>
                                            <div class="text-2xl font-bold text-emerald-600">
                                                ฿<?php echo e(number_format($totalPrice)); ?></div>
                                        <?php endif; ?>
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
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

                        
                        <?php if(isset($applicablePromotions) && $applicablePromotions->isNotEmpty() && $giftableProducts->isNotEmpty()): ?>
                            <div class="mt-8 mb-6">
                                <div class="p-6 bg-emerald-50 border-2 border-dashed border-emerald-200 rounded-lg">
                                    <h2 class="text-xl font-bold text-emerald-800 mb-4">🎉 คุณได้รับสิทธิ์เลือกของแถม</h2>
                                    <form action="<?php echo e(route('cart.addFreebies')); ?>" method="POST">
                                        <?php echo csrf_field(); ?>
                                        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                                            <?php $__currentLoopData = $giftableProducts; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $gift): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                <label
                                                    class="relative flex flex-col items-center p-3 border border-gray-200 rounded-lg cursor-pointer hover:bg-white hover:border-emerald-400 bg-white/50 transition-all">
                                                    <input type="checkbox" name="selected_freebies[]"
                                                        value="<?php echo e($gift->pd_sp_id); ?>"
                                                        class="absolute top-2 right-2 w-5 h-5 text-emerald-600 rounded border-gray-300 focus:ring-emerald-500">
                                                    <img src="<?php echo e($gift->cover_image_url ?? 'https://via.placeholder.com/150'); ?>"
                                                        class="w-20 h-20 object-cover rounded bg-white">
                                                    <p class="text-xs text-center mt-2 font-medium"><?php echo e($gift->pd_sp_name); ?>

                                                    </p>
                                                    <span
                                                        class="text-[10px] font-bold text-white bg-red-500 px-2 py-0.5 rounded-full mt-1">ฟรี</span>
                                                </label>
                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                        </div>
                                        <button type="submit"
                                            class="btn btn-primary mt-4 w-full md:w-auto">ยืนยันของแถม</button>
                                    </form>
                                </div>
                            </div>
                        <?php endif; ?>

                        
                        <div class="flex flex-col lg:flex-row justify-end gap-5 mt-10">
                            <div class="w-full lg:w-[400px]">
                                <div class="flex justify-between mt-5 text-base text-gray-600">
                                    <div>ยอดรวมสินค้า (<span id="selected-count"><?php echo e(count($items)); ?></span> รายการ)</div>
                                    <div class="font-medium">฿<span
                                            id="subtotal-display"><?php echo e(number_format($summaryTotalOriginal)); ?></span></div>
                                </div>
                                <div class="flex justify-between mt-2 text-base text-red-500">
                                    <div>ส่วนลดรวม</div>
                                    <div class="font-medium">-฿<span
                                            id="discount-display"><?php echo e(number_format($summaryTotalOriginal - $summaryTotalPrice)); ?></span>
                                    </div>
                                </div>
                                <div class="border-t border-gray-200 my-4"></div>
                                <div class="flex justify-between items-center mb-6">
                                    <h1 class="font-bold text-xl text-gray-800">ยอดสุทธิ</h1>
                                    <h1 class="text-emerald-600 font-bold text-3xl">฿<span
                                            id="total-display"><?php echo e(number_format($summaryTotalPrice)); ?></span></h1>
                                </div>
                                <button type="submit" id="checkout-btn"
                                    class="btn btn-success text-white w-full text-lg h-12">ชำระเงิน</button>
                            </div>
                        </div>
                    <?php else: ?>
                        <div class="text-center py-20 bg-gray-50 rounded-lg">
                            <h2 class="text-2xl font-bold text-gray-400 mb-2">ตะกร้าว่างเปล่า</h2>
                            <a href="<?php echo e(route('allproducts')); ?>" class="btn btn-primary mt-4">ไปเลือกซื้อสินค้า</a>
                        </div>
                    <?php endif; ?>
                </div>
            </form>
        </div>
    </div>

    <script>
        function numberWithCommas(x) {
            return x.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
        }

        function calculateTotal() {
            let totalSale = 0,
                totalOrig = 0;
            let count = 0;
            document.querySelectorAll('.item-checkbox:checked').forEach(cb => {
                totalSale += parseFloat(cb.dataset.price) || 0;
                totalOrig += parseFloat(cb.dataset.originalPrice) || 0;
                count++;
            });

            let disc = totalOrig - totalSale;
            if (disc < 0) disc = 0;

            document.getElementById('total-display').innerText = numberWithCommas(totalSale);
            document.getElementById('subtotal-display').innerText = numberWithCommas(totalOrig);
            document.getElementById('discount-display').innerText = numberWithCommas(disc);
            document.getElementById('selected-count').innerText = count;

            const btn = document.getElementById('checkout-btn');
            if (btn) btn.disabled = (count === 0);
        }

        function toggleAll(source) {
            document.querySelectorAll('.item-checkbox').forEach(cb => cb.checked = source.checked);
            calculateTotal();
        }

        document.addEventListener("DOMContentLoaded", function() {
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