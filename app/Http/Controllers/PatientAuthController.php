<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Models\Patient;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class PatientAuthController extends Controller
{
    public function showLogin($slug)
    {
        $company = Company::where('slug', $slug)->firstOrFail();
        
        // If already logged in, redirect to slots selection or dashboard
        if (auth('patient')->check()) {
            return redirect()->route('booking.show', $slug);
        }

        return view('patient.auth.login', compact('company'));
    }

    public function login(Request $request, $slug)
    {
        $company = Company::where('slug', $slug)->firstOrFail();

        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        // Find patient scoped to this company
        $patient = Patient::where('company_id', $company->id)
            ->where('email', $credentials['email'])
            ->first();

        if ($patient && Hash::check($credentials['password'], $patient->password)) {
            auth('patient')->login($patient, $request->boolean('remember'));
            $request->session()->regenerate();

            // Redirect back to booking with preserved parameters if present
            $params = $request->only(['professional_id', 'date', 'time']);
            return redirect()->route('booking.show', array_merge(['slug' => $slug], array_filter($params)));
        }

        return back()->withErrors([
            'email' => 'Las credenciales proporcionadas no coinciden con nuestros registros para esta empresa.',
        ])->onlyInput('email');
    }

    public function showRegister($slug)
    {
        $company = Company::where('slug', $slug)->firstOrFail();

        if (auth('patient')->check()) {
            return redirect()->route('booking.show', $slug);
        }

        return view('patient.auth.register', compact('company'));
    }

    public function register(Request $request, $slug)
    {
        $company = Company::where('slug', $slug)->firstOrFail();

        $data = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => [
                'required',
                'email',
                'max:255',
                Rule::unique('patients')->where(function ($query) use ($company) {
                    return $query->where('company_id', $company->id);
                }),
            ],
            'password' => 'required|string|min:8|confirmed',
            'phone' => 'required|string|max:20',
            'dni' => 'required|string|max:20',
            'insurance' => 'required|string|max:255',
            'address' => 'nullable|string|max:255',
            'zip_code' => 'nullable|string|max:10',
            'city' => 'nullable|string|max:100',
            'state' => 'nullable|string|max:100',
        ]);

        $data['company_id'] = $company->id;
        $data['password'] = Hash::make($data['password']);

        $patient = Patient::create($data);

        auth('patient')->login($patient);

        $params = $request->only(['professional_id', 'date', 'time']);
        return redirect()->route('booking.show', array_merge(['slug' => $slug], array_filter($params)))->with('success', 'Registro completado con éxito.');
    }

    public function logout(Request $request, $slug)
    {
        auth('patient')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('booking.show', $slug);
    }
}
