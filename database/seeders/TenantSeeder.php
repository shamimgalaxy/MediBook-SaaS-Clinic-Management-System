<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Tenant;

class TenantSeeder extends Seeder
{
  public function run(): void
  {
    $tenant = Tenant::create([
    'id' => 'clinic1',
    'clinic_name' => 'Test Clinic',
    'plan' => 'basic',
    'is_active' => true,
]);

$tenant->domains()->create([
    'domain' => 'testclinic.medibook.test',
]);
  }
}