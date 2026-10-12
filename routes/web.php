<?php

use App\Models\Asset;
use App\Models\Location;
use App\Models\Product;
use App\Models\User;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    $totalProducts = Product::count();
    $totalAssets = Asset::count();
    $totalLocations = Location::count();
    $totalUsers = User::count();
    $products = Product::with(['category', 'unit'])->get();

    return view('dashboard', compact(
        'totalProducts',
        'totalAssets',
        'totalLocations',
        'totalUsers',
        'products'
    ));
});
