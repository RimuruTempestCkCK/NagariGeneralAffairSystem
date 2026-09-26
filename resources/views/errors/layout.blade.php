<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" data-theme="light">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>@yield('title') - GAS Nagari</title>
    
    <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">
    
    <!-- Adminator Scripts -->
    <script defer="defer" src="{{ asset('adminator/runtime.js') }}"></script>
    <script defer="defer" src="{{ asset('adminator/vendors.js') }}"></script>
    <script defer="defer" src="{{ asset('adminator/2026.js') }}"></script>
    <link href="{{ asset('adminator/style.css') }}" rel="stylesheet">
    
    <style>
        body {
            margin: 0 !important;
            padding: 0 !important;
            height: 100vh !important;
            display: flex !important;
            align-items: center !important;
            justify-content: center !important;
            background-color: #f7f9fa !important;
        }
        .error-shell {
            width: 100%;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .error-card {
            background: var(--bg-base);
            border-radius: 12px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.08);
            padding: 40px;
            text-align: center;
            max-width: 500px;
            width: 100%;
        }
        .error-eyebrow {
            font-size: 12px;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: var(--t-muted);
            margin-bottom: 15px;
            display: block;
            font-weight: 600;
        }
        .error-code {
            font-size: 80px;
            font-weight: 800;
            color: var(--primary);
            line-height: 1;
            margin-bottom: 20px;
        }
        .error-title {
            font-size: 24px;
            font-weight: 700;
            color: var(--t-base);
            margin-bottom: 10px;
        }
        .error-sub {
            color: var(--t-muted);
            margin-bottom: 30px;
            line-height: 1.5;
        }
        .error-actions {
            display: flex;
            gap: 10px;
            justify-content: center;
            margin-bottom: 30px;
        }
        .error-meta {
            border-top: 1px solid var(--border-base);
            padding-top: 20px;
            display: flex;
            justify-content: center;
            gap: 20px;
            font-size: 12px;
            color: var(--t-muted);
        }
        .error-meta strong {
            color: var(--t-base);
        }
        .btn svg {
            width: 16px;
            height: 16px;
            margin-right: 6px;
            vertical-align: middle;
            fill: none;
            stroke: currentColor;
            stroke-width: 2;
        }
    </style>
</head>
<body>
    <div class="error-shell">
        <div class="error-card">
            <span class="error-eyebrow">Error &mdash; @yield('title')</span>
            <div class="error-code">@yield('code')</div>
            <h1 class="error-title">@yield('message')</h1>
            <p class="error-sub">@yield('description', 'Maaf, terjadi kesalahan atau halaman yang Anda cari tidak dapat diakses.')</p>
            
            <div class="error-actions">
                <a href="{{ url('/') }}" class="btn btn--primary">
                    <svg viewBox="0 0 24 24"><path d="M3 12 12 3l9 9"/><path d="M5 10v10h14V10"/></svg>
                    Kembali ke Beranda
                </a>
                <a href="javascript:history.back()" class="btn btn--ghost">
                    <svg viewBox="0 0 24 24"><path d="M19 12H5M12 19l-7-7 7-7"/></svg>
                    Kembali
                </a>
            </div>
            
            <div class="error-meta">
                <span><strong>STATUS</strong> @yield('code')</span> 
                <span><strong>APP</strong> GAS Nagari</span>
            </div>
        </div>
    </div>
</body>
</html>
