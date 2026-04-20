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
                
                if (!$activities && $order->shipment_track_activities) {
                    $activities = $order->shipment_track_activities;
                }

                $timeline = collect();
                
                // Add Internal Status History
                foreach($order->statusHistories as $history) {
                    $labelMap = [
                        'order placed'     => 'Order Confirmed',
                        'processing'       => 'Processing',
                        'ready to ship'    => 'Packed & Ready to Ship',
                        'shipped'          => 'Shipped',
                        'out for delivery' => 'Out for Delivery',
                        'delivered'        => 'Delivered',
                        'cancelled'        => 'Cancelled',
                        'returned'         => 'Returned',
                        'refunded'         => 'Refunded',
                    ];
                    
                    $timeline->push([
                        'date' => $history->created_at,
                        'title' => $labelMap[$history->status] ?? ucwords($history->status),
                        'description' => $history->status === 'order placed' ? 'Your order has been placed.' : ($history->status === 'processing' ? 'Seller has processed your order.' : ''),
                        'type' => 'internal'
                    ]);
                }

                // Add Shiprocket Activities
                if($activities) {
                    foreach($activities as $activity) {
                        if ($activity['activity'] === 'ReadyForReceive') continue;

                        $actTitle = str_replace('Manifested - ', '', $activity['activity']);
                        $friendlyMap = [
                            'Manifest uploaded' => 'Order packed & label generated',
                            'Pickup scheduled' => 'Courier pickup scheduled',
                            'Out for Pickup' => 'Courier partner assigned',
                            'Shipped' => 'In Transit',
                            'Delivered' => 'Delivered Successfully'
                        ];
                        
                        $timeline->push([
                            'date' => \Carbon\Carbon::parse($activity['date']),
                            'title' => $friendlyMap[$actTitle] ?? $actTitle,
                            'description' => $activity['location'],
                            'type' => 'logistics'
                        ]);
                    }
                }

                // Unique by title and date (approx) to avoid near-duplicates if webhook and internal update same time
                $timeline = $timeline->sortBy(function($event) {
                    $priority = [
                        'Order Confirmed' => 10,
                        'Processing' => 20,
                        'Packed & Ready to Ship' => 30,
                        'Order packed & label generated' => 35,
                        'Courier pickup scheduled' => 40,
                        'Courier partner assigned' => 45,
                        'Shipped' => 50,
                        'In Transit' => 55,
                        'Out for Delivery' => 60,
                        'Delivered' => 70,
                        'Delivered Successfully' => 75,
                        'Cancelled' => 80,
                        'Returned' => 90,
                        'Refunded' => 100,
                    ];
                    
                    // Use a combination of priority and date
                    // Priority is the primary sort key (10, 20, 30...)
                    // Date is secondary (as a timestamp)
                    $pVal = $priority[$event['title']] ?? 500;
                    return sprintf('%04d-%s', $pVal, $event['date']->format('YmdHis'));
                })->values();
            @endphp

            @if($timeline->isNotEmpty())
                @php
                    $latest = $timeline->last();
                    $isFinal = in_array(strtolower($order->order_status), ['delivered', 'cancelled', 'returned']);
                    $statusColor = $isFinal && strtolower($order->order_status) !== 'delivered' ? '#dc3545' : '#28a745';
                @endphp
                
                <div class="shiprocket-status" style="border-left-color: {{ $statusColor }};">
                    <h5>Current Status</h5>
                    <div class="current-label" style="color: {{ $statusColor === '#28a745' ? '#1a1a1a' : $statusColor }};">
                        {{ $latest['title'] }}
                    </div>
                    <p style="margin-top: 10px; font-size: 14px; color: #666;">
                        @if($order->order_status === 'cancelled')
                            This order has been cancelled.
                        @elseif($order->order_status === 'delivered')
                            Your order has been delivered successfully.
                        @else
                            {{ $latest['description'] ?: 'Your order is in progress.' }}
                        @endif
                    </p>
                </div>

                <div class="status-timeline">
                    @foreach($timeline as $event)
                        @php
                            $isError = str_contains(strtolower($event['title']), 'error') || str_contains(strtolower($event['title']), 'fail');
                            $isCancel = str_contains(strtolower($event['title']), 'cancel');
                            $evColor = ($isError || $isCancel) ? '#dc3545' : '#28a745';
                        @endphp
                        <div class="status-step completed" style="--timeline-color: {{ $evColor }};">
                            <style>
                                .status-step[style*="--timeline-color: {{ $evColor }}"] .status-icon { background: {{ $evColor }}; }
                                .status-step[style*="--timeline-color: {{ $evColor }}"]::after { background: {{ $evColor }}; }
                            </style>
                            <div class="status-icon">
                                <i class="fas {{ $isCancel ? 'fa-times' : 'fa-circle' }}" style="font-size: {{ $isCancel ? '10px' : '8px' }};"></i>
                            </div>
                            <div class="status-info">
                                <div class="flex items-center justify-between">
                                    <h4 style="color: {{ $isCancel ? $evColor : '#222' }};">{{ $event['title'] }}</h4>
                                    <div class="status-date">{{ $event['date']->format('D, jS M \'y') }}</div>
                                </div>
                                <p>{{ $event['description'] }}</p>
                                <div class="status-date" style="font-size: 11px; color: #999;">{{ $event['date']->format('g:i a') }}</div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="no-tracking">
                    <p>No tracking information available yet. Please check back later.</p>
                </div>
            @endif

            <div style="margin-top: 50px; text-align: center;">
                <a href="{{ route('home') }}" class="login-btn" style="text-decoration: none; padding: 12px 30px;">Back to Shopping</a>
            </div>
        </div>
    </div>
</div>
@endsection
