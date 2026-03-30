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
        .status-cancelled { background-color: #fef2f2; color: #ef4444; border: 1px solid #ef4444; }
        
        .order-info { background-color: #fdfaf0; border: 1px dashed #ad8b4e; padding: 20px; border-radius: 12px; margin: 25px 0; }
        .order-info p { margin: 5px 0; font-size: 14px; color: #555; }
        .order-info strong { color: #a91b43; }
        .table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        .table td { padding: 15px 12px; border-bottom: 1px solid #f9f9f9; font-size: 14px; vertical-align: middle; }
        .button { display: inline-block; padding: 16px 40px; background-color: #a91b43; color: #ffffff; text-decoration: none; border-radius: 12px; font-weight: 800; font-size: 16px; margin-top: 35px; box-shadow: 0 10px 20px rgba(169, 27, 67, 0.2); }
        .footer { background-color: #fafafa; padding: 30px; text-align: center; color: #888; font-size: 12px; border-top: 1px solid #eeeeee; }
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

            <div style="text-align: center;">
                <span class="status-badge 
                    @if($order->order_status == 'processing') status-processing 
                    @elseif($order->order_status == 'dispatched') status-dispatched 
                    @elseif($order->order_status == 'delivered') status-delivered 
                    @elseif($order->order_status == 'cancelled') status-cancelled 
                    @endif">
                    Current Status: {{ strtoupper($order->order_status) }}
                </span>
            </div>

            <div class="order-info">
                <p><strong>Order Number:</strong> #{{ $order->order_number }}</p>
                @if($order->tracking_number)
                <p><strong>Tracking Number:</strong> #{{ $order->tracking_number }}</p>
                @endif
                @if($order->courier_name)
                <p><strong>Courier:</strong> {{ $order->courier_name }}</p>
                @endif
                <p><strong>Delivery Address:</strong> {{ $order->delivery_address }}</p>
            </div>

            <h3 style="color: #111; border-bottom: 2px solid #fdf2f8; padding-bottom: 10px; margin-top: 35px;">Items</h3>
            <table class="table" style="width: 100%;">
                <tbody>
                    @foreach($order->items as $item)
                    <tr>
                        <td style="padding: 15px 12px; border-bottom: 1px solid #f9f9f9;">
                            <span style="font-weight: 700; color: #111; font-size: 15px;">{{ $item->product_name }}</span>
                            @if($item->size || $item->color)
                            <br><span style="color: #999; font-size: 11px;">{{ $item->size }}{{ $item->size && $item->color ? ' | ' : '' }}{{ $item->color }}</span>
                            @endif
                        </td>
                        <td style="padding: 15px 12px; border-bottom: 1px solid #f9f9f9; text-align: center; font-weight: 700; color: #111;">{{ $item->quantity }}x</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>

            <div class="order-summary" style="background-color: #fdfaf0; border: 1px dashed #ad8b4e; padding: 20px; border-radius: 12px; margin: 25px 0;">
                <h3 style="color: #111; border-bottom: 2px solid #fdf2f8; padding-bottom: 10px; margin-top: 35px;">Order Summary</h3>
                <table class="table" style="width: 100%;">
                    <tbody>
                        <tr>
                            <td style="padding: 15px 12px; border-bottom: 1px solid #f9f9f9;">Subtotal</td>
                            <td style="padding: 15px 12px; border-bottom: 1px solid #f9f9f9; text-align: right; font-weight: 700; color: #111;">&#8377;{{ number_format($order->sub_total, 2) }}</td>
                        </tr>
                        <tr>
                            <td style="padding: 15px 12px; border-bottom: 1px solid #f9f9f9;">Shipping</td>
                            <td style="padding: 15px 12px; border-bottom: 1px solid #f9f9f9; text-align: right; font-weight: 700; color: #111;">{{ $order->shipping > 0 ? '₹'.number_format($order->shipping, 2) : 'FREE' }}</td>
                        </tr>
                        <tr>
                            <td style="padding: 15px 12px; border-bottom: 1px solid #f9f9f9;">Tax (GST)</td>
                            <td style="padding: 15px 12px; border-bottom: 1px solid #f9f9f9; text-align: right; font-weight: 700; color: #111;">&#8377;{{ number_format($order->tax, 2) }}</td>
                        </tr>
                        @if($order->discount > 0)
                        <tr>
                            <td style="padding: 15px 12px; border-bottom: 1px solid #f9f9f9;">Discount</td>
                            <td style="padding: 15px 12px; border-bottom: 1px solid #f9f9f9; text-align: right; font-weight: 700; color: #111;">-&#8377;{{ number_format($order->discount, 2) }}</td>
                        </tr>
                        @endif
                        <tr>
                            <td style="padding: 15px 12px; border-bottom: 1px solid #f9f9f9;"><strong>Total</strong></td>
                            <td style="padding: 15px 12px; border-bottom: 1px solid #f9f9f9; text-align: right; font-weight: 700; color: #111;">&#8377;{{ number_format($order->grand_total, 2) }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>

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
