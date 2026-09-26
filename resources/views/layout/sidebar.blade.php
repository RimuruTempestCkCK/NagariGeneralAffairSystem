<aside class="d-sidebar">
    <div class="brand">
        <div class="brand-logo">
            <img src="{{ asset('images/bank-nagari-logo.svg') }}" style="height:32px; width:auto;" alt="GAS">
        </div>
        <div class="brand-text">
            <div class="brand-name">GAS</div>
            <div class="brand-tag">General Affair System</div>
        </div>
    </div>

    <nav class="nav-section">
        <div class="nav-label">Menu Utama</div>
        <a class="nav-link {{ request()->is('dashboard*') || request()->is('admin/dashboard*') || request()->is('staff/dashboard*') ? 'is-active' : '' }}" href="{{ route(Auth::user()->role . '.dashboard') }}">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d='M3 12 12 3l9 9'/><path d='M5 10v10h14V10'/></svg>
            <span>Dashboard</span>
        </a>
    </nav>

    <nav class="nav-section">
        <div class="nav-label">Modul ATK</div>
        
        @if(Auth::user()->role === 'admin')
        <a class="nav-link {{ request()->routeIs('atk.*') || request()->routeIs('admin.atk.*') ? 'is-active' : '' }}" href="{{ route('atk.index') }}">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M3 5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5z"/><path d="M3 9h18"/><path d="M9 21V9"/></svg>
            <span>Master ATK</span>
        </a>
        @endif

        <a class="nav-link {{ request()->is('permintaan-atk*') ? 'is-active' : '' }}" href="{{ route('permintaan-atk.index') }}">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><path d="M14 2v6h6"/><path d="M16 13H8"/><path d="M16 17H8"/><path d="M10 9H8"/></svg>
            <span>Permintaan / PO ATK</span>
        </a>

        @if(Auth::user()->role === 'admin')
        <a class="nav-link {{ request()->is('stok-atk*') ? 'is-active' : '' }}" href="{{ route('stok-atk.index') }}">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"/><polyline points="3.27 6.96 12 12.01 20.73 6.96"/><line x1="12" y1="22.08" x2="12" y2="12"/></svg>
            <span>Stok ATK</span>
        </a>
        <a class="nav-link {{ request()->is('pemakaian-atk*') ? 'is-active' : '' }}" href="{{ route('pemakaian-atk.index') }}">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M16 4h2a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h2"/><rect x="8" y="2" width="8" height="4" rx="1" ry="1"/><path d="M9 14l2 2 4-4"/></svg>
            <span>Pemakaian ATK</span>
        </a>
        @endif
        
        <a class="nav-link {{ request()->is('*/atk/scan*') ? 'is-active' : '' }}" href="{{ route(Auth::user()->role . '.atk.scan') }}">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d='M3 4a1 1 0 011-1h3v2H5v3H3V4zm2 14v-3H3v4a1 1 0 001 1h3v-2H5zm14-14h-3V2h4v5h-2V4zm-3 14h3v-3h2v4a1 1 0 01-1 1h-4v-2z'/></svg>
            <span>QR Scanner</span>
        </a>
    </nav>

    <nav class="nav-section">
        <div class="nav-label">Modul Kendaraan</div>
        @if(Auth::user()->role === 'admin')
        <a class="nav-link {{ request()->is('kendaraan*') ? 'is-active' : '' }}" href="{{ route('kendaraan.index') }}">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><rect x="1" y="3" width="15" height="13"/><polygon points="16 8 20 8 23 11 23 16 16 16 16 8"/><circle cx="5.5" cy="18.5" r="2.5"/><circle cx="18.5" cy="18.5" r="2.5"/></svg>
            <span>Master Kendaraan</span>
        </a>
        @endif
        <a class="nav-link {{ request()->is('perjalanan-kendaraan*') ? 'is-active' : '' }}" href="{{ route('perjalanan-kendaraan.index') }}">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M12 22s-8-4.5-8-11.8A8 8 0 0 1 12 2a8 8 0 0 1 8 8.2c0 7.3-8 11.8-8 11.8z"/><circle cx="12" cy="10" r="3"/></svg>
            <span>Jarak Tempuh</span>
        </a>
        <a class="nav-link {{ request()->is('bbm-kendaraan*') ? 'is-active' : '' }}" href="{{ route('bbm-kendaraan.index') }}">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M3 22h18"/><path d="M19 18v-8l-4-4H5a2 2 0 0 0-2 2v10"/><path d="M14 6h5l4 4v8h-5"/><path d="M9 22v-4a2 2 0 0 1 2-2h2a2 2 0 0 1 2 2v4"/></svg>
            <span>BBM Kendaraan</span>
        </a>
        <a class="nav-link {{ request()->is('pemeliharaan-kendaraan*') ? 'is-active' : '' }}" href="{{ route('pemeliharaan-kendaraan.index') }}">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M14.7 6.3a1 1 0 0 0 0 1.4l1.6 1.6a1 1 0 0 0 1.4 0l3.77-3.77a6 6 0 0 1-7.94 9.36l-7.19 7.19a2.12 2.12 0 0 1-3-3l7.19-7.19a6 6 0 0 1 9.36-7.94z"/></svg>
            <span>Pemeliharaan</span>
        </a>
    </nav>

    <nav class="nav-section">
        <div class="nav-label">Modul Keamanan</div>
        <a class="nav-link {{ request()->is('keamanan*') ? 'is-active' : '' }}" href="{{ route('keamanan.index') }}">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
            <span>Laporan Keamanan</span>
        </a>
        @if(Auth::user()->role === 'admin')
        <a class="nav-link {{ request()->is('evaluasi-keamanan*') ? 'is-active' : '' }}" href="{{ route('evaluasi-keamanan.index') }}">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/><path d="M9 12l2 2 4-4"/></svg>
            <span>Evaluasi Keamanan</span>
        </a>
        @endif
    </nav>

    @if(Auth::user()->role === 'admin')
    <nav class="nav-section">
        <div class="nav-label">Modul Manajemen</div>
        <a class="nav-link {{ request()->is('aset*') ? 'is-active' : '' }}" href="{{ route('aset.index') }}">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><rect x="2" y="7" width="20" height="14" rx="2" ry="2"/><path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"/></svg>
            <span>Manajemen Aset</span>
        </a>
    </nav>

    <nav class="nav-section">
        <div class="nav-label">Modul Reporting</div>
        <a class="nav-link {{ request()->is('laporan/atk*') ? 'is-active' : '' }}" href="{{ route('laporan.atk') }}">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><rect x='3' y='4' width='18' height='16' rx='2'/></svg>
            <span>Laporan ATK</span>
        </a>
        <a class="nav-link {{ request()->is('laporan/kendaraan*') ? 'is-active' : '' }}" href="{{ route('laporan.kendaraan') }}">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><rect x='3' y='4' width='18' height='16' rx='2'/></svg>
            <span>Laporan Kendaraan</span>
        </a>
        <a class="nav-link {{ request()->is('laporan/aset*') ? 'is-active' : '' }}" href="{{ route('laporan.aset') }}">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><rect x='3' y='4' width='18' height='16' rx='2'/></svg>
            <span>Laporan Aset</span>
        </a>
    </nav>
    @endif

    <div class="sidebar-footer">
        <div class="workspace">
            <div class="workspace-avatar">{{ strtoupper(substr(Auth::user()->name, 0, 2)) }}</div>
            <div class="workspace-text">
                <div class="workspace-name">{{ Auth::user()->name }}</div>
                <div class="workspace-role">{{ ucfirst(Auth::user()->role) }}</div>
            </div>
            <svg class="workspace-chev" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                <path d="m7 9 5-5 5 5"/><path d="m7 15 5 5 5-5"/>
            </svg>
        </div>
    </div>
</aside>
