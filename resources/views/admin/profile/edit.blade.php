@extends('layouts.admin')

@section('title', 'My Profile')

@section('content')
<div class="main-content">
    <h2>My Profile</h2>

    {{-- Profile Update --}}
    <form action="{{ route('admin.profile.update') }}" method="POST" class="card p-3 mb-4">
        @csrf
        <h4>Update Profile</h4>
        <div class="form-group">
            <label>Name</label>
            <input type="text" name="name" value="{{ old('name', $user->name) }}" class="form-control">
            @error('name') <small class="text-danger">{{ $message }}</small> @enderror
        </div>

        <div class="form-group">
            <label>Email</label>
            <input type="email" name="email" value="{{ old('email', $user->email) }}" class="form-control">
            @error('email') <small class="text-danger">{{ $message }}</small> @enderror
        </div>

        <div class="form-group">
            <label>Phone</label>
            <input type="text" name="phone" value="{{ old('phone', $user->phone) }}" class="form-control">
        </div>

        <div class="form-group">
            <label>Address</label>
            <input type="text" name="address" value="{{ old('address', $user->address) }}" class="form-control">
        </div>

        <button type="submit" class="btn btn-primary">Update Profile</button>
    </form>

    {{-- Change Password --}}
    <form action="{{ route('admin.profile.password') }}" method="POST" class="card p-3">
        @csrf
        <h4>Change Password</h4>
        <div class="form-group">
            <label>Current Password</label>
            <input type="password" name="current_password" class="form-control" required>
            @error('current_password') <small class="text-danger">{{ $message }}</small> @enderror
        </div>

        <div class="form-group">
            <label>New Password</label>
            <input type="password" name="new_password" class="form-control" required>
            @error('new_password') <small class="text-danger">{{ $message }}</small> @enderror
        </div>

        <div class="form-group">
            <label>Confirm New Password</label>
            <input type="password" name="new_password_confirmation" class="form-control" required>
        </div>

        <button type="submit" class="btn btn-warning">Change Password</button>
    </form>
</div>
@endsection
