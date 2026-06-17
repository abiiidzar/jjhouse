<?php

namespace App\Http\Livewire;

use App\Services\InstagramService;
use Livewire\Component;

class InstagramFeed extends Component
{
    public $feeds = [];
    public $loading = true;

    public function mount()
    {
        $this->loadFeeds();
    }

    public function loadFeeds()
    {
        $this->loading = true;
        $instagram = new InstagramService();
        $this->feeds = $instagram->getRecentMedia(12);
        $this->loading = false;
    }

    public function refresh()
    {
        $this->loadFeeds();
        $this->dispatchBrowserEvent('feed-refreshed');
    }

    public function render()
    {
        return view('livewire.instagram-feed');
    }
}
