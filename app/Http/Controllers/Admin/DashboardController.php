<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Machine;

class DashboardController extends Controller
{
    public function __invoke()
    {
        $stats = Machine::query()
            ->selectRaw('COUNT(*) as total')
            ->selectRaw("SUM(CASE WHEN status = 'available' THEN 1 ELSE 0 END) as available")
            ->selectRaw("SUM(CASE WHEN status = 'reserved' THEN 1 ELSE 0 END) as reserved")
            ->selectRaw("SUM(CASE WHEN status = 'sold' THEN 1 ELSE 0 END) as sold")
            ->selectRaw("SUM(CASE WHEN status = 'inactive' THEN 1 ELSE 0 END) as unavailable")
            ->first();

        $total = (int) ($stats->total ?? 0);
        $available = (int) ($stats->available ?? 0);
        $reserved = (int) ($stats->reserved ?? 0);
        $sold = (int) ($stats->sold ?? 0);
        $unavailable = (int) ($stats->unavailable ?? 0);

        return view('admin.dashboard', compact(
            'total',
            'available',
            'reserved',
            'sold',
            'unavailable'
        ));
    }
}
