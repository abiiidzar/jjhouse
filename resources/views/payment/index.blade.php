<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment - JJHOUSE</title>

    <!-- Midtrans Snap Script -->
    <script src="https://app.sandbox.midtrans.com/snap/snap.js"
            data-client-key="{{ config('services.midtrans.client_key') }}"></script>

    <!-- Midtrans Production Script (uncomment untuk production) -->
    {{-- <script src="https://app.midtrans.com/snap/snap.js"
            data-client-key="{{ config('services.midtrans.client_key') }}"></script> --}}

    <style>
        * {
            box-sizing: border-box;
        }

        body {
            min-height: 100vh;
            margin: 0;
            padding: 24px;
            display: grid;
            place-items: center;
            background: #f9fafb;
            color: #111827;
            font-family: Inter, ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif;
        }

        .payment-container {
            width: min(100%, 480px);
            padding: 32px;
            border: 1px solid #e5e7eb;
            border-radius: 12px;
            background: #fff;
        }

        .payment-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding-bottom: 24px;
            border-bottom: 1px solid #e5e7eb;
        }

        .logo {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            font-size: 16px;
            font-weight: 700;
            letter-spacing: .04em;
        }

        .logo-mark {
            display: inline-grid;
            width: 30px;
            height: 30px;
            place-items: center;
            background: #111827;
            color: #fff;
            font-size: 12px;
            letter-spacing: 0;
        }

        .secure-label {
            color: #4b5563;
            font-size: 12px;
        }

        .payment-content {
            padding-top: 24px;
            text-align: center;
        }

        .eyebrow {
            margin: 0 0 8px;
            color: #4b5563;
            font-size: 12px;
            font-weight: 600;
            letter-spacing: .12em;
            text-transform: uppercase;
        }

        .amount {
            margin: 0;
            font-size: clamp(28px, 8vw, 36px);
            font-weight: 600;
            letter-spacing: -.03em;
            font-variant-numeric: tabular-nums;
        }

        .order-number {
            margin: 8px 0 0;
            color: #4b5563;
            font-size: 13px;
            overflow-wrap: anywhere;
        }

        .status-box {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 12px;
            margin-top: 24px;
            padding: 16px;
            border: 1px solid #e5e7eb;
            border-radius: 8px;
            background: #f9fafb;
        }

        .status-box span {
            color: #374151;
            font-size: 14px;
        }

        .spinner {
            width: 20px;
            height: 20px;
            flex: 0 0 auto;
            border: 2px solid #d1d5db;
            border-top-color: #111827;
            border-radius: 50%;
            animation: spin .8s linear infinite;
        }

        @keyframes spin {
            to { transform: rotate(360deg); }
        }

        .payment-method-badge {
            display: inline-block;
            margin-top: 16px;
            padding: 6px 10px;
            border: 1px solid #d1d5db;
            border-radius: 999px;
            color: #374151;
            font-size: 12px;
            font-weight: 500;
        }

        .retry-btn {
            width: 100%;
            margin-top: 16px;
            padding: 13px 20px;
            border: 1px solid #111827;
            border-radius: 6px;
            background: #111827;
            color: #fff;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            transition: background-color .18s ease, border-color .18s ease;
        }

        .retry-btn:hover {
            border-color: #374151;
            background: #374151;
        }

        .retry-btn:focus-visible,
        .btn-back:focus-visible {
            outline: 2px solid #111827;
            outline-offset: 3px;
        }

        .btn-back {
            display: inline-block;
            margin-top: 20px;
            color: #374151;
            font-size: 13px;
            text-decoration: underline;
            text-underline-offset: 3px;
        }

        .btn-back:hover {
            color: #111827;
        }

        @media (max-width: 480px) {
            body {
                padding: 16px;
            }

            .payment-container {
                padding: 24px 20px;
            }
        }

        @media (prefers-reduced-motion: reduce) {
            .spinner {
                animation-duration: 1.8s;
            }
        }
    </style>
</head>
<body>
    <main class="payment-container">
        <header class="payment-header">
            <div class="logo">
                <span class="logo-mark">JJY</span>
                <span>JJHOUSE</span>
            </div>
            <span class="secure-label">Secure checkout</span>
        </header>

        <section class="payment-content" aria-labelledby="payment-title">
            <p class="eyebrow" id="payment-title">Complete your payment</p>
            <p class="amount">Rp {{ number_format($order->grand_total, 0, ',', '.') }}</p>
            <p class="order-number">Order #{{ preg_replace('/[^a-zA-Z0-9\-_~.]/', '-', $order->order_number) }}</p>

            <div class="status-box" role="status" aria-live="polite">
                <div class="spinner" aria-hidden="true"></div>
                <span>Preparing your secure payment...</span>
            </div>

            <span class="payment-method-badge">
                {{ $order->payment_method === 'midtrans'
                    ? 'All available payment methods'
                    : ($order->payment_method === 'qris' ? 'QRIS' : ucfirst(str_replace('_', ' ', $order->payment_method))) }}
            </span>

            <button id="pay-now-btn" class="retry-btn" style="display: none;">
                Continue to payment
            </button>

            @if($canChangePaymentMethod ?? true)
                <form
                    action="{{ route('order.change-payment-method', $order) }}"
                    method="POST"
                    id="change-payment-form"
                    style="display: none;"
                >
                    @csrf
                    <input type="hidden" name="payment_attempt_id" value="{{ $paymentAttemptId ?? $order->payment_attempt_id ?? $order->order_number }}">
                    <button type="submit" class="retry-btn">
                        Switch to another payment method
                    </button>
                </form>
            @endif

            <div>
                <a href="{{ route('dashboard') }}" class="btn-back">Back to dashboard</a>
            </div>
        </section>
    </main>

    <script>
        const snapToken = @json($snapToken);

        function openSnapPayment() {
            window.snap.pay(snapToken, {
                onSuccess: function(result) {
                    window.location.href = '{{ route("dashboard") }}?payment=success';
                },
                onPending: function(result) {
                    window.location.href = '{{ route("dashboard") }}?payment=pending';
                },
                onError: function(result) {
                    window.location.href = '{{ route("dashboard") }}?payment=error';
                },
                onClose: function() {
                    document.querySelector('.spinner').hidden = true;
                    document.querySelector('.status-box span').textContent =
                        'Payment window closed. Reopen it or switch to another method.';
                    document.getElementById('pay-now-btn').style.display = 'block';

                    const changePaymentForm = document.getElementById('change-payment-form');
                    if (changePaymentForm) {
                        changePaymentForm.style.display = 'block';
                    }
                }
            });
        }

        document.addEventListener('DOMContentLoaded', function() {
            setTimeout(function() {
                try {
                    if (window.snap && typeof window.snap.pay === 'function') {
                        openSnapPayment();
                    } else {
                        throw new Error('Snap not loaded');
                    }
                } catch (e) {
                    console.error('Snap payment error:', e);
                    document.querySelector('.spinner').hidden = true;
                    document.getElementById('pay-now-btn').style.display = 'block';
                    document.getElementById('pay-now-btn').onclick = openSnapPayment;
                    document.querySelector('.status-box span').textContent = 'Payment window did not load. Try again to continue.';

                    const changePaymentForm = document.getElementById('change-payment-form');
                    if (changePaymentForm) {
                        changePaymentForm.style.display = 'block';
                    }
                }
            }, 1500);
        });
    </script>
</body>
</html>
