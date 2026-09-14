<?php

namespace App\Http\Controllers\Admin\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Lockout;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class AdminAuthenticatedSessionController extends Controller
{
    /**
     * Display the backend admin login view.
     */
    public function create(): View|RedirectResponse
    {
        if (Auth::check()) {
            if (Auth::user()->isGuru()) {
                return redirect()->route('admin.dashboard');
            }

            // Siswa dilarang masuk ke portal admin
            return redirect()->route('dashboard')->with('error', 'Akses ditolak. Anda login sebagai Siswa dan tidak diizinkan masuk ke portal admin.');
        }

        return view('admin.auth.login');
    }

    /**
     * Handle an incoming admin authentication request.
     *
     * @throws ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        if (Auth::check() && ! Auth::user()->isGuru()) {
            return redirect()->route('dashboard')->with('error', 'Akses ditolak. Anda login sebagai Siswa dan tidak diizinkan masuk ke portal admin.');
        }

        $request->validate([
            'email' => ['required', 'string', 'email'],
            'password' => ['required', 'string'],
        ]);

        $this->ensureIsNotRateLimited($request);

        $credentials = $request->only('email', 'password');

        // Validasi kredensial email & password
        if (! Auth::validate($credentials)) {
            RateLimiter::hit($this->throttleKey($request));

            throw ValidationException::withMessages([
                'email' => trans('auth.failed'),
            ]);
        }

        $user = User::where('email', $request->email)->first();

        // Verifikasi role: Siswa TIDAK BISA login di backend
        if (! $user || ! $user->isGuru()) {
            RateLimiter::hit($this->throttleKey($request));

            throw ValidationException::withMessages([
                'email' => 'Akses ditolak. Halaman login ini hanya untuk Guru dan Administrator. Siswa silakan login melalui portal utama.',
            ]);
        }

        // Login akun Guru
        Auth::login($user, $request->boolean('remember'));
        RateLimiter::clear($this->throttleKey($request));
        $request->session()->regenerate();

        return redirect()->intended(route('admin.dashboard', absolute: false));
    }

    /**
     * Destroy an authenticated admin session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('admin.login');
    }

    /**
     * Ensure the login request is not rate limited.
     *
     * @throws ValidationException
     */
    protected function ensureIsNotRateLimited(Request $request): void
    {
        if (! RateLimiter::tooManyAttempts($this->throttleKey($request), 5)) {
            return;
        }

        event(new Lockout($request));

        $seconds = RateLimiter::availableIn($this->throttleKey($request));

        throw ValidationException::withMessages([
            'email' => trans('auth.throttle', [
                'seconds' => $seconds,
                'minutes' => ceil($seconds / 60),
            ]),
        ]);
    }

    /**
     * Get the rate limiting throttle key for the request.
     */
    protected function throttleKey(Request $request): string
    {
        return Str::transliterate(Str::lower($request->input('email')).'|admin_login|'.$request->ip());
    }
}
