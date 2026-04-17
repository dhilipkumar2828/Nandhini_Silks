<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Services\ShiprocketService;

class ShiprocketWebhookController extends Controller
{
    /**
     * Handle Shiprocket POST webhook (Step 10)
     * Configure URL in Shiprocket Dashboard → Settings → Webhooks
     * URL: https://yourdomain.com/api/shiprocket/webhook
     */
    public function handle(Request $request, ShiprocketService $shiprocket)
    {
        // Security: Check token from Shiprocket header 'x-api-key'
        $token = $request->header('x-api-key');
        $expectedToken = config('services.shiprocket.webhook_token');

        if ($expectedToken && $token !== $expectedToken) {
            Log::warning('Shiprocket Webhook: Invalid token received.', ['received' => $token]);
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 401);
        }

        Log::info('Shiprocket Webhook Raw Payload:', $request->all());

        $payload = $request->all();
        $success  = $shiprocket->processWebhook($payload);

        return response()->json([
            'success' => $success,
            'message' => $success ? 'Webhook processed successfully.' : 'No matching order found.',
        ], 200);
    }
}
