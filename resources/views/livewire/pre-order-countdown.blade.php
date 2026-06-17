<div class="pre-order-countdown">
    @if($remainingTime)
        @if($remainingTime['expired'])
            <div class="alert alert-info">Pre-order period has ended</div>
        @else
            <div class="countdown">
                <p class="mb-2"><strong>Pre-Order Countdown:</strong></p>
                <div class="d-flex gap-2">
                    <div class="text-center">
                        <span class="badge bg-primary">{{ $remainingTime['days'] }}</span>
                        <small>Days</small>
                    </div>
                    <div class="text-center">
                        <span class="badge bg-primary">{{ $remainingTime['hours'] }}</span>
                        <small>Hours</small>
                    </div>
                    <div class="text-center">
                        <span class="badge bg-primary">{{ $remainingTime['minutes'] }}</span>
                        <small>Minutes</small>
                    </div>
                    <div class="text-center">
                        <span class="badge bg-primary">{{ $remainingTime['seconds'] }}</span>
                        <small>Seconds</small>
                    </div>
                </div>
                @if($estimatedDate)
                    <p class="mt-2 mb-0 text-muted small">
                        Estimated delivery: <strong>{{ $estimatedDate }}</strong>
                    </p>
                @endif
            </div>
        @endif
    @endif
</div>
