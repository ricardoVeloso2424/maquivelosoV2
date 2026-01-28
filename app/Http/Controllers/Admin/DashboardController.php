<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Machine;

class DashboardController extends Controller
{
    public function __invoke()
    {
        $total = Machine::count();

        $available = Machine::where('status', 'available')->count();
        $reserved  = Machine::where('status', 'reserved')->count();
        $sold      = Machine::where('status', 'sold')->count();

        $unavailable = Machine::where('status', 'inactive')->count();

        return view('admin.dashboard', compact(
            'total',
            'available',
            'reserved',
            'sold',
            'unavailable'
        ));
    }
}
