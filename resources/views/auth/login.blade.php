<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Login — {{ tenant('clinic_name') }}</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@latest/tabler-icons.min.css" />
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap">
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            font-family: 'Inter', system-ui, sans-serif;
            background: #F3F4F6;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 1rem;
        }

        .login-wrapper {
            width: 100%;
            max-width: 420px;
        }

        .login-brand {
            text-align: center;
            margin-bottom: 2rem;
        }

        .login-brand .logo {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            font-size: 20px;
            font-weight: 700;
            color: #185FA5;
            text-decoration: none;
            margin-bottom: 8px;
        }

        .login-brand .logo i { font-size: 24px; }

        .login-brand .clinic-name {
            display: block;
            font-size: 14px;
            color: #6B7280;
            font-weight: 400;
        }

        .login-card {
            background: white;
            border-radius: 20px;
            padding: 2rem;
            box-shadow: 0 10px 25px -5px rgba(0,0,0,0.08);
            border: 1px solid #E5E7EB;
        }

        .login-card h1 {
            font-size: 20px;
            font-weight: 700;
            color: #111827;
            margin-bottom: 4px;
        }

        .login-card p {
            font-size: 13px;
            color: #6B7280;
            margin-bottom: 1.75rem;
        }

        .form-group {
            margin-bottom: 1.25rem;
        }

        label {
            display: block;
            font-size: 13px;
            font-weight: 500;
            color: #374151;
            margin-bottom: 6px;
        }

        .input-wrap {
            position: relative;
        }

        .input-wrap i {
            position: absolute;
            left: 12px;
            top: 50%;
            transform: translateY(-50%);
            font-size: 16px;
            color: #9CA3AF;
            pointer-events: none;
        }

        input[type="email"],
        input[type="password"] {
            width: 100%;
            padding: 10px 12px 10px 38px;
            border: 1px solid #E5E7EB;
            border-radius: 10px;
            font-size: 14px;
            font-family: inherit;
            color: #111827;
            background: #F9FAFB;
            outline: none;
            transition: border-color 0.15s, box-shadow 0.15s;
        }

        input[type="email"]:focus,
        input[type="password"]:focus {
            border-color: #185FA5;
            background: white;
            box-shadow: 0 0 0 3px rgba(24,95,165,0.1);
        }

        .input-error {
            font-size: 12px;
            color: #DC2626;
            margin-top: 4px;
            display: flex;
            align-items: center;
            gap: 4px;
        }

        .form-footer {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 1.5rem;
        }

        .remember-label {
            display: flex;
            align-items: center;
            gap: 6px;
            font-size: 13px;
            color: #6B7280;
            cursor: pointer;
            margin-bottom: 0;
            font-weight: 400;
        }

        .remember-label input {
            width: 15px;
            height: 15px;
            accent-color: #185FA5;
            cursor: pointer;
        }

        .forgot-link {
            font-size: 13px;
            color: #185FA5;
            text-decoration: none;
            font-weight: 500;
        }

        .forgot-link:hover { text-decoration: underline; }

        .btn-login {
            width: 100%;
            padding: 11px;
            background: #185FA5;
            color: white;
            border: none;
            border-radius: 10px;
            font-size: 14px;
            font-weight: 600;
            font-family: inherit;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            transition: background 0.15s, transform 0.1s;
        }

        .btn-login:hover { background: #0C447C; }
        .btn-login:active { transform: scale(0.99); }

        .alert-error {
            background: #FEE2E2;
            color: #A32D2D;
            border-radius: 10px;
            padding: 10px 14px;
            font-size: 13px;
            display: flex;
            align-items: center;
            gap: 8px;
            margin-bottom: 1.25rem;
        }

        .alert-status {
            background: #EAF3DE;
            color: #27500A;
            border-radius: 10px;
            padding: 10px 14px;
            font-size: 13px;
            display: flex;
            align-items: center;
            gap: 8px;
            margin-bottom: 1.25rem;
        }

        .login-footer {
            text-align: center;
            margin-top: 1.5rem;
            font-size: 12px;
            color: #9CA3AF;
        }
    </style>
</head>
<body>

<div class="login-wrapper">

    {{-- Brand --}}
    <div class="login-brand">
        <a href="#" class="logo">
            <i class="ti ti-activity-heartbeat"></i> MediBook
        </a>
        <span class="clinic-name">{{ tenant('clinic_name') }}</span>
    </div>

    {{-- Card --}}
    <div class="login-card">
        <h1>Welcome back</h1>
        <p>Sign in to your clinic account</p>

        {{-- Session status --}}
        @if(session('status'))
            <div class="alert-status">
                <i class="ti ti-circle-check"></i> {{ session('status') }}
            </div>
        @endif

        {{-- Auth errors --}}
        @if($errors->any())
            <div class="alert-error">
                <i class="ti ti-alert-circle"></i> {{ $errors->first() }}
            </div>
        @endif

        <form method="POST" action="{{ route('login') }}">
            @csrf

            {{-- Email --}}
            <div class="form-group">
                <label for="email">Email address</label>
                <div class="input-wrap">
                    <i class="ti ti-mail"></i>
                    <input type="email" id="email" name="email"
                           value="{{ old('email') }}"
                           required autofocus autocomplete="username"
                           placeholder="you@clinic.com" />
                </div>
            </div>

            {{-- Password --}}
            <div class="form-group">
                <label for="password">Password</label>
                <div class="input-wrap">
                    <i class="ti ti-lock"></i>
                    <input type="password" id="password" name="password"
                           required autocomplete="current-password"
                           placeholder="••••••••" />
                </div>
            </div>

            {{-- Remember + Forgot --}}
            <div class="form-footer">
                <label class="remember-label">
                    <input type="checkbox" name="remember" id="remember_me">
                    Remember me
                </label>
                @if(Route::has('password.request'))
                    <a href="{{ route('password.request') }}" class="forgot-link">
                        Forgot password?
                    </a>
                @endif
            </div>

            {{-- Submit --}}
            <button type="submit" class="btn-login">
                <i class="ti ti-login"></i> Sign in
            </button>
        </form>
    </div>

    <div class="login-footer">
        Powered by MediBook &copy; {{ date('Y') }}
    </div>

</div>

</body>
</html>