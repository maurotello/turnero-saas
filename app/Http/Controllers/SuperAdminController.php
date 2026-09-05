<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Models\User;
use Illuminate\Http\Request;

class SuperAdminController extends Controller
{
    public function index()
    {
        // Get all companies with their user count
        $companies = Company::withCount('users')->latest()->get();
        
        $totalCompanies = $companies->count();
        $totalUsers = User::count();
        
        return view('superadmin.dashboard', compact('companies', 'totalCompanies', 'totalUsers'));
    }
}
