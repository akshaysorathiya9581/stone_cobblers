@extends('layouts.admin')

@section('title', 'My Profile')

@section('content')

<div class="main-content">
    <!-- Header -->
    <div class="header">
        <button class="sidebar-toggle">
            <i class="fas fa-bars toggle-icon"></i>
        </button>
        <!-- <div class="search-bar">
            <i class="fas fa-search"></i>
            <input id="quote-search" type="text" placeholder="Search quotes, customers...">
        </div> -->

        <div class="header-actions">
            <a href="{{ route('admin.profile.edit') }}" class="user-avatar" aria-label="Open profile">{{ auth()->user() ? Str::upper(Str::substr(auth()->user()->name ?? 'U',0,2)) : 'U' }}</a>
        </div>
    </div>

    <!-- Content -->
    <div class="content profile-main">
        <div class="content-header">
            <div class="profile-header-content">
                <div>
                    <h1 class="content-title">My Profile</h1>
                    <p class="profile-subtitle">Manage your account settings and preferences</p>
                </div>
            </div>
        </div>
        <div class="profile-content">
            {{-- Profile Update --}}
            <form action="{{ route('admin.profile.update') }}" method="POST" class="profile-card profile-form-card">
                @csrf
                <div class="profile-card-header">
                    <div class="profile-card-icon">
                        <i class="fas fa-user-edit"></i>
                    </div>
                    <div>
                        <h2 class="title">Update Profile</h2>
                        <p class="profile-card-description">Update your personal information and contact details</p>
                    </div>
                </div>
                
                <div class="profile-form-body">
                    <div class="form-group">
                        <label class="form-label">
                            <i class="fas fa-user"></i> Full Name
                        </label>
                        <input type="text" name="name" value="{{ old('name', $user->name) }}" class="form-input" placeholder="Enter your full name">
                        @error('name') <small class="text-danger error-message"><i class="fas fa-exclamation-circle"></i> {{ $message }}</small> @enderror
                    </div>
            
                    <div class="form-group">
                        <label class="form-label">
                            <i class="fas fa-envelope"></i> Email Address
                        </label>
                        <input type="email" name="email" value="{{ old('email', $user->email) }}" class="form-input" placeholder="Enter your email address">
                        @error('email') <small class="text-danger error-message"><i class="fas fa-exclamation-circle"></i> {{ $message }}</small> @enderror
                    </div>
            
                    <div class="form-group">
                        <label class="form-label">
                            <i class="fas fa-map-marker-alt"></i> Address
                        </label>
                        <input type="text" name="address" value="{{ old('address', $user->address) }}" class="form-input" placeholder="Enter your street address">
                        @error('address') <small class="text-danger error-message"><i class="fas fa-exclamation-circle"></i> {{ $message }}</small> @enderror
                    </div>
            
                    <div class="form-group__grp">
                        <div class="form-group">
                            <label class="form-label">
                                <i class="fas fa-city"></i> City
                            </label>
                            <input type="text" name="city" class="form-input" value="{{ old('city', $user->city) }}" placeholder="Enter city">
                            @error('city') <small class="text-danger error-message"><i class="fas fa-exclamation-circle"></i> {{ $message }}</small> @enderror
                        </div>
                        <div class="form-group">
                            <label class="form-label">
                                <i class="fas fa-map"></i> State
                            </label>
                            <input type="text" name="state" class="form-input" value="{{ old('state', $user->state) }}" placeholder="Enter state">
                            @error('state') <small class="text-danger error-message"><i class="fas fa-exclamation-circle"></i> {{ $message }}</small> @enderror
                        </div>
                        <div class="form-group">
                            <label class="form-label">
                                <i class="fas fa-mail-bulk"></i> Zip Code
                            </label>
                            <input type="text" name="zipCode" class="form-input" value="{{ old('zipCode', $user->zipCode) }}" placeholder="Enter zip code">
                            @error('zipCode') <small class="text-danger error-message"><i class="fas fa-exclamation-circle"></i> {{ $message }}</small> @enderror
                        </div>
                        <div class="form-group">
                            <label class="form-label">
                                <i class="fas fa-phone"></i> Phone
                            </label>
                            <input type="text" name="phone" value="{{ old('phone', $user->phone) }}" class="form-input" placeholder="Enter phone number">
                            @error('phone') <small class="text-danger error-message"><i class="fas fa-exclamation-circle"></i> {{ $message }}</small> @enderror
                        </div>
                    </div>
            
                    <div class="profile-form-actions">
                        <button type="submit" class="btn theme">
                            Update Profile
                        </button>
                    </div>
                </div>
            </form>
        
            {{-- Change Password --}}
            <form action="{{ route('admin.profile.password') }}" method="POST" class="profile-card profile-form-card">
                @csrf
                <div class="profile-card-header">
                    <div class="profile-card-icon password-icon">
                        <i class="fas fa-lock"></i>
                    </div>
                    <div>
                        <h2 class="title">Change Password</h2>
                        <p class="profile-card-description">Update your password to keep your account secure</p>
                    </div>
                </div>
                
                <div class="profile-form-body">
                    <div class="form-group">
                        <label class="form-label">
                            <i class="fas fa-key"></i> Current Password
                        </label>
                        <div class="password-input-wrapper">
                            <input type="password" name="current_password" id="current_password" class="form-input" placeholder="Enter current password" required>
                            <button type="button" class="password-toggle" data-target="current_password">
                                <i class="fas fa-eye"></i>
                            </button>
                        </div>
                        @error('current_password') <small class="text-danger error-message"><i class="fas fa-exclamation-circle"></i> {{ $message }}</small> @enderror
                    </div>
            
                    <div class="form-group">
                        <label class="form-label">
                            <i class="fas fa-lock"></i> New Password
                        </label>
                        <div class="password-input-wrapper">
                            <input type="password" name="new_password" id="new_password" class="form-input" placeholder="Enter new password" required>
                            <button type="button" class="password-toggle" data-target="new_password">
                                <i class="fas fa-eye"></i>
                            </button>
                        </div>
                        @error('new_password') <small class="text-danger error-message"><i class="fas fa-exclamation-circle"></i> {{ $message }}</small> @enderror
                    </div>
            
                    <div class="form-group">
                        <label class="form-label">
                            <i class="fas fa-lock"></i> Confirm New Password
                        </label>
                        <div class="password-input-wrapper">
                            <input type="password" name="new_password_confirmation" id="new_password_confirmation" class="form-input" placeholder="Confirm new password" required>
                            <button type="button" class="password-toggle" data-target="new_password_confirmation">
                                <i class="fas fa-eye"></i>
                            </button>
                        </div>
                    </div>
            
                    <div class="profile-form-actions">
                        <button type="submit" class="btn theme">
                            Change Password
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

</div>

@push('scripts')
<script>
    // Password toggle functionality
    document.querySelectorAll('.password-toggle').forEach(button => {
        button.addEventListener('click', function() {
            const targetId = this.getAttribute('data-target');
            const input = document.getElementById(targetId);
            const icon = this.querySelector('i');
            
            if (input.type === 'password') {
                input.type = 'text';
                icon.classList.remove('fa-eye');
                icon.classList.add('fa-eye-slash');
            } else {
                input.type = 'password';
                icon.classList.remove('fa-eye-slash');
                icon.classList.add('fa-eye');
            }
        });
    });
</script>
@endpush
@endsection
