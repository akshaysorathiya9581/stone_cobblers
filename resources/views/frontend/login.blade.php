@extends('layouts.frontend')

@section('title','Login')

@push('css')
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, sans-serif;
            background: linear-gradient(135deg, #f0fdf4 0%, #dcfce7 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .login-container {
            background: white;
            border-radius: 16px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
            padding: 48px;
            width: 100%;
            max-width: 420px;
            position: relative;
            overflow: hidden;
        }

        .login-container::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: linear-gradient(90deg, rgb(22, 163, 74) 0%, rgb(34, 197, 94) 100%);
        }

        .logo-section {
            text-align: center;
            margin-bottom: 40px;
        }

        .logo-icon {
            width: 64px;
            height: 64px;
            background: linear-gradient(135deg, rgb(22, 163, 74) 0%, rgb(34, 197, 94) 100%);
            border-radius: 16px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 24px;
            font-weight: bold;
            margin: 0 auto 16px;
            box-shadow: 0 8px 24px rgba(22, 163, 74, 0.3);
        }

        .company-name {
            font-size: 28px;
            font-weight: 700;
            color: #1f2937;
            margin-bottom: 8px;
        }

        .company-tagline {
            color: #6b7280;
            font-size: 16px;
            font-weight: 500;
        }

        .login-form {
            display: flex;
            flex-direction: column;
            gap: 24px;
        }

        .form-group {
            display: flex;
            flex-direction: column;
            gap: 8px;
        }

        .form-label {
            font-weight: 600;
            color: #374151;
            font-size: 14px;
        }

        .form-input {
            padding: 12px 16px;
            border: 2px solid #e5e7eb;
            border-radius: 8px;
            font-size: 16px;
            transition: all 0.2s ease;
            background: #f9fafb;
        }

        .form-input:focus {
            outline: none;
            border-color: rgb(22, 163, 74);
            background: white;
            box-shadow: 0 0 0 3px rgba(22, 163, 74, 0.1);
        }

        .form-input.error {
            border-color: #ef4444;
            background: #fef2f2;
        }

        .password-container {
            position: relative;
        }

        .password-toggle {
            position: absolute;
            right: 12px;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            cursor: pointer;
            color: #6b7280;
            font-size: 18px;
            padding: 4px;
        }

        .password-toggle:hover {
            color: rgb(22, 163, 74);
        }

        .form-options {
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-size: 14px;
        }

        .remember-me {
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .remember-me input[type="checkbox"] {
            width: 16px;
            height: 16px;
            accent-color: rgb(22, 163, 74);
        }

        .forgot-password {
            color: rgb(22, 163, 74);
            text-decoration: none;
            font-weight: 500;
        }

        .forgot-password:hover {
            text-decoration: underline;
        }

        .login-button {
            background: linear-gradient(135deg, rgb(22, 163, 74) 0%, rgb(34, 197, 94) 100%);
            color: white;
            border: none;
            padding: 14px 24px;
            border-radius: 8px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.2s ease;
            box-shadow: 0 4px 12px rgba(22, 163, 74, 0.3);
        }

        .login-button:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 24px rgba(22, 163, 74, 0.4);
        }

        .login-button:active {
            transform: translateY(0);
        }

        .login-button:disabled {
            opacity: 0.6;
            cursor: not-allowed;
            transform: none;
        }

        .divider {
            display: flex;
            align-items: center;
            margin: 32px 0;
            color: #6b7280;
            font-size: 14px;
        }

        .divider::before,
        .divider::after {
            content: '';
            flex: 1;
            height: 1px;
            background: #e5e7eb;
        }

        .divider span {
            padding: 0 16px;
        }

        .social-login {
            display: flex;
            gap: 12px;
        }

        .social-button {
            flex: 1;
            padding: 12px;
            border: 2px solid #e5e7eb;
            border-radius: 8px;
            background: white;
            cursor: pointer;
            transition: all 0.2s ease;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            font-weight: 500;
            color: #374151;
        }

        .social-button:hover {
            border-color: rgb(22, 163, 74);
            background: #f0fdf4;
        }

        .error-message {
            color: #ef4444;
            font-size: 14px;
            margin-top: 8px;
            display: none;
        }

        .success-message {
            color: rgb(22, 163, 74);
            font-size: 14px;
            margin-top: 8px;
            display: none;
        }

        .footer-text {
            text-align: center;
            margin-top: 32px;
            color: #6b7280;
            font-size: 14px;
        }

        .footer-text a {
            color: rgb(22, 163, 74);
            text-decoration: none;
            font-weight: 500;
        }

        .footer-text a:hover {
            text-decoration: underline;
        }

        @media (max-width: 480px) {
            .login-container {
                margin: 20px;
                padding: 32px 24px;
            }

            .company-name {
                font-size: 24px;
            }

            .social-login {
                flex-direction: column;
            }
        }
    </style>
@endpush

@section('content')
<div class="login-container">
    <div class="logo-section">
        <div class="logo-icon">SC</div>
        <h1 class="company-name">Stone Cobblers</h1>
        <p class="company-tagline">Central</p>
    </div>

    <form class="login-form" id="loginForm">
        @csrf
        <div class="form-group">
            <label for="email" class="form-label">Email Address</label>
            <input 
                type="email" 
                id="email" 
                name="email"
                class="form-input" 
                placeholder="Enter your email"
                required
            >
            <div class="error-message" id="emailError"></div>
        </div>

        <div class="form-group">
            <label for="password" class="form-label">Password</label>
            <div class="password-container">
                <input 
                    type="password" 
                    id="password" 
                    name="password"
                    class="form-input" 
                    placeholder="Enter your password"
                    required
                >
                <button type="button" class="password-toggle" id="passwordToggle">
                    👁️
                </button>
            </div>
            <div class="error-message" id="passwordError"></div>
        </div>

        <div class="form-options">
            <label class="remember-me">
                <input type="checkbox" id="rememberMe" name="remember">
                <span>Remember me</span>
            </label>
            <a href="#" class="forgot-password">Forgot password?</a>
        </div>

        <button type="submit" class="login-button" id="loginButton">
            Sign In
        </button>

        <div class="success-message" id="successMessage">
            Login successful! Redirecting...
        </div>
    </form>

    <div class="divider">
        <span>or continue with</span>
    </div>

    <div class="social-login">
        <button class="social-button" id="googleLogin">
            <span>🔍</span>
            Google
        </button>
        <button class="social-button" id="microsoftLogin">
            <span>🪟</span>
            Microsoft
        </button>
    </div>

    <div class="footer-text">
        Don't have an account? <a href="#" id="signupLink">Sign up</a>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css"/>

<script>
$(function () {
    const loginForm = $('#loginForm');
    const emailInput = $('#email');
    const passwordInput = $('#password');
    const loginButton = $('#loginButton');

    // ✅ Password toggle
    $('#passwordToggle').on('click', function () {
        let type = passwordInput.attr('type') === 'password' ? 'text' : 'password';
        passwordInput.attr('type', type);
        $(this).text(type === 'password' ? '👁️' : '🙈');
    });

    // ✅ Validate email
    function validateEmail(email) {
        const regex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        return regex.test(email);
    }

    // ✅ Show/Hide error
    function showError(selector, message) {
        $(selector).text(message).show();
    }
    function hideError(selector) {
        $(selector).text('').hide();
    }

    // ✅ Real-time validation
    emailInput.on('input', function () {
        if ($(this).val() && !validateEmail($(this).val())) {
            showError('#emailError', 'Please enter a valid email address');
        } else {
            hideError('#emailError');
        }
    });

    passwordInput.on('input', function () {
        if ($(this).val() && $(this).val().length < 6) {
            showError('#passwordError', 'Password must be at least 6 characters');
        } else {
            hideError('#passwordError');
        }
    });

    // ✅ Submit form with AJAX
    loginForm.on('submit', function (e) {
        e.preventDefault();

        let email = emailInput.val().trim();
        let password = passwordInput.val().trim();
        let isValid = true;

        hideError('#emailError');
        hideError('#passwordError');

        if (!email) {
            showError('#emailError', 'Email is required');
            isValid = false;
        } else if (!validateEmail(email)) {
            showError('#emailError', 'Please enter a valid email address');
            isValid = false;
        }

        if (!password) {
            showError('#passwordError', 'Password is required');
            isValid = false;
        } else if (password.length < 6) {
            showError('#passwordError', 'Password must be at least 6 characters');
            isValid = false;
        }

        if (isValid) {
            loginButton.prop('disabled', true).text('Signing in...');

            $.ajax({
                url: "{{ route('login.post') }}",
                type: "POST",
                dataType: "json",
                data: loginForm.serialize(),
                success: function (res) {
                    if(res.status) {
                        toastr.success(res.message);
                        setTimeout(() => window.location.href = res.redirect, 200);
                    } else {
                        toastr.error(res.message || "Login failed. Please try again.");
                    }
                    loginButton.prop('disabled', false).text('Sign In');
                },
                error: function (xhr) {
                    loginButton.prop('disabled', false).text('Sign In');
                    if (xhr.status === 422) {
                        let errors = xhr.responseJSON.errors;
                        $.each(errors, function (key, val) {
                            $('#' + key + 'Error').text(val[0]).show();
                        });
                    } else {
                        toastr.error("Login failed. Please try again.");
                    }
                }
            });
        }
    });

    // ✅ Social login placeholders
    $('#googleLogin').on('click', () => alert('Google login functionality would be implemented here'));
    $('#microsoftLogin').on('click', () => alert('Microsoft login functionality would be implemented here'));
    $('#signupLink').on('click', (e) => { e.preventDefault(); alert('Sign up functionality would be implemented here'); });
    $('.forgot-password').on('click', (e) => { e.preventDefault(); alert('Password reset functionality would be implemented here'); });

    // ✅ Redirect if already logged in (localStorage dummy check)
    if (localStorage.getItem('isLoggedIn') === 'true') {
        window.location.href = 'dashboard.html';
    }
});
</script>
@endpush
