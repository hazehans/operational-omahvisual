<?php

namespace App\Http\Controllers;

use App\Models\Crew;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CrewController extends Controller
{
    /**
     * Display a listing of crews.
     */
    public function index(Request $request): View
    {
        $query = Crew::with('user')->latest();

        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('nickname', 'like', "%{$search}%")
                    ->orWhere('full_name', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%")
                    ->orWhere('position', 'like', "%{$search}%")
                    ->orWhere('crew_code', 'like', "%{$search}%");
            });
        }

        if ($request->has('active_only') && $request->input('active_only') === '1') {
            $query->where('is_active', true);
        }

        $crews = $query->paginate(15)->withQueryString();

        return view('crews.index', compact('crews'));
    }

    /**
     * Show the form for creating a new crew.
     */
    public function create(): View
    {
        $users = User::orderBy('name')->get();

        return view('crews.create', compact('users'));
    }

    /**
     * Store a newly created crew in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'nickname' => ['required', 'string', 'max:100'],
            'full_name' => ['required', 'string', 'max:255'],
            'phone' => ['required', 'string', 'max:50'],
            'emergency_phone' => ['nullable', 'string', 'max:50'],
            'position' => ['required', 'string', 'max:100'],
            'address' => ['nullable', 'string'],
            'city' => ['nullable', 'string', 'max:100'],
            'is_active' => ['nullable', 'boolean'],
            'user_id' => ['nullable', 'exists:users,id'],
        ]);

        $code = 'CREW-'.str_pad((string) (Crew::count() + 1), 3, '0', STR_PAD_LEFT);

        Crew::create([
            'crew_code' => $code,
            'nickname' => $validated['nickname'],
            'full_name' => $validated['full_name'],
            'phone' => $validated['phone'],
            'emergency_phone' => $validated['emergency_phone'] ?? null,
            'position' => $validated['position'],
            'address' => $validated['address'] ?? null,
            'city' => $validated['city'] ?? null,
            'is_active' => $request->boolean('is_active', true),
            'user_id' => $validated['user_id'] ?? null,
        ]);

        return redirect()->route('crews.index')
            ->with('success', "Kru {$validated['nickname']} ({$code}) berhasil ditambahkan.");
    }

    /**
     * Show the form for editing the specified crew.
     */
    public function edit(Crew $crew): View
    {
        $users = User::orderBy('name')->get();

        return view('crews.edit', compact('crew', 'users'));
    }

    /**
     * Update the specified crew in storage.
     */
    public function update(Request $request, Crew $crew): RedirectResponse
    {
        $validated = $request->validate([
            'nickname' => ['required', 'string', 'max:100'],
            'full_name' => ['required', 'string', 'max:255'],
            'phone' => ['required', 'string', 'max:50'],
            'emergency_phone' => ['nullable', 'string', 'max:50'],
            'position' => ['required', 'string', 'max:100'],
            'address' => ['nullable', 'string'],
            'city' => ['nullable', 'string', 'max:100'],
            'is_active' => ['nullable', 'boolean'],
            'user_id' => ['nullable', 'exists:users,id'],
        ]);

        $validated['is_active'] = $request->boolean('is_active');

        $crew->update($validated);

        return redirect()->route('crews.index')
            ->with('success', "Data kru {$crew->nickname} berhasil diperbarui.");
    }

    /**
     * Remove the specified crew from storage.
     */
    public function destroy(Crew $crew): RedirectResponse
    {
        $name = $crew->nickname;
        $crew->delete();

        return redirect()->route('crews.index')
            ->with('success', "Kru {$name} berhasil dihapus.");
    }
}
