<?php

namespace App\Http\Controllers;

use App\Models\Asset;
use App\Models\AuditLog;
use App\Models\Category;
use App\Models\InventoryCountItem;
use App\Models\Product;
use App\Models\ServiceTicket;
use App\Models\StockBalance;
use App\Models\StockMovementItem;
use App\Models\Unit;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ProductController extends Controller
{
    /**
     * Display a listing of products.
     */
    public function index(Request $request): View
    {
        $query = Product::with(['category', 'unit', 'stockBalances.location', 'assets'])
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

        // Rekap ringkas ketersediaan untuk kartu KPI atas
        $totalSku = Product::count();
        $totalActiveSku = Product::where('is_active', true)->count();
        $allProducts = Product::with(['stockBalances', 'assets'])->get();

        $totalSqmReady = 0;
        foreach ($allProducts as $p) {
            /** @var Product $p */
            if ($p->sqm_per_unit) {
                $readyQty = $p->tracking_type === 'individual'
                    ? $p->assets->where('condition', 'available')->where('status', 'in_storage')->count()
                    : (float) $p->stockBalances->where('condition', 'available')->sum('quantity');
                $totalSqmReady += round($readyQty * (float) $p->sqm_per_unit, 2);
            }
        }

        return view('products.index', compact('products', 'categories', 'totalSku', 'totalActiveSku', 'totalSqmReady'));
    }

    /**
     * Export monthly / current inventory availability report to Excel-compatible CSV.
     */
    public function exportCsv(Request $request): StreamedResponse
    {
        abort_if(! auth()->user()->can('view inventory'), 403, 'Akses inventaris ditolak.');

        $query = Product::with(['category', 'unit', 'stockBalances', 'assets'])
            ->orderBy('category_id')
            ->orderBy('name');

        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        if ($request->filled('tracking_type')) {
            $query->where('tracking_type', $request->tracking_type);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'ILIKE', "%{$search}%")
                    ->orWhere('code', 'ILIKE', "%{$search}%")
                    ->orWhere('brand', 'ILIKE', "%{$search}%")
                    ->orWhere('model_series', 'ILIKE', "%{$search}%");
            });
        }

        $products = $query->get();
        $filename = 'laporan-ketersediaan-omahvisual-'.now()->format('Ymd-His').'.csv';

        return response()->stream(function () use ($products) {
            $handle = fopen('php://output', 'w');

            // UTF-8 BOM agar Excel di Windows/Mac otomatis mengenali encoding dengan tepat
            fwrite($handle, "\xEF\xBB\xBF");

            // Header kolom CSV
            fputcsv($handle, [
                'No',
                'Kode SKU',
                'Nama Produk / Perangkat',
                'Kategori',
                'Brand / Merk',
                'Model / Seri',
                'Mode Pelacakan',
                'Luas per Unit (m2)',
                'Siap Sewa (Available)',
                'Dalam Servis (In Service)',
                'Rusak (Damaged)',
                'Total Stok Fisik',
                'Total Luas Siap Sewa (m2)',
                'Satuan Dasar',
                'Status Produk',
            ]);

            $no = 1;
            foreach ($products as $prod) {
                /** @var Product $prod */
                if ($prod->tracking_type === 'individual') {
                    $ready = $prod->assets->where('condition', 'available')->where('status', 'in_storage')->count();
                    $service = $prod->assets->where('condition', 'in_service')->count();
                    $damaged = $prod->assets->where('condition', 'damaged')->count();
                } else {
                    $ready = (float) $prod->stockBalances->where('condition', 'available')->sum('quantity');
                    $service = (float) $prod->stockBalances->where('condition', 'in_service')->sum('quantity');
                    $damaged = (float) $prod->stockBalances->where('condition', 'damaged')->sum('quantity');
                }

                $total = $ready + $service + $damaged;
                $totalSqmReady = $prod->sqm_per_unit ? round($ready * (float) $prod->sqm_per_unit, 2) : 0;

                fputcsv($handle, [
                    $no++,
                    $prod->code,
                    $prod->name,
                    $prod->category->name ?? '-',
                    $prod->brand ?? '-',
                    $prod->model_series ?? '-',
                    $prod->tracking_type === 'individual' ? 'Per-Unit Serial' : 'Kuantitas Stok',
                    $prod->sqm_per_unit ? (float) $prod->sqm_per_unit : '-',
                    $ready,
                    $service,
                    $damaged,
                    $total,
                    $totalSqmReady ?: '-',
                    $prod->unit->symbol ?? $prod->unit->name ?? 'Unit',
                    $prod->is_active ? 'Aktif' : 'Nonaktif',
                ]);
            }

            fclose($handle);
        }, 200, [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0',
        ]);
    }

    /**
     * Display printable official A4 Monthly Inventory Availability Report.
     */
    public function printReport(Request $request): View
    {
        abort_if(! auth()->user()->can('view inventory'), 403, 'Akses inventaris ditolak.');

        $query = Product::with(['category', 'unit', 'stockBalances.location', 'assets.currentLocation'])
            ->orderBy('category_id')
            ->orderBy('name');

        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        if ($request->filled('tracking_type')) {
            $query->where('tracking_type', $request->tracking_type);
        }

        $products = $query->get();

        $totalProducts = $products->count();
        $totalActiveProducts = $products->where('is_active', true)->count();

        $totalSqmReady = 0;
        $totalSqmDamaged = 0;
        $totalSqmInService = 0;
        $totalCabinetsReady = 0;

        $totalReadyQuantity = 0;
        $totalServiceQuantity = 0;
        $totalDamagedQuantity = 0;

        foreach ($products as $prod) {
            /** @var Product $prod */
            if ($prod->tracking_type === 'individual') {
                $ready = $prod->assets->where('condition', 'available')->where('status', 'in_storage')->count();
                $service = $prod->assets->where('condition', 'in_service')->count();
                $damaged = $prod->assets->where('condition', 'damaged')->count();
            } else {
                $ready = (float) $prod->stockBalances->where('condition', 'available')->sum('quantity');
                $service = (float) $prod->stockBalances->where('condition', 'in_service')->sum('quantity');
                $damaged = (float) $prod->stockBalances->where('condition', 'damaged')->sum('quantity');
            }

            $totalReadyQuantity += $ready;
            $totalServiceQuantity += $service;
            $totalDamagedQuantity += $damaged;

            if ($prod->sqm_per_unit) {
                $sqmRate = (float) $prod->sqm_per_unit;
                $totalSqmReady += $ready * $sqmRate;
                $totalSqmInService += $service * $sqmRate;
                $totalSqmDamaged += $damaged * $sqmRate;
                $totalCabinetsReady += $ready;
            }
        }

        $totalAllStock = $totalReadyQuantity + $totalServiceQuantity + $totalDamagedQuantity;
        $readinessPercentage = $totalAllStock > 0 ? round(($totalReadyQuantity / $totalAllStock) * 100, 1) : 100;

        $periodText = now()->translatedFormat('F Y');

        return view('products.print', compact(
            'products',
            'totalProducts',
            'totalActiveProducts',
            'totalSqmReady',
            'totalSqmDamaged',
            'totalSqmInService',
            'totalCabinetsReady',
            'totalReadyQuantity',
            'totalServiceQuantity',
            'totalDamagedQuantity',
            'totalAllStock',
            'readinessPercentage',
            'periodText'
        ));
    }

    /**
     * Show the form for creating a new product.
     */
    public function create(): View
    {
        abort_if(! auth()->user()->can('create product'), 403, 'Anda tidak memiliki hak akses untuk menambah barang.');

        $categories = Category::where('is_active', true)->orderBy('name')->get();
        $units = Unit::orderBy('name')->get();

        return view('products.create', compact('categories', 'units'));
    }

    /**
     * Store a newly created product in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        abort_if(! auth()->user()->can('create product'), 403, 'Anda tidak memiliki hak akses untuk menambah barang.');

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
            ->with('success', 'Master barang '.$product->name.' berhasil ditambahkan ke katalog.');
    }

    /**
     * Display the specified product.
     */
    public function show(Product $product): View
    {
        $product->load(['category', 'unit', 'assets.currentLocation', 'stockBalances.location']);

        if ($product->isIndividual()) {
            $totalStockAvailable = $product->assets->where('condition', 'available')->where('status', 'in_storage')->count();
            $totalStockDamaged = $product->assets->where('condition', 'damaged')->count();
            $totalStockInService = $product->assets->where('condition', 'in_service')->count();
        } else {
            $totalStockAvailable = $product->stockBalances->where('condition', 'available')->sum('quantity');
            $totalStockDamaged = $product->stockBalances->where('condition', 'damaged')->sum('quantity');
            $totalStockInService = $product->stockBalances->where('condition', 'in_service')->sum('quantity');
        }

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
        abort_if(! auth()->user()->can('edit product'), 403, 'Anda tidak memiliki hak akses untuk mengedit data barang.');

        $categories = Category::where('is_active', true)->orderBy('name')->get();
        $units = Unit::orderBy('name')->get();

        return view('products.edit', compact('product', 'categories', 'units'));
    }

    /**
     * Update the specified product in storage.
     */
    public function update(Request $request, Product $product): RedirectResponse
    {
        abort_if(! auth()->user()->can('edit product'), 403, 'Anda tidak memiliki hak akses untuk mengedit data barang.');
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

    /**
     * Remove the specified product from storage (or deactivate if has active operational records).
     */
    public function destroy(Product $product): RedirectResponse
    {
        abort_if(
            ! auth()->user()->can('delete product') && ! auth()->user()->hasRole(['Superuser', 'Owner', 'Warehouse']),
            403,
            'Anda tidak memiliki hak akses untuk menghapus barang.'
        );

        // Cek apakah barang memiliki riwayat operasional nyata (di luar pencatatan pendaftaran awal)
        $hasOperationalMovements = StockMovementItem::where('product_id', $product->id)
            ->whereHas('movement', function ($q) {
                $q->whereNotIn('type', ['initial_count', 'inbound'])
                    ->orWhereNull('reference_type')
                    ->orWhere('reference_type', '!=', Product::class);
            })
            ->exists();

        $hasServiceTickets = ServiceTicket::where('product_id', $product->id)->exists();
        $hasDeployedAssets = Asset::where('product_id', $product->id)
            ->whereIn('status', ['deployed', 'in_service'])
            ->exists();

        // Jika memiliki transaksi event / servis aktif, lindungi integritas data dengan menonaktifkan
        if ($hasOperationalMovements || $hasServiceTickets || $hasDeployedAssets) {
            $product->update(['is_active' => false]);

            AuditLog::log(
                action: 'deactivate_product',
                entityType: 'Product',
                entityId: $product->id,
                oldValues: ['is_active' => true],
                newValues: ['is_active' => false],
                description: "Barang '{$product->name}' dinonaktifkan karena memiliki riwayat operasional/servis aktif."
            );

            return redirect()->route('products.index')
                ->with('warning', "Barang '{$product->name}' memiliki riwayat transaksi operasional/servis sehingga tidak dapat dihapus permanen demi keutuhan data pembukuan. Status barang telah otomatis dialihkan menjadi NONAKTIF.");
        }

        $productName = $product->name;
        $productCode = $product->code;

        DB::transaction(function () use ($product, $productName, $productCode) {
            if ($product->image_path) {
                Storage::disk('public')->delete($product->image_path);
            }

            // Bersihkan saldo fisik, aset yang belum dideploy, dan log awal produk
            StockBalance::where('product_id', $product->id)->delete();
            Asset::where('product_id', $product->id)->delete();
            InventoryCountItem::where('product_id', $product->id)->delete();
            StockMovementItem::where('product_id', $product->id)->delete();

            $product->delete();

            AuditLog::log(
                action: 'delete_product',
                entityType: 'Product',
                entityId: $product->id,
                description: "Menghapus permanen master barang: {$productName} ({$productCode})"
            );
        });

        return redirect()->route('products.index')
            ->with('success', "Master barang '{$productName}' ({$productCode}) berhasil dihapus permanen dari sistem.");
    }
}
