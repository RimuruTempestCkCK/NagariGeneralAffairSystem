<header class="d-topbar">
    <div class="crumbs">
        <button class="hamburger" data-drawer-open aria-label="Open navigation">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="3" y1="6" x2="21" y2="6"/><line x1="3" y1="12" x2="21" y2="12"/><line x1="3" y1="18" x2="21" y2="18"/></svg>
        </button>
        
        @php
            $crumbs = trim($__env->yieldContent('breadcrumbs'));
            if ($crumbs) {
                $parts = array_filter(array_map('trim', explode('|', $crumbs)));
                foreach ($parts as $index => $part) {
                    if ($index > 0) {
                        echo '<svg class="sep" width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="m9 18 6-6-6-6"/></svg>';
                    }
                    if ($index === count($parts) - 1) {
                        echo '<span class="current">' . e($part) . '</span>';
                    } else {
                        echo '<span>' . e($part) . '</span>';
                    }
                }
            }
        @endphp
    </div>
    
    <div class="topbar-actions">
        <!-- Notification Bell -->
        <a href="{{ route(Auth::user()->role . '.notifications.index') }}" class="icon-btn" aria-label="Notifications" style="position: relative;">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"></path>
                <path d="M13.73 21a2 2 0 0 1-3.46 0"></path>
            </svg>
            @php $unreadCount = Auth::user()->unreadNotifications->count(); @endphp
            @if($unreadCount > 0)
                <span class="count danger" style="position: absolute; top: -5px; right: -5px; background: red; color: white; border-radius: 50%; padding: 2px 6px; font-size: 10px; font-weight: bold;">{{ $unreadCount }}</span>
            @endif
        </a>

        <!-- Theme Toggle -->
        <button class="icon-btn" id="themeToggle" aria-label="Toggle theme"></button>

        <!-- User Profile Dropdown -->
        <div class="dd-wrap">
            <div class="avatar" data-dropdown tabindex="0" role="button" aria-label="Account menu">
                {{ strtoupper(substr(Auth::user()->name, 0, 2)) }}
            </div>
            <div class="dd-menu dd-profile" role="menu">
                <div class="dd-profile-head">
                    <div class="dd-profile-name">{{ Auth::user()->name }}</div>
                </div>
                <form action="{{ route('logout') }}" method="POST" style="margin: 0;">
                    @csrf
                    <button type="submit" class="dd-menu-item danger" style="width: 100%; border: none; background: transparent; cursor: pointer; text-align: left; padding: 10px 15px; display: flex; align-items: center; gap: 10px;">
                        <svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4M16 17l5-5-5-5M21 12H9"/>
                        </svg>
                        Logout
                    </button>
                </form>
            </div>
        </div>
    </div>
</header>
