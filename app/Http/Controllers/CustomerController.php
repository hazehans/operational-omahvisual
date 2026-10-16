<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class CustomerController extends Controller
{
    /**
     * Display a listing of the customers.
     */
    public function index(Request $request): View
    {
        $query = Customer::withCount('orders')->latest();

        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('organization', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%")
                    ->orWhere('customer_code', 'like', "%{$search}%")
                    ->orWhere('city', 'like', "%{$search}%");
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
        return view('customers.create');
    }

    /**
     * Store a newly created customer in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'phone' => ['required', 'string', 'max:50'],
            'organization' => ['nullable', 'string', 'max:255'],
            'address' => ['nullable', 'string'],
            'city' => ['nullable', 'string', 'max:100'],
            'notes' => ['nullable', 'string'],
        ]);

        $code = 'CUST-'.str_pad((string) (Customer::count() + 1), 4, '0', STR_PAD_LEFT);

        Customer::create([
            'customer_code' => $code,
            'name' => $validated['name'],
            'phone' => $validated['phone'],
            'organization' => $validated['organization'] ?? null,
            'address' => $validated['address'] ?? null,
            'city' => $validated['city'] ?? null,
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
        return view('customers.edit', compact('customer'));
    }

    /**
     * Update the specified customer in storage.
     */
    public function update(Request $request, Customer $customer): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'phone' => ['required', 'string', 'max:50'],
            'organization' => ['nullable', 'string', 'max:255'],
            'address' => ['nullable', 'string'],
            'city' => ['nullable', 'string', 'max:100'],
            'notes' => ['nullable', 'string'],
        ]);

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
