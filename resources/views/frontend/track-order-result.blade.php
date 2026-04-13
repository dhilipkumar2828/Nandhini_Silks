@extends('frontend.layouts.app')

@section('title', 'Track Your Order | Nandhini Silks')

@push('styles')
<style>
    .tracking-result-container {
        padding: 60px 0;
        background: #fffcf5;
        min-height: 60vh;
    }
    .tracking-card {
        max-width: 800px;
        margin: 0 auto;
        background: #fff;
        border-radius: 20px;
        box-shadow: 0 15px 40px rgba(169, 27, 67, 0.08);
        padding: 40px;
        border: 1px solid #f9e1e8;
    }
    .tracking-header {
        text-align: center;
        margin-bottom: 40px;
        border-bottom: 1px solid #f0f0f0;
        padding-bottom: 20px;
    }
    .tracking-header h2 {
        color: #A91B43;
        font-weight: 800;
        font-size: 28px;
        margin-bottom: 10px;
    }
    .order-id-badge {
        display: inline-block;
        background: #fdf2f4;
        color: #A91B43;
        padding: 6px 16px;
        border-radius: 30px;
        font-weight: 700;
        font-size: 14px;
        letter-spacing: 0.5px;
    }
    
    .status-timeline {
        margin-top: 40px;
        position: relative;
        padding-left: 10px;
    }
    .status-step {
        display: flex;
        gap: 25px;
        margin-bottom: 20px;
        position: relative;
        padding-bottom: 20px;
    }
    .status-step:not(:last-child)::after {
        content: '';
        position: absolute;
        left: 15px;
        top: 30px;
        bottom: 0;
        width: 2px;
        background: #28a745; /* Success green line */
        opacity: 0.8;
    }
    .status-icon {
        width: 32px;
        height: 32px;
        border-radius: 50%;
        background: #f0f0f0;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 14px;
        color: #999;
        z-index: 1;
        flex-shrink: 0;
        transition: all 0.3s;
        border: 2px solid #fff;
    }
    .status-step.completed .status-icon {
        background: #28a745; /* Success green */
        color: #fff;
        box-shadow: 0 0 0 4px rgba(40, 167, 69, 0.1);
    }
    .status-info {
        flex: 1;
        padding-top: 4px;
    }
    .status-info h4 {
        margin: 0 0 4px;
        font-size: 15px;
        font-weight: 700;
        color: #222;
        line-height: 1.2;
    }
    .status-info p {
        margin: 0;
        font-size: 13px;
        color: #666;
        line-height: 1.4;
    }
    .status-date {
        font-size: 13px;
        color: #888;
        font-weight: 600;
        white-space: nowrap;
    }

    .shiprocket-status {
        margin-top: 30px;
        padding: 25px;
        background: #fcfcfc;
        border-radius: 16px;
        border-left: 4px solid #28a745;
        box-shadow: 0 4px 15px rgba(0,0,0,0.02);
    }
    .shiprocket-status h5 {
        margin: 0 0 10px;
        font-size: 14px;
        text-transform: uppercase;
        letter-spacing: 1px;
        color: #666;
    }
    .current-label {
        font-size: 20px;
        font-weight: 800;
        color: #1a1a1a;
    }
    
    .no-tracking {
        text-align: center;
        padding: 40px;
        color: #888;
    }
</style>
@endpush

@section('content')
<div class="tracking-result-container">
    <div class="page-shell">
        <div class="tracking-card">
            <div class="tracking-header">
                <h2>Tracking Status</h2>
                <div class="order-id-badge">Order #{{ $order->order_number }}</div>
            </div>

            @php
                $activities = null;
                $track = null;
                
                if ($trackingData && isset($trackingData['tracking_data']) && $trackingData['tracking_data']['track_status'] == 1) {
                    $track = $trackingData['tracking_data']['shipment_track'][0] ?? null;
                    $activities = $trackingData['tracking_data']['shipment_track_activities'] ?? null;
                }
                
                // Fallback to stored activities if real-time fails
                if (!$activities && $order->shipment_track_activities) {
                    $activities = $order->shipment_track_activities;
                }
            @endphp

            @if($activities)
                <div class="shiprocket-status">
                    <h5>Current Logistics Status</h5>
                    <div class="current-label">{{ $track ? $track['current_status'] : ucwords($order->shiprocket_status ?? $order->order_status) }}</div>
                    <p style="margin-top: 10px; font-size: 14px; color: #666;">
                        <strong>Courier:</strong> {{ $track ? $track['courier_name'] : ($order->courier_name ?? 'Delivery Partner') }} | 
                        <strong>AWB:</strong> {{ $track ? ($track['awb_code'] ?? 'N/A') : ($order->shiprocket_awb ?? 'N/A') }}
                    </p>
                </div>

                <div class="status-timeline">
                    {{-- Reverse activities to show latest on top if they come in chronological order, 
                         but Shiprocket usually sends latest first. We'll ensure consistency. --}}
                    @foreach($activities as $activity)
                        <div class="status-step completed">
                            <div class="status-icon"><i class="fas fa-circle" style="font-size: 8px;"></i></div>
                            <div class="status-info">
                                <div class="flex items-center justify-between">
                                    <h4>{{ $activity['activity'] }}</h4>
                                    <div class="status-date" style="margin-bottom: 0;">{{ \Carbon\Carbon::parse($activity['date'])->format('D, jS M \'y') }}</div>
                                </div>
                                <p>{{ $activity['location'] }}</p>
                                <div class="status-date" style="font-size: 11px; color: #999;">{{ \Carbon\Carbon::parse($activity['date'])->format('g:i a') }}</div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                {{-- Same fallback as before for orders not yet in Shiprocket --}}
                <div class="shiprocket-status" style="border-left-color: {{ in_array($order->order_status, ['cancelled', 'returned']) ? '#dc3545' : '#28a745' }};">
                    <h5>Order Status</h5>
                    <div class="current-label" style="color: {{ $order->order_status === 'cancelled' ? '#dc3545' : '#1a1a1a' }};">{{ ucwords($order->order_status) }}</div>
                    <p style="margin-top: 10px; font-size: 14px; color: #666;">
                        @if($order->order_status === 'cancelled')
                            This order has been cancelled. If this was a mistake, please contact our support team.
                        @elseif($order->order_status === 'returned')
                            This order has been returned.
                        @elseif($order->order_status === 'delivered')
                            Your order has been delivered successfully. Thank you for shopping with us!
                        @else
                            Your order is currently being processed. Once it is shipped, you will receive real-time updates here.
                        @endif
                    </p>
                </div>
                
                <div class="status-timeline">
                    <div class="status-step completed">
                        <div class="status-icon"><i class="fas fa-circle" style="font-size: 8px;"></i></div>
                        <div class="status-info">
                            <div class="flex items-center justify-between">
                                <h4>Order Confirmed</h4>
                                <div class="status-date">{{ $order->created_at->format('D, jS M \'y') }}</div>
                            </div>
                            <p>Your order has been placed.</p>
                            <div class="status-date" style="font-size: 11px; color: #999;">{{ $order->created_at->format('g:i a') }}</div>
                        </div>
                    </div>

                    @if($order->order_status === 'cancelled')
                        <div class="status-step completed" style="margin-top: 10px;">
                            <div class="status-icon" style="background: #dc3545;"><i class="fas fa-times" style="font-size: 10px;"></i></div>
                            <div class="status-info">
                                <h4 style="color: #dc3545;">Order Cancelled</h4>
                                <p>This order was cancelled on {{ $order->updated_at->format('D, jS M \'y') }}.</p>
                                <div class="status-date" style="font-size: 11px; color: #999;">{{ $order->updated_at->format('g:i a') }}</div>
                            </div>
                        </div>
                    @elseif($order->order_status === 'returned')
                         <div class="status-step completed" style="margin-top: 10px;">
                            <div class="status-icon" style="background: #6f42c1;"><i class="fas fa-undo" style="font-size: 10px;"></i></div>
                            <div class="status-info">
                                <h4 style="color: #6f42c1;">Order Returned</h4>
                                <p>The items have been returned.</p>
                                <div class="status-date" style="font-size: 11px; color: #999;">{{ $order->updated_at->format('g:i a') }}</div>
                            </div>
                        </div>
                    @endif
                    
                    @if(in_array($order->order_status, ['processing', 'ready to ship', 'shipped', 'delivered']))
                        <div class="status-step completed">
                            <div class="status-icon"><i class="fas fa-circle" style="font-size: 8px;"></i></div>
                            <div class="status-info">
                                <h4>Processing</h4>
                                <p>Seller has processed your order.</p>
                            </div>
                        </div>
                    @endif
                </div>
            @endif

            <div style="margin-top: 50px; text-align: center;">
                <a href="{{ route('home') }}" class="login-btn" style="text-decoration: none; padding: 12px 30px;">Back to Shopping</a>
            </div>
        </div>
    </div>
</div>
@endsection
