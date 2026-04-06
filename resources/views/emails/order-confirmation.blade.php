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
        /* Timeline Styles */
        .timeline { margin: 30px 0; padding: 20px 0; border-top: 1px solid #f0f0f0; border-bottom: 1px solid #f0f0f0; }
        .timeline-table { width: 100%; border-collapse: collapse; table-layout: fixed; }
        .timeline-step { text-align: center; vertical-align: top; }
        .step-circle { width: 24px; height: 24px; border-radius: 50%; display: inline-block; background-color: #e5e7eb; border: 4px solid #f3f4f6; }
        .step-active { background-color: #a91b43 !important; border-color: #fdf2f8 !important; }
        .step-completed { background-color: #10b981 !important; border-color: #ecfdf5 !important; }
        .step-line { height: 4px; background-color: #e5e7eb; margin-top: 14px; }
        .line-active { background-color: #a91b43 !important; }
        .line-completed { background-color: #10b981 !important; }
        .step-label { font-size: 10px; font-weight: 700; color: #94a3b8; text-transform: uppercase; margin-top: 8px; display: block; letter-spacing: 0.5px; }
        .label-active { color: #a91b43 !important; }
        .label-completed { color: #10b981 !important; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <img src="{{ isset($message) ? $message->embed(public_path('images/nandhini-logo.png')) : asset('images/nandhini-logo.png') }}" alt="Nandhini Silks" style="max-height: 80px; width: auto; margin-bottom: 15px; background: white; padding: 10px; border-radius: 12px;">
            <h1>Nandhini Silks</h1>
        </div>
        <div class="content">
            <h2 style="color: #111; font-weight: 800; margin-top: 0;">Hello, {{ $order->customer_name }}!</h2>
            <p>We've received your order and are spinning our looms to get it ready for shipment. Thank you for choosing Nandhini Silks for your celebration.</p>
            
            <div class="timeline">
                <table class="timeline-table">
                    <tr>
                        <td class="timeline-step">
                            <div class="step-circle step-active"></div>
                            <span class="step-label label-active">Placed</span>
                        </td>
                        <td style="vertical-align: top; padding-top: 10px;">
                            <div class="step-line"></div>
                        </td>
                        <td class="timeline-step">
                            <div class="step-circle"></div>
                            <span class="step-label">Shipped</span>
                        </td>
                        <td style="vertical-align: top; padding-top: 10px;">
                            <div class="step-line"></div>
                        </td>
                        <td class="timeline-step">
                            <div class="step-circle"></div>
                            <span class="step-label">Arriving</span>
                        </td>
                        <td style="vertical-align: top; padding-top: 10px;">
                            <div class="step-line"></div>
                        </td>
                        <td class="timeline-step">
                            <div class="step-circle"></div>
                            <span class="step-label">Delivered</span>
                        </td>
                    </tr>
                </table>
                <div style="text-align: center; margin-top: 15px;">
                    <span style="font-size: 12px; font-weight: 800; color: #a91b43; text-transform: uppercase; letter-spacing: 1px;">
                        Current Update: ORDER PLACED
                    </span>
                </div>
            </div>
            
            <div class="order-info">
                <p><strong>Order Number:</strong> #{{ $order->order_number }}</p>
                <p><strong>Order Date:</strong> {{ $order->created_at->format('d M, Y') }}</p>
                <p><strong>Payment Status:</strong> {{ strtoupper($order->payment_status) }}</p>
                <p><strong>Delivery To:</strong> {{ $order->delivery_address }}</p>
            </div>

            <h3 style="color: #111; border-bottom: 2px solid #fdf2f8; padding-bottom: 10px; margin-top: 35px;">Order Summary</h3>
            <table class="table" style="width: 100%;">
                <thead>
                    <tr>
                        <th style="text-align: left; padding: 12px; color: #111; border-bottom: 2px solid #f0f0f0;">Product</th>
                        <th style="text-align: center; padding: 12px; color: #111; border-bottom: 2px solid #f0f0f0;">Qty</th>
                        <th style="text-align: right; padding: 12px; color: #111; border-bottom: 2px solid #f0f0f0;">Price</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($order->items as $item)
                    <tr>
                        <td style="padding: 15px 12px; border-bottom: 1px solid #f9f9f9;">
                            <span style="font-weight: 700; color: #111; font-size: 15px;">{{ $item->product_name }}</span>
                            @if($item->size || $item->color)
                            <br><span style="color: #999; font-size: 11px;">{{ $item->size }}{{ $item->size && $item->color ? ' | ' : '' }}{{ $item->color }}</span>
                            @endif
                        </td>
                        <td style="padding: 15px 12px; border-bottom: 1px solid #f9f9f9; text-align: center; font-weight: 700; color: #111;">{{ $item->quantity }}</td>
                        <td style="padding: 15px 12px; border-bottom: 1px solid #f9f9f9; text-align: right; font-weight: 700; color: #111;">₹{{ number_format($item->total, 0) }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>

            <!-- Totals Section using Table for better alignment -->
            <table width="100%" border="0" cellspacing="0" cellpadding="0" style="margin-top: 30px;">
                <tr>
                    <td align="right">
                        <table border="0" cellspacing="0" cellpadding="0" style="width: 250px;">
                            <tr>
                                <td style="padding-bottom: 8px; color: #666; font-size: 14px;">Subtotal:</td>
                                <td style="padding-bottom: 8px; font-weight: 700; color: #111; text-align: right; font-size: 14px;">₹{{ number_format($order->sub_total, 0) }}</td>
                            </tr>
                            @if($order->tax > 0)
                            <tr>
                                <td style="padding-bottom: 8px; color: #666; font-size: 14px; text-transform: uppercase;">Tax:</td>
                                <td style="padding-bottom: 8px; font-weight: 700; color: #111; text-align: right; font-size: 14px;">₹{{ number_format($order->tax, 2) }}</td>
                            </tr>
                            @endif
                            <tr>
                                <td style="padding-bottom: 8px; color: #666; font-size: 14px; text-transform: uppercase;">Shipping:</td>
                                <td style="padding-bottom: 8px; font-weight: 700; color: #111; text-align: right; font-size: 14px;">₹{{ number_format($order->shipping, 2) }}</td>
                            </tr>
                            @if($order->discount > 0)
                            <tr>
                                <td style="padding-bottom: 8px; color: #10b981; font-size: 14px;">Discount:</td>
                                <td style="padding-bottom: 8px; font-weight: 700; color: #10b981; text-align: right; font-size: 14px;">-₹{{ number_format($order->discount, 0) }}</td>
                            </tr>
                            @endif
                            <tr>
                                <td style="padding-top: 15px; border-top: 2px solid #f0f0f0; font-weight: 800; color: #a91b43; font-size: 18px;">TOTAL:</td>
                                <td style="padding-top: 15px; border-top: 2px solid #f0f0f0; font-weight: 800; color: #a91b43; text-align: right; font-size: 24px;">₹{{ number_format($order->grand_total, 0) }}</td>
                            </tr>
                        </table>
                    </td>
                </tr>
            </table>

            <center>
                <a href="{{ route('order-detail', ['id' => $order->id]) }}" class="button" style="display: inline-block; padding: 16px 40px; background-color: #a91b43; color: #ffffff !important; text-decoration: none; border-radius: 12px; font-weight: 800; font-size: 16px; margin-top: 35px;">Track Your Order</a>
            </center>
        </div>
        <div class="footer">
            <p>&copy; {{ date('Y') }} Nandhini Silks. Arani - 632317, Tamil Nadu.</p>
            <p>Wait times are tough, but excellence takes time. We appreciate your patience!</p>
        </div>
    </div>
</body>
</html>
