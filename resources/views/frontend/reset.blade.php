@extends('layouts.frontend')

@section('title','Reset password')

@section('content')
<div class="main-content">
  <div class="reset-container">
    <h1>Reset Password</h1>

    @if(session('status'))
      <div class="alert success">{{ session('status') }}</div>
    @endif

    <form method="POST" action="{{ route('password.update') }}">
      @csrf
      <input type="hidden" name="token" value="{{ $token }}">

      <div class="form-group">
        <label for="email">Email</label>
        <input id="email" type="email" name="email" value="{{ old('email', $email) }}" required autofocus>
        @error('email') <div class="error-message">{{ $message }}</div> @enderror
      </div>

      <div class="form-group">
        <label for="password">New password</label>
        <input id="password" type="password" name="password" required>
        @error('password') <div class="error-message">{{ $message }}</div> @enderror
      </div>

      <div class="form-group">
        <label for="password_confirmation">Confirm password</label>
        <input id="password_confirmation" type="password" name="password_confirmation" required>
      </div>

      <div style="margin-top:12px;">
        <button type="submit" class="btn primary">Reset password</button>
      </div>
    </form>
  </div>
</div>
@endsection
