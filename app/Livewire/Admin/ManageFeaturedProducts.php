<?php

namespace App\Livewire\Admin;

use App\Models\Category;
use App\Models\Product;
use App\Models\ProductImage;
use Livewire\Component;
use Livewire\WithFileUploads;

class ManageFeaturedProducts extends Component
{
    use WithFileUploads;

    // Featured management
    public $products;
    public $selectedProducts = [];
    public $searchTerm = '';

    // Edit product modal
    public $showEditModal   = false;
    public $editProductId   = null;
    public $editName        = '';
    public $editPrice       = '';
    public $editCostPrice   = '';
    public $editDescription = '';
    public $editShortDesc   = '';
    public $editCategoryId  = '';
    public $editIsActive    = true;
    public $editIsPreorder  = false;
    public $editIsFeatured  = false;

    // Image upload inside edit modal
    public $newPrimaryImage     = null;
    public $newAdditionalImages = [];
    public $deleteImageIds      = [];

    // All categories for dropdown
    public $categories = [];

    public function mount()
    {
        $this->loadFeaturedProducts();
        $this->categories = Category::orderBy('name')->get();
    }

    public function loadFeaturedProducts()
    {
        $this->selectedProducts = Product::where('is_featured_home', true)
            ->pluck('id')
            ->toArray();

        $this->products = Product::with(['primaryImage', 'images', 'category'])
            ->where('is_active', true)
            ->when($this->searchTerm, function ($query) {
                $query->where('name', 'like', '%' . $this->searchTerm . '%');
            })
            ->orderBy('name')
            ->get();
    }

    public function updatedSearchTerm()
    {
        $this->loadFeaturedProducts();
    }

    public function toggleProduct($productId)
    {
        if (in_array($productId, $this->selectedProducts)) {
            $this->selectedProducts = array_values(array_diff($this->selectedProducts, [$productId]));
        } else {
            if (count($this->selectedProducts) < 8) {
                $this->selectedProducts[] = $productId;
            } else {
                $this->dispatch('notify', message: 'Maksimal 8 produk untuk home featured', type: 'warning');
                return;
            }
        }
    }

    public function saveFeatured()
    {
        Product::query()->update(['is_featured_home' => false]);

        if (!empty($this->selectedProducts)) {
            Product::whereIn('id', $this->selectedProducts)->update(['is_featured_home' => true]);
        }

        $this->loadFeaturedProducts();
        $this->dispatch('notify', message: 'Produk featured home berhasil diperbarui!', type: 'success');
    }

    public function clearAll()
    {
        $this->selectedProducts = [];
        Product::query()->update(['is_featured_home' => false]);
        $this->dispatch('notify', message: 'Semua produk featured home telah dihapus', type: 'info');
    }

    // ─── Edit Modal ────────────────────────────────────────────

    public function openEditModal($productId)
    {
        $product = Product::with(['images', 'category'])->findOrFail($productId);

        $this->editProductId   = $product->id;
        $this->editName        = $product->name;
        $this->editPrice       = $product->price;
        $this->editCostPrice   = $product->cost_price ?? '';
        $this->editDescription = $product->description;
        $this->editShortDesc   = $product->short_description ?? '';
        $this->editCategoryId  = $product->category_id;
        $this->editIsActive    = $product->is_active;
        $this->editIsPreorder  = $product->is_preorder;
        $this->editIsFeatured  = $product->is_featured_home;

        $this->newPrimaryImage     = null;
        $this->newAdditionalImages = [];
        $this->deleteImageIds      = [];

        $this->showEditModal = true;
    }

    public function closeEditModal()
    {
        $this->showEditModal = false;
        $this->reset([
            'editProductId', 'editName', 'editPrice', 'editCostPrice',
            'editDescription', 'editShortDesc', 'editCategoryId',
            'editIsActive', 'editIsPreorder', 'editIsFeatured',
            'newPrimaryImage', 'newAdditionalImages', 'deleteImageIds',
        ]);
    }

    public function markDeleteImage($imageId)
    {
        if (!in_array($imageId, $this->deleteImageIds)) {
            $this->deleteImageIds[] = $imageId;
        }
    }

    public function unmarkDeleteImage($imageId)
    {
        $this->deleteImageIds = array_values(array_diff($this->deleteImageIds, [$imageId]));
    }

    public function saveProduct()
    {
        $this->validate([
            'editName'              => 'required|string|max:255',
            'editPrice'             => 'required|numeric|min:0',
            'editCostPrice'         => 'nullable|numeric|min:0',
            'editDescription'       => 'required|string',
            'editShortDesc'         => 'nullable|string|max:500',
            'editCategoryId'        => 'required|exists:categories,id',
            'newPrimaryImage'       => 'nullable|image|max:5120',
            'newAdditionalImages.*' => 'nullable|image|max:5120',
        ], [
            'editName.required'       => 'Nama produk wajib diisi.',
            'editPrice.required'      => 'Harga wajib diisi.',
            'editCategoryId.required' => 'Kategori wajib dipilih.',
            'newPrimaryImage.image'   => 'File harus berupa gambar.',
            'newPrimaryImage.max'     => 'Ukuran gambar maksimal 5MB.',
        ]);

        $product = Product::findOrFail($this->editProductId);

        $product->update([
            'name'              => $this->editName,
            'category_id'       => $this->editCategoryId,
            'description'       => $this->editDescription,
            'short_description' => $this->editShortDesc,
            'price'             => $this->editPrice,
            'cost_price'        => $this->editCostPrice ?: null,
            'is_active'         => $this->editIsActive,
            'is_preorder'       => $this->editIsPreorder,
            'is_featured_home'  => $this->editIsFeatured,
        ]);

        // Delete marked images
        if (!empty($this->deleteImageIds)) {
            ProductImage::whereIn('id', $this->deleteImageIds)
                ->where('product_id', $product->id)
                ->delete();
        }

        // Upload new primary image
        if ($this->newPrimaryImage) {
            // Remove existing primary
            ProductImage::where('product_id', $product->id)
                ->where('is_primary', true)
                ->delete();

            $filename = uniqid() . '.' . $this->newPrimaryImage->getClientOriginalExtension();
            $this->newPrimaryImage->storeAs('products', $filename, 'public');

            ProductImage::create([
                'product_id' => $product->id,
                'image_path' => '/storage/products/' . $filename,
                'is_primary'  => true,
            ]);
        }

        // Upload additional images
        if (!empty($this->newAdditionalImages)) {
            foreach ($this->newAdditionalImages as $img) {
                $filename = uniqid() . '.' . $img->getClientOriginalExtension();
                $img->storeAs('products', $filename, 'public');

                ProductImage::create([
                    'product_id' => $product->id,
                    'image_path' => '/storage/products/' . $filename,
                    'is_primary'  => false,
                ]);
            }
        }

        // Refresh featured list
        if ($this->editIsFeatured && !in_array($product->id, $this->selectedProducts)) {
            $this->selectedProducts[] = $product->id;
        } elseif (!$this->editIsFeatured) {
            $this->selectedProducts = array_values(array_diff($this->selectedProducts, [$product->id]));
        }

        $this->loadFeaturedProducts();
        $this->closeEditModal();
        $this->dispatch('notify', message: 'Produk "' . $product->name . '" berhasil diperbarui!', type: 'success');
    }

    public function render()
    {
        $editProduct = $this->editProductId
            ? Product::with('images')->find($this->editProductId)
            : null;

        return view('livewire.admin.manage-featured-products', [
            'allProducts'  => $this->products,
            'selectedCount' => count($this->selectedProducts),
            'editProduct'  => $editProduct,
        ]);
    }
}
