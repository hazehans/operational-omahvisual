<?php

namespace App\Http\Controllers;

use App\Models\EventOrder;
use App\Models\ServiceTicket;
use App\Models\SpkAssignment;
use Illuminate\View\View;

class GuideController extends Controller
{
    /**
     * Display the operational user guide and system SOP documentation.
     */
    public function index(): View
    {
        $sampleOrder = EventOrder::latest()->first();
        $sampleAssignment = SpkAssignment::latest()->first();
        $sampleTicket = ServiceTicket::latest()->first();

        return view('guide.index', compact('sampleOrder', 'sampleAssignment', 'sampleTicket'));
    }
}

