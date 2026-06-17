<div class="wishlist-button">
    <button
        class="btn btn-sm {{ $isWishlisted ? 'btn-danger' : 'btn-outline-danger' }}"
        wire:click="toggleWishlist"
        title="{{ $isWishlisted ? 'Remove from Wishlist' : 'Add to Wishlist' }}"
    >
        <i class="fas fa-heart"></i>
        {{ $wishlistCount }}
    </button>
</div>
