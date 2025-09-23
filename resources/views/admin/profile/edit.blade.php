@extends('layouts.admin')

@section('title', 'My Profile')

@section('content')

<div class="main-content">
    <!-- Header -->
    <div class="header">
        <div class="search-bar">
            <i>🔍</i>
            <input id="quote-search" type="text" placeholder="Search quotes, customers...">
        </div>

        <div class="header-actions">
            <a href="{{ route('admin.profile.edit') }}" class="user-avatar" aria-label="Open profile">{{ auth()->user() ? Str::upper(Str::substr(auth()->user()->name ?? 'U',0,2)) : 'U' }}</a>
        </div>
    </div>

    <!-- Content -->
    <div class="content profile-main">
        <div class="content-header">
            <h1 class="content-title">My Profile</h1>
        </div>
        <div class="profile-content">
            {{-- Profile Update --}}
            <form action="{{ route('admin.profile.update') }}" method="POST" class="profile-card">
                @csrf
                <h2 class="title">Update Profile</h2>
                <div class="form-group">
                    <label class="form-label">Name</label>
                    <input type="text" name="name" value="{{ old('name', $user->name) }}" class="form-input">
                    @error('name') <small class="text-danger">{{ $message }}</small> @enderror
                </div>
        
                <div class="form-group">
                    <label class="form-label">Email</label>
                    <input type="email" name="email" value="{{ old('email', $user->email) }}" class="form-input">
                    @error('email') <small class="text-danger">{{ $message }}</small> @enderror
                </div>
        
                
                <div class="form-group__grp">
                    <div class="form-group">
                        <label class="form-label">City</label>
                        <input type="text" name="city" class="form-input" value="{{ old('city', $user->city) }}">
                    </div>
                    <div class="form-group">
                        <label class="form-label">State</label>
                        <input type="text" name="state" class="form-input" value="{{ old('state', $user->state) }}">
                    </div>
                    <div class="form-group">
                        <label class="form-label">zipCode</label>
                        <input type="text" name="zipCode" class="form-input" value="{{ old('zipCode', $user->zipCode) }}">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Phone</label>
                        <input type="text" name="phone" value="{{ old('phone', $user->phone) }}" class="form-input">
                    </div>
                </div>
        
        
                <div class="form-group">
                    <label class="form-label">Address</label>
                    <input type="text" name="address" value="{{ old('address', $user->address) }}" class="form-input">
                </div>
        
                <button type="submit" class="btn theme">Update Profile</button>
            </form>
        
            {{-- Change Password --}}
            <form action="{{ route('admin.profile.password') }}" method="POST" class="profile-card">
                @csrf
                <h2 class="title">Change Password</h2>
                <div class="form-group">
                    <label class="form-label">Current Password</label>
                    <input type="password" name="current_password" class="form-input" required>
                    @error('current_password') <small class="text-danger">{{ $message }}</small> @enderror
                </div>
        
                <div class="form-group">
                    <label class="form-label">New Password</label>
                    <input type="password" name="new_password" class="form-input" required>
                    @error('new_password') <small class="text-danger">{{ $message }}</small> @enderror
                </div>
        
                <div class="form-group">
                    <label class="form-label">Confirm New Password</label>
                    <input type="password" name="new_password_confirmation" class="form-input" required>
                </div>
        
                <button type="submit" class="btn theme">Change Password</button>
            </form>
        </div>
    </div>

</div>
@endsection
