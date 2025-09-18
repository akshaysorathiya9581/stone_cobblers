<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Auth\Events\PasswordReset;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class ResetPasswordController extends Controller
{
    /**
     * Show the reset form (link contains token).
     */
    public function showResetForm(Request $request, $token = null)
    {
        // Render a blade that includes token + email fields
        return view('auth.passwords.reset')->with(
            ['token' => $token, 'email' => $request->query('email', '')]
        );
    }

    /**
     * Handle reset (POST).
     */
    public function reset(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'token' => 'required|string',
            'email' => 'required|email|exists:users,email',
            'password' => 'required|string|min:6|confirmed',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function ($user, $password) {
                $user->password = Hash::make($password);
                $user->setRememberToken(Str::random(60));
                $user->save();

                event(new PasswordReset($user));
            }
        );

        if ($status === Password::PASSWORD_RESET) {
            // redirect to login with success message
            return redirect()->route('login')->with('status', trans($status));
        }

        // otherwise back with errors
        return back()->withErrors(['email' => trans($status)]);
    }
}
