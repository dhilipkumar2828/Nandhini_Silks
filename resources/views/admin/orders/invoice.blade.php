<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Invoice #{{ str_pad($order->id, 6, '0', STR_PAD_LEFT) }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: DejaVu Sans, sans-serif; font-size: 11px; color: #1e293b; background: #fff; }

        @page { margin: 0px; }
        .page { padding: 10px 40px 40px; }

        /* Header */
        .header { display: table; width: 100%; margin-bottom: 30px; border-bottom: 3px solid #a91b43; padding-bottom: 20px; }
        .header-left { display: table-cell; width: 55%; vertical-align: top; }
        .header-right { display: table-cell; width: 45%; text-align: right; vertical-align: top; }
        .brand-name { font-size: 24px; font-weight: bold; color: #a91b43; letter-spacing: 1px; }
        .brand-tagline { font-size: 9px; color: #94a3b8; text-transform: uppercase; letter-spacing: 2px; margin-top: 2px; }
        .brand-address { font-size: 9px; color: #64748b; margin-top: 8px; line-height: 1.6; }

        .invoice-title { font-size: 28px; font-weight: bold; color: #1e293b; }
        .invoice-number { font-size: 11px; color: #64748b; margin-top: 4px; }
        .invoice-date { font-size: 9px; color: #94a3b8; margin-top: 2px; text-transform: uppercase; letter-spacing: 1px; }

        /* Info Sections */
        .info-row { display: table; width: 100%; margin-bottom: 25px; }
        .info-cell { display: table-cell; width: 50%; vertical-align: top; }
        .info-cell:last-child { text-align: right; }
        .info-label { font-size: 8px; font-weight: bold; color: #a91b43; text-transform: uppercase; letter-spacing: 1.5px; margin-bottom: 6px; }
        .info-value { font-size: 10px; color: #1e293b; line-height: 1.7; }
        .info-value strong { font-weight: bold; }

        /* Status Badges */
        .badge { display: inline-block; padding: 2px 8px; border-radius: 4px; font-size: 8px; font-weight: bold; text-transform: uppercase; letter-spacing: 1px; }
        .badge-paid { background: #dcfce7; color: #16a34a; }
        .badge-pending { background: #fef9c3; color: #ca8a04; }
        .badge-failed { background: #fee2e2; color: #dc2626; }
        .badge-refunded { background: #e0e7ff; color: #4f46e5; }
        .badge-delivered { background: #dcfce7; color: #16a34a; }
        .badge-dispatched { background: #dbeafe; color: #2563eb; }
        .badge-processing { background: #fef9c3; color: #ca8a04; }
        .badge-cancelled { background: #fee2e2; color: #dc2626; }

        /* Table */
        .items-table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        .items-table thead tr { background: #a91b43; color: #fff; }
        .items-table thead th { padding: 10px 12px; text-align: left; font-size: 8px; text-transform: uppercase; letter-spacing: 1px; font-weight: bold; }
        .items-table thead th:last-child { text-align: right; }
        .items-table tbody tr { border-bottom: 1px solid #f1f5f9; }
        .items-table tbody tr:nth-child(even) { background: #f8fafc; }
        .items-table tbody td { padding: 10px 12px; font-size: 10px; color: #334155; }
        .items-table tbody td:last-child { text-align: right; font-weight: bold; }
        .items-table td.product-name { font-weight: bold; color: #1e293b; }

        /* Totals */
        .totals-wrapper { display: table; width: 100%; }
        .totals-spacer { display: table-cell; width: 55%; }
        .totals-box { display: table-cell; width: 45%; }
        .total-row { display: table; width: 100%; padding: 5px 0; border-bottom: 1px solid #f1f5f9; }
        .total-label { display: table-cell; font-size: 9px; color: #64748b; text-transform: uppercase; letter-spacing: 0.5px; }
        .total-value { display: table-cell; text-align: right; font-size: 10px; font-weight: bold; color: #1e293b; }
        .grand-total-row { display: table; width: 100%; padding: 10px 0; margin-top: 4px; background: #fdf2f5; border-radius: 6px; }
        .grand-total-label { display: table-cell; padding: 0 12px; font-size: 11px; font-weight: bold; color: #a91b43; text-transform: uppercase; }
        .grand-total-value { display: table-cell; text-align: right; padding: 0 12px; font-size: 14px; font-weight: bold; color: #a91b43; }

        /* Footer */
        .footer { margin-top: 40px; padding-top: 20px; border-top: 1px solid #e2e8f0; text-align: center; }
        .footer-text { font-size: 8px; color: #94a3b8; text-transform: uppercase; letter-spacing: 1px; }
        .thank-you { font-size: 13px; font-weight: bold; color: #a91b43; margin-bottom: 6px; }

        /* Divider */
        .divider { border: none; border-top: 1px solid #e2e8f0; margin: 20px 0; }

        /* Tracking */
        .tracking-box { background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 6px; padding: 12px; margin-top: 20px; }
        .tracking-title { font-size: 8px; font-weight: bold; color: #a91b43; text-transform: uppercase; letter-spacing: 1px; margin-bottom: 8px; }
        .tracking-grid { display: table; width: 100%; }
        .tracking-cell { display: table-cell; width: 33%; }
        .tracking-label { font-size: 8px; color: #94a3b8; text-transform: uppercase; letter-spacing: 0.5px; }
        .tracking-value { font-size: 10px; font-weight: bold; color: #1e293b; margin-top: 2px; }
    </style>
</head>
<body>
<div class="page">

    {{-- Header --}}
    <div class="header">
        <div class="header-left">
            @php
                $logoPath = public_path('images/nandhini-logo.png');
                $logoBase64 = '';
                if ($logoPath && is_file($logoPath)) {
                    $logoBase64 = base64_encode(@file_get_contents($logoPath));
                }
            @endphp
            @if($logoBase64)
                <img src="data:image/png;base64,{{ $logoBase64 }}" style="height: 50px; width: auto; margin-bottom: 5px;">
            @else
                <div class="brand-name">Nandhini Silks</div>
            @endif
            <div class="brand-tagline">Premium Silk Collections</div>
            <div class="brand-address">
                Salem, Tamil Nadu, India<br>
                info@nandhinisilks.com | +91 XXXXX XXXXX
            </div>
        </div>
        <div class="header-right">
            <div class="invoice-title">INVOICE</div>
            <div class="invoice-number">#{{ str_pad($order->id, 6, '0', STR_PAD_LEFT) }}</div>
            <div class="invoice-date">{{ $order->created_at->format('d M Y, h:i A') }}</div>
        </div>
    </div>

    {{-- Customer & Order Info --}}
    <div class="info-row">
        <div class="info-cell">
            <div class="info-label">Bill To</div>
            <div class="info-value">
                <strong>{{ $order->customer_name }}</strong><br>
                {{ $order->customer_email }}<br>
                {{ $order->customer_phone }}
            </div>
        </div>
        <div class="info-cell">
            <div class="info-label">Order Status</div>
            <div class="info-value" style="margin-bottom:8px;">
                @php
                    $pClass = match($order->payment_status) { 'paid'=>'paid','failed'=>'failed','refunded'=>'refunded', default=>'pending' };
                    $oClass = match($order->order_status) { 'delivered'=>'delivered','dispatched'=>'dispatched','cancelled'=>'cancelled', default=>'processing' };
                @endphp
                <span class="badge badge-{{ $pClass }}">{{ ucfirst($order->payment_status) }}</span>
                &nbsp;
                <span class="badge badge-{{ $oClass }}">{{ ucfirst($order->order_status) }}</span>
            </div>
            <div class="info-label" style="margin-top:8px;">Payment Method</div>
            <div class="info-value">{{ strtoupper($order->payment_method) }}</div>
        </div>
    </div>

    {{-- Delivery Address --}}
    <div style="margin-bottom: 20px;">
        <div class="info-label">Delivery Address</div>
        <div class="info-value" style="margin-top: 6px; background:#f8fafc; padding: 10px; border-radius: 6px; border: 1px solid #e2e8f0;">
            {{ $order->delivery_address }}
        </div>
    </div>

    {{-- Items Table --}}
    <table class="items-table">
        <thead>
            <tr>
                <th style="width:5%">#</th>
                <th style="width:10%; text-align:center;">Image</th>
                <th style="width:40%">Product</th>
                <th style="width:15%; text-align:center;">Qty</th>
                <th style="width:15%; text-align:right;">Unit Price</th>
                <th style="width:15%; text-align:right;">Total</th>
            </tr>
        </thead>
        <tbody>
            @foreach($order->items as $i => $item)
            <tr>
                <td style="vertical-align: middle;">{{ $i + 1 }}</td>
                <td style="text-align:center; vertical-align: middle; padding: 5px;">
                    @php
                        $itemPath = $item->product_image;
                        if (!$itemPath && $item->product) {
                            $itemPath = $item->product->image_path;
                            if (!$itemPath && !empty($item->product->images)) {
                                $images = $item->product->images;
                                if (is_string($images)) $images = json_decode($images, true);
                                if (is_array($images) && count($images) > 0) $itemPath = $images[0];
                            }
                        }
                        
                        $fullPath = null;
                        if ($itemPath) {
                            if (Str::startsWith($itemPath, 'products/') || Str::startsWith($itemPath, 'categories/')) {
                                $fullPath = public_path('uploads/' . $itemPath);
                            } else {
                                $fullPath = public_path('images/' . $itemPath);
                            }
                        }
                        
                        if (!$fullPath || !is_file($fullPath)) {
                            $fullPath = public_path('images/pro1.png');
                        }
                        
                        // Final safety check for fallback image
                        if (!is_file($fullPath)) {
                            $logoBase64 = null; // We'll just show text or nothing
                        }
                    @endphp
                    @if(isset($fullPath) && is_file($fullPath))
                        <img src="data:image/png;base64,{{ base64_encode(@file_get_contents($fullPath)) }}" style="width: 40px; height: 40px; border-radius: 4px;">
                    @else
                        <div style="width: 40px; height: 40px; background: #f1f5f9; border-radius: 4px; display: flex; align-items: center; justify-content: center; font-size: 8px; color: #94a3b8;">No Image</div>
                    @endif
                </td>
                <td class="product-name" style="vertical-align: middle;">
                    {{ $item->product_name }}
                    @if($item->size || $item->color)
                    <div style="font-size: 8px; color: #64748b; font-weight: normal; margin-top: 2px;">
                        @if($item->size) Size: {{ $item->size }} @endif
                        @if($item->color) {{ $item->size ? '|' : '' }} Color: {{ $item->color }} @endif
                    </div>
                    @endif
                </td>
                <td style="text-align:center; vertical-align: middle;">{{ $item->quantity }}</td>
                <td style="text-align:right; vertical-align: middle;">&#8377;{{ number_format($item->price, 2) }}</td>
                <td style="vertical-align: middle;">&#8377;{{ number_format($item->total, 2) }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    {{-- Totals --}}
    <div class="totals-wrapper">
        <div class="totals-spacer">
            @if($order->coupon_code)
            <div class="tracking-box">
                <div class="tracking-title">Coupon Applied</div>
                <div class="tracking-value" style="color:#a91b43;">{{ $order->coupon_code }}</div>
            </div>
            @endif
        </div>
        <div class="totals-box">
            <div class="total-row">
                <div class="total-label">Sub Total</div>
                <div class="total-value">&#8377;{{ number_format($order->sub_total, 2) }}</div>
            </div>
            @if($order->discount > 0)
            <div class="total-row">
                <div class="total-label" style="color:#dc2626;">Discount</div>
                <div class="total-value" style="color:#dc2626;">- &#8377;{{ number_format($order->discount, 2) }}</div>
            </div>
            @endif
            <div class="total-row">
                <div class="total-label">Tax</div>
                <div class="total-value">&#8377;{{ number_format($order->tax, 2) }}</div>
            </div>
            <div class="total-row">
                <div class="total-label">Shipping</div>
                <div class="total-value">&#8377;{{ number_format($order->shipping, 2) }}</div>
            </div>
            <div class="grand-total-row">
                <div class="grand-total-label">Grand Total</div>
                <div class="grand-total-value">&#8377;{{ number_format($order->grand_total, 2) }}</div>
            </div>
        </div>
    </div>

    {{-- Tracking Info --}}
    @if($order->tracking_number)
    <div class="tracking-box">
        <div class="tracking-title">Shipment Tracking</div>
        <div class="tracking-grid">
            <div class="tracking-cell">
                <div class="tracking-label">Courier</div>
                <div class="tracking-value">{{ $order->courier_name ?? 'N/A' }}</div>
            </div>
            <div class="tracking-cell">
                <div class="tracking-label">Tracking Number</div>
                <div class="tracking-value">{{ $order->tracking_number }}</div>
            </div>
        </div>
    </div>
    @endif

    {{-- Footer --}}
    <div class="footer">
        <div class="thank-you">Thank you for shopping with Nandhini Silks!</div>
        <div class="footer-text">This is a computer-generated invoice. No signature required.</div>
        <div class="footer-text" style="margin-top:4px;">For queries: info@nandhinisilks.com</div>
    </div>

</div>
</body>
</html>
