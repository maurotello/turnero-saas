<?php

namespace App\Http\Controllers;

use App\Models\Company;
use Illuminate\Http\Request;

class PatientDashboardController extends Controller
{
    public function index($slug)
    {
        $company = Company::where('slug', $slug)->firstOrFail();
        $patient = auth('patient')->user();

        // Retrieve appointments ordered by date and time descending
        $appointments = $patient->appointments()
            ->where('company_id', $company->id)
            ->orderBy('date', 'desc')
            ->orderBy('time', 'desc')
            ->get();

        return view('patient.dashboard', compact('company', 'patient', 'appointments'));
    }
}
