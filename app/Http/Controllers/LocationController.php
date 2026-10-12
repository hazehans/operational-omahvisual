<?php

namespace App\Http\Controllers;

use App\Models\Location;
use Illuminate\View\View;

class LocationController extends Controller
{
    /**
     * Display a listing of warehouse storage locations.
     */
    public function index(): View
    {
        $locations = Location::withCount(['assets', 'stockBalances'])
            ->orderBy('id', 'asc')
            ->get();

        return view('locations.index', compact('locations'));
    }

    /**
     * Display items stored in a specific location.
     */
    public function show(Location $location): View
    {
        $location->load([
            'stockBalances.product.unit',
            'assets.product.unit',
        ]);

        return view('locations.show', compact('location'));
    }
}
