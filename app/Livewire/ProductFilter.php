<?php

namespace App\Livewire;

use App\Models\Category;
use App\Models\Product;
use Livewire\Component;
use Livewire\WithPagination;

class ProductFilter extends Component
{
    use WithPagination;

    public $search = '';
    public $category = '';
    public $sort = 'latest';
    public $minPrice = 0;
    public $maxPrice = 5000000;
    public $featured = false;
    public $perPage = 12;

    protected $queryString = ['search', 'category', 'sort', 'minPrice', 'maxPrice'];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingCategory()
    {
        $this->resetPage();
    }

    public function updatingSort()
    {
        $this->resetPage();
    }

    public function resetFilters()
    {
        $this->search = '';
        $this->category = '';
        $this->sort = 'latest';
        $this->minPrice = 0;
        $this->maxPrice = 5000000;
        $this->resetPage();
    }

    public function getProductsProperty()
    {
        $query = Product::where('is_active', true);

        if ($this->featured) {
            $query->where('is_featured', true);
        }

        if ($this->search) {
            $query->where(function($q) {
                $q->where('name', 'like', '%' . $this->search . '%')
                  ->orWhere('short_description', 'like', '%' . $this->search . '%');
            });
        }

        if ($this->category) {
            $query->whereHas('category', function ($q) {
                $q->where('slug', $this->category);
            });
        }

        $query->whereBetween('price', [$this->minPrice, $this->maxPrice]);

        switch ($this->sort) {
            case 'price_asc':
                $query->orderBy('price', 'asc');
                break;
            case 'price_desc':
                $query->orderBy('price', 'desc');
                break;
            case 'oldest':
                $query->orderBy('created_at', 'asc');
                break;
            case 'name_asc':
                $query->orderBy('name', 'asc');
                break;
            default:
                $query->latest();
        }

        return $query->with('primaryImage', 'category')->paginate($this->perPage);
    }

    public function getCategoriesProperty()
    {
        return Category::withCount('products')->get();
    }

    public function render()
    {
        return view('livewire.product-filter', [
            'products' => $this->products,
            'categories' => $this->categories,
        ]);
    }
}
