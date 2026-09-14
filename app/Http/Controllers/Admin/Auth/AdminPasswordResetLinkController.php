<?php

namespace App\Http\Controllers\Admin\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Password;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class AdminPasswordResetLinkController extends Controller
{
    /**
     * Display the admin password reset link request view.
     */
    public function create(): View|RedirectResponse
    {
        if (Auth::check()) {
            if (Auth::user()->isGuru()) {
                return redirect()->route('admin.dashboard');
            }

            return redirect()->route('dashboard')->with('error', 'Akses ditolak. Anda login sebagai Siswa dan tidak diizinkan masuk ke portal admin.');
        }

        return view('admin.auth.forgot-password');
    }

    /**
     * Handle an incoming admin password reset link request.
     *
     * @throws ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        if (Auth::check() && ! Auth::user()->isGuru()) {
            return redirect()->route('dashboard')->with('error', 'Akses ditolak. Anda login sebagai Siswa dan tidak diizinkan masuk ke portal admin.');
        }

        $request->validate([
            'email' => ['required', 'email'],
        ]);

        $user = User::where('email', $request->email)->first();

        // Jika email bukan guru / siswa coba reset lewat admin
        if ($user && ! $user->isGuru()) {
            throw ValidationException::withMessages([
                'email' => 'Alamat email ini terdaftar sebagai Siswa. Silakan gunakan layanan reset kata sandi di portal utama.',
            ]);
        }

        $status = Password::sendResetLink(
            $request->only('email')
        );

        return $status == Password::RESET_LINK_SENT
            ? back()->with('status', __($status))
            : back()->withInput($request->only('email'))->withErrors(['email' => __($status)]);
    }
}
