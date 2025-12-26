<?php

namespace App\Features\Groups\Controllers;

use App\Features\Groups\Services\ZoomWebhookService;
use App\Traits\ApiResponses;
use App\Traits\HandleServiceExceptions;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Log;

class ZoomWebhookController extends Controller
{
    use ApiResponses, HandleServiceExceptions;

    public function __construct(
        protected ZoomWebhookService $service
    ) {}

    /**
     * Handle Zoom Webhook
     * URL: POST /api/webhooks/zoom
     */
    public function handleWebhook(Request $request)
    {
        // Zoom URL Validation (for initial setup)
        if ($request->has('payload') && isset($request->payload['plainToken'])) {
            return $this->handleUrlValidation($request);
        }

        return $this->executeService(function () use ($request) {
            // Verify webhook signature
            if (!$this->verifySignature($request)) {
                Log::warning('Zoom webhook: Invalid signature');
                return response()->json(['error' => 'Invalid signature'], 401);
            }

            $payload = $request->all();

            Log::info('Zoom webhook received', ['event' => $payload['event'] ?? 'unknown']);

            $this->service->processWebhook($payload);

            return response()->json(['success' => true]);
        }, 'ZoomWebhookController@handleWebhook');
    }

    /**
     * Handle Zoom URL Validation Challenge
     * Zoom يرسل هذا عند إعداد الـ Webhook لأول مرة
     */
    protected function handleUrlValidation(Request $request): \Illuminate\Http\JsonResponse
    {
        $plainToken = $request->payload['plainToken'];
        $secret = config('services.zoom.webhook_secret', '');

        $hashToken = hash_hmac('sha256', $plainToken, $secret);

        return response()->json([
            'plainToken' => $plainToken,
            'encryptedToken' => $hashToken,
        ]);
    }

    /**
     * Verify Zoom webhook signature
     */
    protected function verifySignature(Request $request): bool
    {
        $secret = config('services.zoom.webhook_secret');

        // If no secret configured, skip verification
        if (empty($secret)) {
            return true;
        }

        $signature = $request->header('x-zm-signature');
        $timestamp = $request->header('x-zm-request-timestamp');

        if (empty($signature) || empty($timestamp)) {
            return false;
        }

        $payload = $request->getContent();
        $message = "v0:{$timestamp}:{$payload}";
        $expectedSignature = 'v0=' . hash_hmac('sha256', $message, $secret);

        return hash_equals($expectedSignature, $signature);
    }
}
