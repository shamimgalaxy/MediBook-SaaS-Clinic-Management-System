<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\SubscriptionPlan;

class SubscriptionPlanSeeder extends Seeder
{
    public function run(): void
    {
        $plans = [
            [
                'name'              => 'Basic',
                'slug'              => 'basic',
                'price'             => 500.00,
                'max_doctors'       => 1,
                'max_appointments'  => 50,
                'sms_notifications' => false,
                'custom_domain'     => false,
                'excel_reports'     => false,
            ],
            [
                'name'              => 'Pro',
                'slug'              => 'pro',
                'price'             => 1200.00,
                'max_doctors'       => 5,
                'max_appointments'  => -1,
                'sms_notifications' => true,
                'custom_domain'     => false,
                'excel_reports'     => true,
            ],
            [
                'name'              => 'Enterprise',
                'slug'              => 'enterprise',
                'price'             => 2500.00,
                'max_doctors'       => -1,
                'max_appointments'  => -1,
                'sms_notifications' => true,
                'custom_domain'     => true,
                'excel_reports'     => true,
            ],
        ];

        foreach ($plans as $plan) {
            SubscriptionPlan::create($plan);
        }
    }
}