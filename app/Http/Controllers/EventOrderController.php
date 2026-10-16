<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreEventOrderRequest;
use App\Models\Crew;
use App\Models\Customer;
use App\Models\EventOrder;
use App\Models\OrderLedScreen;
use App\Models\SpkAssignment;
use App\Models\Vehicle;
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
        $vehicles = Vehicle::where('is_active', true)->orderBy('name')->get();

        return view('orders.create', compact('customers', 'crews', 'vehicles'));
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
                'provinsi' => $validated['provinsi'] ?? null,
                'kabupaten' => $validated['kabupaten'] ?? null,
                'status_wilayah' => $validated['status_wilayah'] ?? 'dalam_kota',
                'google_maps_url' => $validated['google_maps_url'] ?? null,
                'loading_date' => $validated['loading_date'],
                'loading_time' => $this->formatTimeInput($validated['loading_time'] ?? null),
                'ready_time' => $this->formatTimeInput($validated['ready_time'] ?? null) ?? $this->formatTimeInput($validated['jam_acara'] ?? null),
                'jam_menyala' => $this->formatTimeInput($validated['jam_menyala'] ?? null),
                'waktu_gladi' => $this->formatTimeInput($validated['waktu_gladi'] ?? null),
                'jam_acara' => $this->formatTimeInput($validated['jam_acara'] ?? null) ?? $this->formatTimeInput($validated['ready_time'] ?? null),
                'event_start_date' => $validated['event_start_date'],
                'event_end_date' => $validated['event_end_date'] ?? $validated['event_start_date'],
                'event_time' => $validated['event_time'] ?? ($validated['jam_acara'] ?? null),
                'electricity_source' => $validated['electricity_source'] ?? ($validated['kelistrikan'] ?? 'Genset 40KVA'),
                'kelistrikan' => $validated['kelistrikan'] ?? ($validated['electricity_source'] ?? 'Genset'),
                'kelistrikan_keterangan' => $validated['kelistrikan_keterangan'] ?? null,
                'dress_code' => $validated['dress_code'] ?? 'Seragam Omahvisual',
                'stage_width_m' => isset($validated['stage_width_m']) ? (float) $validated['stage_width_m'] : 10.00,
                'stage_depth_m' => isset($validated['stage_depth_m']) ? (float) $validated['stage_depth_m'] : 6.00,
                'stage_height_m' => isset($validated['stage_height_m']) ? (float) $validated['stage_height_m'] : 0.60,
                'stage_layout_json' => ! empty($validated['stage_layout_json'])
                    ? (is_array($validated['stage_layout_json']) ? $validated['stage_layout_json'] : json_decode((string) $validated['stage_layout_json'], true))
                    : null,
                'stage_layout_image' => $validated['stage_layout_image'] ?? null,
                'stage_layout_front_image' => $validated['stage_layout_front_image'] ?? null,
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

            // 4. Create Screens with calculation and user overrides
            foreach ($validated['screens'] as $screenData) {
                $calc = $this->calculationService->calculateScreen(
                    (float) $screenData['width_m'],
                    (float) $screenData['height_m'],
                    $screenData['pixel_pitch'] ?? 'P3.9',
                    $screenData['brand_series'] ?? 'SAGA'
                );

                $cabinets1m = ! empty($screenData['cabinets_1m']) ? (int) $screenData['cabinets_1m'] : $calc['cabinets_1m'];
                $cabinets50cm = isset($screenData['cabinets_50cm']) && $screenData['cabinets_50cm'] !== '' ? (int) $screenData['cabinets_50cm'] : $calc['cabinets_50cm'];
                $totalCabinets = ! empty($screenData['total_cabinets']) ? (int) $screenData['total_cabinets'] : ($cabinets1m + $cabinets50cm);
                $totalHardcases = ! empty($screenData['total_hardcases']) ? (int) $screenData['total_hardcases'] : $calc['total_hardcases'];
                $processor = ! empty($screenData['processor_model']) ? $screenData['processor_model'] : $calc['processor_model'];
                $powerJumpers = ! empty($screenData['power_jumpers_qty']) ? (int) $screenData['power_jumpers_qty'] : $calc['power_jumpers_qty'];
                $lanJumpers = ! empty($screenData['lan_jumpers_qty']) ? (int) $screenData['lan_jumpers_qty'] : $calc['lan_jumpers_qty'];
                $spareModules = ! empty($screenData['spare_modules_qty']) ? (int) $screenData['spare_modules_qty'] : $calc['spare_modules_qty'];
                $spareRc = ! empty($screenData['spare_receiving_cards_qty']) ? (int) $screenData['spare_receiving_cards_qty'] : $calc['spare_receiving_cards_qty'];
                $sparePsu = ! empty($screenData['spare_psu_qty']) ? (int) $screenData['spare_psu_qty'] : $calc['spare_psu_qty'];

                OrderLedScreen::create([
                    'event_order_id' => $order->id,
                    'screen_name' => $screenData['screen_name'],
                    'brand_series' => $screenData['brand_series'],
                    'pixel_pitch' => $screenData['pixel_pitch'],
                    'width_m' => $calc['width_m'],
                    'height_m' => $calc['height_m'],
                    'total_sqm' => $calc['total_sqm'],
                    'cabinets_1m' => $cabinets1m,
                    'cabinets_50cm' => $cabinets50cm,
                    'total_cabinets' => $totalCabinets,
                    'total_hardcases' => $totalHardcases,
                    'rigging_method' => $screenData['rigging_method'],
                    'stage_specification' => $screenData['stage_specification'] ?? null,
                    'processor_model' => $processor,
                    'power_jumpers_qty' => $powerJumpers,
                    'lan_jumpers_qty' => $lanJumpers,
                    'spare_modules_qty' => $spareModules,
                    'spare_receiving_cards_qty' => $spareRc,
                    'spare_psu_qty' => $sparePsu,
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
     * Update the 3D stage layout configuration and 2D diagram snapshot.
     */
    public function updateStageLayout(Request $request, EventOrder $order): JsonResponse
    {
        $validated = $request->validate([
            'stage_width_m' => ['nullable', 'numeric', 'min:0', 'max:100'],
            'stage_depth_m' => ['nullable', 'numeric', 'min:0', 'max:50'],
            'stage_height_m' => ['nullable', 'numeric', 'min:0', 'max:10'],
            'stage_layout_json' => ['nullable'],
            'stage_layout_image' => ['nullable', 'string'],
            'stage_layout_front_image' => ['nullable', 'string'],
        ]);

        $order->update([
            'stage_width_m' => isset($validated['stage_width_m']) ? (float) $validated['stage_width_m'] : $order->stage_width_m,
            'stage_depth_m' => isset($validated['stage_depth_m']) ? (float) $validated['stage_depth_m'] : $order->stage_depth_m,
            'stage_height_m' => isset($validated['stage_height_m']) ? (float) $validated['stage_height_m'] : $order->stage_height_m,
            'stage_layout_json' => ! empty($validated['stage_layout_json'])
                ? (is_array($validated['stage_layout_json']) ? $validated['stage_layout_json'] : json_decode((string) $validated['stage_layout_json'], true))
                : $order->stage_layout_json,
            'stage_layout_image' => $validated['stage_layout_image'] ?? $order->stage_layout_image,
            'stage_layout_front_image' => $validated['stage_layout_front_image'] ?? $order->stage_layout_front_image,
        ]);

        return response()->json([
            'message' => 'Layout panggung dan blueprint 2D berhasil diperbarui!',
            'order' => $order,
        ]);
    }

    /**
     * Show the form for editing the specified order.
     */
    public function edit(EventOrder $order): View
    {
        $order->load(['customer', 'screens', 'spkAssignment']);
        $customers = Customer::orderBy('name')->get();
        $crews = Crew::where('is_active', true)->orderBy('nickname')->get();
        $vehicles = Vehicle::where('is_active', true)->orderBy('name')->get();

        return view('orders.edit', compact('order', 'customers', 'crews', 'vehicles'));
    }

    /**
     * Update the specified order in storage.
     */
    public function update(Request $request, EventOrder $order): RedirectResponse
    {
        $validated = $request->validate([
            'customer_id' => ['required', 'exists:customers,id'],
            'event_name' => ['required', 'string', 'max:255'],
            'event_type' => ['required', 'string', 'in:indoor,outdoor,semi-outdoor'],
            'venue_name' => ['required', 'string', 'max:255'],
            'venue_address' => ['nullable', 'string'],
            'provinsi' => ['nullable', 'string', 'max:100'],
            'kabupaten' => ['nullable', 'string', 'max:100'],
            'status_wilayah' => ['required', 'string', 'in:dalam_kota,luar_kota'],
            'google_maps_url' => ['nullable', 'string', 'max:500'],
            'loading_date' => ['required', 'date'],
            'loading_time' => ['required', 'string', 'max:50'],
            'ready_time' => ['nullable', 'string', 'max:50'],
            'jam_menyala' => ['nullable', 'string', 'max:50'],
            'waktu_gladi' => ['nullable', 'string', 'max:50'],
            'jam_acara' => ['nullable', 'string', 'max:50'],
            'event_start_date' => ['required', 'date'],
            'event_end_date' => ['nullable', 'date'],
            'event_time' => ['nullable', 'string', 'max:50'],
            'electricity_source' => ['nullable', 'string', 'max:100'],
            'kelistrikan' => ['nullable', 'string', 'max:100'],
            'kelistrikan_keterangan' => ['nullable', 'string'],
            'dress_code' => ['nullable', 'string', 'max:100'],
            'stage_width_m' => ['nullable', 'numeric', 'min:0', 'max:100'],
            'stage_depth_m' => ['nullable', 'numeric', 'min:0', 'max:50'],
            'stage_height_m' => ['nullable', 'numeric', 'min:0', 'max:10'],
            'status' => ['nullable', 'string'],
            'notes' => ['nullable', 'string'],
            'total_price' => ['nullable', 'numeric', 'min:0'],
            'down_payment' => ['nullable', 'numeric', 'min:0'],
            'down_payment_method' => ['nullable', 'string', 'max:100'],
            'tv_43_qty' => ['nullable', 'integer', 'min:0'],
            'tv_50_qty' => ['nullable', 'integer', 'min:0'],
            'tv_55_qty' => ['nullable', 'integer', 'min:0'],
            'tv_65_qty' => ['nullable', 'integer', 'min:0'],
            'jimmy_jib' => ['nullable', 'string', 'max:100'],
        ]);

        $order->update([
            'customer_id' => $validated['customer_id'],
            'event_name' => $validated['event_name'],
            'event_type' => $validated['event_type'],
            'venue_name' => $validated['venue_name'],
            'venue_address' => $validated['venue_address'] ?? null,
            'provinsi' => $validated['provinsi'] ?? null,
            'kabupaten' => $validated['kabupaten'] ?? null,
            'status_wilayah' => $validated['status_wilayah'] ?? 'dalam_kota',
            'google_maps_url' => $validated['google_maps_url'] ?? null,
            'loading_date' => $validated['loading_date'],
            'loading_time' => $this->formatTimeInput($validated['loading_time'] ?? null),
            'ready_time' => $this->formatTimeInput($validated['ready_time'] ?? null) ?? $this->formatTimeInput($validated['jam_acara'] ?? null),
            'jam_menyala' => $this->formatTimeInput($validated['jam_menyala'] ?? null),
            'waktu_gladi' => $this->formatTimeInput($validated['waktu_gladi'] ?? null),
            'jam_acara' => $this->formatTimeInput($validated['jam_acara'] ?? null) ?? $this->formatTimeInput($validated['ready_time'] ?? null),
            'event_start_date' => $validated['event_start_date'],
            'event_end_date' => $validated['event_end_date'] ?? $validated['event_start_date'],
            'event_time' => $validated['event_time'] ?? ($validated['jam_acara'] ?? null),
            'electricity_source' => $validated['electricity_source'] ?? ($validated['kelistrikan'] ?? null),
            'kelistrikan' => $validated['kelistrikan'] ?? ($validated['electricity_source'] ?? null),
            'kelistrikan_keterangan' => $validated['kelistrikan_keterangan'] ?? null,
            'dress_code' => $validated['dress_code'] ?? 'Seragam Omahvisual',
            'stage_width_m' => isset($validated['stage_width_m']) ? (float) $validated['stage_width_m'] : $order->stage_width_m,
            'stage_depth_m' => isset($validated['stage_depth_m']) ? (float) $validated['stage_depth_m'] : $order->stage_depth_m,
            'stage_height_m' => isset($validated['stage_height_m']) ? (float) $validated['stage_height_m'] : $order->stage_height_m,
            'status' => $validated['status'] ?? $order->status,
            'notes' => $validated['notes'] ?? null,
            'total_price' => $validated['total_price'] ?? $order->total_price,
            'down_payment' => $validated['down_payment'] ?? $order->down_payment,
            'down_payment_method' => $validated['down_payment_method'] ?? $order->down_payment_method,
            'tv_43_qty' => $validated['tv_43_qty'] ?? 0,
            'tv_50_qty' => $validated['tv_50_qty'] ?? 0,
            'tv_55_qty' => $validated['tv_55_qty'] ?? 0,
            'tv_65_qty' => $validated['tv_65_qty'] ?? 0,
            'jimmy_jib' => $validated['jimmy_jib'] ?? null,
        ]);

        // Synchronize screens if provided
        if ($request->has('screens') && is_array($request->input('screens'))) {
            foreach ($request->input('screens') as $idx => $screenData) {
                if (empty($screenData['width_m']) || empty($screenData['height_m'])) {
                    continue;
                }

                $screen = $order->screens()->skip($idx)->first();
                $calc = $this->calculationService->calculateScreen(
                    (float) $screenData['width_m'],
                    (float) $screenData['height_m'],
                    $screenData['pixel_pitch'] ?? ($screen?->pixel_pitch ?? 'P3.9'),
                    $screenData['brand_series'] ?? ($screen?->brand_series ?? 'SAGA')
                );

                $cabinets1m = ! empty($screenData['cabinets_1m']) ? (int) $screenData['cabinets_1m'] : $calc['cabinets_1m'];
                $cabinets50cm = isset($screenData['cabinets_50cm']) && $screenData['cabinets_50cm'] !== '' ? (int) $screenData['cabinets_50cm'] : $calc['cabinets_50cm'];
                $totalCabinets = ! empty($screenData['total_cabinets']) ? (int) $screenData['total_cabinets'] : ($cabinets1m + $cabinets50cm);
                $totalHardcases = ! empty($screenData['total_hardcases']) ? (int) $screenData['total_hardcases'] : $calc['total_hardcases'];
                $processor = ! empty($screenData['processor_model']) ? $screenData['processor_model'] : $calc['processor_model'];
                $powerJumpers = ! empty($screenData['power_jumpers_qty']) ? (int) $screenData['power_jumpers_qty'] : $calc['power_jumpers_qty'];
                $lanJumpers = ! empty($screenData['lan_jumpers_qty']) ? (int) $screenData['lan_jumpers_qty'] : $calc['lan_jumpers_qty'];
                $spareModules = ! empty($screenData['spare_modules_qty']) ? (int) $screenData['spare_modules_qty'] : $calc['spare_modules_qty'];
                $spareRc = ! empty($screenData['spare_receiving_cards_qty']) ? (int) $screenData['spare_receiving_cards_qty'] : $calc['spare_receiving_cards_qty'];
                $sparePsu = ! empty($screenData['spare_psu_qty']) ? (int) $screenData['spare_psu_qty'] : $calc['spare_psu_qty'];

                $screenPayload = [
                    'screen_name' => $screenData['screen_name'] ?? ($screen?->screen_name ?? 'Main Screen'),
                    'brand_series' => $screenData['brand_series'] ?? ($screen?->brand_series ?? 'SAGA'),
                    'pixel_pitch' => $screenData['pixel_pitch'] ?? ($screen?->pixel_pitch ?? 'P3.9'),
                    'width_m' => (float) $screenData['width_m'],
                    'height_m' => (float) $screenData['height_m'],
                    'total_sqm' => round((float) $screenData['width_m'] * (float) $screenData['height_m'], 2),
                    'cabinets_1m' => $cabinets1m,
                    'cabinets_50cm' => $cabinets50cm,
                    'total_cabinets' => $totalCabinets,
                    'total_hardcases' => $totalHardcases,
                    'rigging_method' => $screenData['rigging_method'] ?? ($screen?->rigging_method ?? 'ground_stake'),
                    'stage_specification' => $screenData['stage_specification'] ?? ($screen?->stage_specification ?? null),
                    'processor_model' => $processor,
                    'power_jumpers_qty' => $powerJumpers,
                    'lan_jumpers_qty' => $lanJumpers,
                    'spare_modules_qty' => $spareModules,
                    'spare_receiving_cards_qty' => $spareRc,
                    'spare_psu_qty' => $sparePsu,
                ];

                if ($screen) {
                    $screen->update($screenPayload);
                } else {
                    $screenPayload['event_order_id'] = $order->id;
                    OrderLedScreen::create($screenPayload);
                }
            }
        }

        // Synchronize SPK crew & vehicle if present
        if ($order->spkAssignment && ($request->filled('vehicle_name') || $request->filled('crew_pic_event_name'))) {
            $order->spkAssignment->update([
                'vehicle_name' => $request->input('vehicle_name', $order->spkAssignment->vehicle_name),
                'crew_pic_event_name' => $request->input('crew_pic_event_name', $order->spkAssignment->crew_pic_event_name),
                'crew_1_name' => $request->input('crew_1_name', $order->spkAssignment->crew_1_name),
                'crew_2_name' => $request->input('crew_2_name', $order->spkAssignment->crew_2_name),
                'crew_3_name' => $request->input('crew_3_name', $order->spkAssignment->crew_3_name),
                'crew_4_name' => $request->input('crew_4_name', $order->spkAssignment->crew_4_name),
                'crew_5_name' => $request->input('crew_5_name', $order->spkAssignment->crew_5_name),
                'crew_6_name' => $request->input('crew_6_name', $order->spkAssignment->crew_6_name),
                'crew_7_name' => $request->input('crew_7_name', $order->spkAssignment->crew_7_name),
            ]);
        }

        return redirect()->route('orders.show', $order)
            ->with('status', "Pesanan {$order->order_number} berhasil diperbarui.");
    }

    /**
     * Normalize time input to HH:MM format for PostgreSQL time column.
     */
    private function formatTimeInput(?string $time): ?string
    {
        if (empty($time)) {
            return null;
        }

        if (preg_match('/(\d{1,2}:\d{2}(?::\d{2})?)/', $time, $matches)) {
            $parts = explode(':', $matches[1]);
            $parts[0] = str_pad($parts[0], 2, '0', STR_PAD_LEFT);

            return implode(':', array_slice($parts, 0, 2));
        }

        return null;
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
