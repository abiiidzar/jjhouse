@extends('layouts.admin')

@section('title', 'Edit Product')
@section('header', 'Edit Product')
@section('subheader', 'Update product information')

@section('content')

{{-- Validation Errors --}}
@if ($errors->any())
<div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg mb-6">
    <p class="font-semibold mb-1">⚠ Terdapat kesalahan:</p>
    <ul class="list-disc list-inside text-sm space-y-1">
        @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
        @endforeach
    </ul>
</div>
@endif

@if (session('success'))
<div class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-lg mb-6">
    ✓ {{ session('success') }}
</div>
@endif

<form action="{{ route('admin.products.update', $product) }}" method="POST" enctype="multipart/form-data" class="space-y-6">
    @csrf @method('PUT')

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        {{-- ─── Main Form ─── --}}
        <div class="lg:col-span-2 space-y-6">

            {{-- Basic Info --}}
            <div class="bg-white rounded-lg shadow-sm p-6">
                <h3 class="text-lg font-semibold mb-4 text-gray-800">📝 Basic Information</h3>
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Nama Produk <span class="text-red-500">*</span></label>
                        <input type="text" name="name" value="{{ old('name', $product->name) }}" required
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 @error('name') border-red-400 @enderror">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Kategori <span class="text-red-500">*</span></label>
                        <select name="category_id" required
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <option value="">-- Pilih Kategori --</option>
                            @foreach($categories as $category)
                            <option value="{{ $category->id }}" {{ $product->category_id == $category->id ? 'selected' : '' }}>
                                {{ $category->name }}
                            </option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Deskripsi Singkat</label>
                        <input type="text" name="short_description" value="{{ old('short_description', $product->short_description) }}"
                            maxlength="500" placeholder="Tagline singkat produk..."
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Deskripsi Lengkap <span class="text-red-500">*</span></label>
                        <textarea name="description" rows="6" required
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 @error('description') border-red-400 @enderror">{{ old('description', $product->description) }}</textarea>
                    </div>
                </div>
            </div>

            {{-- Pricing & Stock --}}
            <div class="bg-white rounded-lg shadow-sm p-6">
                <h3 class="text-lg font-semibold mb-4 text-gray-800">💰 Pricing & Stock</h3>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Harga Jual (Rp) <span class="text-red-500">*</span></label>
                        <input type="number" name="price" value="{{ old('price', $product->price) }}" required step="1000" min="0"
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 @error('price') border-red-400 @enderror">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">HPP / Cost Price (Rp)</label>
                        <input type="number" name="cost_price" value="{{ old('cost_price', $product->cost_price) }}" step="1000" min="0"
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                </div>
                <div class="mt-4 p-3 bg-blue-50 rounded-lg text-sm text-blue-700">
                    💡 <strong>HPP</strong> = biaya produksi/pembelian. Keuntungan = Harga Jual − HPP.
                </div>

                {{-- Sizes --}}
                <div class="mt-5">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Ukuran & Stok</label>
                    <div id="sizes-container" class="space-y-2">
                        @foreach($product->sizes as $size)
                        <div class="size-row flex gap-3 items-center">
                            <select name="sizes[]" class="w-28 border border-gray-300 rounded-lg px-3 py-2 text-sm">
                                @foreach(['XS','S','M','L','XL','XXL'] as $s)
                                <option value="{{ $s }}" {{ $size->size == $s ? 'selected' : '' }}>{{ $s }}</option>
                                @endforeach
                            </select>
                            <input type="number" name="stock_per_size[]" value="{{ $size->stock }}"
                                placeholder="Stok" min="0"
                                class="w-24 border border-gray-300 rounded-lg px-3 py-2 text-sm">
                            <button type="button" class="remove-size text-red-500 hover:text-red-700 text-sm font-medium">✕ Hapus</button>
                        </div>
                        @endforeach
                    </div>
                    <button type="button" id="add-size"
                        class="mt-3 text-sm text-blue-600 hover:text-blue-800 font-medium">+ Tambah Ukuran</button>
                </div>
            </div>
        </div>

        {{-- ─── Sidebar ─── --}}
        <div class="space-y-6">

            {{-- Images --}}
            <div class="bg-white rounded-lg shadow-sm p-6">
                <h3 class="text-lg font-semibold mb-4 text-gray-800">🖼️ Gambar Produk</h3>

                {{-- Current Images --}}
                @if($product->images->isNotEmpty())
                <div class="mb-4">
                    <p class="text-xs font-medium text-gray-500 uppercase tracking-wide mb-2">Gambar Saat Ini</p>
                    <div class="grid grid-cols-3 gap-2">
                        @foreach($product->images as $image)
                        <div class="relative group rounded-lg overflow-hidden border-2
                            {{ $image->is_primary ? 'border-blue-400' : 'border-gray-200' }}"
                            id="img-wrapper-{{ $image->id }}">
                            <img src="{{ $image->image_path }}"
                                 alt="{{ $product->name }}"
                                 loading="lazy"
                                 decoding="async"
                                 class="w-full h-20 object-cover">
                            @if($image->is_primary)
                            <div class="absolute bottom-0 left-0 right-0 bg-blue-500/80 text-white text-[9px] text-center py-0.5 font-bold">
                                UTAMA
                            </div>
                            @endif
                            {{-- Delete button --}}
                            <button type="button"
                                onclick="deleteImage({{ $image->id }}, this)"
                                class="absolute inset-0 flex items-center justify-center bg-black/0 hover:bg-black/50 text-white text-xs font-bold opacity-0 group-hover:opacity-100 transition-all">
                                🗑 Hapus
                            </button>
                        </div>
                        @endforeach
                    </div>
                </div>
                @endif

                {{-- Upload Primary Image --}}
                <div class="mb-4">
                    <label class="block text-xs font-medium text-gray-600 mb-1">
                        {{ $product->primaryImage ? 'Ganti Foto Utama' : 'Upload Foto Utama *' }}
                    </label>
                    <div class="border-2 border-dashed border-blue-300 rounded-xl p-4 text-center cursor-pointer hover:bg-blue-50 transition"
                         onclick="document.getElementById('primary_image_input').click()">
                        <img id="primary_preview" src="#" alt="preview"
                             class="mx-auto h-28 object-contain rounded mb-2 hidden">
                        <p id="primary_placeholder" class="text-gray-400 text-sm">
                            📷 Klik untuk upload foto utama
                        </p>
                        <p class="text-gray-300 text-xs mt-1">PNG, JPG, WebP — Max 2MB</p>
                    </div>
                    <input type="file" id="primary_image_input" name="primary_image"
                           accept="image/*" class="hidden"
                           onchange="previewImage(this, 'primary_preview', 'primary_placeholder')">
                    @error('primary_image')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Upload Additional Images --}}
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1">Tambah Foto Lain (Multiple)</label>
                    <div class="border-2 border-dashed border-gray-300 rounded-xl p-4 text-center cursor-pointer hover:bg-gray-50 transition"
                         onclick="document.getElementById('additional_images_input').click()">
                        <div id="additional_previews" class="flex flex-wrap gap-2 justify-center mb-2 hidden"></div>
                        <p id="additional_placeholder" class="text-gray-400 text-sm">
                            🗂 Klik untuk upload foto tambahan
                        </p>
                        <p class="text-gray-300 text-xs mt-1">Bisa pilih beberapa sekaligus</p>
                    </div>
                    <input type="file" id="additional_images_input" name="additional_images[]"
                           accept="image/*" multiple class="hidden"
                           onchange="previewMultiple(this, 'additional_previews', 'additional_placeholder')">
                </div>
            </div>

            {{-- Pre-order Settings --}}
            <div class="bg-white rounded-lg shadow-sm p-6">
                <h3 class="text-lg font-semibold mb-4 text-gray-800">⏳ Pre-order Settings</h3>
                <label class="flex items-center gap-2 cursor-pointer">
                    <input type="checkbox" name="is_preorder" value="1" id="is_preorder"
                        {{ $product->is_preorder ? 'checked' : '' }}
                        class="w-4 h-4 accent-yellow-500">
                    <span class="text-sm text-gray-700">Produk ini adalah Pre-order</span>
                </label>
                <div id="preorder-fields" class="mt-3 space-y-3 {{ $product->is_preorder ? '' : 'hidden' }}">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Batas Akhir Pre-order</label>
                        <input type="datetime-local" name="preorder_end_date"
                            value="{{ $product->preorder_end_date ? $product->preorder_end_date->format('Y-m-d\TH:i') : '' }}"
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Estimasi Hari Pengiriman</label>
                        <input type="number" name="preorder_estimated_days"
                            value="{{ $product->preorder_estimated_days }}" min="1"
                            placeholder="Contoh: 14"
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">
                    </div>
                </div>
            </div>

            {{-- Status --}}
            <div class="bg-white rounded-lg shadow-sm p-6">
                <h3 class="text-lg font-semibold mb-4 text-gray-800">🔧 Status</h3>
                <div class="space-y-3">
                    <label class="flex items-center gap-3 cursor-pointer">
                        <input type="checkbox" name="is_active" value="1"
                            {{ $product->is_active ? 'checked' : '' }}
                            class="w-4 h-4 accent-green-600">
                        <span class="text-sm text-gray-700">Aktif (ditampilkan di toko)</span>
                    </label>
                    <label class="flex items-center gap-3 cursor-pointer">
                        <input type="checkbox" name="is_featured_home" value="1"
                            {{ $product->is_featured_home ? 'checked' : '' }}
                            class="w-4 h-4 accent-blue-600">
                        <span class="text-sm text-gray-700">📌 Featured di Home</span>
                    </label>
                </div>
            </div>

            {{-- Buttons --}}
            <div class="flex gap-3">
                <button type="submit"
                    class="flex-1 bg-gray-900 text-white px-4 py-2.5 rounded-lg hover:bg-gray-800 transition font-medium">
                    💾 Update Produk
                </button>
                <a href="{{ route('admin.products.index') }}"
                    class="px-4 py-2.5 border border-gray-300 rounded-lg hover:bg-gray-50 transition text-sm text-center">
                    Batal
                </a>
            </div>
        </div>
    </div>
</form>

@push('scripts')
<script>
    // Toggle pre-order fields
    document.getElementById('is_preorder').addEventListener('change', function() {
        document.getElementById('preorder-fields').classList.toggle('hidden', !this.checked);
    });

    // Add size row
    document.getElementById('add-size').addEventListener('click', function() {
        const container = document.getElementById('sizes-container');
        const row = document.createElement('div');
        row.className = 'size-row flex gap-3 items-center';
        row.innerHTML = `
            <select name="sizes[]" class="w-28 border border-gray-300 rounded-lg px-3 py-2 text-sm">
                <option value="XS">XS</option><option value="S">S</option>
                <option value="M">M</option><option value="L">L</option>
                <option value="XL">XL</option><option value="XXL">XXL</option>
            </select>
            <input type="number" name="stock_per_size[]" placeholder="Stok" min="0"
                class="w-24 border border-gray-300 rounded-lg px-3 py-2 text-sm">
            <button type="button" class="remove-size text-red-500 hover:text-red-700 text-sm font-medium">✕ Hapus</button>
        `;
        container.appendChild(row);
        row.querySelector('.remove-size').addEventListener('click', () => row.remove());
    });

    // Bind remove for existing rows
    document.querySelectorAll('.remove-size').forEach(btn => {
        btn.addEventListener('click', function() {
            this.closest('.size-row').remove();
        });
    });

    // Single image preview
    function previewImage(input, previewId, placeholderId) {
        const preview = document.getElementById(previewId);
        const placeholder = document.getElementById(placeholderId);
        if (input.files && input.files[0]) {
            const reader = new FileReader();
            reader.onload = e => {
                preview.src = e.target.result;
                preview.classList.remove('hidden');
                placeholder.classList.add('hidden');
            };
            reader.readAsDataURL(input.files[0]);
        }
    }

    // Multiple image previews
    function previewMultiple(input, previewsId, placeholderId) {
        const container = document.getElementById(previewsId);
        const placeholder = document.getElementById(placeholderId);
        container.innerHTML = '';
        if (input.files && input.files.length > 0) {
            Array.from(input.files).forEach(file => {
                const reader = new FileReader();
                reader.onload = e => {
                    const img = document.createElement('img');
                    img.src = e.target.result;
                    img.className = 'h-16 w-16 object-cover rounded border';
                    container.appendChild(img);
                };
                reader.readAsDataURL(file);
            });
            container.classList.remove('hidden');
            placeholder.textContent = `✓ ${input.files.length} foto dipilih`;
        }
    }

    // Delete image via AJAX
    function deleteImage(imageId, btn) {
        if (!confirm('Hapus gambar ini?')) return;

        fetch(`/admin/products/image/${imageId}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json',
            }
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                const wrapper = document.getElementById(`img-wrapper-${imageId}`);
                if (wrapper) {
                    wrapper.style.transition = 'opacity 0.3s';
                    wrapper.style.opacity = '0';
                    setTimeout(() => wrapper.remove(), 300);
                }
            }
        })
        .catch(() => alert('Gagal menghapus gambar. Coba lagi.'));
    }
</script>
@endpush
@endsection
