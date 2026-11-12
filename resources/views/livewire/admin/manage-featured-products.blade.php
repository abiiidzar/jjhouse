<div>
    {{-- ─── Notification Toast ─── --}}
    <div
        x-data="{ show: false, message: '', type: 'success' }"
        x-on:notify.window="show = true; message = $event.detail.message; type = $event.detail.type; setTimeout(() => show = false, 3500)"
        x-show="show"
        x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0 translate-y-4"
        x-transition:enter-end="opacity-100 translate-y-0"
        x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="opacity-100 translate-y-0"
        x-transition:leave-end="opacity-0 translate-y-4"
        class="fixed bottom-6 right-6 z-[200] px-5 py-3 rounded-xl shadow-2xl text-white text-sm font-medium flex items-center gap-2"
        :class="{
            'bg-green-600': type === 'success',
            'bg-yellow-500': type === 'warning',
            'bg-blue-600': type === 'info',
            'bg-red-600': type === 'error'
        }"
        style="display:none"
    >
        <x-icon x-show="type === 'success'" name="check" class="w-4 h-4 shrink-0" />
        <x-icon x-show="type === 'warning'" name="exclamation-triangle" class="w-4 h-4 shrink-0" />
        <x-icon x-show="type === 'info'" name="information-circle" class="w-4 h-4 shrink-0" />
        <x-icon x-show="type === 'error'" name="x-mark" class="w-4 h-4 shrink-0" />
        <span x-text="message"></span>
    </div>

    {{-- ─── Search + Counter Bar ─── --}}
    <div class="flex flex-col sm:flex-row items-start sm:items-center gap-3 mb-5">
        <div class="relative flex-1 w-full">
            <span class="absolute inset-y-0 left-3 flex items-center text-gray-400">
                <x-icon name="search" class="w-4 h-4" />
            </span>
            <input
                type="text"
                wire:model.live.debounce.300ms="searchTerm"
                placeholder="Cari produk..."
                class="w-full pl-9 pr-4 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
            >
        </div>
        <div class="flex items-center gap-2">
            <span class="px-3 py-1.5 rounded-full text-xs font-semibold
                {{ $selectedCount >= 8 ? 'bg-red-100 text-red-700' : 'bg-blue-100 text-blue-700' }}">
                {{ $selectedCount }}/8 dipilih
            </span>
        </div>
    </div>

    {{-- ─── Selected Products Preview ─── --}}
    @if (!empty($selectedProducts))
    <div class="mb-6">
        <p class="text-xs font-semibold text-gray-500 uppercase tracking-widest mb-3 flex items-center gap-1.5">
            <x-icon name="check-circle" class="w-4 h-4 text-green-600" />
            <span>Produk yang akan ditampilkan di Home</span>
        </p>
        <div class="grid grid-cols-2 sm:grid-cols-4 md:grid-cols-4 lg:grid-cols-8 gap-2">
            @foreach ($allProducts as $product)
                @if (in_array($product->id, $selectedProducts))
                <div class="relative group rounded-xl overflow-hidden border-2 border-blue-400 bg-blue-50 shadow-sm">
                    {{-- Thumbnail --}}
                    <div class="relative cursor-pointer" wire:click="toggleProduct({{ $product->id }})">
                        @if ($product->primaryImage)
                            <img src="{{ $product->primaryImage->image_path }}"
                                 alt="{{ $product->name }}"
                                 loading="lazy"
                                 decoding="async"
                                 class="w-full h-20 object-cover">
                        @else
                            <div class="w-full h-20 bg-gray-200 flex items-center justify-center">
                                <span class="text-2xl"><img src="{{ asset('images/asset.png') }}" alt="No Image" loading="lazy" decoding="async" class="w-6 h-6"></span>
                            </div>
                        @endif
                        {{-- Check badge --}}
                        <div class="absolute top-1 left-1 w-5 h-5 bg-blue-500 rounded-full flex items-center justify-center shadow">
                            <svg class="w-3 h-3 text-white" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                            </svg>
                        </div>
                    </div>
                    <div class="p-1.5">
                        <p class="text-[10px] font-semibold text-gray-700 truncate leading-tight">{{ Str::limit($product->name, 18) }}</p>
                        <p class="text-[10px] text-gray-500">Rp {{ number_format($product->price, 0, ',', '.') }}</p>
                    </div>
                    {{-- Edit button --}}
                    <button
                        wire:click.stop="openEditModal({{ $product->id }})"
                        title="Edit Produk"
                        class="absolute top-1 right-1 w-5 h-5 bg-white/90 hover:bg-white rounded-full flex items-center justify-center shadow text-gray-600 opacity-0 group-hover:opacity-100 transition-opacity"
                    >
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                        </svg>
                    </button>
                </div>
                @endif
            @endforeach
        </div>
    </div>
    @endif

    {{-- ─── Available Products Grid ─── --}}
    <div class="mb-6">
        <p class="text-xs font-semibold text-gray-500 uppercase tracking-widest mb-3 flex items-center gap-1.5">
            <x-icon name="cube" class="w-4 h-4 text-gray-500" />
            <span>Semua Produk Aktif</span>
        </p>
        <div class="border border-gray-200 rounded-xl p-3 max-h-[420px] overflow-y-auto bg-gray-50">
            <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6 gap-3">
                @forelse ($allProducts as $product)
                    @php $isSel = in_array($product->id, $selectedProducts); @endphp
                    <div class="relative group rounded-xl overflow-hidden border-2 transition-all duration-200
                        {{ $isSel ? 'border-blue-400 shadow-md' : 'border-gray-200 hover:border-blue-300 hover:shadow-sm' }}
                        bg-white cursor-pointer">

                        <div class="relative" wire:click="toggleProduct({{ $product->id }})">
                            @if ($product->primaryImage)
                                <img src="{{ $product->primaryImage->image_path }}"
                                     alt="{{ $product->name }}"
                                     loading="lazy"
                                     decoding="async"
                                     class="w-full h-24 object-cover">
                            @else
                                <div class="w-full h-24 bg-gradient-to-br from-gray-100 to-gray-200 flex items-center justify-center">
                                    <img src="{{ asset('images/asset.png') }}" alt="No Image" loading="lazy" decoding="async" class="w-6 h-6">
                                </div>
                            @endif

                            @if ($isSel)
                                <div class="absolute inset-0 bg-blue-500/10 flex items-center justify-center">
                                    <div class="w-8 h-8 bg-blue-500 rounded-full flex items-center justify-center shadow-lg">
                                        <svg class="w-4 h-4 text-white" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                        </svg>
                                    </div>
                                </div>
                            @endif
                        </div>

                        <div class="p-2">
                            <p class="text-xs font-semibold text-gray-800 truncate">{{ Str::limit($product->name, 20) }}</p>
                            <p class="text-[11px] text-gray-500 mt-0.5">Rp {{ number_format($product->price, 0, ',', '.') }}</p>
                            @if ($product->is_preorder)
                                <span class="inline-block mt-1 px-1 py-0.5 bg-yellow-100 text-yellow-700 text-[9px] font-bold rounded">PRE-ORDER</span>
                            @endif
                        </div>

                        {{-- Edit button --}}
                        <button
                            wire:click.stop="openEditModal({{ $product->id }})"
                            title="Edit Produk"
                            class="absolute top-1.5 right-1.5 w-6 h-6 bg-white/90 hover:bg-blue-600 hover:text-white rounded-full flex items-center justify-center shadow text-gray-600 opacity-0 group-hover:opacity-100 transition-all"
                        >
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                            </svg>
                        </button>
                    </div>
                @empty
                    <div class="col-span-full text-center py-12 text-gray-400">
                        <div class="mb-2 flex justify-center">
                            <x-icon name="search" class="w-9 h-9 text-gray-300" />
                        </div>
                        <p class="text-sm">Tidak ada produk ditemukan</p>
                    </div>
                @endforelse
            </div>
        </div>
    </div>

    {{-- ─── Action Buttons ─── --}}
    <div class="flex flex-wrap items-center justify-between gap-3 pt-4 border-t border-gray-100">
        <p class="text-xs text-gray-400">Klik produk untuk pilih/hapus dari featured. Hover untuk edit.</p>
        <div class="flex gap-2">
            <button wire:click="clearAll" type="button"
                class="px-4 py-2 text-sm font-medium text-red-600 border border-red-200 rounded-lg hover:bg-red-50 transition inline-flex items-center gap-1.5">
                <x-icon name="trash" class="w-4 h-4" />
                <span>Bersihkan Semua</span>
            </button>
            <button wire:click="saveFeatured" type="button"
                class="px-5 py-2 text-sm font-medium bg-blue-600 text-white rounded-lg hover:bg-blue-700 shadow-sm transition inline-flex items-center gap-1.5">
                <x-icon name="save" class="w-4 h-4" />
                <span>Simpan Perubahan</span>
            </button>
        </div>
    </div>

    {{-- ─────────────────────────────────────────────────────────
         EDIT PRODUCT MODAL
    ──────────────────────────────────────────────────────────── --}}
    @if ($showEditModal && $editProduct)
    <div
        class="fixed inset-0 z-[100] flex items-center justify-center p-4"
        x-data
        x-on:keydown.escape.window="$wire.closeEditModal()"
    >
        {{-- Backdrop --}}
        <div class="absolute inset-0 bg-black/60 backdrop-blur-sm" wire:click="closeEditModal"></div>

        {{-- Modal Panel --}}
        <div class="relative bg-white rounded-2xl shadow-2xl w-full max-w-3xl max-h-[90vh] overflow-y-auto z-10">

            {{-- Header --}}
            <div class="sticky top-0 bg-white border-b border-gray-100 px-6 py-4 flex items-center justify-between z-10 rounded-t-2xl">
                <div>
                    <h3 class="text-lg font-bold text-gray-800 flex items-center gap-2">
                        <x-icon name="pencil-square" class="w-5 h-5 text-blue-600" />
                        <span>Edit Produk</span>
                    </h3>
                    <p class="text-xs text-gray-500 mt-0.5">{{ $editProduct->name }}</p>
                </div>
                <button wire:click="closeEditModal"
                    class="w-8 h-8 flex items-center justify-center rounded-full hover:bg-gray-100 text-gray-500 hover:text-gray-800 transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>

            <div class="p-6 space-y-6">

                {{-- ─ Basic Info ─ --}}
                <div>
                    <h4 class="text-sm font-semibold text-gray-700 mb-3 pb-1 border-b border-gray-100 flex items-center gap-1.5">
                        <x-icon name="pencil-square" class="w-4 h-4 text-gray-500" />
                        <span>Informasi Dasar</span>
                    </h4>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">

                        <div class="md:col-span-2">
                            <label class="block text-xs font-medium text-gray-600 mb-1">Nama Produk <span class="text-red-500">*</span></label>
                            <input type="text" wire:model="editName"
                                class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                            @error('editName') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label class="block text-xs font-medium text-gray-600 mb-1">Kategori <span class="text-red-500">*</span></label>
                            <select wire:model="editCategoryId"
                                class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                                <option value="">-- Pilih Kategori --</option>
                                @foreach ($categories as $cat)
                                    <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                                @endforeach
                            </select>
                            @error('editCategoryId') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label class="block text-xs font-medium text-gray-600 mb-1">Harga Jual (Rp) <span class="text-red-500">*</span></label>
                            <input type="number" wire:model="editPrice" min="0" step="1000"
                                class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                            @error('editPrice') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label class="block text-xs font-medium text-gray-600 mb-1">Harga Modal / HPP (Rp)</label>
                            <input type="number" wire:model="editCostPrice" min="0" step="1000"
                                class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                        </div>

                        <div>
                            <label class="block text-xs font-medium text-gray-600 mb-1">Deskripsi Singkat</label>
                            <input type="text" wire:model="editShortDesc" maxlength="500"
                                class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
                                placeholder="Tagline singkat produk...">
                        </div>

                        <div class="md:col-span-2">
                            <label class="block text-xs font-medium text-gray-600 mb-1">Deskripsi Lengkap <span class="text-red-500">*</span></label>
                            <textarea wire:model="editDescription" rows="4"
                                class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 resize-none"></textarea>
                            @error('editDescription') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    {{-- Toggles --}}
                    <div class="flex flex-wrap gap-4 mt-4">
                        <label class="flex items-center gap-2 cursor-pointer">
                            <div class="relative">
                                <input type="checkbox" wire:model="editIsActive" class="sr-only peer">
                                <div class="w-10 h-5 bg-gray-200 peer-checked:bg-green-500 rounded-full transition-colors"></div>
                                <div class="absolute top-0.5 left-0.5 w-4 h-4 bg-white rounded-full shadow transition-transform peer-checked:translate-x-5"></div>
                            </div>
                            <span class="text-sm text-gray-700">Produk Aktif</span>
                        </label>

                        <label class="flex items-center gap-2 cursor-pointer">
                            <div class="relative">
                                <input type="checkbox" wire:model="editIsPreorder" class="sr-only peer">
                                <div class="w-10 h-5 bg-gray-200 peer-checked:bg-yellow-500 rounded-full transition-colors"></div>
                                <div class="absolute top-0.5 left-0.5 w-4 h-4 bg-white rounded-full shadow transition-transform peer-checked:translate-x-5"></div>
                            </div>
                            <span class="text-sm text-gray-700">Pre-Order</span>
                        </label>

                        <label class="flex items-center gap-2 cursor-pointer">
                            <div class="relative">
                                <input type="checkbox" wire:model="editIsFeatured" class="sr-only peer">
                                <div class="w-10 h-5 bg-gray-200 peer-checked:bg-blue-500 rounded-full transition-colors"></div>
                                <div class="absolute top-0.5 left-0.5 w-4 h-4 bg-white rounded-full shadow transition-transform peer-checked:translate-x-5"></div>
                            </div>
                            <span class="text-sm text-gray-700">Featured di Home</span>
                        </label>
                    </div>
                </div>

                {{-- ─ Images ─ --}}
                <div>
                    <h4 class="text-sm font-semibold text-gray-700 mb-3 pb-1 border-b border-gray-100 flex items-center gap-1.5">
                        <x-icon name="image" class="w-4 h-4 text-gray-500" />
                        <span>Gambar Produk</span>
                    </h4>

                    {{-- Existing images --}}
                    @if ($editProduct->images->isNotEmpty())
                    <div class="mb-4">
                        <p class="text-xs text-gray-500 mb-2">Gambar saat ini (hover untuk hapus):</p>
                        <div class="flex flex-wrap gap-3">
                            @foreach ($editProduct->images as $img)
                            @php $markedForDelete = in_array($img->id, $deleteImageIds); @endphp
                            <div class="relative group w-24 h-24 rounded-lg overflow-hidden border-2
                                {{ $markedForDelete ? 'border-red-400 opacity-50' : ($img->is_primary ? 'border-blue-400' : 'border-gray-200') }}">
                                <img src="{{ $img->image_path }}" alt="{{ $editProduct->name }}" loading="lazy" decoding="async" class="w-full h-full object-cover">

                                @if ($img->is_primary)
                                    <div class="absolute bottom-0 left-0 right-0 bg-blue-500/80 text-white text-[9px] text-center py-0.5 font-bold">UTAMA</div>
                                @endif

                                @if ($markedForDelete)
                                    <button wire:click="unmarkDeleteImage({{ $img->id }})"
                                        class="absolute inset-0 flex items-center justify-center bg-red-500/20 text-red-600 text-xs font-bold">
                                        ↩ Batal
                                    </button>
                                @else
                                    <button wire:click="markDeleteImage({{ $img->id }})"
                                        class="absolute inset-0 flex items-center justify-center gap-1 bg-black/0 hover:bg-black/40 text-white text-xs opacity-0 group-hover:opacity-100 transition-all font-bold">
                                        <x-icon name="trash" class="w-3.5 h-3.5" />
                                        <span>Hapus</span>
                                    </button>
                                @endif
                            </div>
                            @endforeach
                        </div>
                        @if (!empty($deleteImageIds))
                            <p class="text-xs text-red-500 mt-2 flex items-center gap-1">
                                <x-icon name="exclamation-triangle" class="w-3.5 h-3.5 shrink-0" />
                                <span>{{ count($deleteImageIds) }} gambar akan dihapus saat disimpan.</span>
                            </p>
                        @endif
                    </div>
                    @endif

                    {{-- New primary image --}}
                    <div class="mb-4">
                        <label class="block text-xs font-medium text-gray-600 mb-1">Ganti Foto Utama (Opsional)</label>
                        <div class="border-2 border-dashed border-blue-300 rounded-xl p-4 text-center hover:bg-blue-50 transition cursor-pointer"
                             onclick="document.getElementById('newPrimaryImageInput').click()">
                            @if ($newPrimaryImage)
                                <img src="{{ $newPrimaryImage->temporaryUrl() }}" class="mx-auto h-28 object-contain rounded mb-2">
                                <p class="text-xs text-green-600 font-medium inline-flex items-center justify-center gap-1">
                                    <x-icon name="check" class="w-3.5 h-3.5" />
                                    <span>{{ $newPrimaryImage->getClientOriginalName() }}</span>
                                </p>
                            @else
                                <p class="text-gray-400 text-sm inline-flex items-center justify-center gap-1.5">
                                    <x-icon name="camera" class="w-4 h-4" />
                                    <span>Klik untuk upload foto utama</span>
                                </p>
                                <p class="text-gray-300 text-xs mt-1">PNG, JPG, WebP — Max 5MB</p>
                            @endif
                        </div>
                        <input type="file" id="newPrimaryImageInput" wire:model="newPrimaryImage"
                               accept="image/*" class="hidden">
                        @error('newPrimaryImage') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    {{-- Additional images --}}
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1">Tambah Foto Lain (Opsional, multiple)</label>
                        <div class="border-2 border-dashed border-gray-300 rounded-xl p-4 text-center hover:bg-gray-50 transition cursor-pointer"
                             onclick="document.getElementById('newAdditionalImagesInput').click()">
                            @if (!empty($newAdditionalImages))
                                <div class="flex flex-wrap gap-2 justify-center">
                                    @foreach ($newAdditionalImages as $idx => $img)
                                        <img src="{{ $img->temporaryUrl() }}" class="h-16 w-16 object-cover rounded">
                                    @endforeach
                                </div>
                                <p class="text-xs text-green-600 font-medium mt-2 inline-flex items-center justify-center gap-1">
                                    <x-icon name="check" class="w-3.5 h-3.5" />
                                    <span>{{ count($newAdditionalImages) }} foto dipilih</span>
                                </p>
                            @else
                                <p class="text-gray-400 text-sm inline-flex items-center justify-center gap-1.5">
                                    <x-icon name="folder-plus" class="w-4 h-4" />
                                    <span>Klik untuk upload foto tambahan</span>
                                </p>
                                <p class="text-gray-300 text-xs mt-1">Bisa pilih beberapa sekaligus</p>
                            @endif
                        </div>
                        <input type="file" id="newAdditionalImagesInput" wire:model="newAdditionalImages"
                               accept="image/*" multiple class="hidden">
                        @error('newAdditionalImages.*') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>
            </div>

            {{-- ─ Footer ─ --}}
            <div class="sticky bottom-0 bg-white border-t border-gray-100 px-6 py-4 flex items-center justify-between gap-3 rounded-b-2xl">
                <a href="{{ route('admin.products.edit', $editProduct->id) }}"
                   target="_blank"
                   class="text-xs text-blue-600 hover:underline flex items-center gap-1.5">
                    <x-icon name="link" class="w-3.5 h-3.5" />
                    <span>Buka halaman edit lengkap</span>
                </a>
                <div class="flex gap-3">
                    <button wire:click="closeEditModal" type="button"
                        class="px-4 py-2 text-sm border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition">
                        Batal
                    </button>
                    <button wire:click="saveProduct" type="button"
                        wire:loading.attr="disabled"
                        wire:loading.class="opacity-70 cursor-not-allowed"
                        class="px-6 py-2 text-sm bg-blue-600 text-white rounded-lg hover:bg-blue-700 shadow-sm transition font-medium flex items-center gap-2">
                        <span wire:loading.remove wire:target="saveProduct" class="inline-flex items-center gap-1.5">
                            <x-icon name="save" class="w-4 h-4" />
                            <span>Simpan</span>
                        </span>
                        <span wire:loading wire:target="saveProduct" class="flex items-center gap-2">
                            <svg class="animate-spin w-4 h-4" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
                            </svg>
                            Menyimpan...
                        </span>
                    </button>
                </div>
            </div>
        </div>
    </div>
    @endif
</div>
