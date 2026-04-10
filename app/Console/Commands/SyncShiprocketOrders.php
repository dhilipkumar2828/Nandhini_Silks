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

        // Get orders that are currently in transit, shipped or out for delivery
        // These are the only ones that need constant tracking
        $activeOrders = Order::whereIn('order_status', ['shipped', 'out for delivery', 'order placed', 'processing'])
            ->whereNotNull('shiprocket_shipment_id')
            ->get();

        if ($activeOrders->isEmpty()) {
            $this->info('No active orders found for synchronization.');
            return 0;
        }

        $this->info('Processing ' . $activeOrders->count() . ' orders...');

        foreach ($activeOrders as $order) {
            try {
                $this->comment("Checking order #{$order->order_number} (Shipment ID: {$order->shiprocket_shipment_id})");
                
                $trackingData = $shiprocket->trackOrder($order->shiprocket_shipment_id);

                if (isset($trackingData['tracking_data']) && $trackingData['tracking_data']['track_status'] == 1) {
                    $shipmentTrack = $trackingData['tracking_data']['shipment_track'][0] ?? null;
                    
                    if ($shipmentTrack) {
                        // Mock a webhook payload so we reuse the same logic
                        $payload = [
                            'shipment_id'    => $order->shiprocket_shipment_id,
                            'current_status' => $shipmentTrack['current_status'],
                            'awb'            => $shipmentTrack['awb_code'] ?? $order->shiprocket_awb,
                        ];

                        $updated = $shiprocket->processWebhook($payload);
                        
                        if ($updated) {
                            $this->info("Successfully updated order #{$order->order_number} to {$shipmentTrack['current_status']}");
                        } else {
                            $this->warn("No status change for order #{$order->order_number}");
                        }
                    } else {
                        $this->warn("No tracking information found for order #{$order->order_number}");
                    }
                } else {
                    $errorMsg = $trackingData['message'] ?? ($trackingData['tracking_data']['error'] ?? 'Tracking not available yet or invalid Shipment ID');
                    $this->error("Failed to fetch tracking data for order #{$order->order_number}: {$errorMsg}");
                }
            } catch (\Exception $e) {
                $this->error("Error processing order #{$order->order_number}: " . $e->getMessage());
                Log::error("Cron Job Error - SyncShiprocketOrders: " . $e->getMessage());
            }
            
            // Avoid hitting rate limits (Shiprocket rate limits vary, small delay helps)
            usleep(200000); // 0.2 seconds
        }

        $this->info('Synchronization completed.');
        return 0;
    }
}
