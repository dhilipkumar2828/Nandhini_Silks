<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Order;
use App\Services\ShiprocketService;
use Illuminate\Support\Facades\Log;

class SyncShiprocketOrders extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'shiprocket:sync-status';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Automatically synchronize order statuses from Shiprocket for active shipments';

    /**
     * Execute the console command.
     */
    public function handle(ShiprocketService $shiprocket)
    {
        $this->info('Starting Shiprocket status synchronization...');

        // ONLY sync orders that are actively in transit.
        // Do NOT include: 'delivered', 'cancelled', 'returned', 'refunded' — these are final states.
        $activeOrders = Order::whereIn('order_status', [
                'order placed',
                'processing',
                'ready to ship',
                'shipped',
                'out for delivery',
            ])
            ->whereNotNull('shiprocket_shipment_id')
            ->get();

        if ($activeOrders->isEmpty()) {
            $this->info('No active orders found for synchronization.');
            return 0;
        }

        $this->info('Processing ' . $activeOrders->count() . ' orders...');

        foreach ($activeOrders as $order) {
            try {
                $this->comment("Checking order #{$order->order_number} (Shipment ID: {$order->shiprocket_shipment_id}) — Current: {$order->order_status}");

                $trackingData = $shiprocket->trackOrder($order->shiprocket_shipment_id);

                if (isset($trackingData['tracking_data']) && $trackingData['tracking_data']['track_status'] == 1) {
                    $shipmentTrack = $trackingData['tracking_data']['shipment_track'][0] ?? null;

                    if ($shipmentTrack) {
                        // Update EDD (Estimated Delivery Date) if provided
                        if (!empty($shipmentTrack['edd']) && $order->edd !== $shipmentTrack['edd']) {
                            $order->update(['edd' => $shipmentTrack['edd']]);
                        }

                        // Build a webhook-compatible payload — reuse the unified mapping logic
                        $payload = [
                            'shipment_id'               => $order->shiprocket_shipment_id,
                            'current_status'            => $shipmentTrack['current_status'],
                            'awb'                       => $shipmentTrack['awb_code'] ?? $order->shiprocket_awb,
                            'shipment_track_activities' => $shipmentTrack['shipment_track_activities'] ?? [],
                        ];

                        $updated = $shiprocket->processWebhook($payload);

                        if ($updated) {
                            $this->info("✓ Updated order #{$order->order_number} → {$shipmentTrack['current_status']}");
                        } else {
                            $this->warn("~ No change for order #{$order->order_number} (same or final status)");
                        }
                    } else {
                        $this->warn("No shipment track data for order #{$order->order_number}");
                    }
                } else {
                    $errorMsg = $trackingData['message']
                        ?? ($trackingData['tracking_data']['error'] ?? 'Tracking not available yet');
                    $this->error("✗ Order #{$order->order_number}: {$errorMsg}");
                }
            } catch (\Exception $e) {
                $this->error("Error processing order #{$order->order_number}: " . $e->getMessage());
                Log::error("CronJob SyncShiprocketOrders - Order #{$order->order_number}: " . $e->getMessage());
            }

            // Avoid hitting Shiprocket API rate limits
            usleep(300000); // 0.3 seconds between each order
        }

        $this->info('Synchronization completed.');
        return 0;
    }
}
