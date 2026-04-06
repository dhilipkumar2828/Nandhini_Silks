<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Return Status Updated - Nandhini Silks</title>
    <style>
        body { font-family: 'Plus Jakarta Sans', Arial, sans-serif; background-color: #f7f7f7; margin: 0; padding: 0; }
        .container { max-width: 600px; margin: 20px auto; background-color: #ffffff; border-radius: 15px; overflow: hidden; box-shadow: 0 4px 25px rgba(0,0,0,0.05); }
        .header { background: linear-gradient(90deg, #a91b43 0%, #fbb624 100%); padding: 40px 20px; text-align: center; color: #ffffff; }
        .header h1 { margin: 0; font-size: 28px; font-weight: 800; text-transform: uppercase; letter-spacing: 2px; }
        .content { padding: 40px 30px; line-height: 1.6; color: #444444; }
        .status-badge { display: inline-block; padding: 10px 20px; border-radius: 50px; font-weight: 900; font-size: 16px; text-transform: uppercase; margin: 15px 0; letter-spacing: 1px; }
        
        .status-approved { background-color: #ecfdf5; color: #10b981; border: 1px solid #10b981; }
        .status-rejected { background-color: #fef2f2; color: #ef4444; border: 1px solid #ef4444; }
        .status-picked { background-color: #eff6ff; color: #3b82f6; border: 1px solid #3b82f6; }
        .status-received { background-color: #f5f3ff; color: #8b5cf6; border: 1px solid #8b5cf6; }
        .status-refunded { background-color: #f0fdf4; color: #16a34a; border: 1px solid #16a34a; }
        
        .order-info { background-color: #fdfaf0; border: 1px dashed #ad8b4e; padding: 20px; border-radius: 12px; margin: 25px 0; }
        .order-info p { margin: 5px 0; font-size: 14px; color: #555; }
        .order-info strong { color: #a91b43; }
        .footer { background-color: #fafafa; padding: 30px; text-align: center; color: #888; font-size: 12px; border-top: 1px solid #eeeeee; }
        .button { display: inline-block; padding: 16px 40px; background-color: #a91b43; color: #ffffff !important; text-decoration: none; border-radius: 12px; font-weight: 800; font-size: 16px; margin-top: 25px; box-shadow: 0 10px 20px rgba(169, 27, 67, 0.2); }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <img src="{{ isset($message) ? $message->embed(public_path('images/nandhini-logo.png')) : asset('images/nandhini-logo.png') }}" alt="Nandhini Silks" style="max-height: 80px; width: auto; margin-bottom: 15px; background: white; padding: 10px; border-radius: 12px;">
            <h1>Nandhini Silks</h1>
        </div>
        <div class="content">
            <h2 style="color: #111; font-weight: 800; margin-top: 0;">Update on Your Return Request</h2>
            <p>Hello, {{ $order->customer_name }}! We have updated the status of your return request for <strong>Order #{{ $order->order_number }}</strong>.</p>

            <div style="text-align: center;">
                <span class="status-badge 
                    @if($order->return_status == 'approved') status-approved 
                    @elseif($order->return_status == 'rejected') status-rejected 
                    @elseif($order->return_status == 'picked') status-picked
                    @elseif($order->return_status == 'received') status-received
                    @elseif($order->return_status == 'refunded') status-refunded
                    @endif">
                    {{ strtoupper($order->return_status) }}
                </span>
            </div>

            @if($order->return_admin_notes)
            <div class="order-info">
                <p><strong>Admin Message:</strong></p>
                <p style="background: #fff; padding: 10px; border-radius: 8px; border: 1px solid #eee; margin-top: 5px;">{{ $order->return_admin_notes }}</p>
            </div>
            @endif

            <div class="order-info">
                <p><strong>Order Number:</strong> #{{ $order->order_number }}</p>
                <p><strong>Order Date:</strong> {{ $order->created_at->format('d M, Y') }}</p>
                @if($order->reverse_awb)
                <p><strong>Reverse Tracking:</strong> {{ $order->reverse_awb }}</p>
                @endif
            </div>

            <center>
                <a href="{{ route('order-detail', ['id' => $order->id]) }}" class="button">View Order Details</a>
            </center>
        </div>
        <div class="footer">
            <p>&copy; {{ date('Y') }} Nandhini Silks. All Rights Reserved.</p>
        </div>
    </div>
</body>
</html>
