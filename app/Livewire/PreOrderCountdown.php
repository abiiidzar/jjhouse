<?php

namespace App\Livewire;

use Carbon\Carbon;
use Livewire\Component;

class PreOrderCountdown extends Component
{
    public $product;
    public $endDate;

    protected $listeners = ['refreshCountdown' => '$refresh'];

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
            'total_seconds' => $now->diffInSeconds($end),
            'expired' => false,
        ];
    }

    public function getEstimatedDateProperty()
    {
        if ($this->product->preorder_estimated_days) {
            return Carbon::now()->addDays($this->product->preorder_estimated_days)->format('d M Y');
        }
        return null;
    }

    public function render()
    {
        return view('livewire.pre-order-countdown', [
            'product' => $this->product,
            'remainingTime' => $this->remainingTime,
            'estimatedDate' => $this->estimatedDate,
        ]);
    }
}
