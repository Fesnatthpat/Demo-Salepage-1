<div>
    {{-- This will replace the existing cart badge. --}}
    <span class="badge badge-sm indicator-item bg-red-600 text-white border-none {{ $count > 0 ? '' : 'hidden' }}"
        id="cart-badge">{{ $count }}</span>
</div>