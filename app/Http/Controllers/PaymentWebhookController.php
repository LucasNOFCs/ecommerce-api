<?php

namespace App\Http\Controllers;

use App\Services\PaymentWebhookService;
use Illuminate\Http\Request;

class PaymentWebhookController extends Controller
{
    public function __invoke(
        Request $request,
        PaymentWebhookService $webhookService
    ) {
        $webhookService->handle($request->all());

        return response()->json([
            'message' => 'Webhook processed successfully.',
        ]);
    }
}
