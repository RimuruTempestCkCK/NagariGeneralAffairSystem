<!doctype html>
<html lang="en" data-theme="light">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Adminator - General Affair System')</title>
    <script>!function(){try{var t=localStorage.getItem("dash26-theme"),e=window.matchMedia("(prefers-color-scheme: dark)").matches;document.documentElement.setAttribute("data-theme",t||(e?"dark":"light"))}catch(t){document.documentElement.setAttribute("data-theme","light")}}()</script>
    <script>window.GAS_USER_ROLE = "{{ Auth::check() ? Auth::user()->role : 'staff' }}";</script>
    <script defer="defer" src="{{ asset('adminator_templete/runtime.js?v=' . time()) }}"></script>
    <script defer="defer" src="{{ asset('adminator_templete/vendor-fullcalendar.js?v=' . time()) }}"></script>
    <script defer="defer" src="{{ asset('adminator_templete/vendor-chartjs.js?v=' . time()) }}"></script>
    <script defer="defer" src="{{ asset('adminator_templete/vendors.js?v=' . time()) }}"></script>
    <script defer="defer" src="{{ asset('adminator_templete/2026.js?v=' . time()) }}"></script>
    <link href="{{ asset('adminator_templete/style.css?v=' . time()) }}" rel="stylesheet">
        <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
    <!-- Inline styles to adjust SweetAlert2 z-index if it conflicts with Adminator shell -->
    <style>
        .swal2-container {
            z-index: 10000 !important;
        }
        /* Make sure modal content looks decent in adminator */
        .modal {
            display: none;
            position: fixed;
            top: 0; left: 0; right: 0; bottom: 0;
            background: rgba(0,0,0,0.5);
            z-index: 9999;
            align-items: center;
            justify-content: center;
        }
        .modal.flex {
            display: flex;
        }
        .modal-content {
            background: #ffffff;
            padding: 24px;
            border-radius: 12px;
            width: 100%;
            max-width: 600px;
            max-height: 90vh;
            overflow-y: auto;
            color: #333333;
        }
        .form-group input, .form-group select, .form-group textarea {
            width: 100%;
            padding: 8px 12px;
            border-radius: 6px;
            border: 1px solid #ddd;
            background: #f9f9f9;
            color: #333333;
            margin-bottom: 12px;
        }
        .form-label {
            display: block;
            margin-bottom: 6px;
            font-size: 13px;
            font-weight: 500;
        }
        .modal-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
            border-bottom: 1px solid var(--border-base);
            padding-bottom: 10px;
        }
        .modal-header h3 {
            margin: 0;
        }
    </style>
    @stack('styles')
</head>
<body data-active="@yield('active_menu', 'dashboard')" data-crumbs="@yield('breadcrumbs', 'Dashboard')">
    <div class="shell">
        <!-- Sidebar natively rendered via Blade -->
        @include('layout.sidebar')
        
        <div class="main">
            <!-- Topbar/Header natively rendered via Blade -->
            @include('layout.header')
            
            <main class="content">
                @yield('content')
            </main>
            
            <!-- Footer natively rendered via Blade -->
            @include('layout.footer')
        </div>
    </div>
    
    @stack('scripts')
    
    <style>
        @media print {
            .d-sidebar, .d-topbar, .card-action, .btn, .hamburger, form, .data-toolbar {
                display: none !important;
            }
            .main { margin-left: 0 !important; }
            .content { padding: 0 !important; }
            .card { box-shadow: none !important; border: none !important; margin: 0 !important; }
            body { background: white !important; }
        }
    </style>
</body>
</html>
