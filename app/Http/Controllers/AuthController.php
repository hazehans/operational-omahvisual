<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthController extends Controller
{
    /**
     * Show the login form.
     */
    public function showLoginForm(): View|RedirectResponse
    {
        if (Auth::check()) {
            return redirect()->route('dashboard');
        }

        return view('auth.login');
    }

    /**
     * Handle an authentication attempt.
     */
    public function login(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'email' => ['required', 'string', 'email'],
            'password' => ['required', 'string'],
        ], [
            'email.required' => 'Email wajib diisi.',
            'email.email' => 'Format email tidak valid.',
            'password.required' => 'Password wajib diisi.',
        ]);

        $remember = $request->boolean('remember');

        if (Auth::attempt($credentials, $remember)) {
            $request->session()->regenerate();

            AuditLog::log(
                action: 'login',
                entityType: 'User',
                entityId: Auth::id(),
                description: 'User '.Auth::user()->name.' berhasil masuk ke sistem.'
            );

            return redirect()->intended(route('dashboard'))
                ->with('success', 'Selamat datang kembali, '.Auth::user()->name.'!');
        }

        return back()
            ->withErrors([
                'email' => 'Kombinasi email dan password yang Anda masukkan tidak cocok.',
            ])
            ->onlyInput('email');
    }

    /**
     * Log the user out of the application.
     */
    public function logout(Request $request): RedirectResponse
    {
        $userName = Auth::user()?->name ?? 'Pengguna';
        $userId = Auth::id();

        if ($userId) {
            AuditLog::log(
                action: 'logout',
                entityType: 'User',
                entityId: $userId,
                description: 'User '.$userName.' keluar dari sistem.'
            );
        }

        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')
            ->with('info', 'Anda telah berhasil keluar.');
    }
}
