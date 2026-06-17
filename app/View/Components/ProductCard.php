<?php

namespace App\View\Components;

use Illuminate\View\Component;

class ProductCard extends Component
{
    public $product;
    public $showAddToCart;

    public function __construct($product, $showAddToCart = true)
    {
        $this->product = $product;
        $this->showAddToCart = $showAddToCart;
    }

    public function render()
    {
        return view('components.product-card');
    }
}
