<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Support\WhatsAppHelper;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class CustomerController extends Controller
{
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
        $query = Customer::withCount('orders')->latest();

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

        $customers = $query->paginate(15)->withQueryString();

        return view('customers.index', compact('customers'));
    }

    /**
     * Show the form for creating a new customer.
     */
    public function create(): View
    {
        $provinces = self::PROVINCES;
        $categories = self::CATEGORIES;

        return view('customers.create', compact('provinces', 'categories'));
    }

    /**
     * Store a newly created customer in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'full_name' => ['nullable', 'string', 'max:255'],
            'birth_date' => ['nullable', 'date'],
            'phone' => ['required', 'string', 'max:50'],
            'email' => ['nullable', 'email', 'max:255'],
            'organization' => ['nullable', 'string', 'max:255'],
            'category' => ['nullable', 'string', 'max:100'],
            'address' => ['nullable', 'string'],
            'province' => ['nullable', 'string', 'max:100'],
            'city' => ['nullable', 'string', 'max:100'],
            'pic_secondary_name' => ['nullable', 'string', 'max:255'],
            'pic_secondary_phone' => ['nullable', 'string', 'max:50'],
            'pic_secondary_relation' => ['nullable', 'string', 'max:100'],
            'registration_date' => ['nullable', 'date'],
            'notes' => ['nullable', 'string'],
        ]);

        $validated['phone'] = WhatsAppHelper::normalizeIndonesianPhone($validated['phone'], true) ?? $validated['phone'];
        if (! empty($validated['pic_secondary_phone'])) {
            $validated['pic_secondary_phone'] = WhatsAppHelper::normalizeIndonesianPhone($validated['pic_secondary_phone'], true) ?? $validated['pic_secondary_phone'];
        }

        $nextNum = Customer::count() + 1;
        do {
            $code = 'CUST-'.str_pad((string) $nextNum, 4, '0', STR_PAD_LEFT);
            $nextNum++;
        } while (Customer::where('customer_code', $code)->exists());

        Customer::create([
            'customer_code' => $code,
            'name' => $validated['name'],
            'full_name' => $validated['full_name'] ?? null,
            'birth_date' => $validated['birth_date'] ?? null,
            'phone' => $validated['phone'],
            'email' => $validated['email'] ?? null,
            'organization' => $validated['organization'] ?? null,
            'category' => $validated['category'] ?? 'Event Organizer (EO)',
            'address' => $validated['address'] ?? null,
            'province' => $validated['province'] ?? null,
            'city' => $validated['city'] ?? null,
            'pic_secondary_name' => $validated['pic_secondary_name'] ?? null,
            'pic_secondary_phone' => $validated['pic_secondary_phone'] ?? null,
            'pic_secondary_relation' => $validated['pic_secondary_relation'] ?? null,
            'registration_date' => $validated['registration_date'] ?? now()->toDateString(),
            'notes' => $validated['notes'] ?? null,
            'created_by' => Auth::id(),
        ]);

        return redirect()->route('customers.index')
            ->with('success', "Customer {$validated['name']} ({$code}) berhasil ditambahkan.");
    }

    /**
     * Show the form for editing the specified customer.
     */
    public function edit(Customer $customer): View
    {
        $provinces = self::PROVINCES;
        $categories = self::CATEGORIES;

        return view('customers.edit', compact('customer', 'provinces', 'categories'));
    }

    /**
     * Update the specified customer in storage.
     */
    public function update(Request $request, Customer $customer): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'full_name' => ['nullable', 'string', 'max:255'],
            'birth_date' => ['nullable', 'date'],
            'phone' => ['required', 'string', 'max:50'],
            'email' => ['nullable', 'email', 'max:255'],
            'organization' => ['nullable', 'string', 'max:255'],
            'category' => ['nullable', 'string', 'max:100'],
            'address' => ['nullable', 'string'],
            'province' => ['nullable', 'string', 'max:100'],
            'city' => ['nullable', 'string', 'max:100'],
            'pic_secondary_name' => ['nullable', 'string', 'max:255'],
            'pic_secondary_phone' => ['nullable', 'string', 'max:50'],
            'pic_secondary_relation' => ['nullable', 'string', 'max:100'],
            'registration_date' => ['nullable', 'date'],
            'notes' => ['nullable', 'string'],
        ]);

        $validated['phone'] = WhatsAppHelper::normalizeIndonesianPhone($validated['phone'], true) ?? $validated['phone'];
        if (! empty($validated['pic_secondary_phone'])) {
            $validated['pic_secondary_phone'] = WhatsAppHelper::normalizeIndonesianPhone($validated['pic_secondary_phone'], true) ?? $validated['pic_secondary_phone'];
        }

        $customer->update($validated);

        return redirect()->route('customers.index')
            ->with('success', "Data customer {$customer->name} berhasil diperbarui.");
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

        $customerName = $customer->name;
        $customer->delete();

        return redirect()->route('customers.index')
            ->with('success', "Customer {$customerName} berhasil dihapus.");
    }
}
