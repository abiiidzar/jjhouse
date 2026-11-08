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
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 20px;
        }
        .payment-container {
            background: white;
            border-radius: 20px;
            padding: 48px;
            max-width: 500px;
            width: 100%;
            box-shadow: 0 20px 60px rgba(0,0,0,0.15);
            text-align: center;
        }
        .logo {
            font-size: 28px;
            font-weight: bold;
            letter-spacing: -1px;
            margin-bottom: 8px;
        }
        .logo span:first-child {
            background: #000;
            color: #fff;
            padding: 4px 10px;
        }
        .order-number {
            color: #666;
            font-size: 14px;
            margin-bottom: 24px;
        }
        .amount {
            font-size: 36px;
            font-weight: bold;
            color: #1a1a1a;
            margin: 16px 0;
        }
        .amount small {
            font-size: 16px;
            font-weight: normal;
            color: #666;
        }
        .status-box {
            background: #f8f9fa;
            border-radius: 12px;
            padding: 16px;
            margin: 20px 0;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 12px;
        }
        .spinner {
            width: 24px;
            height: 24px;
            border: 3px solid #e9ecef;
            border-top: 3px solid #000;
            border-radius: 50%;
            animation: spin 0.8s linear infinite;
        }
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
        .btn-back {
            display: inline-block;
            margin-top: 16px;
            color: #666;
            text-decoration: none;
            font-size: 14px;
            transition: color 0.2s;
        }
        .btn-back:hover {
            color: #000;
        }
        .error-message {
            background: #fee2e2;
            color: #991b1b;
            padding: 12px;
            border-radius: 8px;
            margin: 16px 0;
            font-size: 14px;
        }
        .payment-method-badge {
            display: inline-block;
            background: #e9ecef;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 12px;
            color: #495057;
        }
        .retry-btn {
            background: #000;
            color: white;
            border: none;
            padding: 12px 24px;
            border-radius: 8px;
            font-size: 14px;
            cursor: pointer;
            transition: background 0.2s;
            margin-top: 12px;
        }
        .retry-btn:hover {
            background: #333;
        }
    </style>
</head>
<body>
    <div class="payment-container">
        <div class="logo">
            <span>JJY</span><span>HOUSE</span>
        </div>

        <div class="order-number">Order #{{ preg_replace('/[^a-zA-Z0-9\-_~.]/', '-', $order->order_number) }}</div>

        <div class="amount">
            Rp {{ number_format($order->grand_total, 0, ',', '.') }}
            <small>total</small>
        </div>

        <div class="status-box">
            <div class="spinner"></div>
            <span style="color: #495057; font-size: 14px;">Preparing your payment...</span>
        </div>

        <p style="color: #666; font-size: 13px; margin-bottom: 8px;">
            You will be redirected to the payment gateway
        </p>
        <span class="payment-method-badge">
            {{ ucfirst(str_replace('_', ' ', $order->payment_method)) }}
        </span>

        <!-- 🔥 Tombol untuk membuka Snap jika auto-redirect tidak berhasil -->
        <button id="pay-now-btn" class="retry-btn" style="display: none;">
            Pay Now
        </button>

        <a href="{{ route('dashboard') }}" class="btn-back">
            ← Back to Dashboard
        </a>
    </div>

    <script>
        // 🔥 Fungsi untuk membuka payment popup
        function openSnapPayment() {
            window.snap.pay('{{ $snapToken }}', {
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
                    // User menutup popup tanpa menyelesaikan pembayaran
                    if (confirm('Payment not completed. Are you sure you want to leave?')) {
                        window.location.href = '{{ route("dashboard") }}';
                    }
                }
            });
        }

        // 🔥 Auto-open Snap payment setelah halaman dimuat
        document.addEventListener('DOMContentLoaded', function() {
            // Tunggu 1 detik untuk memastikan Snap sudah ready
            setTimeout(function() {
                try {
                    if (typeof window.snap !== 'undefined') {
                        openSnapPayment();
                    } else {
                        throw new Error('Snap not loaded');
                    }
                } catch (e) {
                    console.error('Snap payment error:', e);
                    // Tampilkan tombol manual jika auto-open gagal
                    document.getElementById('pay-now-btn').style.display = 'block';
                    document.getElementById('pay-now-btn').onclick = openSnapPayment;
                    document.querySelector('.status-box span').textContent = 'Click "Pay Now" to continue';
                }
            }, 1500);
        });
    </script>
</body>
</html>
