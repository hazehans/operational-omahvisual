<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\Category;
use App\Models\Product;
use App\Models\Unit;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class ProductController extends Controller
{
    /**
     * Display a listing of products.
     */
    public function index(Request $request): View
    {
        $query = Product::with(['category', 'unit', 'stockBalances.location'])
            ->withCount(['assets', 'stockBalances']);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'ILIKE', "%{$search}%")
                    ->orWhere('code', 'ILIKE', "%{$search}%")
                    ->orWhere('brand', 'ILIKE', "%{$search}%")
                    ->orWhere('model_series', 'ILIKE', "%{$search}%");
            });
        }

        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        if ($request->filled('tracking_type')) {
            $query->where('tracking_type', $request->tracking_type);
        }

        $products = $query->orderBy('name', 'asc')->paginate(15)->withQueryString();
        $categories = Category::where('is_active', true)->orderBy('name')->get();

        return view('products.index', compact('products', 'categories'));
    }

    /**
     * Show the form for creating a new product.
     */
    public function create(): View
    {
        $categories = Category::where('is_active', true)->orderBy('name')->get();
        $units = Unit::orderBy('name')->get();

        return view('products.create', compact('categories', 'units'));
    }

    /**
     * Store a newly created product in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'category_id' => ['required', 'exists:categories,id'],
            'unit_id' => ['required', 'exists:units,id'],
            'code' => ['required', 'string', 'max:50', 'unique:products,code'],
            'name' => ['required', 'string', 'max:255'],
            'brand' => ['nullable', 'string', 'max:100'],
            'model_series' => ['nullable', 'string', 'max:100'],
            'specification' => ['nullable', 'string', 'max:255'],
            'image' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:4096'],
            'tracking_type' => ['required', 'in:individual,quantity'],
            'sqm_per_unit' => ['nullable', 'numeric', 'min:0'],
            'min_stock' => ['nullable', 'numeric', 'min:0'],
            'notes' => ['nullable', 'string'],
        ]);

        $validated['is_active'] = true;

        if ($request->hasFile('image')) {
            $validated['image_path'] = $request->file('image')->store('products', 'public');
        }
        unset($validated['image']);

        $product = Product::create($validated);

        AuditLog::log(
            action: 'create_product',
            entityType: 'Product',
            entityId: $product->id,
            newValues: $validated,
            description: 'Menambahkan master barang baru: '.$product->name.' ('.$product->code.')'
        );

        return redirect()->route('products.show', $product)
            ->with('success', 'Master barang '.$product->name.' berhasil ditambahkan.');
    }

    /**
     * Display the specified product.
     */
    public function show(Product $product): View
    {
        $product->load(['category', 'unit', 'assets.currentLocation', 'stockBalances.location']);

        $totalStockAvailable = $product->stockBalances->where('condition', 'available')->sum('quantity');
        $totalStockDamaged = $product->stockBalances->where('condition', 'damaged')->sum('quantity');
        $totalStockInService = $product->stockBalances->where('condition', 'in_service')->sum('quantity');

        return view('products.show', compact(
            'product',
            'totalStockAvailable',
            'totalStockDamaged',
            'totalStockInService'
        ));
    }

    /**
     * Show the form for editing the specified product.
     */
    public function edit(Product $product): View
    {
        $categories = Category::where('is_active', true)->orderBy('name')->get();
        $units = Unit::orderBy('name')->get();

        return view('products.edit', compact('product', 'categories', 'units'));
    }

    /**
     * Update the specified product in storage.
     */
    public function update(Request $request, Product $product): RedirectResponse
    {
        $validated = $request->validate([
            'category_id' => ['required', 'exists:categories,id'],
            'unit_id' => ['required', 'exists:units,id'],
            'name' => ['required', 'string', 'max:255'],
            'brand' => ['nullable', 'string', 'max:100'],
            'model_series' => ['nullable', 'string', 'max:100'],
            'specification' => ['nullable', 'string', 'max:255'],
            'image' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:4096'],
            'remove_image' => ['nullable', 'boolean'],
            'sqm_per_unit' => ['nullable', 'numeric', 'min:0'],
            'min_stock' => ['nullable', 'numeric', 'min:0'],
            'is_active' => ['boolean'],
            'notes' => ['nullable', 'string'],
        ]);

        if ($request->boolean('remove_image')) {
            if ($product->image_path) {
                Storage::disk('public')->delete($product->image_path);
            }
            $validated['image_path'] = null;
        }

        if ($request->hasFile('image')) {
            if ($product->image_path) {
                Storage::disk('public')->delete($product->image_path);
            }
            $validated['image_path'] = $request->file('image')->store('products', 'public');
        }

        unset($validated['image'], $validated['remove_image']);

        $oldValues = $product->only(array_keys($validated));
        $product->update($validated);

        AuditLog::log(
            action: 'update_product',
            entityType: 'Product',
            entityId: $product->id,
            oldValues: $oldValues,
            newValues: $validated,
            description: 'Memperbarui data master barang: '.$product->name
        );

        return redirect()->route('products.show', $product)
            ->with('success', 'Data master barang berhasil diperbarui.');
    }
}
