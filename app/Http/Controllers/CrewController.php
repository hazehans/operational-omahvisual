<?php

namespace App\Http\Controllers;

use App\Models\Crew;
use App\Models\User;
use App\Support\WhatsAppHelper;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class CrewController extends Controller
{
    public const AVAILABLE_POSITIONS = [
        'PIC' => 'PIC Lapangan / Lead Technician',
        'Crew' => 'Crew Lapangan / Rigger',
        'Supir' => 'Supir / Driver Armada',
        'Operator' => 'Operator Layar (Novastar / VJ)',
    ];

    public const EMPLOYMENT_TYPES = [
        'freelance_outsourcing' => 'Freelance / Outsourcing',
        'karyawan_tetap' => 'Karyawan Internal / Tetap',
        'magang' => 'Anak Magang / Intern',
    ];

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
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('position', 'like', "%{$search}%")
                    ->orWhere('crew_code', 'like', "%{$search}%");
            });
        }

        if ($request->has('active_only') && $request->input('active_only') === '1') {
            $query->where('is_active', true);
        }

        if ($empType = $request->input('employment_type')) {
            $query->where('employment_type', $empType);
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
        $availablePositions = self::AVAILABLE_POSITIONS;
        $employmentTypes = self::EMPLOYMENT_TYPES;

        return view('crews.create', compact('users', 'availablePositions', 'employmentTypes'));
    }

    /**
     * Store a newly created crew in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'nickname' => ['required', 'string', 'max:100'],
            'full_name' => ['required', 'string', 'max:255'],
            'birth_date' => ['nullable', 'date'],
            'email' => ['nullable', 'email', 'max:255'],
            'phone' => ['required', 'string', 'max:50'],
            'emergency_phone' => ['nullable', 'string', 'max:50'],
            'positions' => ['nullable', 'array'],
            'positions.*' => ['string'],
            'employment_type' => ['required', 'string', 'in:freelance_outsourcing,karyawan_tetap,magang'],
            'hris_employee_id' => ['nullable', 'string', 'max:100'],
            'join_date' => ['nullable', 'date'],
            'address' => ['nullable', 'string'],
            'city' => ['nullable', 'string', 'max:100'],
            'is_active' => ['nullable', 'boolean'],
            'user_id' => ['nullable', 'exists:users,id'],
            'default_fee_loading_in' => ['nullable', 'numeric', 'min:0'],
            'default_fee_loading_out' => ['nullable', 'numeric', 'min:0'],
            'default_fee_meal' => ['nullable', 'numeric', 'min:0'],
            'default_fee_standby' => ['nullable', 'numeric', 'min:0'],
            'bank_name' => ['nullable', 'string', 'max:100'],
            'bank_account_number' => ['nullable', 'string', 'max:100'],
            'bank_account_holder' => ['nullable', 'string', 'max:150'],
            'photo' => ['nullable', 'image', 'max:5120'],
            'ktp_photo' => ['nullable', 'image', 'max:5120'],
            'rate_card_photo' => ['nullable', 'image', 'max:5120'],
        ]);

        $nextNum = Crew::count() + 1;
        do {
            $code = 'CRW-'.str_pad((string) $nextNum, 4, '0', STR_PAD_LEFT);
            $nextNum++;
        } while (Crew::where('crew_code', $code)->exists());

        // Upload photos
        $photoPath = $request->hasFile('photo') ? $request->file('photo')->store('crews/photos', 'public') : null;
        $ktpPath = $request->hasFile('ktp_photo') ? $request->file('ktp_photo')->store('crews/ktp', 'public') : null;
        $rateCardPath = $request->hasFile('rate_card_photo') ? $request->file('rate_card_photo')->store('crews/rates', 'public') : null;

        $positions = $request->input('positions', ['Crew']);
        if (empty($positions)) {
            $positions = ['Crew'];
        }

        $normalizedPhone = WhatsAppHelper::normalizePhone($validated['phone'], null, true) ?? $validated['phone'];
        $normalizedEmergencyPhone = ! empty($validated['emergency_phone'])
            ? (WhatsAppHelper::normalizePhone($validated['emergency_phone'], null, true) ?? $validated['emergency_phone'])
            : null;

        Crew::create([
            'crew_code' => $code,
            'nickname' => $validated['nickname'],
            'full_name' => $validated['full_name'],
            'birth_date' => $validated['birth_date'] ?? null,
            'email' => $validated['email'] ?? null,
            'phone' => $normalizedPhone,
            'emergency_phone' => $normalizedEmergencyPhone,
            'position' => implode(', ', $positions),
            'positions' => $positions,
            'employment_type' => $validated['employment_type'],
            'hris_employee_id' => $validated['hris_employee_id'] ?? null,
            'join_date' => $validated['join_date'] ?? now()->toDateString(),
            'default_fee_loading_in' => $validated['default_fee_loading_in'] ?? null,
            'default_fee_loading_out' => $validated['default_fee_loading_out'] ?? null,
            'default_fee_meal' => $validated['default_fee_meal'] ?? null,
            'default_fee_standby' => $validated['default_fee_standby'] ?? null,
            'bank_name' => $validated['bank_name'] ?? null,
            'bank_account_number' => $validated['bank_account_number'] ?? null,
            'bank_account_holder' => $validated['bank_account_holder'] ?? null,
            'address' => $validated['address'] ?? null,
            'city' => $validated['city'] ?? null,
            'photo_path' => $photoPath,
            'ktp_photo_path' => $ktpPath,
            'rate_card_photo_path' => $rateCardPath,
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
        $availablePositions = self::AVAILABLE_POSITIONS;
        $employmentTypes = self::EMPLOYMENT_TYPES;

        return view('crews.edit', compact('crew', 'users', 'availablePositions', 'employmentTypes'));
    }

    /**
     * Update the specified crew in storage.
     */
    public function update(Request $request, Crew $crew): RedirectResponse
    {
        $validated = $request->validate([
            'nickname' => ['required', 'string', 'max:100'],
            'full_name' => ['required', 'string', 'max:255'],
            'birth_date' => ['nullable', 'date'],
            'email' => ['nullable', 'email', 'max:255'],
            'phone' => ['required', 'string', 'max:50'],
            'emergency_phone' => ['nullable', 'string', 'max:50'],
            'positions' => ['nullable', 'array'],
            'positions.*' => ['string'],
            'employment_type' => ['required', 'string', 'in:freelance_outsourcing,karyawan_tetap,magang'],
            'hris_employee_id' => ['nullable', 'string', 'max:100'],
            'join_date' => ['nullable', 'date'],
            'address' => ['nullable', 'string'],
            'city' => ['nullable', 'string', 'max:100'],
            'is_active' => ['nullable', 'boolean'],
            'user_id' => ['nullable', 'exists:users,id'],
            'default_fee_loading_in' => ['nullable', 'numeric', 'min:0'],
            'default_fee_loading_out' => ['nullable', 'numeric', 'min:0'],
            'default_fee_meal' => ['nullable', 'numeric', 'min:0'],
            'default_fee_standby' => ['nullable', 'numeric', 'min:0'],
            'bank_name' => ['nullable', 'string', 'max:100'],
            'bank_account_number' => ['nullable', 'string', 'max:100'],
            'bank_account_holder' => ['nullable', 'string', 'max:150'],
            'photo' => ['nullable', 'image', 'max:5120'],
            'ktp_photo' => ['nullable', 'image', 'max:5120'],
            'rate_card_photo' => ['nullable', 'image', 'max:5120'],
        ]);

        $normalizedPhone = WhatsAppHelper::normalizePhone($validated['phone'], null, true) ?? $validated['phone'];
        $normalizedEmergencyPhone = ! empty($validated['emergency_phone'])
            ? (WhatsAppHelper::normalizePhone($validated['emergency_phone'], null, true) ?? $validated['emergency_phone'])
            : null;

        $updateData = [
            'nickname' => $validated['nickname'],
            'full_name' => $validated['full_name'],
            'birth_date' => $validated['birth_date'] ?? null,
            'email' => $validated['email'] ?? null,
            'phone' => $normalizedPhone,
            'emergency_phone' => $normalizedEmergencyPhone,
            'employment_type' => $validated['employment_type'],
            'hris_employee_id' => $validated['hris_employee_id'] ?? null,
            'join_date' => $validated['join_date'] ?? null,
            'default_fee_loading_in' => $validated['default_fee_loading_in'] ?? null,
            'default_fee_loading_out' => $validated['default_fee_loading_out'] ?? null,
            'default_fee_meal' => $validated['default_fee_meal'] ?? null,
            'default_fee_standby' => $validated['default_fee_standby'] ?? null,
            'bank_name' => $validated['bank_name'] ?? null,
            'bank_account_number' => $validated['bank_account_number'] ?? null,
            'bank_account_holder' => $validated['bank_account_holder'] ?? null,
            'address' => $validated['address'] ?? null,
            'city' => $validated['city'] ?? null,
            'is_active' => $request->boolean('is_active'),
            'user_id' => $validated['user_id'] ?? null,
        ];

        if ($request->has('positions')) {
            $positions = $request->input('positions', []);
            $updateData['positions'] = $positions;
            $updateData['position'] = implode(', ', $positions);
        }

        // Handle Foto Profil
        if ($request->boolean('remove_photo')) {
            if ($crew->photo_path) {
                Storage::disk('public')->delete($crew->photo_path);
            }
            $updateData['photo_path'] = null;
        } elseif ($request->hasFile('photo')) {
            if ($crew->photo_path) {
                Storage::disk('public')->delete($crew->photo_path);
            }
            $updateData['photo_path'] = $request->file('photo')->store('crews/photos', 'public');
        }

        // Handle Foto KTP
        if ($request->boolean('remove_ktp_photo')) {
            if ($crew->ktp_photo_path) {
                Storage::disk('public')->delete($crew->ktp_photo_path);
            }
            $updateData['ktp_photo_path'] = null;
        } elseif ($request->hasFile('ktp_photo')) {
            if ($crew->ktp_photo_path) {
                Storage::disk('public')->delete($crew->ktp_photo_path);
            }
            $updateData['ktp_photo_path'] = $request->file('ktp_photo')->store('crews/ktp', 'public');
        }

        // Handle Foto Rate Card
        if ($request->boolean('remove_rate_card_photo')) {
            if ($crew->rate_card_photo_path) {
                Storage::disk('public')->delete($crew->rate_card_photo_path);
            }
            $updateData['rate_card_photo_path'] = null;
        } elseif ($request->hasFile('rate_card_photo')) {
            if ($crew->rate_card_photo_path) {
                Storage::disk('public')->delete($crew->rate_card_photo_path);
            }
            $updateData['rate_card_photo_path'] = $request->file('rate_card_photo')->store('crews/rates', 'public');
        }

        $crew->update($updateData);

        return redirect()->route('crews.index')
            ->with('success', "Data kru {$crew->nickname} berhasil diperbarui.");
    }

    /**
     * Remove the specified crew from storage.
     */
    public function destroy(Crew $crew): RedirectResponse
    {
        $name = $crew->nickname;

        if ($crew->photo_path) {
            Storage::disk('public')->delete($crew->photo_path);
        }
        if ($crew->ktp_photo_path) {
            Storage::disk('public')->delete($crew->ktp_photo_path);
        }
        if ($crew->rate_card_photo_path) {
            Storage::disk('public')->delete($crew->rate_card_photo_path);
        }

        $crew->delete();

        return redirect()->route('crews.index')
            ->with('success', "Kru {$name} berhasil dihapus.");
    }
}
