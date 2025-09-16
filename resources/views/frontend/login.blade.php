@extends('layouts.frontend')

@section('title','Login')

@push('css')

@endpush

@section('content')

<div class="main-content">
    <div class="login-main">
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
