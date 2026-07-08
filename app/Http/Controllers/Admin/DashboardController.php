<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Appointment;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $stats = [
            'today_count' => Appointment::whereDate('date', now())->count(),
            'pending_count' => Appointment::where('status', 'active')->count(),
            'cancelled_count' => Appointment::where('status', 'cancelled')->count(),
            'total_history' => Appointment::count(),
        ];

        $nextAppointments = Appointment::where('status', 'active')
            ->whereDate('date', '>=', now())
            ->orderBy('date')
            ->orderBy('time')
            ->take(10)
            ->get();

        $professionals = \App\Models\Professional::where('company_id', auth()->user()->company_id)
            ->withCount([
                'appointments as today_count' => function ($query) {
                    $query->whereDate('date', now());
                },
                'appointments as pending_count' => function ($query) {
                    $query->where('status', 'active');
                },
                'appointments as cancelled_count' => function ($query) {
                    $query->where('status', 'cancelled');
                },
                'appointments as total_history'
            ])
            ->get();

        return view('admin.dashboard', compact('stats', 'nextAppointments', 'professionals'));
    }
}
