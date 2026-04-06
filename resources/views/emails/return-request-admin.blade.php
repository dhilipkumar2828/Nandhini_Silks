<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>New Return Request - Nandhini Silks</title>
    <style>
        body { font-family: 'Plus Jakarta Sans', Arial, sans-serif; background-color: #f7f7f7; margin: 0; padding: 0; }
        .container { max-width: 600px; margin: 20px auto; background-color: #ffffff; border-radius: 15px; overflow: hidden; box-shadow: 0 4px 25px rgba(0,0,0,0.05); }
        .header { background: linear-gradient(90deg, #a91b43 0%, #fbb624 100%); padding: 40px 20px; text-align: center; color: #ffffff; }
        .header h1 { margin: 0; font-size: 28px; font-weight: 800; text-transform: uppercase; letter-spacing: 2px; }
        .content { padding: 40px 30px; line-height: 1.6; color: #444444; }
        .status-badge { display: inline-block; padding: 8px 16px; border-radius: 50px; font-weight: 900; font-size: 14px; text-transform: uppercase; margin: 15px 0; letter-spacing: 1px; background-color: #fefce8; color: #facc15; border: 1px solid #facc15; }
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
            <h2 style="color: #111; font-weight: 800; margin-top: 0;">New Return Request Received</h2>
            <p>A customer has requested a return for <strong>Order #{{ $order->order_number }}</strong>.</p>

            <div class="order-info">
                <p><strong>Customer Name:</strong> {{ $order->customer_name }}</p>
                <p><strong>Customer Email:</strong> {{ $order->customer_email }}</p>
                <p><strong>Order Number:</strong> #{{ $order->order_number }}</p>
                <p><strong>Order Date:</strong> {{ $order->created_at->format('d M, Y') }}</p>
                <p><strong>Return Reason:</strong></p>
                <p style="background: #fff; padding: 10px; border-radius: 8px; border: 1px solid #eee; margin-top: 5px;">{{ $order->return_reason }}</p>
            </div>

            <center>
                <a href="{{ route('admin.orders.show', $order->id) }}" class="button">Review Return Request</a>
            </center>
        </div>
        <div class="footer">
            <p>&copy; {{ date('Y') }} Nandhini Silks Admin Portal.</p>
        </div>
    </div>
</body>
</html>
