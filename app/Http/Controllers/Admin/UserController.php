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
            'password' => ['nullable', 'string', 'min:6', 'confirmed'],
        ], [
            'name.required' => 'Nama lengkap wajib diisi.',
            'email.required' => 'Alamat email wajib diisi.',
            'email.email' => 'Format email tidak valid.',
            'email.unique' => 'Email ini sudah digunakan oleh akun lain.',
            'nip.unique' => 'NIP/NISN ini sudah digunakan oleh akun lain.',
            'role_id.required' => 'Role pengguna wajib dipilih.',
            'role_id.exists' => 'Role yang dipilih tidak valid.',
            'class_id.exists' => 'Kelas yang dipilih tidak valid.',
            'password.min' => 'Password minimal harus terdiri dari 6 karakter.',
            'password.confirmed' => 'Konfirmasi password baru tidak cocok.',
        ]);

        $updateData = [
            'name' => $request->name,
            'email' => $request->email,
            'nip' => $request->filled('nip') ? trim($request->nip) : null,
            'role_id' => $request->role_id,
            'class_id' => $request->filled('class_id') ? $request->class_id : null,
        ];

        if ($request->filled('password')) {
            $updateData['password'] = Hash::make($request->password);
        }

        $user->update($updateData);

        return redirect()->route('admin.users.index')->with('success', 'Data user ' . $user->name . ' berhasil diperbarui.');
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

    public function destroy(User $user): RedirectResponse
    {
        $currentUser = auth()->user();

        // 1. Cannot delete own account
        if ($currentUser && $currentUser->id === $user->id) {
            return redirect()->back()->with('error', 'Anda tidak dapat menghapus akun Anda sendiri.');
        }

        // 2. Guru can only delete student accounts (cannot delete other teachers or admins)
        if ($currentUser && $currentUser->isGuru() && !$user->isSiswa()) {
            return redirect()->back()->with('error', 'Anda hanya memiliki akses untuk menghapus data siswa. Tidak dapat menghapus akun guru lain.');
        }

        $userName = $user->name;
        $user->delete();

        return redirect()->route('admin.users.index')->with('success', 'Data akun pengguna ' . $userName . ' berhasil dihapus.');
    }
}
