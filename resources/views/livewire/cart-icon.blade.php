<div>
    {{-- This will replace the existing cart badge. --}}
    <span class="badge badge-sm indicator-item bg-white text-red-600 border-none {{ $count > 0 ? '' : 'hidden' }}"
        id="cart-badge">{{ $count }}</span>
</div>