<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rules\Password;
use Illuminate\View\View;

class ProfileController extends Controller
{
    /**
     * Show the user profile settings page.
     */
    public function edit(): View
    {
        $user = Auth::user();

        return view('profile.edit', compact('user'));
    }

    /**
     * Update user profile information and avatar.
     */
    public function update(Request $request): RedirectResponse
    {
        $user = Auth::user();

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:30'],
            'avatar' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:4096'],
            'remove_avatar' => ['nullable', 'boolean'],
        ]);

        if ($request->boolean('remove_avatar')) {
            if ($user->avatar_path) {
                Storage::disk('public')->delete($user->avatar_path);
            }
            $validated['avatar_path'] = null;
        }

        if ($request->hasFile('avatar')) {
            if ($user->avatar_path) {
                Storage::disk('public')->delete($user->avatar_path);
            }
            $validated['avatar_path'] = $request->file('avatar')->store('avatars', 'public');
        }

        unset($validated['avatar'], $validated['remove_avatar']);

        $oldValues = [
            'name' => $user->name,
            'phone' => $user->phone,
            'avatar_path' => $user->avatar_path,
        ];

        $user->update($validated);

        AuditLog::log(
            action: 'update_profile',
            entityType: 'User',
            entityId: $user->id,
            oldValues: $oldValues,
            newValues: $validated,
            description: 'Pengguna '.$user->name.' memperbarui informasi profil dan avatar.'
        );

        return back()->with('success', 'Profil dan foto avatar berhasil diperbarui.');
    }

    /**
     * Update user password.
     */
    public function updatePassword(Request $request): RedirectResponse
    {
        $user = Auth::user();

        $validated = $request->validate([
            'current_password' => ['required', 'current_password'],
            'password' => ['required', 'string', 'min:8', 'confirmed', Password::defaults()],
        ]);

        $user->update([
            'password' => Hash::make($validated['password']),
        ]);

        AuditLog::log(
            action: 'update_password',
            entityType: 'User',
            entityId: $user->id,
            description: 'Pengguna '.$user->name.' berhasil memperbarui kata sandi akun.'
        );

        return back()->with('success', 'Kata sandi berhasil diperbarui. Silakan gunakan kata sandi baru untuk login berikutnya.');
    }
}
