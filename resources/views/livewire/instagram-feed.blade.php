<div class="instagram-feed">
    @if($loading)
        <div class="text-center">
            <div class="spinner-border" role="status">
                <span class="sr-only">Loading...</span>
            </div>
        </div>
    @else
        <div class="instagram-gallery">
            @if($feeds && count($feeds) > 0)
                <div class="row">
                    @foreach($feeds as $feed)
                        <div class="col-md-4 mb-3">
                            <div class="instagram-item">
                                @if(isset($feed['media_type']) && $feed['media_type'] === 'IMAGE')
                                    <img src="{{ $feed['media_url'] ?? '' }}" alt="Instagram Post" class="img-fluid">
                                @elseif(isset($feed['media_type']) && in_array($feed['media_type'], ['VIDEO', 'CAROUSEL']))
                                    <video class="img-fluid" controls>
                                        <source src="{{ $feed['media_url'] ?? '' }}" type="video/mp4">
                                    </video>
                                @endif
                                @if(isset($feed['caption']))
                                    <p class="caption mt-2">{{ Str::limit($feed['caption'], 100) }}</p>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <p class="text-center text-muted">No Instagram posts available.</p>
            @endif
        </div>
        <div class="text-center mt-3">
            <button wire:click="refresh" class="btn btn-outline-primary">Refresh Feed</button>
        </div>
    @endif
</div>
