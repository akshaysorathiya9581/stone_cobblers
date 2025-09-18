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
                    <a href="#" class="forgot-password" id="forgotPasswordLink">Forgot password?</a>
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

<!-- Forgot Password Modal (hidden) -->
<div id="forgotPasswordModal" class="modal" style="display:none;">
  <div class="modal-inner">
    <button class="close-modal" data-close-modal>&times;</button>
    <h3>Reset your password</h3>
    <p>Enter the email address associated with your account. We'll send a link to reset your password.</p>

    <div id="forgot-msg" style="display:none;" class="muted"></div>

    <form id="forgotForm">
      @csrf
      <div class="form-group">
        <label for="forgot-email">Email</label>
        <input type="email" name="email" id="forgot-email" class="form-input" required>
        <div class="error-message" id="forgotEmailError" style="display:none"></div>
      </div>

      <div style="margin-top:12px;">
        <button type="submit" class="btn primary" id="forgotSubmit">Send reset link</button>
        <button type="button" class="btn secondary" data-close-modal>Cancel</button>
      </div>
    </form>
  </div>
</div>

@endsection

@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css"/>

<script>
    $(function () {

        // show modal
        $('#forgotPasswordLink').on('click', function(e){
            e.preventDefault();
            $('#forgotPasswordModal').show().attr('aria-hidden','false');
            $('#forgot-msg').hide();
            $('#forgot-email').val('');
            $('#forgotEmailError').hide();
        });

        // close modal
        $(document).on('click', '[data-close-modal]', function(){
            $(this).closest('.modal').hide().attr('aria-hidden','true');
        });

        // submit forgot password
        $('#forgotForm').on('submit', function(e){
            e.preventDefault();

            var $btn = $('#forgotSubmit');
            $btn.prop('disabled', true).text('Sending...');

            var data = $(this).serialize();

            $.ajax({
                url: "{{ route('password.email') }}",
                method: "POST",
                dataType: "json",
                data: data
            }).done(function(res){
                if (res && res.status) {
                    toastr.success(res.message || 'Email sent. Check your inbox.');
                    $('#forgot-msg').text(res.message || 'Email sent.').show();
                    // optionally auto-close after a few seconds
                    setTimeout(function(){ $('#forgotPasswordModal').hide(); }, 1800);
                } else {
                    toastr.error(res && res.message ? res.message : 'Failed to send reset email.');
                    if (res && res.errors && res.errors.email) {
                        $('#forgotEmailError').text(res.errors.email[0]).show();
                    }
                }
            }).fail(function(xhr){
                if (xhr.status === 422) {
                    var json = xhr.responseJSON || {};
                    var errs = json.errors || {};
                    if (errs.email) {
                        $('#forgotEmailError').text(errs.email[0]).show();
                    } else {
                        toastr.error('Validation error.');
                    }
                } else {
                    toastr.error('Failed to send reset email.');
                }
            }).always(function(){
                $btn.prop('disabled', false).text('Send reset link');
            });
        });
    
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
        // $('.forgot-password').on('click', (e) => { e.preventDefault(); alert('Password reset functionality would be implemented here'); });

        // ✅ Redirect if already logged in (localStorage dummy check)
        if (localStorage.getItem('isLoggedIn') === 'true') {
            window.location.href = 'dashboard.html';
        }
    });
</script>
@endpush
