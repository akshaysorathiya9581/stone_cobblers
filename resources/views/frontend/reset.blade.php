@extends('layouts.frontend')

@section('title', 'Reset password')

@push('css')

@endpush

@section('content')

  <div class="main-content">
    <div class="login-main">
      <div class="login-container">

        <div class="logo-section">
            <div class="logo">
                <div class="logo-icon">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M22 11C22.5523 11 23 11.4477 23 12C23 12.5523 22.5523 13 22 13L2 13C1.44772 13 1 12.5523 1 12C1 11.4477 1.44772 11 2 11L22 11Z" fill="white"/>
                        <path d="M3 20L3 12C3 11.4477 3.44772 11 4 11C4.55228 11 5 11.4477 5 12L5 20L5.00488 20.0986C5.02757 20.3276 5.12883 20.5429 5.29297 20.707C5.48051 20.8946 5.73478 21 6 21L18 21C18.2652 21 18.5195 20.8946 18.707 20.707C18.8946 20.5195 19 20.2652 19 20V12C19 11.4477 19.4477 11 20 11C20.5523 11 21 11.4477 21 12L21 20C21 20.7957 20.6837 21.5585 20.1211 22.1211C19.5585 22.6837 18.7957 23 18 23L6 23C5.20435 23 4.44152 22.6837 3.87891 22.1211C3.38671 21.6289 3.08291 20.9835 3.01465 20.2969L3 20Z" fill="white"/>
                        <path d="M19.7578 3.03029C20.2935 2.89649 20.8358 3.22211 20.9697 3.75783C21.1035 4.29355 20.7779 4.83581 20.2422 4.96974L4.24221 8.96974C3.70649 9.10354 3.16422 8.77792 3.03029 8.2422C2.8965 7.70648 3.22211 7.16422 3.75783 7.03029L19.7578 3.03029Z" fill="white"/>
                        <path d="M11.5598 1.0903C11.9418 0.994634 12.3392 0.973946 12.7288 1.0317C13.1195 1.08968 13.4947 1.22477 13.8333 1.42819C14.1717 1.63157 14.4668 1.8996 14.7014 2.21725C14.9355 2.5344 15.1046 2.89519 15.1995 3.2778L15.6497 5.0776C15.7836 5.61339 15.4579 6.15654 14.9221 6.29049C14.3866 6.42409 13.8443 6.09831 13.7102 5.56295L13.26 3.76217L13.259 3.76022C13.2275 3.63243 13.1712 3.51164 13.093 3.40573C13.0148 3.29977 12.9159 3.20989 12.803 3.14205C12.6902 3.07435 12.565 3.02951 12.4348 3.01022C12.3046 2.99094 12.1718 2.99761 12.0442 3.02975L12.0403 3.03073L10.0999 3.5112L10.0989 3.51022C9.97359 3.54255 9.8561 3.59994 9.75223 3.67721C9.64682 3.75567 9.55784 3.85441 9.49051 3.96725C9.42328 4.07996 9.37867 4.20459 9.35965 4.33444C9.34541 4.43196 9.34502 4.53128 9.35965 4.62838L9.37918 4.72506L9.38016 4.72897L9.83036 6.53854L9.84989 6.63912C9.92192 7.14088 9.60337 7.62552 9.10086 7.75045C8.56503 7.88343 8.02314 7.55679 7.88993 7.02096L7.43973 5.21139L7.44071 5.21041C7.345 4.82968 7.32341 4.43387 7.38016 4.04537C7.43716 3.65541 7.57079 3.2803 7.77274 2.94186C7.97472 2.60342 8.24174 2.30805 8.55789 2.07272C8.87401 1.83747 9.23342 1.66711 9.61551 1.57077L9.61942 1.56881L11.5598 1.0903Z" fill="white"/>
                    </svg>
                </div>
                <span class="logo-text">Stone Cobblers</span>
            </div>
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