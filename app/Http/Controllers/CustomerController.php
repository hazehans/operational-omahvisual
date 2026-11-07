<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Support\WhatsAppHelper;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class CustomerController extends Controller
{
    public const SALUTATIONS = [
        'Bapak',
        'Ibu',
        'Kak',
        'Mas',
        'Mbak',
        'Dek',
    ];

    public const PROVINCES = [
        'Aceh', 'Sumatera Utara', 'Sumatera Barat', 'Riau', 'Kepulauan Riau', 'Jambi',
        'Sumatera Selatan', 'Kepulauan Bangka Belitung', 'Bengkulu', 'Lampung',
        'DKI Jakarta', 'Banten', 'Jawa Barat', 'Jawa Tengah', 'DI Yogyakarta', 'Jawa Timur',
        'Bali', 'Nusa Tenggara Barat', 'Nusa Tenggara Timur',
        'Kalimantan Barat', 'Kalimantan Tengah', 'Kalimantan Selatan', 'Kalimantan Timur', 'Kalimantan Utara',
        'Sulawesi Utara', 'Gorontalo', 'Sulawesi Tengah', 'Sulawesi Barat', 'Sulawesi Selatan', 'Sulawesi Tenggara',
        'Maluku', 'Maluku Utara', 'Papua', 'Papua Barat', 'Papua Selatan', 'Papua Tengah', 'Papua Pegunungan', 'Papua Barat Daya',
    ];

    public const CATEGORIES = [
        'Event Organizer (EO)',
        'Wedding Organizer (WO)',
        'Corporate / Perusahaan',
        'Pemerintahan / BUMN',
        'Vendor Rental / Sound',
        'Lembaga Pendidikan / Kampus',
        'Individu / Pribadi',
        'Lainnya',
    ];

    /**
     * Display a listing of the customers.
     */
    public function index(Request $request): View
    {
        $query = Customer::withCount('orders')->with('verifier')->latest();

        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('full_name', 'like', "%{$search}%")
                    ->orWhere('organization', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('customer_code', 'like', "%{$search}%")
                    ->orWhere('city', 'like', "%{$search}%")
                    ->orWhere('province', 'like', "%{$search}%");
            });
        }

        if ($status = $request->input('status')) {
            if ($status === 'verified') {
                $query->where('verification_status', 'verified');
            } elseif ($status === 'pending') {
                $query->where('verification_status', 'pending_verification');
            }
        }

        $totalCustomers = Customer::count();
        $verifiedCount = Customer::where('verification_status', 'verified')->count();
        $pendingCount = Customer::where('verification_status', 'pending_verification')->count();

        $customers = $query->paginate(15)->withQueryString();

        return view('customers.index', compact('customers', 'totalCustomers', 'verifiedCount', 'pendingCount'));
    }

    /**
     * Show the form for creating a new customer.
     */
    public function create(): View
    {
        $salutations = self::SALUTATIONS;
        $provinces = self::PROVINCES;
        $categories = self::CATEGORIES;

        return view('customers.create', compact('salutations', 'provinces', 'categories'));
    }

    /**
     * Store a newly created customer in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'salutation' => ['nullable', 'string', 'max:20'],
            'name' => ['required', 'string', 'max:255'],
            'full_name' => ['nullable', 'string', 'max:255'],
            'birth_date' => ['nullable', 'date'],
            'phone' => ['required', 'string', 'max:50'],
            'email' => ['nullable', 'email', 'max:255'],
            'organization' => ['nullable', 'string', 'max:255'],
            'category' => ['nullable', 'string', 'max:100'],
            'photo' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:4096'],
            'address' => ['nullable', 'string'],
            'province' => ['nullable', 'string', 'max:100'],
            'city' => ['nullable', 'string', 'max:100'],
            'pic_secondary_name' => ['nullable', 'string', 'max:255'],
            'pic_secondary_phone' => ['nullable', 'string', 'max:50'],
            'pic_secondary_relation' => ['nullable', 'string', 'max:100'],
            'registration_date' => ['nullable', 'date'],
            'verification_status' => ['nullable', 'in:pending_verification,verified'],
            'notes' => ['nullable', 'string'],
        ]);

        $validated['phone'] = WhatsAppHelper::normalizePhone($validated['phone'], null, true) ?? $validated['phone'];
        if (! empty($validated['pic_secondary_phone'])) {
            $validated['pic_secondary_phone'] = WhatsAppHelper::normalizePhone($validated['pic_secondary_phone'], null, true) ?? $validated['pic_secondary_phone'];
        }

        $photoPath = null;
        if ($request->hasFile('photo')) {
            $photoPath = $request->file('photo')->store('customers', 'public');
        }

        $nextNum = Customer::count() + 1;
        do {
            $code = 'CUST-'.str_pad((string) $nextNum, 4, '0', STR_PAD_LEFT);
            $nextNum++;
        } while (Customer::where('customer_code', $code)->exists());

        $isVerified = ($validated['verification_status'] ?? 'verified') === 'verified';

        $customer = Customer::create([
            'customer_code' => $code,
            'salutation' => $validated['salutation'] ?? null,
            'name' => $validated['name'],
            'full_name' => $validated['full_name'] ?? null,
            'birth_date' => $validated['birth_date'] ?? null,
            'phone' => $validated['phone'],
            'email' => $validated['email'] ?? null,
            'organization' => $validated['organization'] ?? null,
            'category' => $validated['category'] ?? 'Event Organizer (EO)',
            'photo_path' => $photoPath,
            'address' => $validated['address'] ?? null,
            'province' => $validated['province'] ?? null,
            'city' => $validated['city'] ?? null,
            'pic_secondary_name' => $validated['pic_secondary_name'] ?? null,
            'pic_secondary_phone' => $validated['pic_secondary_phone'] ?? null,
            'pic_secondary_relation' => $validated['pic_secondary_relation'] ?? null,
            'registration_date' => $validated['registration_date'] ?? now()->toDateString(),
            'verification_status' => $isVerified ? 'verified' : 'pending_verification',
            'verified_at' => $isVerified ? now() : null,
            'verified_by' => $isVerified ? Auth::id() : null,
            'notes' => $validated['notes'] ?? null,
            'created_by' => Auth::id(),
        ]);

        return redirect()->route('customers.index')
            ->with('success', "Customer {$customer->name} ({$code}) berhasil didaftarkan.");
    }

    /**
     * Display the specified customer.
     */
    public function show(Customer $customer): View
    {
        $customer->load(['orders' => function ($q) {
            $q->latest()->limit(10);
        }, 'verifier', 'creator']);

        return view('customers.show', compact('customer'));
    }

    /**
     * Show the form for editing the specified customer.
     */
    public function edit(Customer $customer): View
    {
        $salutations = self::SALUTATIONS;
        $provinces = self::PROVINCES;
        $categories = self::CATEGORIES;

        return view('customers.edit', compact('customer', 'salutations', 'provinces', 'categories'));
    }

    /**
     * Update the specified customer in storage.
     */
    public function update(Request $request, Customer $customer): RedirectResponse
    {
        $validated = $request->validate([
            'salutation' => ['nullable', 'string', 'max:20'],
            'name' => ['required', 'string', 'max:255'],
            'full_name' => ['nullable', 'string', 'max:255'],
            'birth_date' => ['nullable', 'date'],
            'phone' => ['required', 'string', 'max:50'],
            'email' => ['nullable', 'email', 'max:255'],
            'organization' => ['nullable', 'string', 'max:255'],
            'category' => ['nullable', 'string', 'max:100'],
            'photo' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:4096'],
            'remove_photo' => ['nullable', 'boolean'],
            'address' => ['nullable', 'string'],
            'province' => ['nullable', 'string', 'max:100'],
            'city' => ['nullable', 'string', 'max:100'],
            'pic_secondary_name' => ['nullable', 'string', 'max:255'],
            'pic_secondary_phone' => ['nullable', 'string', 'max:50'],
            'pic_secondary_relation' => ['nullable', 'string', 'max:100'],
            'registration_date' => ['nullable', 'date'],
            'verification_status' => ['nullable', 'in:pending_verification,verified'],
            'notes' => ['nullable', 'string'],
        ]);

        $validated['phone'] = WhatsAppHelper::normalizePhone($validated['phone'], null, true) ?? $validated['phone'];
        if (! empty($validated['pic_secondary_phone'])) {
            $validated['pic_secondary_phone'] = WhatsAppHelper::normalizePhone($validated['pic_secondary_phone'], null, true) ?? $validated['pic_secondary_phone'];
        }

        if ($request->boolean('remove_photo')) {
            if ($customer->photo_path) {
                Storage::disk('public')->delete($customer->photo_path);
            }
            $validated['photo_path'] = null;
        }

        if ($request->hasFile('photo')) {
            if ($customer->photo_path) {
                Storage::disk('public')->delete($customer->photo_path);
            }
            $validated['photo_path'] = $request->file('photo')->store('customers', 'public');
        }

        unset($validated['photo'], $validated['remove_photo']);

        if (isset($validated['verification_status'])) {
            if ($validated['verification_status'] === 'verified' && $customer->verification_status !== 'verified') {
                $validated['verified_at'] = now();
                $validated['verified_by'] = Auth::id();
            } elseif ($validated['verification_status'] === 'pending_verification') {
                $validated['verified_at'] = null;
                $validated['verified_by'] = null;
            }
        }

        $customer->update($validated);

        return redirect()->route('customers.index')
            ->with('success', "Data customer {$customer->name} berhasil diperbarui.");
    }

    /**
     * Quick verification by company admin.
     */
    public function verify(Customer $customer): RedirectResponse
    {
        $customer->update([
            'verification_status' => 'verified',
            'verified_at' => now(),
            'verified_by' => Auth::id(),
        ]);

        return redirect()->back()
            ->with('success', "Status customer {$customer->name} berhasil diverifikasi oleh perusahaan.");
    }

    /**
     * Remove the specified customer from storage.
     */
    public function destroy(Customer $customer): RedirectResponse
    {
        if ($customer->orders()->count() > 0) {
            return redirect()->route('customers.index')
                ->with('error', "Customer {$customer->name} tidak dapat dihapus karena memiliki riwayat {$customer->orders()->count()} pesanan event.");
        }

        if ($customer->photo_path) {
            Storage::disk('public')->delete($customer->photo_path);
        }

        $customerName = $customer->name;
        $customer->delete();

        return redirect()->route('customers.index')
            ->with('success', "Customer {$customerName} berhasil dihapus.");
    }

    /**
     * Public self-registration form for clients.
     */
    public function showPublicRegisterForm(): View
    {
        $salutations = self::SALUTATIONS;
        $provinces = self::PROVINCES;
        $categories = self::CATEGORIES;

        return view('customers.register_public', compact('salutations', 'provinces', 'categories'));
    }

    /**
     * Store self-registered client data from public form.
     */
    public function storePublicRegister(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'salutation' => ['required', 'string', 'max:20'],
            'name' => ['required', 'string', 'max:255'],
            'full_name' => ['nullable', 'string', 'max:255'],
            'phone' => ['required', 'string', 'max:50'],
            'email' => ['nullable', 'email', 'max:255'],
            'organization' => ['nullable', 'string', 'max:255'],
            'category' => ['nullable', 'string', 'max:100'],
            'photo' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:4096'],
            'address' => ['nullable', 'string'],
            'province' => ['nullable', 'string', 'max:100'],
            'city' => ['nullable', 'string', 'max:100'],
            'pic_secondary_name' => ['nullable', 'string', 'max:255'],
            'pic_secondary_phone' => ['nullable', 'string', 'max:50'],
            'pic_secondary_relation' => ['nullable', 'string', 'max:100'],
            'notes' => ['nullable', 'string'],
        ]);

        $validated['phone'] = WhatsAppHelper::normalizePhone($validated['phone'], null, true) ?? $validated['phone'];
        if (! empty($validated['pic_secondary_phone'])) {
            $validated['pic_secondary_phone'] = WhatsAppHelper::normalizePhone($validated['pic_secondary_phone'], null, true) ?? $validated['pic_secondary_phone'];
        }

        $photoPath = null;
        if ($request->hasFile('photo')) {
            $photoPath = $request->file('photo')->store('customers', 'public');
        }

        $nextNum = Customer::count() + 1;
        do {
            $code = 'CUST-'.str_pad((string) $nextNum, 4, '0', STR_PAD_LEFT);
            $nextNum++;
        } while (Customer::where('customer_code', $code)->exists());

        $customer = Customer::create([
            'customer_code' => $code,
            'salutation' => $validated['salutation'],
            'name' => $validated['name'],
            'full_name' => $validated['full_name'] ?? null,
            'phone' => $validated['phone'],
            'email' => $validated['email'] ?? null,
            'organization' => $validated['organization'] ?? null,
            'category' => $validated['category'] ?? 'Event Organizer (EO)',
            'photo_path' => $photoPath,
            'address' => $validated['address'] ?? null,
            'province' => $validated['province'] ?? null,
            'city' => $validated['city'] ?? null,
            'pic_secondary_name' => $validated['pic_secondary_name'] ?? null,
            'pic_secondary_phone' => $validated['pic_secondary_phone'] ?? null,
            'pic_secondary_relation' => $validated['pic_secondary_relation'] ?? null,
            'registration_date' => now()->toDateString(),
            'verification_status' => 'pending_verification',
            'notes' => $validated['notes'] ?? 'Pendaftaran mandiri via form online client.',
        ]);

        return redirect()->route('customers.public-register.success', ['code' => $customer->customer_code])
            ->with('registered_name', $customer->salutation.' '.$customer->name);
    }

    /**
     * Confirmation view for successful self-registration.
     */
    public function publicRegisterSuccess(Request $request): View
    {
        $code = $request->query('code');
        $customer = Customer::where('customer_code', $code)->first();

        return view('customers.register_success', compact('customer'));
    }
}
