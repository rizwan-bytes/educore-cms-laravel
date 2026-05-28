<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ app()->getLocale() === 'ur' ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ __('auth.login') }} — {{ \App\Services\SettingService::get('college_name', 'EduCore') }}</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('assets/vendor/bootstrap/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/vendor/fontawesome/all.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/style.css') }}">
</head>
<body class="login-page {{ app()->getLocale() === 'ur' ? 'rtl' : '' }}">

<div class="login-wrapper">
    <div class="login-card">
        {{-- Logo / School Name --}}
        <div class="login-header">
            <div class="login-logo">
                <i class="fas fa-graduation-cap"></i>
            </div>
            <h1 class="login-title">{{ \App\Services\SettingService::get('college_name', 'EduCore CMS') }}</h1>
            <p class="login-subtitle">{{ \App\Services\SettingService::get('college_tagline', 'School Management System') }}</p>
        </div>

        {{-- Flash error --}}
        @if(session('error'))
            <div class="login-alert login-alert-danger">
                <i class="fas fa-exclamation-circle me-2"></i>{{ session('error') }}
            </div>
        @endif

        {{-- Login Form --}}
        <form method="POST" action="{{ route('login') }}" class="login-form">
            @csrf

            <div class="form-group-login">
                <label class="form-label-login">{{ __('auth.email') }}</label>
                <div class="input-login-wrap">
                    <i class="fas fa-envelope input-login-icon"></i>
                    <input type="email" name="email" value="{{ old('email') }}"
                           class="form-control-login @error('email') is-invalid @enderror"
                           placeholder="{{ __('auth.email_placeholder') }}"
                           required autofocus autocomplete="email">
                </div>
                @error('email')
                    <div class="invalid-feedback d-block">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group-login">
                <label class="form-label-login">{{ __('auth.password') }}</label>
                <div class="input-login-wrap">
                    <i class="fas fa-lock input-login-icon"></i>
                    <input type="password" name="password" id="passwordField"
                           class="form-control-login @error('password') is-invalid @enderror"
                           placeholder="{{ __('auth.password_placeholder') }}"
                           required autocomplete="current-password">
                    <button type="button" class="password-toggle" onclick="togglePassword()">
                        <i class="fas fa-eye" id="pwdToggleIcon"></i>
                    </button>
                </div>
                @error('password')
                    <div class="invalid-feedback d-block">{{ $message }}</div>
                @enderror
            </div>

            <div class="login-options">
                <label class="remember-me">
                    <input type="checkbox" name="remember" {{ old('remember') ? 'checked' : '' }}>
                    <span>{{ __('auth.remember_me') }}</span>
                </label>
            </div>

            <button type="submit" class="btn-login">
                <i class="fas fa-sign-in-alt me-2"></i>{{ __('auth.login') }}
            </button>
        </form>

        <div class="login-footer">
            <p>{{ \App\Services\SettingService::get('college_name', 'EduCore') }} &copy; {{ date('Y') }}</p>
        </div>
    </div>
</div>

<script src="{{ asset('assets/vendor/bootstrap/bootstrap.bundle.min.js') }}"></script>
<script>
function togglePassword() {
    const field = document.getElementById('passwordField');
    const icon  = document.getElementById('pwdToggleIcon');
    if (field.type === 'password') {
        field.type = 'text';
        icon.classList.replace('fa-eye', 'fa-eye-slash');
    } else {
        field.type = 'password';
        icon.classList.replace('fa-eye-slash', 'fa-eye');
    }
}
</script>
</body>
</html>
