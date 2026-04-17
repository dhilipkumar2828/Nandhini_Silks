<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>New Order Alert - Nandhini Silks</title>
    <style>
        body { font-family: 'Inter', Arial, sans-serif; background-color: #fce7f3; margin: 0; padding: 20px; }
        .alert-card { max-width: 600px; margin: 0 auto; background-color: #ffffff; border-radius: 20px; box-shadow: 0 15px 40px rgba(169, 27, 67, 0.1); border-top: 10px solid #a91b43; }
        .header { padding: 30px; border-bottom: 2px solid #fdf2f8; text-align: center; }
        .badge { background-color: #fdf2f8; color: #a91b43; padding: 5px 15px; border-radius: 20px; font-size: 11px; font-weight: 800; text-transform: uppercase; margin-bottom: 15px; display: inline-block; letter-spacing: 1px; }
        .title { margin: 0; font-size: 24px; font-weight: 800; color: #111; }
        .details { padding: 30px; }
        .info-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 30px; }
        .info-item { background: #fafafa; padding: 15px; border-radius: 12px; }
        .info-label { display: block; font-size: 11px; color: #888; text-transform: uppercase; font-weight: 700; margin-bottom: 5px; }
        .info-value { display: block; font-size: 14px; color: #111; font-weight: 700; }
        .order-table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        .order-table th { text-align: left; font-size: 12px; color: #888; padding: 10px; border-bottom: 1px solid #eee; text-transform: uppercase; }
        .order-table td { padding: 12px 10px; font-size: 14px; border-bottom: 1px solid #f9f9f9; color: #444; }
        .button { display: block; width: 100%; padding: 18px; background-color: #a91b43; color: #ffffff; text-decoration: none; border-radius: 15px; text-align: center; font-weight: 800; font-size: 15px; margin-top: 30px; box-sizing: border-box; }
        .footer { padding: 25px; text-align: center; font-size: 12px; color: #999; border-top: 1px solid #fdf2f8; }
    </style>
</head>
<body>
    <div class="alert-card">
        <div class="header">
            <img src="{{ isset($message) ? $message->embed(public_path('images/nandhini-logo.png')) : asset('images/nandhini-logo.png') }}" alt="Nandhini Silks" style="max-height: 50px; width: auto; background: white; padding: 8px; border-radius: 10px; margin-bottom: 12px;">
            <table width="100%" border="0" cellspacing="0" cellpadding="0">
                <tr>
                    <td align="center">
                        <span class="badge">Sales Alert</span>
                        <h1 class="title">New Sales Order!</h1>
                    </td>
                </tr>
            </table>
        </div>
        <div class="details">
            <!-- Info Table instead of Grid -->
            <table width="100%" border="0" cellspacing="0" cellpadding="0" style="margin-bottom: 30px;">
                <tr>
                    <td width="50%" style="padding-right: 10px;">
                        <div class="info-item">
                            <span class="info-label">Order #</span>
                            <span class="info-value">{{ $order->order_number }}</span>
                        </div>
                    </td>
                    <td width="50%" style="padding-left: 10px;">
                        <div class="info-item">
                            <span class="info-label">Customer</span>
                            <span class="info-value">{{ $order->customer_name }}</span>
                        </div>
                    </td>
                </tr>
            </table>

            <div class="info-item" style="margin-bottom: 30px;">
                <span class="info-label">Address</span>
                <span class="info-value">{{ $order->delivery_address }}</span>
            </div>

            <table class="order-table" width="100%">
                <thead>
                    <tr>
                        <th style="text-align: left;">Item</th>
                        <th style="text-align: center;">Qty</th>
                        <th style="text-align: right;">Total</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($order->items as $item)
                    <tr>
                        <td style="font-weight: 600;">{{ $item->product_name }}</td>
                        <td style="text-align: center;">{{ $item->quantity }}</td>
                        <td style="text-align: right; font-weight: 600;">₹{{ number_format($item->total, 0) }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>

            <!-- Revenue Table instead of Flexbox -->
            <table width="100%" border="0" cellspacing="0" cellpadding="0" style="background: #a91b43; border-radius: 15px; margin-top: 30px; color: #fff;">
                <tr>
                    <td style="padding: 25px;">
                        <span style="font-weight: 700; opacity: 0.8; text-transform: uppercase; font-size: 14px;">Total Revenue</span>
                    </td>
                    <td style="padding: 25px; text-align: right;">
                        <span style="font-size: 26px; font-weight: 900;">₹{{ number_format($order->grand_total, 0) }}</span>
                    </td>
                </tr>
            </table>

            <a href="{{ url('/admin/orders/'.$order->id) }}" class="button" style="display: block; width: 100%; border-radius: 15px; background-color: #a91b43; color: #ffffff !important; padding: 18px; text-align: center; text-decoration: none; font-weight: 800; font-size: 15px; box-sizing: border-box; margin-top: 30px;">Process Order in Admin Panel</a>
        </div>
        <div class="footer">
            Generated by Nandhini Silks System. <br> Time: {{ date('d M Y, H:i') }}
        </div>
    </div>
</body>
</html>
