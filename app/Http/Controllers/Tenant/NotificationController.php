<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;

class NotificationController extends Controller
{
    public function index()
    {
        return view('tenant.notifications.index');
    }
}
