<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;


class AuthController extends Controller
{
    public function index()
    {
        return view('frontend.login');
    }

    public function login(Request $request)
    {
        // Use Validator instead of $request->validate()
        $validator = Validator::make($request->all(), [
            'email'    => ['required', 'email'],
            'password' => ['required', 'string', 'min:6'],
        ]);

        // If validation fails → return JSON errors
        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'errors' => $validator->errors() // 👈 full error array
            ], 422);
        }

        $credentials = $request->only('email', 'password');
        $remember = $request->boolean('remember');

        if (Auth::attempt($credentials, $remember)) {
            $user = Auth::user();

            $redirect =  route('admin.dashboard');

            return response()->json([
                'status'   => true,
                'message'  => 'Login successful',
                'redirect' => $redirect
            ]);
        }

        return response()->json([
            'status' => false,
            'message' =>'Invalid email or password'
        ]);
    }

    public function logout()
    {
        Auth::logout();
        return redirect()->route('login');
    }
}
