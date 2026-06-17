<?php

namespace App\Http\Livewire;

use App\Services\InstagramService;
use Livewire\Component;

class InstagramFeed extends Component
{
    public $feeds = [];

    public function mount()
    {
        $this->loadFeeds();
    }

    public function loadFeeds()
    {
        $instagram = new InstagramService();
        $this->feeds = $instagram->getRecentMedia(12);
    }

    public function render()
    {
        return view('livewire.instagram-feed');
    }
}
