<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Role;
use App\Models\SchoolClass;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\View\View;

class UserController extends Controller
{
    public function index(Request $request): View
    {
        $query = User::with(['role', 'schoolClass']);

        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('email', 'like', '%' . $request->search . '%')
                  ->orWhere('nip', 'like', '%' . $request->search . '%');
            });
        }

        if ($request->filled('role_id')) {
            $query->where('role_id', $request->role_id);
        }

        $users = $query->latest()->paginate(10)->withQueryString();
        $roles = Role::orderBy('name')->get();

        return view('admin.users.index', compact('users', 'roles'));
    }

    public function edit(User $user): View
    {
        $roles = Role::orderBy('name')->get();
        $classes = SchoolClass::orderBy('name')->get();

        return view('admin.users.edit', compact('user', 'roles', 'classes'));
    }

    public function update(Request $request, User $user): RedirectResponse
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email,' . $user->id],
            'nip' => ['nullable', 'string', 'max:50', 'unique:users,nip,' . $user->id],
            'role_id' => ['required', 'exists:roles,id'],
            'class_id' => ['nullable', 'exists:classes,id'],
        ]);

        $user->update([
            'name' => $request->name,
            'email' => $request->email,
            'nip' => $request->nip,
            'role_id' => $request->role_id,
            'class_id' => $request->class_id,
        ]);

        return redirect()->route('admin.users.index')->with('success', 'Data user berhasil diperbarui.');
    }

    public function resetPassword(Request $request, User $user): RedirectResponse|\Illuminate\Http\JsonResponse
    {
        $firstName = preg_replace('/[^a-zA-Z0-9]/', '', explode(' ', trim($user->name))[0] ?? '');
        $namePrefix = !empty($firstName) ? Str::ucfirst(Str::lower($firstName)) : 'User';

        $nipDigits = $user->nip ? preg_replace('/[^0-9]/', '', $user->nip) : '';
        $nipSuffix = !empty($nipDigits) ? substr($nipDigits, -4) : (string) random_int(1000, 9999);

        $randomChars = Str::lower(Str::random(4));

        $newPassword = $namePrefix . $nipSuffix . '@' . $randomChars;

        $user->forceFill([
            'password' => $newPassword,
        ])->save();

        if ($request->wantsJson() || $request->ajax() || $request->expectsJson() || $request->header('X-Requested-With') === 'XMLHttpRequest') {
            return response()->json([
                'success' => true,
                'message' => 'Password untuk ' . $user->name . ' berhasil direset.',
                'user_name' => $user->name,
                'new_password' => $newPassword,
            ]);
        }

        return redirect()->back(fallback: route('admin.users.index'))
            ->with('success', 'Password untuk ' . $user->name . ' berhasil direset menjadi: ' . $newPassword)
            ->with('reset_user_name', $user->name)
            ->with('reset_new_password', $newPassword);
    }
}
