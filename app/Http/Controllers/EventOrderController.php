<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreEventOrderRequest;
use App\Models\Crew;
use App\Models\Customer;
use App\Models\EventOrder;
use App\Models\OrderLedScreen;
use App\Models\SpkAssignment;
use App\Services\LedCalculationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class EventOrderController extends Controller
{
    public function __construct(
        protected LedCalculationService $calculationService
    ) {}

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): View
    {
        $query = EventOrder::with(['customer', 'screens', 'spkAssignment'])
            ->latest('loading_date');

        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('order_number', 'ilike', "%{$search}%")
                    ->orWhere('event_name', 'ilike', "%{$search}%")
                    ->orWhere('venue_name', 'ilike', "%{$search}%")
                    ->orWhereHas('customer', function ($cq) use ($search) {
                        $cq->where('name', 'ilike', "%{$search}%")
                            ->orWhere('organization', 'ilike', "%{$search}%");
                    });
            });
        }

        if ($status = $request->input('status')) {
            $query->where('status', $status);
        }

        $orders = $query->paginate(15)->withQueryString();

        // Metrics for summary cards
        $thisMonthOrders = EventOrder::whereMonth('loading_date', now()->month)
            ->whereYear('loading_date', now()->year)
            ->count();
        $confirmedCount = EventOrder::where('status', 'confirmed')->count();
        $completedCount = EventOrder::where('status', 'completed')->count();
        $totalRevenue = EventOrder::where('payment_status', 'paid')->sum('total_price');

        return view('orders.index', compact('orders', 'thisMonthOrders', 'confirmedCount', 'completedCount', 'totalRevenue'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        $customers = Customer::orderBy('name')->get();
        $crews = Crew::where('is_active', true)->orderBy('nickname')->get();

        return view('orders.create', compact('customers', 'crews'));
    }

    /**
     * AJAX endpoint for live screen calculation.
     */
    public function calculate(Request $request): JsonResponse
    {
        $request->validate([
            'width_m' => ['required', 'numeric', 'min:0.5'],
            'height_m' => ['required', 'numeric', 'min:0.5'],
            'pixel_pitch' => ['nullable', 'string'],
            'brand_series' => ['nullable', 'string'],
        ]);

        $width = (float) $request->input('width_m');
        $height = (float) $request->input('height_m');
        $pitch = $request->input('pixel_pitch', 'P3.9');
        $brand = $request->input('brand_series', 'SAGA');

        $result = $this->calculationService->calculateScreen($width, $height, $pitch, $brand);

        return response()->json($result);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreEventOrderRequest $request): RedirectResponse
    {
        $validated = $request->validated();

        $order = DB::transaction(function () use ($validated) {
            // 1. Resolve or create customer
            if (! empty($validated['customer_id'])) {
                $customerId = $validated['customer_id'];
            } else {
                $customer = Customer::create([
                    'customer_code' => 'CUST-'.str_pad((string) (Customer::count() + 1), 4, '0', STR_PAD_LEFT),
                    'name' => $validated['customer_name'],
                    'organization' => $validated['customer_organization'] ?? null,
                    'phone' => $validated['customer_phone'],
                    'created_by' => Auth::id(),
                ]);
                $customerId = $customer->id;
            }

            // 2. Generate unique order number
            $orderCount = EventOrder::count() + 1;
            $orderNumber = 'ORD-'.date('Y').'-'.str_pad((string) $orderCount, 4, '0', STR_PAD_LEFT);

            // 3. Create EventOrder
            $order = EventOrder::create([
                'order_number' => $orderNumber,
                'customer_id' => $customerId,
                'event_name' => $validated['event_name'],
                'event_type' => $validated['event_type'],
                'venue_name' => $validated['venue_name'],
                'venue_address' => $validated['venue_address'] ?? null,
                'google_maps_url' => $validated['google_maps_url'] ?? null,
                'loading_date' => $validated['loading_date'],
                'loading_time' => $validated['loading_time'],
                'ready_time' => $validated['ready_time'] ?? null,
                'event_start_date' => $validated['event_start_date'],
                'event_end_date' => $validated['event_end_date'] ?? $validated['event_start_date'],
                'event_time' => $validated['event_time'] ?? null,
                'electricity_source' => $validated['electricity_source'] ?? 'PLN 16A',
                'dress_code' => $validated['dress_code'] ?? 'Seragam Omahvisual',
                'status' => 'confirmed',
                'notes' => $validated['notes'] ?? null,
                'total_price' => $validated['total_price'] ?? 0,
                'down_payment' => $validated['down_payment'] ?? 0,
                'down_payment_method' => $validated['down_payment_method'] ?? null,
                'paid_amount' => $validated['down_payment'] ?? 0,
                'payment_status' => (! empty($validated['down_payment']) && $validated['down_payment'] >= ($validated['total_price'] ?? 0)) ? 'paid' : ((! empty($validated['down_payment'])) ? 'partial' : 'unpaid'),
                'vehicle_loading_in' => $validated['vehicle_name'] ?? 'TRAGA 1',
                'vehicle_loading_out' => $validated['vehicle_name'] ?? 'TRAGA 1',
                'tv_43_qty' => $validated['tv_43_qty'] ?? 0,
                'tv_50_qty' => $validated['tv_50_qty'] ?? 0,
                'tv_55_qty' => $validated['tv_55_qty'] ?? 0,
                'tv_65_qty' => $validated['tv_65_qty'] ?? 0,
                'jimmy_jib' => $validated['jimmy_jib'] ?? null,
                'created_by' => Auth::id(),
            ]);

            // 4. Create Screens with calculation
            foreach ($validated['screens'] as $screenData) {
                $calc = $this->calculationService->calculateScreen(
                    (float) $screenData['width_m'],
                    (float) $screenData['height_m'],
                    $screenData['pixel_pitch'] ?? 'P3.9',
                    $screenData['brand_series'] ?? 'SAGA'
                );

                OrderLedScreen::create([
                    'event_order_id' => $order->id,
                    'screen_name' => $screenData['screen_name'],
                    'brand_series' => $screenData['brand_series'],
                    'pixel_pitch' => $screenData['pixel_pitch'],
                    'width_m' => $calc['width_m'],
                    'height_m' => $calc['height_m'],
                    'total_sqm' => $calc['total_sqm'],
                    'cabinets_1m' => $calc['cabinets_1m'],
                    'cabinets_50cm' => $calc['cabinets_50cm'],
                    'total_cabinets' => $calc['total_cabinets'],
                    'total_hardcases' => $calc['total_hardcases'],
                    'rigging_method' => $screenData['rigging_method'],
                    'stage_specification' => $screenData['stage_specification'] ?? null,
                    'processor_model' => $calc['processor_model'],
                    'power_jumpers_qty' => $calc['power_jumpers_qty'],
                    'lan_jumpers_qty' => $calc['lan_jumpers_qty'],
                    'spare_modules_qty' => $calc['spare_modules_qty'],
                    'spare_receiving_cards_qty' => $calc['spare_receiving_cards_qty'],
                    'spare_psu_qty' => $calc['spare_psu_qty'],
                ]);
            }

            // 5. Create SPK Assignment
            $spkNumber = 'SPK-'.date('Y').'-'.str_pad((string) $orderCount, 4, '0', STR_PAD_LEFT);
            $assignment = SpkAssignment::create([
                'event_order_id' => $order->id,
                'spk_number' => $spkNumber,
                'crew_pic_event_name' => $validated['crew_pic_event_name'] ?? null,
                'crew_1_name' => $validated['crew_1_name'] ?? null,
                'crew_2_name' => $validated['crew_2_name'] ?? null,
                'crew_3_name' => $validated['crew_3_name'] ?? null,
                'crew_4_name' => $validated['crew_4_name'] ?? null,
                'crew_5_name' => $validated['crew_5_name'] ?? null,
                'crew_6_name' => $validated['crew_6_name'] ?? null,
                'crew_7_name' => $validated['crew_7_name'] ?? null,
                'vehicle_name' => $validated['vehicle_name'] ?? 'TRAGA 1',
                'has_etoll' => true,
                'has_kunci_ban' => true,
                'has_stnk' => true,
                'has_kir' => true,
                'has_barcode_bbm' => true,
            ]);

            // 6. Generate standard 3-page checklist items
            $this->calculationService->generateStandardChecklist($assignment, $order);

            return $order;
        });

        return redirect()->route('orders.show', $order)
            ->with('status', "Pesanan {$order->order_number} berhasil dibuat dan SPK otomatis terbit!");
    }

    /**
     * Display the specified resource.
     */
    public function show(EventOrder $order): View
    {
        $order->load(['customer', 'screens', 'spkAssignment.checklistItems']);

        return view('orders.show', compact('order'));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(EventOrder $order): RedirectResponse
    {
        $orderNumber = $order->order_number;
        $order->delete();

        return redirect()->route('orders.index')
            ->with('status', "Pesanan {$orderNumber} berhasil dihapus.");
    }
}
