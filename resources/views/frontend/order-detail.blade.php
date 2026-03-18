@extends('frontend.layouts.app')

@section('title', 'Order Details #NS7842 | Nandhini Silks')

@push('styles')
<style>
    .order-detail-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 30px;
        flex-wrap: wrap;
        gap: 20px;
    }

    .order-id-badge {
        font-size: 24px;
        font-weight: 700;
        color: #333;
    }

    .order-actions-top {
        display: flex;
        gap: 15px;
    }

    .timeline-card {
        background: #fff;
        border-radius: 15px;
        padding: 30px;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.02);
        margin-bottom: 30px;
        border: 1px solid #f0f0f0;
    }

    .timeline {
        display: flex;
        justify-content: space-between;
        position: relative;
        margin-top: 20px;
    }

    .timeline::before {
        content: '';
        position: absolute;
        top: 15px;
        left: 5%;
        right: 5%;
        height: 2px;
        background: #eee;
        z-index: 1;
    }

    .timeline-step {
        position: relative;
        z-index: 2;
        text-align: center;
        flex: 1;
    }

    .step-icon {
        width: 32px;
        height: 32px;
        background: #fff;
        border: 2px solid #eee;
        border-radius: 50%;
        display: grid;
        place-items: center;
        margin: 0 auto 10px;
        font-size: 14px;
        color: #999;
        transition: all 0.3s ease;
    }

    .timeline-step.active .step-icon {
        background: var(--pink);
        border-color: var(--pink);
        color: #fff;
    }

    .timeline-step.completed .step-icon {
        background: #52c41a;
        border-color: #52c41a;
        color: #fff;
    }

    .step-label {
        font-size: 13px;
        font-weight: 600;
        color: #333;
    }

    .step-date {
        font-size: 11px;
        color: #999;
        display: block;
    }

    .order-info-grid {
        display: grid;
        grid-template-columns: 2fr 1fr;
        gap: 30px;
    }

    .info-section {
        background: #fff;
        border-radius: 15px;
        padding: 25px;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.02);
        border: 1px solid #f0f0f0;
        margin-bottom: 30px;
    }

    .info-title {
        font-size: 16px;
        font-weight: 700;
        margin-bottom: 20px;
        color: #333;
        border-bottom: 1px solid #f5f5f5;
        padding-bottom: 15px;
    }

    .order-items-table {
        width: 100%;
        border-collapse: collapse;
    }

    .order-items-table th {
        text-align: left;
        font-size: 12px;
        color: #999;
        text-transform: uppercase;
        padding: 10px 0;
        border-bottom: 1px solid #f5f5f5;
    }

    .order-items-table td {
        padding: 20px 0;
        border-bottom: 1px solid #f9f9f9;
        vertical-align: middle;
    }

    .item-cell {
        display: flex;
        align-items: center;
        gap: 15px;
    }

    .item-img {
        width: 60px;
        height: 60px;
        border-radius: 6px;
        object-fit: cover;
    }

    .item-name {
        font-weight: 600;
        font-size: 14px;
        color: #333;
    }

    .item-meta {
        font-size: 12px;
        color: #999;
    }

    .item-actions-cell {
        display: flex;
        flex-direction: column;
        gap: 5px;
    }

    .action-link {
        font-size: 12px;
        color: var(--pink);
        text-decoration: none;
        font-weight: 600;
    }

    .summary-row {
        display: flex;
        justify-content: space-between;
        margin-bottom: 12px;
        font-size: 14px;
        color: #666;
    }

    .summary-row.total {
        margin-top: 20px;
        padding-top: 15px;
        border-top: 2px solid #f5f5f5;
        font-size: 18px;
        font-weight: 700;
        color: #333;
    }

    .address-card p {
        margin-bottom: 5px;
        font-size: 14px;
        color: #666;
    }

    .tracking-info {
        background: #f8f9fa;
        padding: 15px;
        border-radius: 10px;
        margin-top: 10px;
    }

    .courier-link {
        color: var(--pink);
        font-weight: 600;
        text-decoration: underline;
    }

    .account-nav-link {
        padding: 10px 20px;
        border-radius: 50px;
        text-decoration: none;
        font-weight: 600;
        transition: all 0.3s ease;
    }

    .status-badge {
        padding: 5px 12px;
        border-radius: 50px;
        font-size: 12px;
        font-weight: 600;
    }

    .status-delivered {
        background: #e6f7ff;
        color: #1890ff;
    }

    @media (max-width: 900px) {
        .order-info-grid {
            grid-template-columns: 1fr;
        }

        .timeline::before {
            display: none;
        }

        .timeline {
            flex-direction: column;
            gap: 20px;
            text-align: left;
        }

        .timeline-step {
            display: flex;
            align-items: flex-start;
            gap: 15px;
        }

        .step-icon {
            margin: 0;
        }
    }
</style>
@endpush

@section('content')
<main class="account-page">
    <div class="page-shell">
        <div class="breadcrumb">
            <a href="{{ route('home') }}">Home</a> &nbsp; / &nbsp;
            <a href="{{ url('my-account') }}">My Account</a> &nbsp; / &nbsp;
            <a href="{{ url('my-orders') }}">My Orders</a> &nbsp; / &nbsp;
            <span>Order Details</span>
        </div>

        <div class="order-detail-header">
            <div>
                <h1 class="order-id-badge">Order #NS7842</h1>
                <p style="color: #999; margin-top: 5px;">Placed on Oct 12, 2023 &middot; 10:45 AM</p>
            </div>
            <div class="order-actions-top">
                <button onclick="handleDownload()" class="account-nav-link"
                    style="background: #fff; border: 1px solid #ddd; cursor: pointer; display: flex; align-items: center; gap: 8px;">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v4"></path>
                        <polyline points="7 10 12 15 17 10"></polyline>
                        <line x1="12" y1="15" x2="12" y2="3"></line>
                    </svg>
                    Download Invoice
                </button>
                <button class="account-nav-link"
                    style="background: var(--pink); color: #fff; border: none; cursor: pointer;">
                    Buy it Again
                </button>
            </div>
        </div>

        <div class="timeline-card">
            <h3 class="info-title">Order Status</h3>
            <div class="timeline">
                <div class="timeline-step completed">
                    <div class="step-icon">&#10003;</div>
                    <span class="step-label">Order Placed</span>
                    <span class="step-date">Oct 12</span>
                </div>
                <div class="timeline-step completed">
                    <div class="step-icon">&#10003;</div>
                    <span class="step-label">Confirmed</span>
                    <span class="step-date">Oct 12</span>
                </div>
                <div class="timeline-step active">
                    <div class="step-icon">&#9679;</div>
                    <span class="step-label">Shipped</span>
                    <span class="step-date">Processing</span>
                </div>
                <div class="timeline-step">
                    <div class="step-icon">&#9675;</div>
                    <span class="step-label">Delivered</span>
                    <span class="step-date">Est Oct 15</span>
                </div>
            </div>

            <div class="tracking-info">
                <p style="font-size: 14px;"><strong>Tracking ID:</strong> DN678429103 <span
                        style="margin: 0 10px; color: #ccc;">|</span> <strong>Courier:</strong> Delhivery <a href="#"
                        class="courier-link" style="margin-left: 10px;">Track on Website</a></p>
            </div>
        </div>

        <div class="order-info-grid">
            <div class="grid-left">
                <div class="info-section">
                    <h3 class="info-title">Order Items</h3>
                    <table class="order-items-table">
                        <thead>
                            <tr>
                                <th>Product</th>
                                <th>Price</th>
                                <th>Qty</th>
                                <th>Subtotal</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>
                                    <div class="item-cell">
                                        <img src="{{ asset('images/pro1.png') }}" alt="" class="item-img">
                                        <div>
                                            <div class="item-name">Royal Gold Handloom Silk Saree</div>
                                            <div class="item-meta">Color: Gold Jari | Size: Free Size</div>
                                        </div>
                                    </div>
                                </td>
                                <td>&#8377;7,490</td>
                                <td class="item-qty">1</td>
                                <td>&#8377;7,490</td>
                                <td class="item-actions-cell">
                                    <a href="#" class="action-link">Write Review</a>
                                    <a href="#" class="action-link" style="color: #999;">Need Help?</a>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="grid-right">
                <div class="info-section">
                    <h3 class="info-title">Order Summary</h3>
                    <div class="summary-details">
                        <div class="summary-row subtotal-row">
                            <span>Subtotal</span>
                            <span class="subtotal-val">&#8377;7,490</span>
                        </div>
                        <div class="summary-row">
                            <span>Shipping</span>
                            <span style="color: #52c41a;">FREE</span>
                        </div>
                        <div class="summary-row tax-row">
                            <span>Tax (GST 12%)</span>
                            <span class="tax-val">&#8377;898</span>
                        </div>
                        <div class="summary-row total">
                            <span>Total</span>
                            <span class="total-val">&#8377;8,388</span>
                        </div>
                    </div>
                </div>

                <div class="info-section">
                    <h3 class="info-title">Delivery Address</h3>
                    <div class="address-card">
                        <p><strong class="cust-name">John Doe</strong></p>
                        <p class="addr-line">45, Rajaji Street, T-Nagar</p>
                        <p class="city-line">Chennai, Tamil Nadu - 600017</p>
                        <p class="phone-line">Phone: +91 98765 43210</p>
                    </div>
                </div>

                <div class="info-section">
                    <h3 class="info-title">Payment Method</h3>
                    <div class="payment-info-card">
                        <p class="pay-method" style="font-size: 14px; font-weight: 600;">Credit Card (Ending in 4242)</p>
                        <p style="font-size: 12px; color: #999;">Transaction ID: #TRX9023485</p>
                        <span class="status-badge status-delivered" style="display: inline-block; margin-top: 10px;">Payment
                            Successful</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>
@endsection

@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>
<script src="{{ asset('js/invoice.js') }}"></script>
<script>
    function handleDownload() {
        const orderNo = document.querySelector('.order-id-badge').innerText.replace('Order ', '').replace('#', '').trim();
        const totalText = document.querySelector('.total-val').innerText.replace('₹', '').replace(',', '').trim();
        const subtotalText = document.querySelector('.subtotal-val').innerText.replace('₹', '').replace(',', '').trim();
        const gstText = document.querySelector('.tax-val').innerText.replace('₹', '').replace(',', '').trim();

        const customerName = document.querySelector('.cust-name').innerText;
        const addrLine = document.querySelector('.addr-line').innerText;
        const cityLine = document.querySelector('.city-line').innerText;
        const phone = document.querySelector('.phone-line').innerText.replace('Phone: ', '').trim();

        const orderData = {
            orderNumber: orderNo,
            date: new Date().toLocaleDateString(),
            customer: {
                name: customerName,
                address: addrLine + ', ' + cityLine,
                phone: phone
            },
            items: [
                {
                    name: document.querySelector('.item-name').innerText,
                    variant: document.querySelector('.item-meta').innerText,
                    hsn: "5007",
                    qty: parseInt(document.querySelector('.item-qty').innerText),
                    rate: parseFloat(subtotalText),
                    taxRate: 12
                }
            ],
            paymentMethod: document.querySelector('.pay-method').innerText,
            subtotal: parseFloat(subtotalText),
            taxAmount: parseFloat(gstText),
            shipping: 0,
            total: parseFloat(totalText)
        };

        if (typeof InvoiceGenerator !== 'undefined') {
            InvoiceGenerator.download(orderData);
        } else {
            alert('Invoice generator is still loading. Please try again.');
        }
    }
</script>
@endpush
