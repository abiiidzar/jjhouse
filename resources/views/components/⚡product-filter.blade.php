<?php

namespace App\Http\Livewire;

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
    public $maxPrice = 1000000;

    protected $queryString = ['search', 'category', 'sort', 'minPrice', 'maxPrice'];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function getProductsProperty()
    {
        $query = Product::where('is_active', true);

        if ($this->search) {
            $query->where('name', 'like', '%' . $this->search . '%');
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
            default:
                $query->latest();
        }

        return $query->paginate(12);
    }

    public function render()
    {
        return view('livewire.product-filter', [
            'products' => $this->products,
        ]);
    }
}
