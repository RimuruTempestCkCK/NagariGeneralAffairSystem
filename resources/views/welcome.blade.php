<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" data-theme="light">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>GAS - Bank Nagari Login</title>
    
    <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">
    
    <!-- Adminator Scripts -->
    <script defer="defer" src="{{ asset('adminator/runtime.js') }}"></script>
    <script defer="defer" src="{{ asset('adminator/vendors.js') }}"></script>
    <script defer="defer" src="{{ asset('adminator/2026.js') }}"></script>
    <link href="{{ asset('adminator/style.css') }}" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body>
    <div class="auth-shell">
        <aside class="auth-aside">
            <div class="auth-brand">
                <div class="logo">
                    <img src="{{ asset('images/bank-nagari-logo.svg') }}" style="height:40px; filter: brightness(0) invert(1);" alt="Bank Nagari">
                </div>
                <div class="name">GAS Nagari</div>
            </div>
            <div class="auth-aside-body">
                <span class="auth-aside-eyebrow">General Affair System</span>
                <h1>Sistem Tata Usaha & Logistik Terpadu</h1>
                <p>Mengelola Master ATK, Kendaraan, Aset, Keamanan, dan Reporting secara efisien dalam satu pintu.</p>
                
                <div class="auth-quote">
                    "Platform GAS meningkatkan efisiensi pendataan aset dan mempercepat birokrasi permohonan inventaris."
                    <div class="auth-quote-author">
                        <div class="av">UM</div>
                        <div>Unit Umum<br>Bank Nagari</div>
                    </div>
                </div>
            </div>
            <div class="auth-aside-footer">
                <span>&copy; {{ date('Y') }} Bank Nagari</span> 
                <span>Divisi Umum</span>
            </div>
        </aside>
        
        <main class="auth-main">
            <div class="auth-main-top">
                <!-- Optional link top right -->
            </div>
            <div class="auth-card">
                <h2>Selamat Datang</h2>
                <p class="sub">Silakan masuk menggunakan kredensial Anda.</p>
                
                <form class="auth-form" method="POST" action="{{ route('login.post') }}">
                    @csrf
                    <div class="field">
                        <label class="field-label" for="email">Alamat Email</label>
                        <div class="input-icon">
                            <span class="ico">
                                <svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="2" fill="none"><rect x="3" y="5" width="18" height="14" rx="2"/><path d="m3 7 9 6 9-6"/></svg>
                            </span>
                            <input id="email" name="email" class="input" type="email" placeholder="email@banknagari.co.id" value="{{ old('email') }}" required autofocus>
                        </div>
                        @error('email')
                            <span style="color: #e74c3c; font-size: 12px; margin-top: 5px; display: block;">{{ $message }}</span>
                        @enderror
                    </div>
                    
                    <div class="field">
                        <div class="field-row">
                            <label class="field-label" for="password">Kata Sandi</label> 
                        </div>
                        <div class="input-icon">
                            <span class="ico">
                                <svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="2" fill="none"><rect x="3" y="11" width="18" height="11" rx="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
                            </span>
                            <input id="password" name="password" class="input" type="password" placeholder="••••••••" required>
                        </div>
                        @error('password')
                            <span style="color: #e74c3c; font-size: 12px; margin-top: 5px; display: block;">{{ $message }}</span>
                        @enderror
                    </div>
                    
                    <button class="btn btn--primary auth-submit" type="submit" style="margin-top: 15px;">
                        Sign In 
                        <svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="2" fill="none"><path d="M5 12h14M13 5l7 7-7 7"/></svg>
                    </button>
                </form>
            </div>
            
            <div class="auth-main-bottom">
                Hanya untuk penggunaan internal Bank Nagari.
            </div>
        </main>
    </div>
    
    @if($errors->has('email') || $errors->has('password'))
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            Swal.fire({
                icon: 'error',
                title: 'Login Gagal',
                text: 'Kredensial yang diberikan tidak cocok dengan catatan kami.',
                confirmButtonColor: '#2c3e50'
            });
        });
    </script>
    @endif
</body>
</html>
