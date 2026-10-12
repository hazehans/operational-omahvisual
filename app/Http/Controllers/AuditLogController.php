<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AuditLogController extends Controller
{
    /**
     * Display a listing of system audit logs.
     */
    public function index(Request $request): View
    {
        $query = AuditLog::with('user');

        if ($request->filled('action')) {
            $query->where('action', $request->action);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('description', 'ILIKE', "%{$search}%")
                    ->orWhere('ip_address', 'ILIKE', "%{$search}%")
                    ->orWhereHas('user', function ($uq) use ($search) {
                        $uq->where('name', 'ILIKE', "%{$search}%");
                    });
            });
        }

        $logs = $query->orderBy('created_at', 'desc')->paginate(25)->withQueryString();

        return view('audit_logs.index', compact('logs'));
    }
}
