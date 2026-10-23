<?php

namespace App\Http\Controllers;

use App\Models\Asset;
use App\Models\AuditLog;
use App\Models\InventoryCount;
use App\Models\Location;
use App\Models\Product;
use App\Models\StockAdjustment;
use App\Models\StockBalance;
use App\Models\StockMovement;
use App\Models\User;
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

        // Recent Live Audit & Team Activity Stream (Boss Requirement)
        $recentActivities = AuditLog::with('user.roles')
            ->latest('id')
            ->take(6)
            ->get();

        // Team Roles Status Radar
        $roleUsers = User::with('roles')
            ->whereIn('email', [
                'manager.ops@omahvisual.com',
                'admin@omahvisual.com',
                'warehouse@omahvisual.com',
                'kepala.servis@omahvisual.com',
            ])
            ->get()
            ->keyBy('email');

        $roleRadar = [
            'manager_ops' => [
                'title' => 'Manager Operasional',
                'user' => $roleUsers->get('manager.ops@omahvisual.com'),
                'focus' => 'Alokasi Armada & SPK Lapangan',
                'route' => route('orders.index'),
                'last_log' => AuditLog::where('user_id', $roleUsers->get('manager.ops@omahvisual.com')?->id)->latest('id')->first(),
            ],
            'admin_finance' => [
                'title' => 'Admin LED & Finance',
                'user' => $roleUsers->get('admin@omahvisual.com'),
                'focus' => 'Pesanan, Faktur & Kuitansi',
                'route' => route('invoices.index'),
                'last_log' => AuditLog::where('user_id', $roleUsers->get('admin@omahvisual.com')?->id)->latest('id')->first(),
            ],
            'warehouse' => [
                'title' => 'Tim Gudang (Warehouse)',
                'user' => $roleUsers->get('warehouse@omahvisual.com'),
                'focus' => 'Loading Out/In & Gate Pass',
                'route' => route('products.index'),
                'last_log' => AuditLog::where('user_id', $roleUsers->get('warehouse@omahvisual.com')?->id)->latest('id')->first(),
            ],
            'workshop' => [
                'title' => 'Ruang Servis & Workshop',
                'user' => $roleUsers->get('kepala.servis@omahvisual.com'),
                'focus' => 'Reparasi Modul LED & Bengkel',
                'route' => route('service-tickets.index'),
                'last_log' => AuditLog::where('user_id', $roleUsers->get('kepala.servis@omahvisual.com')?->id)->latest('id')->first(),
            ],
        ];

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
            'locationsWithStock',
            'recentActivities',
            'roleRadar'
        ));
    }
}
