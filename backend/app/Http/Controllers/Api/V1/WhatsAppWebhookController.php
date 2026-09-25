<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\MetaCredential;
use App\Scopes\WorkspaceScope;
use App\Services\WhatsApp\WebhookProcessor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class WhatsAppWebhookController extends Controller
{
    /**
     * Handle Meta Webhook verification handshake.
     */
    public function verify(Request $request)
    {
        $mode = $request->query('hub_mode');
        $token = $request->query('hub_verify_token');
        $challenge = $request->query('hub_challenge');

        $defaultToken = config('services.meta.verify_token', 'whatscrm_secure_token');

        // Check against default or existing workspace credentials
        $isValid = ($token === $defaultToken) || 
            MetaCredential::withoutGlobalScope(WorkspaceScope::class)->where('verify_token', $token)->exists();

        if ($mode === 'subscribe' && $isValid) {
            return response($challenge, 200)->header('Content-Type', 'text/plain');
        }

        return response('Forbidden', 403);
    }

    /**
     * Handle incoming Meta Webhook notifications.
     */
    public function handle(Request $request, WebhookProcessor $processor)
    {
        $payload = $request->all();

        try {
            $processor->process($payload);
        } catch (\Throwable $e) {
            Log::error('Webhook processing exception: ' . $e->getMessage(), [
                'payload' => $payload,
                'trace' => $e->getTraceAsString(),
            ]);
        }

        return response()->json(['status' => 'success'], 200);
    }
}
