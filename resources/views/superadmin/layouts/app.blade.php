<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>@yield('title', 'SuperAdmin') — MediBook</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@latest/tabler-icons.min.css" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
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
        body { font-family: var(--font-sans); color: var(--color-text-primary); }

        /* ── Layout ── */
        .sa-layout { display: grid; grid-template-columns: 220px minmax(0,1fr); min-height: 100vh; }

        /* ── Sidebar ── */
        .sidebar { background: #042C53; display: flex; flex-direction: column; position: sticky; top: 0; height: 100vh; }
        .sb-logo { display: flex; align-items: center; gap: 8px; padding: 1.25rem 1rem; font-size: 16px; font-weight: 500; color: #E6F1FB; border-bottom: 0.5px solid #0C447C; text-decoration: none; }
        .sb-logo i { font-size: 20px; color: #85B7EB; }
        .sb-section { padding: 1rem 0.75rem 0.5rem; font-size: 11px; font-weight: 500; color: #378ADD; letter-spacing: 0.8px; text-transform: uppercase; }
        .sb-item { display: flex; align-items: center; gap: 10px; padding: 9px 1rem; font-size: 13px; color: #85B7EB; border-radius: 6px; margin: 1px 6px; text-decoration: none; }
        .sb-item i { font-size: 16px; }
        .sb-item:hover { background: #0C447C; color: #E6F1FB; }
        .sb-item.active { background: #185FA5; color: #fff; }
        .sb-item .badge { margin-left: auto; background: #378ADD; color: #E6F1FB; font-size: 11px; padding: 2px 7px; border-radius: 99px; }
        .sb-footer { margin-top: auto; padding: 1rem; border-top: 0.5px solid #0C447C; }
        .sb-user { display: flex; align-items: center; gap: 10px; }
        .sb-avatar { width: 32px; height: 32px; border-radius: 50%; background: #185FA5; display: flex; align-items: center; justify-content: center; font-size: 12px; font-weight: 500; color: #E6F1FB; flex-shrink: 0; }
        .sb-user-info p { font-size: 13px; font-weight: 500; color: #E6F1FB; }
        .sb-user-info span { font-size: 11px; color: #85B7EB; }

        /* ── Main ── */
        .sa-main { background: var(--color-background-secondary); display: flex; flex-direction: column; min-height: 100vh; }
        .topbar { background: var(--color-background-primary); border-bottom: 0.5px solid var(--color-border-tertiary); padding: 0.875rem 1.5rem; display: flex; align-items: center; justify-content: space-between; position: sticky; top: 0; z-index: 10; }
        .topbar-left h1 { font-size: 17px; font-weight: 500; }
        .topbar-left p { font-size: 12px; color: var(--color-text-secondary); margin-top: 2px; }
        .topbar-right { display: flex; align-items: center; gap: 10px; }
        .tb-btn { background: transparent; border: 0.5px solid var(--color-border-tertiary); border-radius: var(--border-radius-md); padding: 6px 12px; font-size: 12px; color: var(--color-text-secondary); cursor: pointer; display: inline-flex; align-items: center; gap: 5px; text-decoration: none; }
        .tb-btn.primary { background: #185FA5; color: #fff; border-color: #185FA5; }
        .tb-btn:hover { background: var(--color-background-secondary); }
        .tb-btn.primary:hover { background: #0C447C; }
        .sa-content { padding: 1.5rem; flex: 1; }

        /* ── Common components ── */
        .card { background: var(--color-background-primary); border: 0.5px solid var(--color-border-tertiary); border-radius: var(--border-radius-lg); }
        .pill { display: inline-block; padding: 2px 8px; border-radius: 99px; font-size: 11px; font-weight: 500; }
        .pill-green { background: #EAF3DE; color: #27500A; }
        .pill-amber { background: #FAEEDA; color: #633806; }
        .pill-red   { background: #FCEBEB; color: #791F1F; }
        .pill-blue  { background: #E6F1FB; color: #0C447C; }
        .plan-basic { background: #E6F1FB; color: #0C447C; padding: 2px 8px; border-radius: 99px; font-size: 11px; }
        .plan-pro   { background: #EAF3DE; color: #27500A; padding: 2px 8px; border-radius: 99px; font-size: 11px; }
        .plan-ent   { background: #EEEDFE; color: #3C3489; padding: 2px 8px; border-radius: 99px; font-size: 11px; }
        .stats-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(130px,1fr)); gap: 12px; margin-bottom: 1.5rem; }
        .stat-card { background: var(--color-background-primary); border: 0.5px solid var(--color-border-tertiary); border-radius: var(--border-radius-lg); padding: 1rem; }
        .stat-label { font-size: 12px; color: var(--color-text-secondary); margin-bottom: 6px; display: flex; align-items: center; gap: 6px; }
        .stat-label i { font-size: 14px; }
        .stat-val { font-size: 24px; font-weight: 500; }
        .stat-sub { font-size: 12px; margin-top: 4px; }
        .stat-up { color: #3B6D11; }
        .stat-down { color: #A32D2D; }
        table { width: 100%; border-collapse: collapse; font-size: 13px; }
        th { text-align: left; font-size: 11px; font-weight: 500; color: var(--color-text-secondary); padding: 0 0 8px; text-transform: uppercase; letter-spacing: 0.5px; border-bottom: 0.5px solid var(--color-border-tertiary); }
        td { padding: 10px 0; border-bottom: 0.5px solid var(--color-border-tertiary); vertical-align: middle; }
        tr:last-child td { border-bottom: none; }
        .alert-success { background: #EAF3DE; color: #27500A; border-radius: 8px; padding: 10px 14px; display: flex; align-items: center; gap: 8px; font-size: 13px; margin-bottom: 1rem; }
        .alert-error { background: #FCEBEB; color: #791F1F; border-radius: 8px; padding: 10px 14px; display: flex; align-items: center; gap: 8px; font-size: 13px; margin-bottom: 1rem; }
        .mb-4 { margin-bottom: 1rem; }
        .filter-input { padding: 6px 10px; border: 0.5px solid var(--color-border-tertiary); border-radius: var(--border-radius-md); font-size: 13px; background: var(--color-background-primary); color: var(--color-text-primary); }
        .filter-input:focus { outline: none; border-color: #185FA5; }
        .field { margin-bottom: 10px; }
        .field label { display: block; font-size: 12px; color: var(--color-text-secondary); margin-bottom: 4px; }
        .field-input { width: 100%; padding: 7px 10px; border: 0.5px solid var(--color-border-tertiary); border-radius: var(--border-radius-md); font-size: 13px; background: var(--color-background-primary); color: var(--color-text-primary); }
        .field-input:focus { outline: none; border-color: #185FA5; }
    </style>
    @stack('styles')
</head>
<body>

<div class="sa-layout">

    {{-- ── Sidebar ── --}}
    <aside class="sidebar">
        <a href="{{ route('superadmin.dashboard') }}" class="sb-logo">
            <i class="ti ti-activity-heartbeat"></i> MediBook
        </a>

        <div class="sb-section">Overview</div>
        <a href="{{ route('superadmin.dashboard') }}"
           class="sb-item {{ request()->routeIs('superadmin.dashboard') ? 'active' : '' }}">
            <i class="ti ti-layout-dashboard"></i> Dashboard
        </a>
        <a href="{{ route('superadmin.tenants.index') }}"
           class="sb-item {{ request()->routeIs('superadmin.tenants.*') ? 'active' : '' }}">
            <i class="ti ti-building-hospital"></i> Clinics
        </a>

        <div class="sb-section">Billing</div>
        <a href="{{ route('superadmin.plans.index') }}"
           class="sb-item {{ request()->routeIs('superadmin.plans.*') ? 'active' : '' }}">
            <i class="ti ti-credit-card"></i> Plans
        </a>

        <div class="sb-section">Account</div>
        <form method="POST" action="{{ route('superadmin.logout') }}">
            @csrf
            <button type="submit" class="sb-item" style="width:100%;background:none;border:none;cursor:pointer;text-align:left;">
                <i class="ti ti-logout"></i> Logout
            </button>
        </form>

        <div class="sb-footer">
            <div class="sb-user">
                <div class="sb-avatar">SA</div>
                <div class="sb-user-info">
                    <p>{{ auth('super_admin')->user()->name ?? 'Super Admin' }}</p>
                    <span>{{ auth('super_admin')->user()->email ?? '' }}</span>
                </div>
            </div>
        </div>
    </aside>

    {{-- ── Main ── --}}
    <div class="sa-main">
        <div class="topbar">
            <div class="topbar-left">
                <h1>@yield('page_title', 'Dashboard')</h1>
                <p>{{ now()->format('l, d F Y') }}</p>
            </div>
            <div class="topbar-right">
                @yield('topbar_actions')
            </div>
        </div>

        <div class="sa-content">
            @if(session('success'))
                <div class="alert-success"><i class="ti ti-circle-check"></i> {{ session('success') }}</div>
            @endif
            @if(session('error'))
                <div class="alert-error"><i class="ti ti-alert-circle"></i> {{ session('error') }}</div>
            @endif

            @yield('content')
        </div>
    </div>

</div>

@stack('scripts')
</body>
</html>