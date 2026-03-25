<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Confirmation - Nandhini Silks</title>
    <style>
        body { font-family: 'Plus Jakarta Sans', Arial, sans-serif; background-color: #f7f7f7; margin: 0; padding: 0; }
        .container { max-width: 600px; margin: 20px auto; background-color: #ffffff; border-radius: 15px; overflow: hidden; box-shadow: 0 4px 25px rgba(0,0,0,0.05); }
        .header { background: linear-gradient(90deg, #a91b43 0%, #fbb624 100%); padding: 40px 20px; text-align: center; color: #ffffff; }
        .header h1 { margin: 0; font-size: 28px; font-weight: 800; text-transform: uppercase; letter-spacing: 2px; }
        .content { padding: 40px 30px; line-height: 1.6; color: #444444; }
        .order-info { background-color: #fdfaf0; border: 1px dashed #ad8b4e; padding: 20px; border-radius: 12px; margin: 25px 0; }
        .order-info p { margin: 5px 0; font-size: 14px; color: #555; }
        .order-info strong { color: #a91b43; }
        .table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        .table th { text-align: left; padding: 12px; border-bottom: 2px solid #f0f0f0; color: #111; font-size: 13px; text-transform: uppercase; }
        .table td { padding: 15px 12px; border-bottom: 1px solid #f9f9f9; font-size: 14px; vertical-align: middle; }
        .product-img { width: 45px; height: 60px; border-radius: 6px; object-fit: cover; margin-right: 12px; vertical-align: middle; background: #eee; }
        .totals { margin-top: 30px; text-align: right; }
        .totals-row { display: flex; justify-content: flex-end; margin-bottom: 8px; font-size: 14px; }
        .totals-label { width: 120px; color: #666; }
        .totals-value { width: 100px; font-weight: 700; color: #111; }
        .grand-total { font-size: 20px; color: #a91b43; border-top: 2px solid #f0f0f0; padding-top: 15px; margin-top: 15px; }
        .button { display: inline-block; padding: 16px 40px; background-color: #a91b43; color: #ffffff; text-decoration: none; border-radius: 12px; font-weight: 800; font-size: 16px; margin-top: 35px; box-shadow: 0 10px 20px rgba(169, 27, 67, 0.2); }
        .footer { background-color: #fafafa; padding: 30px; text-align: center; color: #888; font-size: 12px; border-top: 1px solid #eeeeee; }
        .social-link { color: #a91b43; text-decoration: none; font-weight: 700; }
        @media only screen and (max-width: 600px) {
            .container { margin: 0; border-radius: 0; width: 100%; }
            .content { padding: 25px 20px; }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <img src="{{ url('images/image 1.png') }}" alt="Nandhini Silks" style="max-height: 80px; width: auto; margin-bottom: 15px; background: white; padding: 10px; border-radius: 12px; mix-blend-mode: multiply;" onerror="this.src='https://nandhinisilks.com/images/image%201.png'; this.onerror=null;">
            <h1>Nandhini Silks</h1>
            <p style="margin: 5px 0 0; opacity: 0.9; font-weight: 600;">Elegance Redefined</p>
        </div>
        <div class="content">
            <h2 style="color: #111; font-weight: 800; margin-top: 0;">Namaste, {{ $order->customer_name }}!</h2>
            <p>Aapka swagat hai! We've received your order and are spinning our looms to get it ready for shipment. Thank you for choosing Nandhini Silks for your celebration.</p>
            
            <div class="order-info">
                <p><strong>Order Number:</strong> #{{ $order->order_number }}</p>
                <p><strong>Order Date:</strong> {{ $order->created_at->format('d M, Y') }}</p>
                <p><strong>Payment Status:</strong> {{ strtoupper($order->payment_status) }}</p>
                <p><strong>Delivery To:</strong> {{ $order->delivery_address }}</p>
            </div>

            <h3 style="color: #111; border-bottom: 2px solid #fdf2f8; padding-bottom: 10px; margin-top: 35px;">Order Summary</h3>
            <table class="table">
                <thead>
                    <tr>
                        <th>Product</th>
                        <th style="text-align: center;">Qty</th>
                        <th style="text-align: right;">Price</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($order->items as $item)
                    <tr>
                        <td>
                            <span style="font-weight: 700; color: #111; font-size: 15px;">{{ $item->product_name }}</span>
                            @if($item->size || $item->color)
                            <br><span style="color: #999; font-size: 11px;">{{ $item->size }}{{ $item->size && $item->color ? ' | ' : '' }}{{ $item->color }}</span>
                            @endif
                        </td>
                        <td style="text-align: center; font-weight: 700;">{{ $item->quantity }}</td>
                        <td style="text-align: right; font-weight: 700; color: #111;">₹{{ number_format($item->total, 2) }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>

            <div class="totals">
                <div class="totals-row">
                    <span class="totals-label">Subtotal:</span>
                    <span class="totals-value">₹{{ number_format($order->sub_total, 2) }}</span>
                </div>
                @if($order->tax > 0)
                <div class="totals-row">
                    <span class="totals-label">Tax:</span>
                    <span class="totals-value">₹{{ number_format($order->tax, 2) }}</span>
                </div>
                @endif
                <div class="totals-row">
                    <span class="totals-label">Shipping:</span>
                    <span class="totals-value">₹{{ number_format($order->shipping, 2) }}</span>
                </div>
                @if($order->discount > 0)
                <div class="totals-row">
                    <span class="totals-label" style="color: #10b981;">Discount:</span>
                    <span class="totals-value" style="color: #10b981;">-₹{{ number_format($order->discount, 2) }}</span>
                </div>
                @endif
                <div class="totals-row grand-total">
                    <span class="totals-label" style="font-weight: 800;">TOTAL:</span>
                    <span class="totals-value" style="font-size: 24px; font-weight: 800;">₹{{ number_format($order->grand_total, 2) }}</span>
                </div>
            </div>

            <center>
                <a href="{{ route('order-detail', ['id' => $order->id]) }}" class="button">Track Your Order</a>
            </center>
        </div>
        <div class="footer">
            <p>&copy; {{ date('Y') }} Nandhini Silks. Arani - 632317, Tamil Nadu.</p>
            <p>Wait times are tough, but excellence takes time. We appreciate your patience!</p>
            <p>Questions? Contact us at <a href="mailto:nandhinisilks.arani@gmail.com" class="social-link">nandhinisilks.arani@gmail.com</a></p>
        </div>
    </div>
</body>
</html>
