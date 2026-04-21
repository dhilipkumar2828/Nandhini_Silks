<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $isForAdmin ? 'Admin Notification' : 'Order Update' }} - Nandhini Silks</title>
    <style>
        body { font-family: 'Plus Jakarta Sans', Arial, sans-serif; background-color: #f7f7f7; margin: 0; padding: 0; }
        .container { max-width: 600px; margin: 20px auto; background-color: #ffffff; border-radius: 15px; overflow: hidden; box-shadow: 0 4px 25px rgba(0,0,0,0.05); }
        .header { background: linear-gradient(90deg, #a91b43 0%, #fbb624 100%); padding: 40px 20px; text-align: center; color: #ffffff; }
        .header h1 { margin: 0; font-size: 28px; font-weight: 800; text-transform: uppercase; letter-spacing: 2px; }
        .content { padding: 40px 30px; line-height: 1.6; color: #444444; }
        .status-badge { display: inline-block; padding: 8px 16px; border-radius: 50px; font-weight: 900; font-size: 14px; text-transform: uppercase; margin: 15px 0; letter-spacing: 1px; }
        .status-processing { background-color: #ecfdf5; color: #10b981; border: 1px solid #10b981; }
        .status-dispatched { background-color: #fefce8; color: #facc15; border: 1px solid #facc15; }
        .status-delivered { background-color: #eff6ff; color: #3b82f6; border: 1px solid #3b82f6; }
        .status-pending { background-color: #f7f7f7; color: #64748b; border: 1px solid #64748b; }
        .status-cancelled { background-color: #fef2f2; color: #ef4444; border: 1px solid #ef4444; }
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

        @media only screen and (max-width: 600px) {
            .container { margin: 0; border-radius: 0; width: 100%; }
            .content { padding: 25px 20px; }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <img src="{{ isset($message) ? $message->embed(public_path('images/nandhini-logo.png')) : asset('images/nandhini-logo.png') }}" alt="Nandhini Silks" style="max-height: 80px; width: auto; margin-bottom: 15px; background: white; padding: 10px; border-radius: 12px;">
            <h1>Nandhini Silks</h1>
        </div>
        <div class="content">
            @if($isForAdmin)
                <h2 style="color: #111; font-weight: 800; margin-top: 0;">Admin Alert: Order Update</h2>
                <p>Order #{{ $order->order_number }} has been updated by the dashboard.</p>
            @else
                <h2 style="color: #111; font-weight: 800; margin-top: 0;">Hello, {{ $order->customer_name }}!</h2>
                <p>We're pleased to share an update on your order. Something special is coming your way from the heart of Arani weaving.</p>
            @endif

            <div class="timeline">
                {{-- <table class="timeline-table">
                    <tr>
                        <td class="timeline-step">
                            <div class="step-circle step-completed"></div>
                            <span class="step-label label-completed">Placed</span>
                        </td>
                        <td style="vertical-align: top; padding-top: 10px;">
                            <div class="step-line {{ in_array($order->order_status, ['shipped', 'dispatched', 'out for delivery', 'delivered']) ? 'line-completed' : '' }}"></div>
                        </td>
                        <td class="timeline-step">
                            <div class="step-circle {{ in_array($order->order_status, ['shipped', 'dispatched', 'out for delivery', 'delivered']) ? 'step-completed' : ($order->order_status == 'order placed' ? 'step-active' : '') }}"></div>
                            <span class="step-label {{ in_array($order->order_status, ['shipped', 'dispatched', 'out for delivery', 'delivered']) ? 'label-completed' : ($order->order_status == 'order placed' ? 'label-active' : '') }}">Shipped</span>
                        </td>
                        <td style="vertical-align: top; padding-top: 10px;">
                            <div class="step-line {{ in_array($order->order_status, ['out for delivery', 'delivered']) ? 'line-completed' : '' }}"></div>
                        </td>
                        <td class="timeline-step">
                            <div class="step-circle {{ in_array($order->order_status, ['out for delivery', 'delivered']) ? 'step-completed' : ($order->order_status == 'shipped' ? 'step-active' : '') }}"></div>
                            <span class="step-label {{ in_array($order->order_status, ['out for delivery', 'delivered']) ? 'label-completed' : ($order->order_status == 'shipped' ? 'label-active' : '') }}">Out for Delivery</span>
                        </td>
                        <td style="vertical-align: top; padding-top: 10px;">
                            <div class="step-line {{ $order->order_status == 'delivered' ? 'line-completed' : '' }}"></div>
                        </td>
                        <td class="timeline-step">
                            <div class="step-circle {{ $order->order_status == 'delivered' ? 'step-completed' : ($order->order_status == 'out for delivery' ? 'step-active' : '') }}"></div>
                            <span class="step-label {{ $order->order_status == 'delivered' ? 'label-completed' : ($order->order_status == 'out for delivery' ? 'label-active' : '') }}">Delivered</span>
                        </td>
                    </tr>
                </table> --}}
                <div style="text-align: center; margin-top: 15px;">
                    <span style="font-size: 12px; font-weight: 800; color: #a91b43; text-transform: uppercase; letter-spacing: 1px;">
                        Current Update: {{ strtoupper($order->order_status) }}
                    </span>
                </div>
            </div>

            <div class="order-info">
                <p><strong>Order Number:</strong> #{{ $order->order_number }}</p>
                <p><strong>Order Date:</strong> {{ $order->created_at->format('d M, Y') }}</p>
                @if($order->tracking_number)
                <p><strong>Tracking Number:</strong> #{{ $order->tracking_number }}</p>
                @endif
                @if($order->courier_name)
                <p><strong>Courier:</strong> {{ $order->courier_name }}</p>
                @endif
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
                            @if(!empty($item->attributes) && is_array($item->attributes))
                                @foreach($item->attributes as $attr)
                                    <br><span style="color: #666; font-size: 11px; font-weight: 700;">{{ $attr['name'] }}: {{ $attr['value'] }}</span>
                                @endforeach
                            @else
                                @if($item->size || $item->color)
                                    <br><span style="color: #666; font-size: 11px; font-weight: 700;">
                                        @if($item->size) Size: {{ $item->size }} @endif
                                        @if($item->size && $item->color) | @endif
                                        @if($item->color) Color: {{ $item->color }} @endif
                                    </span>
                                @endif
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
                                <td style="padding-bottom: 8px; font-weight: 700; color: #111; text-align: right; font-size: 14px;">₹{{ number_format($order->sub_total, 2) }}</td>
                            </tr>
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
                                <td style="padding-top: 15px; border-top: 2px solid #f0f0f0; font-weight: 800; color: #a91b43; text-align: right; font-size: 24px;">₹{{ number_format($order->grand_total, 2) }}</td>
                            </tr>
                        </table>
                    </td>
                </tr>
            </table>

            @if(!$isForAdmin)
            <center>
                <a href="{{ route('order-detail', ['id' => $order->id]) }}" class="button" style="display: inline-block; padding: 16px 40px; background-color: #a91b43; color: #ffffff !important; text-decoration: none; border-radius: 12px; font-weight: 800; font-size: 16px; margin-top: 35px;">Track Shipment</a>
            </center>
            @endif
        </div>
        <div class="footer">
            <p>&copy; {{ date('Y') }} Nandhini Silks. Arani - 632317, Tamil Nadu.</p>
        </div>
    </div>
</body>
</html>
