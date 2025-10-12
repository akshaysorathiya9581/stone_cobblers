@extends('layouts.frontend')

@section('title', 'Reset password')

@push('css')

@endpush

@section('content')

  <div class="main-content">
    <div class="login-main">
      <div class="login-container">
        <div class="logo-section">
          <div class="logo-icon">SC</div>
          <h1 class="company-name">Reset Password</h1>
        </div>

        @if(session('status'))
          <div class="alert success">{{ session('status') }}</div>
        @endif


        <form class="login-form" method="POST" action="{{ route('password.update') }}">
          @csrf
          <input type="hidden" name="token" value="{{ $token }}">

          <div class="form-group">
            <label for="email" class="form-label">Email Address</label>
            <input id="email" type="email" name="email" value="{{ old('email', $email) }}"  class="form-input" placeholder="Enter your email" required autofocus>
            @error('email') <div class="error-message">{{ $message }}</div> @enderror
          </div>

          <div class="form-group">
            <label for="password" class="form-label">New password</label>
            <input id="password" type="password" name="password" class="form-input" required>
            @error('password') <div class="error-message">{{ $message }}</div> @enderror
          </div>

          <div class="form-group">
            <label for="password_confirmation" class="form-label">Confirm password</label>
            <input id="password_confirmation" type="password" name="password_confirmation" class="form-input" required>
          </div>

          <div class="mt-12">
            <button type="submit" class="btn theme">Reset password</button>
          </div>
        </form>
      </div>
    </div>
  </div>

@endsection