<?php

namespace App\Services;

use App\Models\Order;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

class ShiprocketService
{
    protected string $baseUrl = 'https://apiv2.shiprocket.in/v1/external';
    protected ?string $email;
    protected ?string $password;

    public function __construct()
    {
        $this->email    = config('services.shiprocket.email');
        $this->password = config('services.shiprocket.password');
    }

    // ─────────────────────────────────────────────────────────────────────────
    // STEP 1 — Authentication (auto-refresh on 401)
    // ─────────────────────────────────────────────────────────────────────────
    public function getToken(bool $forceRefresh = false): ?string
    {
        if (!$forceRefresh && Cache::has('shiprocket_token')) {
            return Cache::get('shiprocket_token');
        }

        try {
            $response = Http::post("{$this->baseUrl}/auth/login", [
                'email'    => $this->email,
                'password' => $this->password,
            ]);

            if ($response->successful()) {
                $token = $response->json('token');
                // Cache for 23.5 hours (expires in 24, refresh slightly early)
                Cache::put('shiprocket_token', $token, now()->addMinutes(1410));
                return $token;
            }

            Log::error('Shiprocket Auth Failed: ' . $response->body());
            return null;
        } catch (\Exception $e) {
            Log::error('Shiprocket Auth Exception: ' . $e->getMessage());
            return null;
        }
    }

    private function request(string $method, string $endpoint, array $data = []): \Illuminate\Http\Client\Response
    {
        $token = $this->getToken();
        
        $request = Http::withToken($token)
            ->withHeaders([
                'Accept' => 'application/json',
                'User-Agent' => 'NandhiniSilks/1.0',
            ]);

        if (strtolower($method) === 'get') {
            $response = $request->get("{$this->baseUrl}{$endpoint}", $data);
        } else {
            $response = $request->post("{$this->baseUrl}{$endpoint}", $data);
        }

        // Auto-refresh on 401 Unauthorized
        if ($response->status() === 401) {
            Log::warning('Shiprocket 401 — refreshing token and retrying…');
            $token = $this->getToken(true);
            
            $request = Http::withToken($token)
                ->withHeaders([
                    'Accept' => 'application/json',
                    'User-Agent' => 'NandhiniSilks/1.0',
                ]);

            if (strtolower($method) === 'get') {
                $response = $request->get("{$this->baseUrl}{$endpoint}", $data);
            } else {
                $response = $request->post("{$this->baseUrl}{$endpoint}", $data);
            }
        }

        if (!$response->successful()) {
            Log::error('Shiprocket API Error:', [
                'endpoint' => $endpoint,
                'status'   => $response->status(),
                'payload'  => $data,
                'response' => $response->json() ?? ['body' => substr($response->body(), 0, 500)],
            ]);
        }

        return $response;
    }

    // ─────────────────────────────────────────────────────────────────────────
    // STEP 2 — Create Order
    // ─────────────────────────────────────────────────────────────────────────
    public function createOrder(Order $order): array
    {
        $items       = [];
        $totalWeight = 0;

        foreach ($order->items as $item) {
            $product = $item->product;
            $variant = $item->variant;
            $unitPriceWithTax = (float) $item->price;
            
            // Prioritize Variant SKU, then Product SKU
            $sku = ($variant && $variant->sku) ? $variant->sku : ($product->sku ?? 'SKU-' . ($product->id ?? time()));
            
            // If no variant SKU was found, ensure uniqueness by appending attributes
            if (!$variant || !$variant->sku) {
                $variantParts = [];
                if (!empty($item->attributes) && is_array($item->attributes)) {
                    foreach ($item->attributes as $attr) {
                        $variantParts[] = $attr['value'];
                    }
                } else {
                    if ($item->size) $variantParts[] = $item->size;
                    if ($item->color) $variantParts[] = $item->color;
                }

                if (!empty($variantParts)) {
                    $sku .= '-' . \Illuminate\Support\Str::slug(implode('-', $variantParts));
                }
            }
            
            $itemPath = $item->product_image;
            if (!$itemPath && $item->product) {
                $itemPath = $item->product->image_path;
            }
            $imageUrl = $itemPath ? asset('uploads/' . $itemPath) : null;
            
            // Shiprocket API blocks localhost/127.0.0.1 URLs in payloads (SSRF protection)
            if ($imageUrl && (str_contains($imageUrl, '127.0.0.1') || str_contains($imageUrl, 'localhost'))) {
                $imageUrl = null;
            }

            $items[] = [
                'name'          => $item->product_name,
                'sku'           => $sku,
                'units'         => $item->quantity,
                'selling_price' => $unitPriceWithTax,
                'discount'      => 0,
                'tax'           => 0,
                'hsn'           => 0,
                'image'         => $imageUrl,
            ];

            // Prioritize Variant Weight
            $itemWeight = ($variant && ($variant->weight > 0)) ? (float)$variant->weight : (float)($product->weight ?? 0.5);
            $totalWeight += ($itemWeight > 0 ? $itemWeight : 0.5) * $item->quantity;
        }

        $nameParts = explode(' ', trim($order->customer_name), 2);
        $firstName = $nameParts[0];
        $lastName  = $nameParts[1] ?? '.';

        // Generate a unique ID for Shiprocket to allow retries if a previous shipment was cancelled/stuck
        // Format: ORD-NUMBER-XX (where XX is a short unique suffix)
        $shiprocketOrderId = $order->order_number . '-' . strtoupper(\Illuminate\Support\Str::random(3));

        $payload = [
            'order_id'                => $shiprocketOrderId,
            'order_date'              => $order->created_at->format('Y-m-d H:i'),
            'pickup_location'         => config('services.shiprocket.pickup_location', 'Primary'),
            'channel_id'              => '',
            'comment'                 => 'Nandhini Silks Order',
            'billing_customer_name'   => $firstName,
            'billing_last_name'       => $lastName,
            'billing_address'         => $order->delivery_address ?: 'No Address Provided',
            'billing_address_2'       => '',
            'billing_city'            => $order->shipping_city    ?: '-',
            'billing_pincode'         => $order->shipping_pincode ?: '-',
            'billing_state'           => $order->shipping_state   ?: '-',
            'billing_country'         => $order->shipping_country ?: '-',
            'billing_email'           => $order->customer_email,
            'billing_phone'           => $order->customer_phone,
            'shipping_is_billing'     => true,
            'order_items'             => $items,
            'payment_method'          => $order->payment_method === 'cod' ? 'COD' : 'Prepaid',
            'shipping_charges'        => (float) $order->shipping,
            'giftwrap_charges'        => 0,
            'transaction_parameters'  => ['is_gift' => false, 'gift_message' => ''],
            'total_discount'          => (float) $order->discount,
            'sub_total'               => (float) ($order->grand_total - $order->shipping),
            'length'                  => 10,
            'breadth'                 => 10,
            'height'                  => 10,
            'weight'                  => (float) ($totalWeight >= 0.01 ? $totalWeight : 0.5),
        ];

        // Ensure weight is at least 0.01 but recommended 0.5 for most couriers
        if ($payload['weight'] < 0.1) {
            $payload['weight'] = 0.5;
        }

        try {
            Log::info('Shiprocket CreateOrder Payload:', $payload);
            $response = $this->request('post', '/orders/create/adhoc', $payload);
            Log::info('Shiprocket CreateOrder Response:', $response->json() ?? ['body' => $response->body()]);

            if ($response->successful()) {
                $data = $response->json();
                $order->update([
                    'shiprocket_order_id'    => $data['order_id'],
                    'shiprocket_shipment_id' => $data['shipment_id'],
                    'shiprocket_status'      => 'NEW',
                ]);
                return ['status' => true, 'data' => $data];
            }

            return ['status' => false, 'message' => $response->json('message') ?? 'Unknown error'];
        } catch (\Exception $e) {
            Log::error('Shiprocket CreateOrder Exception: ' . $e->getMessage());
            return ['status' => false, 'message' => $e->getMessage()];
        }
    }

    // ─────────────────────────────────────────────────────────────────────────
    // STEP 3 — Check Serviceability
    // ─────────────────────────────────────────────────────────────────────────
    public function checkServiceability(string $deliveryPincode, float $weight = 0.5, bool $cod = true): array
    {
        try {
            $response = $this->request('get', '/courier/serviceability', [
                'pickup_postcode'   => config('services.shiprocket.pickup_pincode', '632317'),
                'delivery_postcode' => $deliveryPincode,
                'weight'            => $weight,
                'cod'               => $cod ? 1 : 0,
            ]);

            Log::info('Shiprocket Serviceability Response:', $response->json() ?? []);

            if ($response->successful()) {
                $data = $response->json('data');

                // Filter out "Air" couriers as requested (e.g., Xpressbees Air, Blue Dart Air)
                if (isset($data['available_courier_companies']) && is_array($data['available_courier_companies'])) {
                    // Log the couriers found before filtering to help debugging
                    $allNames = array_column($data['available_courier_companies'], 'courier_name');
                    Log::info('All Available Couriers for Pincode ' . $deliveryPincode . ':', $allNames);

                    $data['available_courier_companies'] = array_values(array_filter($data['available_courier_companies'], function($courier) {
                        $name = strtolower($courier['courier_name'] ?? '');
                        // Check for 'air' or 'express' (which often implies air) if they want pure surface
                        return !str_contains($name, 'air');
                    }));
                    
                    $filteredNames = array_column($data['available_courier_companies'], 'courier_name');
                    Log::info('Filtered (Surface-Only) Couriers for Pincode ' . $deliveryPincode . ':', $filteredNames);
                }

                return ['status' => true, 'data' => $data];
            }
            return ['status' => false, 'message' => $response->json('message') ?? 'Pincode not serviceable'];
        } catch (\Exception $e) {
            Log::error('Shiprocket Serviceability Exception: ' . $e->getMessage());
            return ['status' => false, 'message' => 'API Error'];
        }
    }

    // ─────────────────────────────────────────────────────────────────────────
    // STEP 4 & 5 — Assign Courier & Generate AWB
    // ─────────────────────────────────────────────────────────────────────────
    public function assignCourierAndAWB(Order $order, ?int $courierId = null): array
    {
        if (!$order->shiprocket_shipment_id) {
            return ['status' => false, 'message' => 'No shipment ID. Push order to Shiprocket first.'];
        }

        // If no courier is specified, find the cheapest SURFACE courier manually
        if (!$courierId) {
            $totalWeight = 0;
            foreach ($order->items as $item) {
                $itemWeight = ($item->variant && ($item->variant->weight > 0)) ? (float)$item->variant->weight : (float)($item->product->weight ?? 0.5);
                $totalWeight += ($itemWeight > 0 ? $itemWeight : 0.5) * $item->quantity;
            }
            if ($totalWeight < 0.1) $totalWeight = 0.5;

            $isCod = (strtoupper($order->payment_method) === 'COD');
            $pincode = $order->shipping_pincode;

            Log::info("Auto-assigning courier: Checking serviceability for {$pincode}, weight {$totalWeight}, COD=" . ($isCod?'Y':'N'));
            
            $service = $this->checkServiceability($pincode, $totalWeight, $isCod);

            if ($service['status'] && !empty($service['data']['available_courier_companies'])) {
                $couriers = $service['data']['available_courier_companies'];
                
                // Sort by rate (cheapest first)
                usort($couriers, function($a, $b) {
                    $rateA = (float)($a['rate'] ?? 0);
                    $rateB = (float)($b['rate'] ?? 0);
                    return $rateA <=> $rateB;
                });

                $bestCourier = $couriers[0];
                $courierId = $bestCourier['courier_company_id'];
                $tempEtd = $bestCourier['etd'] ?? $bestCourier['edd'] ?? null;
                Log::info("Auto-assignment: Selected {$bestCourier['courier_name']} (ID: {$courierId}) for Order #{$order->order_number}, ETD: {$tempEtd}");
            } else {
                Log::error("Auto-assignment FAILED for Order #{$order->order_number}: No surface couriers found. Blocking assignment to prevent Air fallback.");
                return [
                    'status' => false, 
                    'message' => 'No surface couriers available for this location. Shiprocket auto-assignment blocked to prevent Air delivery.'
                ];
            }
        }
        
        $payload = ['shipment_id' => $order->shiprocket_shipment_id];
        if ($courierId) {
            $payload['courier_id'] = $courierId;
        }
        
        Log::info('Shiprocket Assign AWB Payload:', $payload);
        try {
            $response = $this->request('post', '/courier/assign/awb', $payload);
            Log::info('Shiprocket AWB Assignment Response:', $response->json() ?? ['body' => $response->body()]);

            if ($response->successful()) {
                $data = $response->json();
                $awb = $data['response']['data']['awb_code']
                    ?? $data['awb_code']
                    ?? null;
                $cid = $data['response']['data']['courier_company_id']
                    ?? $data['courier_company_id']
                    ?? null;
                $cname = $data['response']['data']['courier_name']
                    ?? $data['courier_name']
                    ?? 'Shiprocket';

                if ($awb) {
                    // Logic to ensure EDD is realistic and after pickup
                    $rawEdd = $data['response']['data']['expected_delivery_date'] 
                              ?? ($data['response']['data']['etd'] 
                              ?? ($data['response']['data']['edd'] ?? ($tempEtd ?? null)));
                    
                    $finalEdd = $rawEdd;
                    try {
                        $pickupRef = $order->pickup_scheduled_at ? \Carbon\Carbon::parse($order->pickup_scheduled_at) : \Carbon\Carbon::now();
                        $eddObj = $rawEdd ? \Carbon\Carbon::parse($rawEdd) : $pickupRef->copy()->addDays(5);
                        
                        // If EDD is before or too close to pickup, move it to 5 days after pickup (realistic for surface)
                        if ($eddObj->lte($pickupRef->copy()->addDays(1))) {
                            $eddObj = $pickupRef->copy()->addDays(5);
                        }
                        $finalEdd = $eddObj->format('Y-m-d');
                    } catch (\Exception $e) {
                        Log::warning("EDD Parsing failed for Order #{$order->order_number}: " . $e->getMessage());
                    }

                    $order->update([
                        'shiprocket_awb'          => $awb,
                        'shiprocket_courier_id'   => $cid,
                        'shiprocket_courier_name' => $cname,
                        'tracking_number'         => $awb,
                        'courier_name'            => $cname,
                        'edd'                     => $finalEdd,
                    ]);
                    return ['status' => true, 'awb' => $awb, 'courier' => $cname, 'data' => $data];
                }
            }
            return ['status' => false, 'message' => $response->json('message') ?? $response->json('response.message') ?? 'Failed to assign AWB'];
        } catch (\Exception $e) {
            Log::error('Shiprocket AWB Exception: ' . $e->getMessage());
            return ['status' => false, 'message' => $e->getMessage()];
        }
    }

    // Backwards-compat alias
    public function assignAWB($shipmentId): array
    {
        try {
            $response = $this->request('post', '/courier/assign/awb', [
                'shipment_id' => $shipmentId,
            ]);

            if ($response->successful()) {
                $data = $response->json();
                if (isset($data['response']['data']['awb_code'])) {
                    return ['status' => true, 'awb' => $data['response']['data']['awb_code']];
                }
            }
            return ['status' => false, 'message' => $response->json('message') ?? 'Failed to assign AWB'];
        } catch (\Exception $e) {
            Log::error('Shiprocket AWB Exception: ' . $e->getMessage());
            return ['status' => false, 'message' => $e->getMessage()];
        }
    }

    // ─────────────────────────────────────────────────────────────────────────
    // STEP 6 — Generate Shipping Label
    // ─────────────────────────────────────────────────────────────────────────
    public function generateLabel($shipmentId): array
    {
        try {
            $response = $this->request('post', '/courier/generate/label', [
                'shipment_id' => [$shipmentId],
            ]);

            if ($response->successful()) {
                return ['status' => true, 'data' => $response->json()];
            }
            return ['status' => false, 'message' => $response->json('message') ?? 'Failed to generate label'];
        } catch (\Exception $e) {
            Log::error('Shiprocket Label Exception: ' . $e->getMessage());
            return ['status' => false, 'message' => $e->getMessage()];
        }
    }

    // ─────────────────────────────────────────────────────────────────────────
    // STEP 6b — Generate Manifest
    // ─────────────────────────────────────────────────────────────────────────
    public function generateManifest($shipmentId): array
    {
        try {
            $payload = ['shipment_id' => [$shipmentId]];
            $response = $this->request('post', '/manifests/generate', $payload);

            if ($response->successful()) {
                $data = $response->json();
                Log::info('Shiprocket Generate Manifest Response:', $data);
                
                $manifestUrl = $data['manifest_url'] ?? null;
                
                if (!$manifestUrl) {
                    // Fallback: If generate didn't return URL, try the print endpoint after a short sleep
                    sleep(2);
                    $printResponse = $this->request('post', '/manifests/print', $payload);
                    Log::info('Shiprocket Print Manifest Fallback Response:', $printResponse->json());
                    if ($printResponse->successful()) {
                        $manifestUrl = $printResponse->json('manifest_url') ?? null;
                    }
                }
                
                return ['status' => true, 'manifest_url' => $manifestUrl, 'data' => $data];
            }
            return ['status' => false, 'message' => $response->json('message') ?? 'Failed to generate manifest'];
        } catch (\Exception $e) {
            Log::error('Shiprocket Manifest Exception: ' . $e->getMessage());
            return ['status' => false, 'message' => $e->getMessage()];
        }
    }

    // ─────────────────────────────────────────────────────────────────────────
    // STEP 7 — Generate GST Invoice (optional)
    // ─────────────────────────────────────────────────────────────────────────
    public function generateInvoice($orderId): array
    {
        try {
            $response = $this->request('post', '/orders/print/invoice', [
                'ids' => [$orderId],
            ]);

            if ($response->successful()) {
                return ['status' => true, 'data' => $response->json()];
            }
            return ['status' => false, 'message' => $response->json('message') ?? 'Failed to generate invoice'];
        } catch (\Exception $e) {
            Log::error('Shiprocket Invoice Exception: ' . $e->getMessage());
            return ['status' => false, 'message' => $e->getMessage()];
        }
    }

    // ─────────────────────────────────────────────────────────────────────────
    // STEP 8 — Schedule Pickup
    // ─────────────────────────────────────────────────────────────────────────
    public function requestPickup($shipmentId, ?string $pickupDate = null): array
    {
        // Security Check: Avoid Air delivery only surface delivery need to accept
        $order = Order::where('shiprocket_shipment_id', $shipmentId)->first();
        if ($order && $order->shiprocket_courier_name) {
            $name = strtolower($order->shiprocket_courier_name);
            if (str_contains($name, 'air')) {
                Log::warning("Pickup Blocked: Attempted pickup for AIR shipment #{$shipmentId} (Courier: {$order->shiprocket_courier_name})");
                return [
                    'status' => false, 
                    'message' => 'Air delivery is blocked. Please re-assign a Surface courier company in the Shiprocket panel or re-sync.'
                ];
            }
        }

        try {
            $payload = ['shipment_id' => [$shipmentId]];
            if ($pickupDate) {
                $payload['pickup_date'] = $pickupDate; // Format: YYYY-MM-DD
            }
            $response = $this->request('post', '/courier/generate/pickup', $payload);

            if ($response->successful()) {
                return ['status' => true, 'data' => $response->json()];
            }
            return ['status' => false, 'message' => $response->json('message') ?? 'Failed to request pickup'];
        } catch (\Exception $e) {
            Log::error('Shiprocket Pickup Exception: ' . $e->getMessage());
            return ['status' => false, 'message' => $e->getMessage()];
        }
    }

    // ─────────────────────────────────────────────────────────────────────────
    // STEP 9 — Track Shipment
    // ─────────────────────────────────────────────────────────────────────────
    public function trackOrder($shipmentId): ?array
    {
        try {
            $response = $this->request('get', "/courier/track/shipment/{$shipmentId}");
            return $response->json();
        } catch (\Exception $e) {
            Log::error('Shiprocket Tracking Exception: ' . $e->getMessage());
            return null;
        }
    }

    public function trackByAWB(string $awb): ?array
    {
        try {
            $response = $this->request('get', "/courier/track/awb/{$awb}");
            return $response->json();
        } catch (\Exception $e) {
            Log::error('Shiprocket Track AWB Exception: ' . $e->getMessage());
            return null;
        }
    }

    public function getOrderDetails($orderId): ?array
    {
        try {
            $response = $this->request('get', "/orders/show/{$orderId}");
            return $response->json();
        } catch (\Exception $e) {
            Log::error('Shiprocket Show Order Exception: ' . $e->getMessage());
            return null;
        }
    }

    // ─────────────────────────────────────────────────────────────────────────
    // STEP 10 — Handle Webhook (called from controller)
    // ─────────────────────────────────────────────────────────────────────────
    public function processWebhook(array $payload): bool
    {
        try {
            $awb         = $payload['awb']         ?? null;
            $status      = $payload['current_status'] ?? $payload['status'] ?? null;
            $shipmentId  = $payload['shipment_id'] ?? null;
            $orderId     = $payload['order_id']    ?? null;
            $edd         = $payload['edd']         ?? $payload['etd'] ?? $payload['expected_delivery_date'] ?? null;

            Log::info('Shiprocket Webhook Received:', [
                'awb' => $awb, 'status' => $status, 'shipment_id' => $shipmentId, 'edd' => $edd
            ]);

            if (!$awb && !$shipmentId && !$orderId) {
                return false;
            }

            // Find the order — check both forward and return shipment identifiers
            $order = null;
            if ($awb) {
                $order = Order::where('shiprocket_awb', $awb)
                             ->orWhere('reverse_awb', $awb)
                             ->first();
            }
            if (!$order && $shipmentId) {
                $order = Order::where('shiprocket_shipment_id', $shipmentId)
                             ->orWhere('shiprocket_return_shipment_id', $shipmentId)
                             ->first();
            }
            if (!$order && $orderId) {
                $order = Order::where('shiprocket_order_id', $orderId)
                             ->orWhere('shiprocket_return_order_id', $orderId)
                             ->first();
            }

            if (!$order) {
                Log::warning('Shiprocket Webhook: Order not found', $payload);
                return false;
            }

            // ── Store Tracking Activities (Scans) ──────────────────────────
            $activities = $payload['shipment_track_activities'] ?? $payload['scans'] ?? null;
            if ($activities && is_array($activities)) {
                $order->update(['shipment_track_activities' => $activities]);
            }

            // Determine if this update is for the return shipment
            $isReturn = ($awb && $order->reverse_awb === $awb) ||
                        ($shipmentId && $order->shiprocket_return_shipment_id == $shipmentId);

            // ── Status Map: Shiprocket raw status → our internal status ──────────
            $statusMap = [
                'READY TO SHIP'      => 'processing',
                'PICKUP SCHEDULED'   => 'processing',
                'PICKUP GENERATED'   => 'processing',
                'PICKUP RESCHEDULED' => 'processing',
                'PICKUP EXCEPTION'   => 'processing',
                'PICKED UP'          => 'shipped',
                'IN TRANSIT'         => 'shipped',
                'DISPATCHED'         => 'shipped',
                'OUT FOR DELIVERY'   => 'out for delivery',
                'DELIVERED'          => 'delivered',
                'RETURN RECEIVED'    => 'received',
                'QC PASSED'          => 'received',
                'QC FAILED'          => 'requested',
                'RTO'                => 'returned',
                'RTO INITIATED'      => 'returned',
                'UNDELIVERED'        => 'cancelled',
                'CANCELED'           => 'processing',
                'CANCELLED'          => 'processing',
            ];

            $normalizedStatus = strtoupper(trim($status ?? ''));
            $newMappedStatus  = $statusMap[$normalizedStatus] ?? null;

            if ($isReturn) {
                // ── Return Shipment Logic ─────────────────────────────────────────
                if ($newMappedStatus) {
                    $order->update(['return_status' => $newMappedStatus]);

                    // When the return item is received back at warehouse → set order as returned
                    if ($newMappedStatus === 'received') {
                        $order->syncStatus('returned', 'Return Received', null);
                    }
                }
                $order->update(['shiprocket_status' => 'Return: ' . $status]);
                Log::info("Shiprocket Return Update - Order #{$order->order_number}: return_status={$newMappedStatus}");
            } else {
                // ── Forward Shipment Logic ────────────────────────────────────────
                // Update EDD if present in payload
                if ($edd) {
                    $order->update(['edd' => $edd]);
                }

                if ($newMappedStatus) {
                    $result = $order->syncStatus(
                        $newMappedStatus,
                        $status,
                        $awb
                    );
                    Log::info("Shiprocket Forward Update - Order #{$order->order_number}: {$order->order_status} → {$newMappedStatus} (result=" . ($result ? 'updated' : 'skipped') . ")");
                } else {
                    // Unknown Shiprocket status — just log it, don't touch order_status
                    $order->update(['shiprocket_status' => $status]);
                    Log::info("Shiprocket Unmapped Status - Order #{$order->order_number}: raw='{$status}'");
                }
            }

            return true;
        } catch (\Exception $e) {
            Log::error('Shiprocket Webhook Processing Error: ' . $e->getMessage());
            return false;
        }
    }


    // ─────────────────────────────────────────────────────────────────────────
    // STEP 11 — Returns / RTO
    // ─────────────────────────────────────────────────────────────────────────
    public function createReturnOrder(Order $order): array
    {
        try {
            $payload = [
                'order_id'               => $order->order_number . '-RET',
                'order_date'             => now()->format('Y-m-d H:i'),
                'channel_id'             => '',
                'pickup_customer_name'   => $order->customer_name,
                'pickup_last_name'       => '',
                'pickup_address'         => $order->delivery_address,
                'pickup_city'            => $order->shipping_city    ?? '-',
                'pickup_state'           => $order->shipping_state   ?? '-',
                'pickup_country'         => $order->shipping_country ?? '-',
                'pickup_pincode'         => $order->shipping_pincode,
                'pickup_phone'           => $order->customer_phone,
                'shipping_customer_name' => config('app.name', 'Nandhini Silks'),
                'shipping_address'       => '416/9 Aranmanai Street ,S.V. Nagaram, Arni',
                'shipping_city'          => 'Thiruvannamalai',
                'shipping_state'         => 'Tamil Nadu',
                'shipping_country'       => 'India',
                'shipping_pincode'       => '632317',
                'shipping_phone'         => '9363152822',
                'order_items'            => [],
                'payment_method'         => 'Prepaid',
                'weight'                 => 0.5, // Total weight for the return shipment
                'sub_total'              => (float) $order->sub_total,
                'length'                 => 10,
                'breadth'                => 10,
                'height'                 => 10,
            ];
            
            $totalWeight = 0;
            foreach ($order->items as $item) {
                $itemPath = $item->product_image;
                if (!$itemPath && $item->product) {
                    $itemPath = $item->product->image_path;
                }
                $imageUrl = $itemPath ? asset('uploads/' . $itemPath) : null;

                $payload['order_items'][] = [
                    'sku'           => $item->product ? $item->product->sku : 'N-' . time(),
                    'name'          => $item->product_name,
                    'units'         => $item->quantity,
                    'selling_price' => (float) $item->price,
                    'image'         => $imageUrl,
                ];
                $itemWeight = (float) ($item->product->weight ?? 0.5);
                $totalWeight += ($itemWeight > 0 ? $itemWeight : 0.5) * $item->quantity;
            }
            $payload['weight'] = $totalWeight;

            Log::info('Shiprocket CreateReturn Payload:', $payload);
            $response = $this->request('post', '/orders/create/return', $payload);
            Log::info('Shiprocket CreateReturn Response:', $response->json() ?? ['body' => $response->body()]);

            if ($response->successful()) {
                return ['status' => true, 'data' => $response->json()];
            }
            return ['status' => false, 'message' => $response->json('message') ?? 'Failed to create return order'];
        } catch (\Exception $e) {
            Log::error('Shiprocket Return Exception: ' . $e->getMessage());
            return ['status' => false, 'message' => $e->getMessage()];
        }
    }

    // ─────────────────────────────────────────────────────────────────────────
    // STEP 12 — COD Remittance / Wallet
    // ─────────────────────────────────────────────────────────────────────────
    public function getWalletBalance(): array
    {
        try {
            $response = $this->request('get', '/account/details/wallet');
            if ($response->successful()) {
                return ['status' => true, 'data' => $response->json()];
            }
            return ['status' => false, 'message' => $response->json('message') ?? 'Failed to fetch wallet'];
        } catch (\Exception $e) {
            Log::error('Shiprocket Wallet Exception: ' . $e->getMessage());
            return ['status' => false, 'message' => $e->getMessage()];
        }
    }

    public function getRemittanceDetails(): array
    {
        try {
            $response = $this->request('get', '/account/details/remittance');
            if ($response->successful()) {
                return ['status' => true, 'data' => $response->json()];
            }
            return ['status' => false, 'message' => $response->json('message') ?? 'Failed to fetch remittance'];
        } catch (\Exception $e) {
            Log::error('Shiprocket Remittance Exception: ' . $e->getMessage());
            return ['status' => false, 'message' => $e->getMessage()];
        }
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Cancel Order
    // ─────────────────────────────────────────────────────────────────────────
    public function cancelOrder($shiprocketOrderId): array
    {
        try {
            Log::info("Shiprocket Cancel Request for Order ID: {$shiprocketOrderId}");
            $response = $this->request('post', '/orders/cancel', [
                'ids' => [$shiprocketOrderId],
            ]);

            $responseData = $response->json();
            Log::info("Shiprocket Cancel Response for Order ID {$shiprocketOrderId}:", $responseData ?? ['body' => $response->body()]);

            if ($response->successful()) {
                return ['status' => true, 'message' => 'Order cancelled in Shiprocket'];
            }
            return ['status' => false, 'message' => $responseData['message'] ?? 'Failed to cancel'];
        } catch (\Exception $e) {
            Log::error('Shiprocket Cancel Exception: ' . $e->getMessage());
            return ['status' => false, 'message' => $e->getMessage()];
        }
    }
}
