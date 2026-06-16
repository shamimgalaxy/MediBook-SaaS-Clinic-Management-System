<?php

if (!function_exists('clinic_settings')) {
    function clinic_settings(): \App\Models\ClinicSetting
    {
        return \App\Models\ClinicSetting::firstOrCreate(
            [],
            array_merge(
                ['clinic_name' => tenant('clinic_name')],
                \App\Models\ClinicSetting::defaultWorkingHours()
            )
        );
    }
}