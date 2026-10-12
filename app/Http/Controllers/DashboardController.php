<?php

namespace App\Http\Controllers;

use App\Models\Asset;
use App\Models\InventoryCount;
use App\Models\Location;
use App\Models\Product;
use App\Models\StockAdjustment;
use App\Models\StockBalance;
use App\Models\StockMovement;
use Illuminate\View\View;

class DashboardController extends Controller
{
    /**
     * Display the main operational & inventory dashboard.
     */
    public function index(): View
    {
        $totalProducts = Product::where('is_active', true)->count();
        $totalAssets = Asset::count();
        $totalLocations = Location::where('is_active', true)->count();

        // Total meter persegi kabinet LED tersedia di gudang
        $totalLedCabinets = StockBalance::whereHas('product', function ($query) {
            $query->whereNotNull('sqm_per_unit');
        })->where('condition', 'available')->sum('quantity');

        $totalLedSqm = StockBalance::with('product')
            ->whereHas('product', function ($query) {
                $query->whereNotNull('sqm_per_unit');
            })
            ->where('condition', 'available')
            ->get()
            ->sum(function ($balance) {
                return (float) $balance->quantity * (float) ($balance->product->sqm_per_unit ?? 0);
            });

        // Pending approval adjustments
        $pendingAdjustmentsCount = StockAdjustment::where('status', 'pending_approval')->count();

        // Initial count session status
        $initialCountSession = InventoryCount::first();

        // Recent high-value assets (Processors Novastar & TV)
        $recentAssets = Asset::with(['product', 'currentLocation'])
            ->orderBy('id', 'asc')
            ->take(8)
            ->get();

        // Recent stock movements
        $recentMovements = StockMovement::with(['sourceLocation', 'destinationLocation', 'creator'])
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();

        // Stock balances overview per location
        $locationsWithStock = Location::where('is_active', true)
            ->withCount(['assets', 'stockBalances'])
            ->get();

        return view('dashboard', compact(
            'totalProducts',
            'totalAssets',
            'totalLocations',
            'totalLedCabinets',
            'totalLedSqm',
            'pendingAdjustmentsCount',
            'initialCountSession',
            'recentAssets',
            'recentMovements',
            'locationsWithStock'
        ));
    }
}
