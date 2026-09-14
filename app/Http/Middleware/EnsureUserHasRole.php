<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserHasRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, string $role): Response
    {
        $user = $request->user();

        if (! $user) {
            return redirect()->route('login');
        }

        if ($role === 'guru' && ! $user->isGuru()) {
            return redirect()->route('dashboard')->with('error', 'Akses ditolak. Anda tidak memiliki akses ke portal admin.');
        }

        if ($role === 'siswa' && ! $user->isSiswa()) {
            return redirect()->route('admin.dashboard')->with('error', 'Anda login sebagai Guru.');
        }

        return $next($request);
    }
}
