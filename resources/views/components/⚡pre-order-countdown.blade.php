<?php

namespace App\Http\Livewire;

use Carbon\Carbon;
use Livewire\Component;

class PreOrderCountdown extends Component
{
    public $product;
    public $endDate;
    public $remaining = [];

    public function mount($product)
    {
        $this->product = $product;
        if ($product->is_preorder && $product->preorder_end_date) {
            $this->endDate = $product->preorder_end_date;
        }
    }

    public function getRemainingTimeProperty()
    {
        if (!$this->endDate) {
            return null;
        }

        $now = Carbon::now();
        $end = Carbon::parse($this->endDate);

        if ($now->gt($end)) {
            return ['expired' => true];
        }

        $diff = $now->diff($end);
        return [
            'days' => $diff->d,
            'hours' => $diff->h,
            'minutes' => $diff->i,
            'seconds' => $diff->s,
            'expired' => false,
        ];
    }

    public function render()
    {
        return view('livewire.pre-order-countdown', [
            'remainingTime' => $this->remainingTime,
        ]);
    }
}
