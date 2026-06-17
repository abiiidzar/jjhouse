<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Your Pre-order is Ready!</title>
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;
            line-height: 1.6;
            color: #1a1a1a;
            background-color: #f5f5f5;
            margin: 0;
            padding: 0;
        }
        .container {
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }
        .card {
            background-color: #ffffff;
            border-radius: 12px;
            padding: 40px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
        }
        .header {
            text-align: center;
            border-bottom: 2px solid #000;
            padding-bottom: 20px;
            margin-bottom: 30px;
        }
        .logo {
            font-size: 24px;
            font-weight: bold;
            letter-spacing: -0.5px;
        }
        .logo span:first-child {
            background: #000;
            color: #fff;
            padding: 4px 8px;
        }
        .btn {
            display: inline-block;
            background-color: #000;
            color: #fff;
            padding: 12px 30px;
            text-decoration: none;
            border-radius: 6px;
            font-weight: 500;
            margin-top: 20px;
        }
        .btn:hover {
            background-color: #333;
        }
        .footer {
            text-align: center;
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #eaeaea;
            color: #666;
            font-size: 12px;
        }
        .product-image {
            width: 80px;
            height: 80px;
            background-color: #f0f0f0;
            border-radius: 8px;
            margin-right: 15px;
        }
        .summary {
            background-color: #f9f9f9;
            padding: 20px;
            border-radius: 8px;
            margin: 20px 0;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="card">
            <div class="header">
                <div class="logo">
                    <span>JJ</span><span>HOUSE</span>
                </div>
            </div>

            <h1 style="font-size: 24px; margin-bottom: 10px;">Your Pre-order is Ready! 🎉</h1>
            <p style="color: #666;">Hi {{ $user->name }},</p>
            <p>Good news! Your pre-ordered item is now ready to be shipped.</p>

            <div class="summary">
                <div style="display: flex; align-items: center; margin-bottom: 15px;">
                    <div class="product-image" style="background-color: #eaeaea;"></div>
                    <div>
                        <strong style="font-size: 16px;">{{ $product->name }}</strong>
                        <p style="margin: 5px 0 0; color: #666; font-size: 14px;">Quantity: {{ $orderItem->quantity }}</p>
                    </div>
                </div>

                @if($isDp && $remainingPayment > 0)
                <div style="background-color: #fef3c7; padding: 12px; border-radius: 6px; margin-top: 15px;">
                    <p style="margin: 0; font-size: 14px;">
                        <strong>⚠️ Remaining Payment Required</strong><br>
                        You still have remaining payment of <strong>Rp {{ number_format($remainingPayment, 0, ',', '.') }}</strong>.
                        Please complete your payment to proceed with shipping.
                    </p>
                    @if($paymentLink)
                    <a href="{{ $paymentLink }}" class="btn" style="display: inline-block; margin-top: 10px;">Complete Payment →</a>
                    @endif
                </div>
                @else
                <div style="background-color: #d1fae5; padding: 12px; border-radius: 6px; margin-top: 15px;">
                    <p style="margin: 0; font-size: 14px;">
                        ✅ Your item is fully paid and ready to ship! We'll notify you once it's on the way.
                    </p>
                </div>
                @endif
            </div>

            <div style="text-align: center;">
                <a href="{{ url('/dashboard/orders/' . $order->id) }}" class="btn">View My Order</a>
            </div>

            <div style="margin-top: 30px; text-align: center;">
                <p style="color: #666; font-size: 14px;">Need help? Contact us at <a href="mailto:hello@jjhouse.com" style="color: #000;">hello@jjhouse.com</a></p>
            </div>

            <div class="footer">
                <p>&copy; {{ date('Y') }} JJHOUSE. All rights reserved.</p>
                <p style="margin-top: 10px;">
                    <a href="{{ url('/') }}" style="color: #666; text-decoration: none;">Shop</a> &nbsp;|&nbsp;
                    <a href="{{ url('/about') }}" style="color: #666; text-decoration: none;">About</a> &nbsp;|&nbsp;
                    <a href="{{ url('/contact') }}" style="color: #666; text-decoration: none;">Contact</a>
                </p>
            </div>
        </div>
    </div>
</body>
</html>
