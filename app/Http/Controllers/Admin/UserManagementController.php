<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class UserManagementController extends Controller
{
    public function index(Request $request): View
    {
        $q = trim((string) $request->query('q', ''));

        $users = User::query()
            ->where('role', 'petugas')
            ->when($q !== '', function ($query) use ($q) {
                $query->where(function ($w) use ($q) {
                    $w->where('name', 'like', '%'.$q.'%')
                        ->orWhere('email', 'like', '%'.$q.'%');
                });
            })
            ->orderBy('name')
            ->paginate(10)
            ->withQueryString();

        return view('admin.users.index', compact('users', 'q'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users', 'email')],
            'password' => ['required', 'string', 'min:8'],
        ]);

        User::query()->create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role' => 'petugas',
        ]);

        return redirect()
            ->route('admin.users.index')
            ->with('status', 'Petugas berhasil ditambahkan.');
    }

    public function edit(User $user): View
    {
        if ($user->role !== 'petugas') {
            abort(403, 'Hanya akun petugas yang dapat diedit dari halaman ini.');
        }

        return view('admin.users.edit', compact('user'));
    }

    public function update(Request $request, User $user): RedirectResponse
    {
        if ($user->role !== 'petugas') {
            abort(403, 'Hanya akun petugas yang dapat diedit dari halaman ini.');
        }

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users', 'email')->ignore($user->id)],
            'password' => ['nullable', 'string', 'min:8'],
        ]);

        $user->name = $validated['name'];
        $user->email = $validated['email'];

        if ($validated['password']) {
            $user->password = Hash::make($validated['password']);
        }

        $user->save();

        return redirect()
            ->route('admin.users.index')
            ->with('status', 'Petugas berhasil diperbarui.');
    }

    public function destroy(Request $request, User $user): RedirectResponse
    {
        if ($user->role !== 'petugas') {
            abort(403, 'Hanya akun petugas yang dapat dihapus dari halaman ini.');
        }

        if ((int) $request->user()->id === (int) $user->id) {
            return redirect()
                ->route('admin.users.index')
                ->withErrors(['user' => 'Anda tidak dapat menghapus akun yang sedang digunakan.']);
        }

        $user->delete();

        return redirect()
            ->route('admin.users.index')
            ->with('delete', 'Petugas berhasil dihapus.');
    }
}
