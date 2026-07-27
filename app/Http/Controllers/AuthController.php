<?php

namespace App\Http\Controllers;

use App\Models\SchoolSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class AuthController extends Controller
{
    public function show()
    {
        return view('auth.login', ['school' => SchoolSetting::first()]);
    }

    public function login(Request $r)
    {
        $data = $r->validate(['login' => 'required', 'password' => 'required']);
        $field = filter_var($data['login'], FILTER_VALIDATE_EMAIL) ? 'email' : 'username';
        if (! Auth::attempt([$field => $data['login'], 'password' => $data['password'], 'is_active' => true], $r->boolean('remember'))) {
            if ($r->expectsJson()) {
                return response()->json(['message' => 'Email/username atau password salah.'], 422);
            }

            return back()->withErrors(['login' => 'Email/username atau password salah.'])->onlyInput('login');
        }
        $r->session()->regenerate();
        $r->user()->update(['last_login_at' => now()]);

        if ($r->expectsJson()) {
            return response()->json(['redirect' => redirect()->intended(route('dashboard'))->getTargetUrl()]);
        }

        return redirect()->intended(route('dashboard'));
    }

    public function account(Request $r)
    {
        return view('auth.account', ['user' => $r->user()]);
    }

    public function updateAccount(Request $r)
    {
        $user = $r->user();
        $data = $r->validate(['name' => 'required|string|max:100', 'email' => ['required', 'email', Rule::unique('users')->ignore($user)], 'username' => ['required', 'string', Rule::unique('users')->ignore($user)], 'current_password' => 'nullable|required_with:password', 'password' => 'nullable|min:8|confirmed']);
        if (! empty($data['password']) && ! Hash::check($data['current_password'], $user->password)) {
            return back()->withErrors(['current_password' => 'Password saat ini tidak sesuai.']);
        }unset($data['current_password']);
        if (empty($data['password'])) {
            unset($data['password']);
        }$user->update($data);

        return back()->with('success', 'Akun berhasil diperbarui.');
    }

    public function logout(Request $r)
    {
        Auth::logout();
        $r->session()->invalidate();
        $r->session()->regenerateToken();

        return redirect()->route('login');
    }
}
