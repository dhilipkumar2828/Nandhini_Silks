<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Invoice #{{ $order->order_number }}</title>
    <style>
        /* Modern Typography & Reset */
        @font-face {
            font-family: 'Outfit';
            font-style: normal;
            font-weight: 400;
            src: url(https://fonts.gstatic.com/s/outfit/v11/QGYsz_OBy1qW9CzEt3lEGRY.ttf) format('truetype');
        }
        @font-face {
            font-family: 'Outfit';
            font-style: normal;
            font-weight: 700;
            src: url(https://fonts.gstatic.com/s/outfit/v11/QGYsz_OBy1qW9CzEt3lEGRY.ttf) format('truetype');
        }

        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { 
            font-family: 'Outfit', 'DejaVu Sans', sans-serif; 
            font-size: 13px; 
            color: #333; 
            line-height: 1.5;
            background: #fff;
        }

        @page { margin: 0; }
        .invoice-container { padding: 25px 40px; background: #fff; position: relative; }

        /* Premium Header */
        .header { 
            border-bottom: 2px solid #a91b43; 
            padding-bottom: 12px; 
            margin-bottom: 20px;
            display: table;
            width: 100%;
        }
        .header-left { display: table-cell; width: 60%; vertical-align: top; }
        .header-right { display: table-cell; width: 40%; text-align: right; vertical-align: top; }

        .brand-name { font-size: 22px; font-weight: 800; color: #a91b43; letter-spacing: 0.2px; margin-bottom: 3px; line-height: 1; }
        .brand-address { font-size: 10px; color: #555; line-height: 1.3; }

        .invoice-title { font-size: 26px; font-weight: 800; color: #a91b43; text-transform: uppercase; margin: 0; line-height: 1; }
        .order-info { margin-top: 8px; font-size: 12px; color: #333; }
        .info-item { margin-bottom: 2px; }
        .info-label { color: #888; font-weight: normal; }

        /* Customer Info Grid */
        .details-grid { display: table; width: 100%; margin-bottom: 25px; border-spacing: 15px 0; margin-left: -15px; }
        .details-cell { display: table-cell; width: 50%; vertical-align: top; }
        .card { 
            background: #fffcf0; 
            border: 1px solid #f9e1e8; 
            border-radius: 10px; 
            padding: 15px; 
        }
        .card-secondary { background: #fafafa; border: 1px solid #eee; }
        
        .card-label { 
            font-size: 10px; 
            color: #a91b43; 
            font-weight: 800; 
            text-transform: uppercase; 
            letter-spacing: 1px; 
            margin-bottom: 8px; 
        }
        .customer-name { font-size: 18px; font-weight: 800; color: #333; margin-bottom: 3px; }
        .address-text { font-size: 13px; color: #555; line-height: 1.4; }

        /* Items Table */
        .items-table { width: 100%; border-collapse: collapse; margin-bottom: 20px; border: 1px solid #eee; border-radius: 10px; overflow: hidden; }
        .items-table thead tr { background: #a91b43; color: white; }
        .items-table th { padding: 10px 8px; font-size: 11px; text-transform: uppercase; letter-spacing: 1px; font-weight: 800; border: 1px solid #a91b43; text-align: center; }
        .items-table th.text-left { text-align: left; }
        .items-table th.text-right { text-align: right; }

        .items-table td { padding: 10px 8px; border: 1px solid #eee; vertical-align: middle; font-size: 13px; }
        .items-table tr:nth-child(even) { background: #fafafa; }
        
        .product-info { display: table; width: 100%; }
        .product-img { display: table-cell; width: 35px; vertical-align: middle; }
        .product-details { display: table-cell; padding-left: 10px; vertical-align: middle; }
        .product-name { font-size: 14px; font-weight: 800; color: #1a1a1a; margin-bottom: 2px; }
        .product-meta { font-size: 11px; color: #999; }

        .text-right { text-align: right; }
        .text-center { text-align: center; }

        /* Totals */
        .totals-section { display: table; width: 100%; margin-bottom: 30px; page-break-inside: avoid; }
        .totals-spacer { display: table-cell; width: 60%; }
        .totals-box { display: table-cell; width: 40%; }
        
        .totals-table { width: 100%; border-collapse: collapse; border: 1px solid #eee; }
        .totals-table td { padding: 10px; border: 1px solid #eee; font-size: 14px; color: #666; }
        .totals-table td.val { text-align: right; font-weight: 700; color: #333; width: 50%; }
        .total-row { background: #a91b43; color: white !important; }
        .total-row td { color: white !important; font-size: 18px !important; font-weight: 800 !important; border-color: #a91b43 !important; }

        /* Footer */
        .footer-grid { display: table; width: 100%; border-top: 1.5px solid #f0f0f0; padding-top: 20px; margin-top: 10px; page-break-inside: avoid; }
        .words-section { display: table-cell; width: 65%; vertical-align: bottom; }
        .signature-section { display: table-cell; width: 35%; text-align: right; vertical-align: bottom; }
        
        .words-label { font-size: 11px; color: #bbb; font-weight: 800; text-transform: uppercase; letter-spacing: 1px; margin-bottom: 6px; }
        .words-value { background: #fffcf0; padding: 8px 12px; border-radius: 6px; font-size: 14px; font-weight: 700; color: #a91b43; display: inline-block; }
        
        .legal-declaration { margin-top: 15px; font-size: 11px; color: #999; font-style: italic; border-left: 3px solid #a91b43; padding-left: 12px; line-height: 1.4; }
        
        .signature-title { font-size: 11px; font-weight: 800; color: #333; text-transform: uppercase; margin-bottom: 40px; }
        .signature-line { border-top: 2px solid #333; padding-top: 8px; font-size: 12px; font-weight: 800; color: #a91b43; text-transform: uppercase; display: inline-block; width: 180px; }

        .final-footer { margin-top: 40px; text-align: center; font-size: 12px; color: #999; border-top: 1px solid #f0f0f0; padding-top: 15px; }
        .final-footer strong { color: #555; }
    </style>
</head>
<body>
    <div class="invoice-container">
        <!-- Header -->
        <div class="header">
            <div class="header-left">
                @php
                    $logoPath = public_path('images/image 1.png');
                    $logoBase64 = '';
                    if ($logoPath && is_file($logoPath)) {
                        $logoBase64 = base64_encode(@file_get_contents($logoPath));
                    }
                @endphp
                @if($logoBase64)
                    <img src="data:image/png;base64,{{ $logoBase64 }}" style="height: 45px; width: auto; margin-bottom: 8px;">
                @endif
                <div class="brand-name">NANDHINI SILKS</div>
                <div class="brand-address">
                    416/9 Aranmanai Street, S.V. Nagaram, Arni - 632317<br>
                    Tamil Nadu, India | Ph: +91 96295 52822
                    <div style="margin-top: 2px;"><strong>GSTIN:</strong> 33AAZFN1900B1ZK</div>
                </div>
            </div>
            <div class="header-right">
                <h1 class="invoice-title">Tax Invoice</h1>
                <div class="order-info">
                    <div class="info-item"><span class="info-label">Invoice No:</span> <strong>#{{ $order->order_number }}</strong></div>
                    <div class="info-item"><span class="info-label">Date:</span> <strong>{{ $order->created_at->format('d-m-Y') }}</strong></div>
                    <div class="info-item"><span class="info-label">Payment Status:</span> <strong>{{ strtoupper($order->payment_method) }}</strong></div>
                </div>
            </div>
        </div>

        <!-- Details Grid -->
        <div class="details-grid">
            <div class="details-cell">
                <div class="card">
                    <div class="card-label">Billing & Shipping Details</div>
                    <div class="customer-name">{{ $order->customer_name }}</div>
                    <div class="address-text">
                        {!! nl2br(e($order->delivery_address)) !!}
                    </div>
                    <div style="margin-top: 10px; font-weight: 700;">Phone: {{ $order->customer_phone }}</div>
                </div>
            </div>
            <div class="details-cell">
                <div class="card card-secondary">
                    <div style="padding: 10px 0;">
                        <span style="color: #777;">Place of Supply:</span>
                        <strong style="float: right;">Tamil Nadu</strong>
                    </div>
                    <div style="padding: 10px 0; border-top: 1px solid #eee;">
                        <span style="color: #777;">Order Source:</span>
                        <strong style="float: right;">nandhinisilks.com</strong>
                    </div>
                </div>
            </div>
        </div>

        <!-- Table -->
        <table class="items-table">
            <thead>
                <tr>
                    <th style="width: 5%">SNo</th>
                    <th style="width: 10%">Preview</th>
                    <th class="text-left" style="width: 45%">Item Description</th>
                    <th style="width: 8%">Qty</th>
                    <th class="text-right" style="width: 12%">Rate</th>
                    <th class="text-right" style="width: 10%">Tax</th>
                    <th class="text-right" style="width: 10%">Amount</th>
                </tr>
            </thead>
            <tbody>
                @foreach($order->items as $i => $item)
                <tr>
                    <td class="text-center">{{ $i + 1 }}</td>
                    <td class="text-center">
                        @php
                            $itemPath = $item->product_image;
                            if (!$itemPath && $item->product) $itemPath = $item->product->image_path;
                            $fullPath = null;
                            if ($itemPath) {
                                if (Str::startsWith($itemPath, 'products/')) $fullPath = public_path('uploads/' . $itemPath);
                                else $fullPath = public_path('images/' . $itemPath);
                            }
                            if (!$fullPath || !is_file($fullPath)) $fullPath = public_path('images/pro1.png');
                        @endphp
                        @if(isset($fullPath) && is_file($fullPath))
                            <img src="data:image/png;base64,{{ base64_encode(@file_get_contents($fullPath)) }}" style="width: 40px; height: 50px; object-fit: cover; border-radius: 6px; border: 1px solid #f0f0f0;">
                        @endif
                    </td>
                    <td>
                        <div class="product-name">{{ $item->product_name }}</div>
                        <div class="product-meta">HSN: 5007 | Variant: {{ ($item->color ? $item->color : '') . ($item->size ? ' / '.$item->size : '') ?: '-' }}</div>
                    </td>
                    <td class="text-center" style="font-weight: 700;">{{ $item->quantity }}</td>
                    <td class="text-right">₹{{ number_format($item->price, 2) }}</td>
                    <td class="text-right">
                        ₹{{ number_format($item->tax_amount ?? 0, 2) }}
                        <div style="font-size: 10px; color: #999;">({{ $item->tax_rate ?? 0 }}%)</div>
                    </td>
                    <td class="text-right" style="font-weight: 800; color: #1a1a1a;">₹{{ number_format($item->total, 2) }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>

        <!-- Totals -->
        <div class="totals-section">
            <div class="totals-spacer"></div>
            <div class="totals-box">
                <table class="totals-table">
                    <tr>
                        <td>Subtotal</td>
                        <td class="val">₹{{ number_format($order->sub_total, 2) }}</td>
                    </tr>
                    <tr>
                        <td>Tax (GST)</td>
                        <td class="val">₹{{ number_format($order->tax, 2) }}</td>
                    </tr>
                    <tr>
                        <td>Shipping Fees</td>
                        <td class="val" style="color: {{ $order->shipping > 0 ? '#333' : '#2e7d32' }}">{{ $order->shipping > 0 ? '₹' . number_format($order->shipping, 2) : 'FREE' }}</td>
                    </tr>
                    @if($order->discount > 0)
                    <tr style="background: #f1fcf1;">
                        <td style="color: #2e7d32; font-weight: 700;">Discount</td>
                        <td class="val" style="color: #2e7d32;">- ₹{{ number_format($order->discount, 2) }}</td>
                    </tr>
                    @endif
                    <tr class="total-row">
                        <td>Net Total</td>
                        <td class="val">₹{{ number_format($order->grand_total, 2) }}</td>
                    </tr>
                </table>
            </div>
        </div>

        <!-- Words & Signature -->
        <div class="footer-grid">
            <div class="words-section">
                <div class="words-label">Amount in Words</div>
                @php
                    $f = new NumberFormatter("en", NumberFormatter::SPELLOUT);
                    $words = $f->format($order->grand_total);
                @endphp
                <div class="words-value">{{ ucwords($words) }} Rupees Only</div>
                
                <div class="legal-declaration">
                    <strong>Legal Declaration:</strong><br>
                    This digital receipt serves as an official tax invoice. Certified that the particulars given above are true and correct and the amount indicated represents the price actually charged. Computer generated - No signature required.
                </div>
            </div>
            <div class="signature-section">
                <div class="signature-title">For NANDHINI SILKS</div>
                <div class="signature-line">Authorized Signatory</div>
            </div>
        </div>

        <!-- Final Footer -->
        <div class="final-footer">
            Thank you for choosing Nandhini Silks - Arani's Pride in Handloom Artistry since years.<br>
            Visit us online at <strong>www.nandhinisilks.com</strong>
        </div>
    </div>
</body>
</html>
