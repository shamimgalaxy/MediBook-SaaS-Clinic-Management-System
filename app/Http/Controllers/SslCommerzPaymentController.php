<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Tenant\SubscriptionController;
use Illuminate\Http\Request;
use Stancl\Tenancy\Database\Models\Domain;

class SslCommerzPaymentController extends Controller
{
    protected function ensureTenancy(Request $request): void
    {
        if (! tenancy()->initialized) {
            $domain = Domain::where('domain', $request->getHost())->firstOrFail();
            tenancy()->initialize($domain->tenant);
        }
    }

    public function success(Request $request)
    {
        $this->ensureTenancy($request);
        return app(SubscriptionController::class)->success($request);
    }

    public function fail(Request $request)
    {
        $this->ensureTenancy($request);
        return app(SubscriptionController::class)->fail($request);
    }

    public function cancel(Request $request)
    {
        $this->ensureTenancy($request);
        return app(SubscriptionController::class)->cancel($request);
    }

    public function ipn(Request $request)
    {
        $this->ensureTenancy($request);
        return app(SubscriptionController::class)->ipn($request);
    }
}