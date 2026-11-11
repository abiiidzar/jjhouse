<div class="product-filter">
    <div class="filters mb-4">
        <div class="row">
            <div class="col-md-3">
                <input
                    type="text"
                    class="form-control"
                    placeholder="Search products..."
                    wire:model.live="search"
                >
            </div>
            <div class="col-md-3">
                <select class="form-control" wire:model.live="category">
                    <option value="">All Categories</option>
                    @foreach($categories as $cat)
                        <option value="{{ $cat->slug }}">{{ $cat->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <select class="form-control" wire:model.live="sort">
                    <option value="latest">Latest</option>
                    <option value="oldest">Oldest</option>
                    <option value="price_asc">Price: Low to High</option>
                    <option value="price_desc">Price: High to Low</option>
                    <option value="name_asc">Name: A to Z</option>
                </select>
            </div>
            <div class="col-md-3">
                <button class="btn btn-secondary w-100" wire:click="resetFilters">Reset Filters</button>
            </div>
        </div>
    </div>

    <div class="products-grid">
        <div class="row">
            @forelse($products as $product)
                <div class="col-md-4 mb-4">
                    <div class="card product-card">
                        @if($product->primaryImage)
                            <img src="{{ $product->primaryImage->path }}" class="card-img-top" alt="{{ $product->name }}" loading="lazy" decoding="async">
                        @endif
                        <div class="card-body">
                            <h5 class="card-title">{{ $product->name }}</h5>
                            <p class="card-text text-muted">{{ Str::limit($product->short_description, 50) }}</p>
                            <p class="card-text"><strong>${{ number_format($product->price, 2) }}</strong></p>
                            <a href="#" class="btn btn-primary btn-sm">View Details</a>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-12">
                    <p class="text-center text-muted">No products found.</p>
                </div>
            @endforelse
        </div>
    </div>

    @if($products->hasPages())
        <div class="d-flex justify-content-center mt-4">
            {{ $products->links() }}
        </div>
    @endif
</div>
