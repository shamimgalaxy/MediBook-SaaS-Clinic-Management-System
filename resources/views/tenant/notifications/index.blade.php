{{-- resources/views/tenant/notifications/index.blade.php --}}
@extends('tenant.layouts.app')

@section('title', 'Notifications')

@section('content')
<div class="content-header">
    <div>
        <h1 class="page-title">Notifications</h1>
        <p class="page-sub">Your activity and appointment updates</p>
    </div>
    @if($notifications->total() > 0)
    <div style="display:flex;gap:8px;">
        <form method="POST" action="{{ route('notifications.markAllRead') }}">
            @csrf
            <button type="submit" class="tb-btn">
                <i class="ti ti-checks"></i> Mark all read
            </button>
        </form>
        <form method="POST" action="{{ route('notifications.destroyAll') }}"
            onsubmit="return confirm('Clear all notifications?')">
            @csrf @method('DELETE')
            <button type="submit" class="tb-btn" style="color:#A32D2D;border-color:#A32D2D;">
                <i class="ti ti-trash"></i> Clear all
            </button>
        </form>
    </div>
    @endif
</div>

@if(session('success'))
    <div class="alert-item mb-4"><i class="ti ti-circle-check"></i> {{ session('success') }}</div>
@endif

<div class="card" style="padding:0;">
    @forelse($notifications as $notification)
    @php
        $data   = $notification->data;
        $isRead = !is_null($notification->read_at);

        $iconBg = match($data['type'] ?? '') {
            'appointment_booked'           => ['#E6F1FB','#185FA5'],
            'appointment_status_confirmed' => ['#E6F1FB','#185FA5'],
            'appointment_status_in_progress' => ['#EDE9FE','#5B21B6'],
            'appointment_status_completed' => ['#EAF3DE','#27500A'],
            'appointment_status_cancelled' => ['#FEE2E2','#A32D2D'],
            'payment_received'             => ['#EAF3DE','#27500A'],
            default                        => ['#F3F4F6','#6B7280'],
        };
    @endphp
    <div style="display:flex;gap:12px;padding:14px 16px;border-bottom:0.5px solid var(--color-border-tertiary);{{ $isRead ? '' : 'background:rgba(24,95,165,0.03);' }}"
        class="notif-row">

        {{-- Icon --}}
        <div style="width:36px;height:36px;border-radius:50%;background:{{ $iconBg[0] }};display:flex;align-items:center;justify-content:center;flex-shrink:0;margin-top:2px;">
            <i class="ti ti-{{ $data['icon'] ?? 'bell' }}" style="font-size:16px;color:{{ $iconBg[1] }};"></i>
        </div>

        {{-- Content --}}
        <div style="flex:1;min-width:0;">
            <div style="display:flex;justify-content:space-between;align-items:flex-start;gap:8px;">
                <div>
                    <span style="font-size:13px;font-weight:{{ $isRead ? '400' : '600' }};color:var(--color-text-primary);">
                        {{ $data['title'] ?? 'Notification' }}
                    </span>
                    @if(!$isRead)
                        <span style="display:inline-block;width:6px;height:6px;background:#185FA5;border-radius:50%;margin-left:6px;vertical-align:middle;"></span>
                    @endif
                </div>
                <span style="font-size:11px;color:var(--color-text-secondary);white-space:nowrap;flex-shrink:0;">
                    {{ $notification->created_at->diffForHumans() }}
                </span>
            </div>
            <p style="font-size:12px;color:var(--color-text-secondary);margin:3px 0 8px;line-height:1.5;">
                {{ $data['body'] ?? '' }}
            </p>
            <div style="display:flex;gap:10px;align-items:center;">
                @if(isset($data['action_url']))
                <a href="{{ $data['action_url'] }}" style="font-size:12px;color:#185FA5;text-decoration:none;">
                    View details <i class="ti ti-arrow-right" style="font-size:11px;"></i>
                </a>
                @endif
                @if(!$isRead)
                <form method="POST" action="{{ route('notifications.markRead', $notification->id) }}">
                    @csrf
                    <button type="submit"
                        style="background:none;border:none;font-size:12px;color:var(--color-text-secondary);cursor:pointer;padding:0;">
                        Mark read
                    </button>
                </form>
                @endif
                <form method="POST" action="{{ route('notifications.destroy', $notification->id) }}">
                    @csrf @method('DELETE')
                    <button type="submit"
                        style="background:none;border:none;font-size:12px;color:#A32D2D;cursor:pointer;padding:0;">
                        Delete
                    </button>
                </form>
            </div>
        </div>
    </div>
    @empty
    <div style="padding:3rem;text-align:center;">
        <i class="ti ti-bell-off" style="font-size:32px;color:var(--color-text-secondary);display:block;margin-bottom:8px;"></i>
        <p style="font-size:13px;color:var(--color-text-secondary);margin:0;">No notifications yet.</p>
    </div>
    @endforelse

    @if($notifications->hasPages())
    <div style="padding:0.75rem 1rem;border-top:0.5px solid var(--color-border-tertiary);">
        {{ $notifications->links() }}
    </div>
    @endif
</div>

<style>
.content-header{display:flex;align-items:flex-start;justify-content:space-between;margin-bottom:1.25rem;}
.page-title{font-size:17px;font-weight:500;}
.page-sub{font-size:12px;color:var(--color-text-secondary);margin-top:2px;}
.tb-btn{background:transparent;border:0.5px solid var(--color-border-tertiary);border-radius:var(--border-radius-md);padding:6px 12px;font-size:12px;color:var(--color-text-secondary);cursor:pointer;display:inline-flex;align-items:center;gap:5px;text-decoration:none;}
.card{background:var(--color-background-primary);border:0.5px solid var(--color-border-tertiary);border-radius:var(--border-radius-lg);}
.mb-4{margin-bottom:1rem;}
.alert-item{background:#E6F1FB;color:#0C447C;border-radius:8px;padding:10px 14px;display:flex;align-items:center;gap:8px;font-size:13px;}
.notif-row:last-child{border-bottom:none;}
.notif-row:hover{background:var(--color-background-secondary);}
</style>
@endsection