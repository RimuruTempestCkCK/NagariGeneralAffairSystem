<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" data-theme="light">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>NGAS - Bank Nagari Login</title>

    <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">

    <!-- Adminator Scripts -->
    <script defer="defer" src="{{ asset('adminator_templete/runtime.js?v=' . time()) }}"></script>
    <script defer="defer" src="{{ asset('adminator_templete/vendors.js?v=' . time()) }}"></script>
    <script defer="defer" src="{{ asset('adminator_templete/2026.js?v=' . time()) }}"></script>
    <link href="{{ asset('adminator_templete/style.css?v=' . time()) }}" rel="stylesheet">
<link href="{{ asset('css/ngas-ui.css?v=' . time()) }}" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

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
        /* Override auth layout to center the login card */
        .auth-shell {
            position: fixed !important;
            top: 0 !important;
            left: 0 !important;
            right: 0 !important;
            bottom: 0 !important;
            display: flex !important;
            align-items: center !important; /* Centered vertically */
            justify-content: center !important;
            width: 100vw !important;
            height: 100vh !important;
            background: url("{{ asset('images/login-bg.jpeg') }}") no-repeat center center fixed !important;
            background-size: cover !important;
            z-index: 9999;
        }
        .auth-main {
            width: 100%;
            max-width: 450px;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            gap: 15px;
            padding: 15px !important; /* Mobile responsiveness */
            box-sizing: border-box !important;
            margin: auto !important; /* Ensures it stays dead center */
        }
        .auth-card {
            width: 100%;
            background: #ffffff !important;
            border-radius: 12px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.15);
            padding: 30px 20px !important; /* Adjusted for mobile */
            border: none;
            margin: 0 !important;
            text-align: center; /* Center contents */
            box-sizing: border-box !important;
        }
        @media (min-width: 576px) {
            .auth-card {
                padding: 40px !important; /* Original padding for larger screens */
            }
        }
        .login-logo {
            display: flex;
            justify-content: center;
            align-items: center;
            width: 100%;
            margin-bottom: 24px !important;
        }
        .login-logo img {
            height: 48px;
            width: auto;
            border: none;
            outline: none;
            margin: 0 auto;
        }
        .toggle-password {
            position: absolute;
            right: 12px;
            top: 50%;
            transform: translateY(-50%);
            cursor: pointer;
            color: #7f8fa4;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .toggle-password:hover {
            color: var(--primary);
        }
    </style>
</head>
<body>
    <div class="auth-shell">
        <main class="auth-main">
            <div class="auth-card">
                <div class="login-logo" style="margin-bottom: 24px;">
                    <img src="{{ asset('brand/bank-nagari-logo.svg') }}" alt="Bank Nagari">
                </div>

                <h2>Selamat Datang</h2>
                <p class="sub">Silakan masuk.</p>

                <form class="auth-form" method="POST" action="{{ route('login.post') }}" style="text-align: left;">
                    @csrf
                    <div class="field">
                        <label class="field-label" for="email">Alamat Email</label>
                        <div class="input-icon">
                            <span class="ico">
                                <svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="2" fill="none"><rect x="3" y="5" width="18" height="14" rx="2"/><path d="m3 7 9 6 9-6"/></svg>
                            </span>
                            <input id="email" name="email" class="input" type="email" placeholder="notaris@example.com" value="{{ old('email') }}" required autofocus>
                        </div>
                        @error('email')
                            <span style="color: #e74c3c; font-size: 12px; margin-top: 5px; display: block;">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="field">
                        <div class="field-row">
                            <label class="field-label" for="password">Kata Sandi</label>
                        </div>
                        <div class="input-icon" style="position: relative;">
                            <span class="ico">
                                <svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="2" fill="none"><rect x="3" y="11" width="18" height="11" rx="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
                            </span>
                            <input id="password" name="password" class="input" type="password" placeholder="Password" required style="padding-right: 40px;">
                            <span class="toggle-password" id="togglePassword">
                                <svg viewBox="0 0 24 24" width="18" height="18" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round" class="eye-icon"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><circle cx="12" cy="12" r="3"></circle></svg>
                            </span>
                        </div>
                        @error('password')
                            <span style="color: #e74c3c; font-size: 12px; margin-top: 5px; display: block;">{{ $message }}</span>
                        @enderror
                    </div>

                    <button class="btn btn--primary btn--block auth-submit" type="submit" style="display: flex; justify-content: center; align-items: center; gap: 8px;">
                        Sign In
                        <svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="2" fill="none"><path d="M5 12h14M13 5l7 7-7 7"/></svg>
                    </button>
                </form>
            </div>

            <!-- <div class="auth-main-bottom" style="margin-top: 20px; color: var(--t-muted); text-align: center; font-size: 12px;">
                Hanya untuk penggunaan internal Bank Nagari.
            </div> -->
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

    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const togglePassword = document.querySelector("#togglePassword");
            const password = document.querySelector("#password");

            if (togglePassword && password) {
                togglePassword.addEventListener("click", function () {
                    const type = password.getAttribute("type") === "password" ? "text" : "password";
                    password.setAttribute("type", type);

                    if (type === "text") {
                        this.innerHTML = '<svg viewBox="0 0 24 24" width="18" height="18" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round"><path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"></path><line x1="1" y1="1" x2="23" y2="23"></line></svg>';
                    } else {
                        this.innerHTML = '<svg viewBox="0 0 24 24" width="18" height="18" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round" class="eye-icon"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><circle cx="12" cy="12" r="3"></circle></svg>';
                    }
                });
            }
        });
    </script>
</body>
</html>
