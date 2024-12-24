<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        // Validasi input
        $request->validate([
            'username' => 'required|string',
            'password' => 'required|string',
        ]);

        // Data login
        $credentials = $request->only('username', 'password');

        // Autentikasi user
        if (Auth::attempt($credentials, $request->filled('remember'))) {
            // Jika berhasil login
            return redirect()->intended('/')->with('success', 'Welcome back!');
        }

        // Jika gagal login
        return back()->withErrors(['login' => 'Invalid username or password'])->withInput($request->only('username'));
    }

    // Menangani proses logout
    public function logout()
    {
        Auth::logout();
        return redirect('/login')->with('success', 'You have been logged out.');
    }
}
