{{-- resources/views/tenant/partials/notification-bell.blade.php --}}
{{-- Include in your navbar: @include('tenant.partials.notification-bell') --}}

<div style="position:relative;" x-data="notificationBell()" x-init="load()">

    {{-- Bell button --}}
    <button @click="toggle()" @click.outside="open = false"
        style="background:none;border:none;cursor:pointer;position:relative;padding:4px 6px;color:var(--color-text-secondary);display:flex;align-items:center;">
        <i class="ti ti-bell" style="font-size:18px;"></i>
        <span x-show="count > 0" x-text="count > 9 ? '9+' : count"
            style="position:absolute;top:0;right:0;background:#A32D2D;color:#fff;border-radius:99px;font-size:9px;font-weight:700;min-width:16px;height:16px;display:flex;align-items:center;justify-content:center;padding:0 3px;line-height:1;">
        </span>
    </button>

    {{-- Dropdown --}}
    <div x-show="open" x-cloak
        style="position:absolute;right:0;top:calc(100% + 8px);width:320px;background:var(--color-background-primary);border:0.5px solid var(--color-border-tertiary);border-radius:var(--border-radius-lg);box-shadow:0 8px 24px rgba(0,0,0,0.12);z-index:999;">

        {{-- Header --}}
        <div style="padding:10px 14px;border-bottom:0.5px solid var(--color-border-tertiary);display:flex;justify-content:space-between;align-items:center;">
            <span style="font-size:13px;font-weight:600;">Notifications</span>
            <div style="display:flex;gap:10px;align-items:center;">
                <span x-show="count > 0" x-text="count + ' unread'"
                    style="font-size:11px;color:#185FA5;"></span>
                <form method="POST" action="{{ route('notifications.markAllRead') }}">
                    @csrf
                    <button type="submit"
                        style="background:none;border:none;font-size:11px;color:var(--color-text-secondary);cursor:pointer;padding:0;">
                        Mark all read
                    </button>
                </form>
            </div>
        </div>

        {{-- Notification list --}}
        <div style="max-height:320px;overflow-y:auto;">
            <template x-if="loading">
                <div style="padding:20px;text-align:center;font-size:12px;color:var(--color-text-secondary);">
                    Loading…
                </div>
            </template>

            <template x-if="!loading && notifications.length === 0">
                <div style="padding:24px;text-align:center;">
                    <i class="ti ti-bell-off" style="font-size:24px;color:var(--color-text-secondary);"></i>
                    <p style="font-size:12px;color:var(--color-text-secondary);margin:6px 0 0;">You're all caught up</p>
                </div>
            </template>

            <template x-for="n in notifications" :key="n.id">
                <a :href="n.action_url"
                    style="display:flex;gap:10px;padding:10px 14px;border-bottom:0.5px solid var(--color-border-tertiary);text-decoration:none;color:inherit;transition:background 0.1s;"
                    onmouseover="this.style.background='var(--color-background-secondary)'"
                    onmouseout="this.style.background=''">
                    <div style="width:30px;height:30px;border-radius:50%;background:#E6F1FB;display:flex;align-items:center;justify-content:center;flex-shrink:0;margin-top:2px;">
                        <i class="ti" :class="'ti-' + n.icon" style="font-size:14px;color:#185FA5;"></i>
                    </div>
                    <div style="flex:1;min-width:0;">
                        <div style="font-size:12px;font-weight:600;color:var(--color-text-primary);" x-text="n.title"></div>
                        <div style="font-size:11px;color:var(--color-text-secondary);line-height:1.4;margin-top:1px;" x-text="n.body"></div>
                        <div style="font-size:10px;color:var(--color-text-secondary);margin-top:3px;" x-text="n.time"></div>
                    </div>
                </a>
            </template>
        </div>

        {{-- Footer --}}
        <div style="padding:8px 14px;border-top:0.5px solid var(--color-border-tertiary);text-align:center;">
            <a href="{{ route('notifications.index') }}"
                style="font-size:12px;color:#185FA5;text-decoration:none;">
                View all notifications
            </a>
        </div>
    </div>
</div>

<script>
function notificationBell() {
    return {
        open: false,
        loading: false,
        count: 0,
        notifications: [],

        toggle() {
            this.open = !this.open;
            if (this.open) this.load();
        },

        async load() {
            this.loading = true;
            try {
                const res  = await fetch('{{ route('notifications.unread') }}');
                const data = await res.json();
                this.count         = data.count;
                this.notifications = data.notifications;
            } catch (e) {
                console.error('Notification load failed', e);
            } finally {
                this.loading = false;
            }
        },

        init() {
            // Poll every 60 seconds for new notifications
            setInterval(() => this.load(), 60000);
        }
    }
}
</script>