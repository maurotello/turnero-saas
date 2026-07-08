<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BlockedDay;
use Illuminate\Http\Request;

class BlockedDayController extends Controller
{
    public function index()
    {
        $blockedDays = BlockedDay::orderBy('date', 'desc')->get();
        return view('admin.blocked-days.index', compact('blockedDays'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'date' => 'required|date|after_or_equal:today',
            'reason' => 'nullable|string|max:255',
        ]);

        BlockedDay::create($request->all());

        return back()->with('success', 'Día bloqueado correctamente.');
    }

    public function destroy(BlockedDay $blockedDay)
    {
        $blockedDay->delete();
        return back()->with('success', 'Día desbloqueado.');
    }
}
