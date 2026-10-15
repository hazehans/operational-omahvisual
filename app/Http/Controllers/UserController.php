<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Models\AuditLog;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;
use Spatie\Permission\Models\Role;

class UserController extends Controller
{
    /**
     * Display a listing of all system users.
     */
    public function index(Request $request): View
    {
        $this->authorizeAdmin();

        $query = User::with('roles')->latest();

        // Filter search (name, email, phone)
        if ($request->filled('search')) {
            $search = trim($request->search);
            $query->where(function ($q) use ($search) {
                $q->where('name', 'ilike', "%{$search}%")
                    ->orWhere('email', 'ilike', "%{$search}%")
                    ->orWhere('phone', 'ilike', "%{$search}%");
            });
        }

        // Filter by role
        if ($request->filled('role')) {
            $query->role($request->role);
        }

        $users = $query->paginate(15)->withQueryString();
        $roles = Role::orderBy('name')->get();

        // User count metrics
        $metrics = [
            'total' => User::count(),
            'superuser' => User::role('Superuser')->count(),
            'owner' => User::role('Owner')->count(),
            'warehouse' => User::role('Warehouse')->count(),
            'teknisi' => User::role('Teknisi Servis')->count(),
            'admin_led' => User::role('Admin LED')->count(),
        ];

        return view('users.index', compact('users', 'roles', 'metrics'));
    }

    /**
     * Show the form for creating a new user.
     */
    public function create(): View
    {
        $this->authorizeAdmin();

        $roles = Role::orderBy('name')->get();

        return view('users.create', compact('roles'));
    }

    /**
     * Store a newly created user in storage.
     */
    public function store(StoreUserRequest $request): RedirectResponse
    {
        $validated = $request->validated();

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'] ?? null,
            'password' => Hash::make($validated['password']),
        ]);

        $user->syncRoles([$validated['role']]);

        AuditLog::log(
            action: 'create_user',
            entityType: 'User',
            entityId: $user->id,
            oldValues: null,
            newValues: ['name' => $user->name, 'email' => $user->email, 'role' => $validated['role']],
            description: "Membuat akun pengguna baru: {$user->name} ({$user->email}) dengan peran {$validated['role']}"
        );

        return redirect()->route('users.index')->with('success', "Akun pengguna {$user->name} berhasil dibuat!");
    }

    /**
     * Show the form for editing the specified user.
     */
    public function edit(User $user): View
    {
        $this->authorizeAdmin();

        $roles = Role::orderBy('name')->get();
        $userRole = $user->roles->first()?->name;

        return view('users.edit', compact('user', 'roles', 'userRole'));
    }

    /**
     * Update the specified user in storage.
     */
    public function update(UpdateUserRequest $request, User $user): RedirectResponse
    {
        $validated = $request->validated();

        $oldRole = $user->roles->first()?->name;
        $oldValues = [
            'name' => $user->name,
            'email' => $user->email,
            'phone' => $user->phone,
            'role' => $oldRole,
        ];

        // Guard against removing the last superuser
        if ($user->hasRole('Superuser') && $validated['role'] !== 'Superuser') {
            if (User::role('Superuser')->count() <= 1) {
                return back()->with('error', 'Tidak dapat mengubah peran: harus ada minimal 1 akun Superuser di sistem.');
            }
        }

        $user->name = $validated['name'];
        $user->email = $validated['email'];
        $user->phone = $validated['phone'] ?? null;

        if (! empty($validated['password'])) {
            $user->password = Hash::make($validated['password']);
        }

        $user->save();
        $user->syncRoles([$validated['role']]);

        AuditLog::log(
            action: 'update_user',
            entityType: 'User',
            entityId: $user->id,
            oldValues: $oldValues,
            newValues: [
                'name' => $user->name,
                'email' => $user->email,
                'phone' => $user->phone,
                'role' => $validated['role'],
                'password_changed' => ! empty($validated['password']),
            ],
            description: "Memperbarui data pengguna: {$user->name} ({$user->email})"
        );

        return redirect()->route('users.index')->with('success', "Data pengguna {$user->name} berhasil diperbarui!");
    }

    /**
     * Remove the specified user from storage.
     */
    public function destroy(Request $request, User $user): RedirectResponse
    {
        $this->authorizeAdmin();

        // Cannot delete self
        if ($user->id === auth()->id()) {
            return back()->with('error', 'Anda tidak dapat menghapus akun Anda sendiri yang sedang aktif.');
        }

        // Cannot delete last superuser
        if ($user->hasRole('Superuser') && User::role('Superuser')->count() <= 1) {
            return back()->with('error', 'Tidak dapat menghapus akun Superuser terakhir dalam sistem.');
        }

        $name = $user->name;
        $email = $user->email;
        $role = $user->roles->first()?->name;

        $user->delete();

        AuditLog::log(
            action: 'delete_user',
            entityType: 'User',
            entityId: $user->id,
            oldValues: ['name' => $name, 'email' => $email, 'role' => $role],
            newValues: null,
            description: "Menghapus akun pengguna: {$name} ({$email})"
        );

        return redirect()->route('users.index')->with('success', "Akun pengguna {$name} telah berhasil dihapus.");
    }

    /**
     * Quick reset password to default.
     */
    public function resetPassword(Request $request, User $user): RedirectResponse
    {
        $this->authorizeAdmin();

        $defaultPassword = 'password123';
        $user->password = Hash::make($defaultPassword);
        $user->save();

        AuditLog::log(
            action: 'reset_user_password',
            entityType: 'User',
            entityId: $user->id,
            oldValues: null,
            newValues: null,
            description: "Mereset kata sandi pengguna: {$user->name} ({$user->email}) ke standar default"
        );

        return back()->with('success', "Kata sandi untuk {$user->name} berhasil direset menjadi: {$defaultPassword}");
    }

    /**
     * Check if authenticated user has privilege to manage users.
     */
    private function authorizeAdmin(): void
    {
        $user = auth()->user();
        if (! $user || (! $user->hasAnyRole(['Superuser', 'Owner']) && ! $user->can('manage users'))) {
            abort(403, 'Anda tidak memiliki hak akses untuk mengelola akun pengguna.');
        }
    }
}
