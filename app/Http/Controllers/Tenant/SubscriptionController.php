<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Models\SubscriptionPayment;
use App\Models\SubscriptionPlan;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Karim007\SslcommerzLaravel\Facade\SSLCommerzPayment;

class SubscriptionController extends Controller
{
    // ── Show available plans & current subscription ────────────
    public function index()
    {
        $tenant = tenant();
        $plans  = SubscriptionPlan::where('is_active', true)->orderBy('price')->get();

        return view('tenant.subscription.index', compact('tenant', 'plans'));
    }

    // ── Initiate payment ───────────────────────────────────────
    public function initiate(Request $request)
    {
        $request->validate([
            'plan_id'      => 'required|exists:central.subscription_plans,id',
            'duration_days'=> 'required|integer|in:30,90,180,365',
        ]);

        $plan   = SubscriptionPlan::findOrFail($request->plan_id);
        $tenant = tenant();

        $months = match((int) $request->duration_days) {
            90  => 3,
            180 => 6,
            365 => 12,
            default => 1,
        };
        $amount = $plan->price * $months;

        $transactionId = 'MB-' . strtoupper(Str::random(10));

        $payment = SubscriptionPayment::create([
            'tenant_id'     => $tenant->id,
            'plan_id'       => $plan->id,
            'amount'        => $amount,
            'duration_days' => $request->duration_days,
            'transaction_id'=> $transactionId,
            'status'        => 'pending',
        ]);

        $data = [
            'total_amount'        => $amount,
            'currency'            => 'BDT',
            'tran_id'             => $transactionId,
            'success_url'         => route('subscription.success'),
            'fail_url'            => route('subscription.fail'),
            'cancel_url'          => route('subscription.cancel'),
            'ipn_url'             => route('subscription.ipn'),
            'shipping_method'     => 'NO',
            'product_name'        => 'MediBook ' . $plan->name . ' Plan',
            'product_category'    => 'SaaS Subscription',
            'product_profile'     => 'non-physical-goods',
            'cus_name'            => $tenant->clinic_name,
            'cus_email'           => auth()->user()->email,
            'cus_add1'            => 'Dhaka',
            'cus_city'            => 'Dhaka',
            'cus_country'         => 'Bangladesh',
            'cus_phone'           => '01700000000',
        ];

        $response = SSLCommerzPayment::makePayment($data);

        $responseArray = is_string($response) ? json_decode($response, true) : $response;

        if (isset($responseArray['status']) && $responseArray['status'] === 'success' && !empty($responseArray['data'])) {
            return redirect($responseArray['data']);
        }

        return back()->with('error', 'Payment gateway error. Please try again.');
    }

    // ── Success callback ───────────────────────────────────────
    public function success(Request $request)
    {
        $payment = SubscriptionPayment::where('transaction_id', $request->tran_id)
            ->where('status', 'pending')
            ->firstOrFail();

        $validation = SSLCommerzPayment::orderValidate(
            $request->all(),
            $request->tran_id,
            $payment->amount,
            'BDT'
        );

        if ($validation === true || (isset($validation['status']) && $validation['status'] === 'VALID')) {
            $payment->update([
                'status'           => 'completed',
                'val_id'           => $request->val_id,
                'gateway_response' => $request->all(),
            ]);

            $tenant = \App\Models\Tenant::find($payment->tenant_id);
            $tenant->update([
                'plan_id'         => $payment->plan_id,
                'plan_expires_at' => now()->addDays($payment->duration_days),
                'on_trial'        => false,
            ]);

            return response()->view('tenant.subscription.result', [
                'status'  => 'success',
                'message' => 'Subscription activated successfully! Plan valid until ' .
                    now()->addDays($payment->duration_days)->format('d M Y') . '.',
            ]);
        }

        $payment->update(['status' => 'failed', 'gateway_response' => $request->all()]);

        return response()->view('tenant.subscription.result', [
            'status'  => 'error',
            'message' => 'Payment validation failed. Please contact support.',
        ]);
    }

    // ── Fail callback ──────────────────────────────────────────
    public function fail(Request $request)
    {
        SubscriptionPayment::where('transaction_id', $request->tran_id)
            ->update(['status' => 'failed', 'gateway_response' => $request->all()]);

        return response()->view('tenant.subscription.result', [
            'status'  => 'error',
            'message' => 'Payment failed. Please try again.',
        ]);
    }

    // ── Cancel callback ────────────────────────────────────────
    public function cancel(Request $request)
    {
        SubscriptionPayment::where('transaction_id', $request->tran_id)
            ->update(['status' => 'cancelled', 'gateway_response' => $request->all()]);

        return response()->view('tenant.subscription.result', [
            'status'  => 'error',
            'message' => 'Payment cancelled.',
        ]);
    }

    // ── IPN callback ───────────────────────────────────────────
    public function ipn(Request $request)
    {
        if ($request->tran_id) {
            $payment = SubscriptionPayment::where('transaction_id', $request->tran_id)
                ->where('status', 'pending')
                ->first();

            if ($payment && $request->status === 'VALID') {
                $payment->update([
                    'status'           => 'completed',
                    'val_id'           => $request->val_id,
                    'gateway_response' => $request->all(),
                ]);

                $tenant = \App\Models\Tenant::find($payment->tenant_id);
                $tenant->update([
                    'plan_id'         => $payment->plan_id,
                    'plan_expires_at' => now()->addDays($payment->duration_days),
                    'on_trial'        => false,
                ]);
            }
        }

        return response('IPN received', 200);
    }
}