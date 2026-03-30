<div>
    {{-- This will replace the existing cart badge. --}}
    <span class="cart-badge-count badge badge-sm indicator-item bg-white text-red-600 border-none {{ $count > 0 ? '' : 'hidden' }}">
        {{ $count }}
    </span>
</div>