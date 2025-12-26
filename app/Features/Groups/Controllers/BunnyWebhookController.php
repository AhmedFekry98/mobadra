<?php

namespace App\Features\Groups\Controllers;

use App\Features\Groups\Services\BunnyWebhookService;
use App\Traits\ApiResponses;
use App\Traits\HandleServiceExceptions;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Log;

class BunnyWebhookController extends Controller
{
    use ApiResponses, HandleServiceExceptions;

    public function __construct(
        protected BunnyWebhookService $service
    ) {}

    /**
     * Handle Bunny Stream webhook
     * هذا الـ endpoint يستقبل بيانات المشاهدة من Bunny
     * URL: POST /api/webhooks/bunny
     */
    public function handleWebhook(Request $request)
    {
        return $this->executeService(function () use ($request) {
            // Verify webhook signature if configured
            if (!$this->verifyWebhookSignature($request)) {
                Log::warning('Bunny webhook: Invalid signature');
                return response()->json(['error' => 'Invalid signature'], 401);
            }

            $data = $request->all();

            Log::info('Bunny webhook received', ['data' => $data]);

            $this->service->processWebhook($data);

            return response()->json(['success' => true]);
        }, 'BunnyWebhookController@handleWebhook');
    }

    /**
     * Verify Bunny webhook signature
     */
    protected function verifyWebhookSignature(Request $request): bool
    {
        $secret = config('services.bunny.webhook_secret');

        // If no secret configured, skip verification
        if (empty($secret)) {
            return true;
        }

        $signature = $request->header('X-Bunny-Signature');

        if (empty($signature)) {
            return false;
        }

        $payload = $request->getContent();
        $expectedSignature = hash_hmac('sha256', $payload, $secret);

        return hash_equals($expectedSignature, $signature);
    }
}
