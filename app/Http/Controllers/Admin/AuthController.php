<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    // Tampilkan form login admin
    public function showLogin()
    {
        if (Auth::check() && Auth::user()->is_admin) {
            return redirect()->route('admin.dashboard');
        }
        
        return view('admin.login');
    }
    
    // Proses login
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required|min:6'
        ]);
        
        if (Auth::attempt($credentials, $request->remember)) {
            $request->session()->regenerate();
            
            // Cek apakah user admin
            if (Auth::user()->is_admin) {
                return redirect()->route('admin.dashboard');
            }
            
            // Jika bukan admin, logout
            Auth::logout();
            return back()->withErrors([
                'email' => 'Akun ini bukan admin.',
            ]);
        }
        
        return back()->withErrors([
            'email' => 'Email atau password salah.',
        ])->onlyInput('email');
    }
    
    // Logout
    public function logout(Request $request)
    {
        Auth::logout();
        
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        
        return redirect()->route('admin.login');
    }
}