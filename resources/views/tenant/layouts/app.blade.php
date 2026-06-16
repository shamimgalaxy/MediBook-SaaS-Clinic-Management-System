<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta name="csrf-token" content="{{ csrf_token() }}" />
    <title>@yield('title', 'MediBook') — {{ tenant('clinic_name') }}</title>

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@latest/tabler-icons.min.css" />
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&display=swap">

    {{-- Load Vite assets only if manifest exists (production build), otherwise skip --}}
    @php
        $manifestPath = public_path('build/manifest.json');
        $viteReady = file_exists($manifestPath);
    @endphp
    @if($viteReady)
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    @endif

    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        :root {
            --font-sans: 'Inter', system-ui, sans-serif;
            --color-text-primary: #111827;
            --color-text-secondary: #6b7280;
            --color-background-primary: #ffffff;
            --color-background-secondary: #f9fafb;
            --color-border-tertiary: #e5e7eb;
            --border-radius-md: 6px;
            --border-radius-lg: 10px;
        }

        body {
            font-family: var(--font-sans);
            color: var(--color-text-primary);
            background: var(--color-background-secondary);
        }

        /* ── Top navbar ── */
        .topnav {
            background: var(--color-background-primary);
            border-bottom: 0.5px solid var(--color-border-tertiary);
            padding: 0 1.5rem;
            height: 52px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            position: sticky;
            top: 0;
            z-index: 100;
        }
        .topnav-left {
            display: flex;
            align-items: center;
            gap: 1.5rem;
        }
        .topnav-logo {
            font-size: 15px;
            font-weight: 600;
            color: #185FA5;
            display: flex;
            align-items: center;
            gap: 6px;
            text-decoration: none;
        }
        .topnav-logo i { font-size: 18px; }
        .topnav-clinic {
            font-size: 13px;
            color: var(--color-text-secondary);
            padding-left: 1.5rem;
            border-left: 0.5px solid var(--color-border-tertiary);
        }
        .topnav-nav {
            display: flex;
            align-items: center;
            gap: 4px;
        }
        .nav-item {
            display: flex;
            align-items: center;
            gap: 6px;
            padding: 6px 10px;
            font-size: 13px;
            color: var(--color-text-secondary);
            border-radius: var(--border-radius-md);
            text-decoration: none;
            white-space: nowrap;
        }
        .nav-item i { font-size: 15px; }
        .nav-item:hover {
            background: var(--color-background-secondary);
            color: var(--color-text-primary);
        }
        .nav-item.active {
            background: #E6F1FB;
            color: #185FA5;
            font-weight: 500;
        }
        .topnav-right {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        /* ── Notification bell ── */
        .notif-btn {
            position: relative;
            background: none;
            border: none;
            cursor: pointer;
            color: var(--color-text-secondary);
            font-size: 18px;
            padding: 4px;
            display: flex;
            align-items: center;
        }
        .notif-badge {
            position: absolute;
            top: 0;
            right: 0;
            width: 8px;
            height: 8px;
            background: #E24B4A;
            border-radius: 50%;
        }

        /* ── User menu ── */
        .user-menu {
            position: relative;
        }
        .user-btn {
            display: flex;
            align-items: center;
            gap: 8px;
            background: none;
            border: 0.5px solid var(--color-border-tertiary);
            border-radius: var(--border-radius-md);
            padding: 5px 10px;
            cursor: pointer;
            font-size: 13px;
            color: var(--color-text-primary);
        }
        .user-avatar {
            width: 26px;
            height: 26px;
            border-radius: 50%;
            background: #E6F1FB;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 11px;
            font-weight: 600;
            color: #0C447C;
            flex-shrink: 0;
        }
        .user-dropdown {
            display: none;
            position: absolute;
            right: 0;
            top: calc(100% + 6px);
            background: var(--color-background-primary);
            border: 0.5px solid var(--color-border-tertiary);
            border-radius: var(--border-radius-lg);
            box-shadow: 0 4px 16px rgba(0,0,0,0.08);
            min-width: 180px;
            z-index: 200;
            padding: 6px;
        }
        .user-menu:hover .user-dropdown,
        .user-menu:focus-within .user-dropdown {
            display: block;
        }
        .dropdown-item {
            display: flex;
            align-items: center;
            gap: 8px;
            padding: 8px 10px;
            font-size: 13px;
            color: var(--color-text-primary);
            border-radius: var(--border-radius-md);
            text-decoration: none;
            cursor: pointer;
            background: none;
            border: none;
            width: 100%;
            text-align: left;
        }
        .dropdown-item:hover { background: var(--color-background-secondary); }
        .dropdown-item i { font-size: 15px; color: var(--color-text-secondary); }
        .dropdown-divider { height: 0.5px; background: var(--color-border-tertiary); margin: 4px 0; }

        /* ── Page wrapper ── */
        .page-wrapper {
            padding: 1.5rem;
            max-width: 1280px;
            margin: 0 auto;
        }

        /* ── Flash messages ── */
        .alert-success {
            background: #EAF3DE; color: #27500A;
            border-radius: 8px; padding: 10px 14px;
            display: flex; align-items: center; gap: 8px;
            font-size: 13px; margin-bottom: 1rem;
        }
        .alert-error {
            background: #FEE2E2; color: #A32D2D;
            border-radius: 8px; padding: 10px 14px;
            display: flex; align-items: center; gap: 8px;
            font-size: 13px; margin-bottom: 1rem;
        }
        @media (max-width: 768px) {
    .topnav-nav { display: none; } /* add hamburger menu later */
    .topnav-clinic { display: none; }
}
    </style>
    @stack('styles')
</head>
<body>

{{-- ── Top Navigation ── --}}
<nav class="topnav">
    <div class="topnav-left">
        <a href="{{ route('dashboard') }}" class="topnav-logo">
            <i class="ti ti-activity-heartbeat"></i> MediBook
        </a>
        @php $cs = \App\Models\ClinicSetting::first(); @endphp
        <span class="topnav-clinic">
            @if($cs?->logo)
                <img src="{{ $cs->logoUrl() }}"
                     style="height:22px;object-fit:contain;vertical-align:middle;margin-right:4px;" />
            @endif
            {{ $cs?->clinic_name ?? tenant('clinic_name') }}
        </span>
    </div>

    <div class="topnav-nav">
        @hasanyrole('clinic_admin|receptionist|doctor|patient')
        <a href="{{ route('appointments.index') }}"
           class="nav-item {{ request()->routeIs('appointments.*') ? 'active' : '' }}">
            <i class="ti ti-calendar-event"></i> Appointments
        </a>
        @endhasanyrole

        @hasanyrole('clinic_admin|receptionist')
        <a href="{{ route('invoices.index') }}"
           class="nav-item {{ request()->routeIs('invoices.*') ? 'active' : '' }}">
            <i class="ti ti-receipt"></i> Invoices
        </a>
        @endhasanyrole

        @hasanyrole('patient')
        <a href="{{ route('invoices.index') }}"
           class="nav-item {{ request()->routeIs('invoices.*') ? 'active' : '' }}">
            <i class="ti ti-receipt"></i> My Invoices
        </a>
        @endhasanyrole

        @hasanyrole('clinic_admin')
        <a href="{{ route('admin.doctors.index') }}"
           class="nav-item {{ request()->routeIs('admin.doctors.*') ? 'active' : '' }}">
            <i class="ti ti-stethoscope"></i> Doctors
        </a>
        <a href="{{ route('reports.revenue') }}"
           class="nav-item {{ request()->routeIs('reports.*') ? 'active' : '' }}">
            <i class="ti ti-chart-bar"></i> Reports
        </a>
        <a href="{{ route('settings.index') }}"
           class="nav-item {{ request()->routeIs('settings.*') ? 'active' : '' }}">
            <i class="ti ti-settings"></i> Settings
        </a>
        @endhasanyrole

        @hasanyrole('clinic_admin|receptionist|doctor|patient')
        <a href="{{ route('prescriptions.index') }}"
           class="nav-item {{ request()->routeIs('prescriptions.*') ? 'active' : '' }}">
            <i class="ti ti-file-text"></i> Prescriptions
        </a>
        @endhasanyrole
    </div>

    <div class="topnav-right">

        {{-- Notifications --}}
        <button class="notif-btn" onclick="window.location='{{ route('notifications.index') }}'">
            <i class="ti ti-bell"></i>
            @php
                $unreadCount = auth()->user()?->unreadNotifications->count() ?? 0;
            @endphp
            @if($unreadCount > 0)
                <span class="notif-badge"></span>
            @endif
        </button>

        {{-- User menu --}}
        <div class="user-menu">
            <button class="user-btn">
                <div class="user-avatar">
                    {{ substr(auth()->user()?->name ?? 'U', 0, 1) }}
                </div>
                <span>{{ auth()->user()?->name }}</span>
                <i class="ti ti-chevron-down" style="font-size:13px;color:var(--color-text-secondary);"></i>
            </button>
            <div class="user-dropdown">
                <div style="padding:8px 10px;border-bottom:0.5px solid var(--color-border-tertiary);margin-bottom:4px;">
                    <p style="font-size:13px;font-weight:500;">{{ auth()->user()?->name }}</p>
                    <p style="font-size:11px;color:var(--color-text-secondary);margin-top:1px;">{{ auth()->user()?->email }}</p>
                </div>
                @hasanyrole('patient')
                <a href="{{ route('patient.profile') }}" class="dropdown-item">
                    <i class="ti ti-user"></i> Profile
                </a>
                @else
                <a href="{{ route('profile.edit') }}" class="dropdown-item">
                    <i class="ti ti-user"></i> Profile
                </a>
                @endhasanyrole
                @hasanyrole('clinic_admin')
                <a href="{{ route('subscription.index') }}" class="dropdown-item">
                    <i class="ti ti-credit-card"></i> Subscription
                </a>
                @endhasanyrole
                <div class="dropdown-divider"></div>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="dropdown-item" style="color:#A32D2D;">
                        <i class="ti ti-logout" style="color:#A32D2D;"></i> Logout
                    </button>
                </form>
            </div>
        </div>

    </div>
</nav>

{{-- ── Page Content ── --}}
<div class="page-wrapper">

    @if(session('success'))
        <div class="alert-success">
            <i class="ti ti-circle-check"></i> {{ session('success') }}
        </div>
    @endif
    @if(session('error'))
        <div class="alert-error">
            <i class="ti ti-alert-circle"></i> {{ session('error') }}
        </div>
    @endif

    @yield('content')
</div>

@stack('scripts')
</body>
</html>