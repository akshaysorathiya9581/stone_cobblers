<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class ProfileController extends Controller
{
    // Show profile page
    public function edit()
    {
        $user = Auth::user();
        return view('admin.profile.edit', compact('user'));
    }

    // Update profile info
    public function update(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'name'   => 'required|string|max:255',
            'email'  => 'required|email|unique:users,email,' . $user->id,
            'phone'  => 'nullable|string|max:20',
            'address'=> 'nullable|string|max:255',
            'city'   => 'nullable|string|max:100',
            'state'  => 'nullable|string|max:100',
            'zipCode'=> 'nullable|max:20',
        ]);

        $user->update($request->only(['name', 'email', 'phone', 'address', 'city', 'state', 'zipCode']));

        return redirect()->back()->with('message', 'Profile updated successfully.');
    }

    // Update password
    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password'      => 'required',
            'new_password'          => 'required|min:6|confirmed',
        ]);

        $user = Auth::user();

        if (!Hash::check($request->current_password, $user->password)) {
            return back()->withErrors(['current_password' => 'Your current password is incorrect.']);
        }

        $user->update(['password' => Hash::make($request->new_password)]);

        return back()->with('message', 'Password changed successfully.');
    }
}
