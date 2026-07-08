<?php

namespace App\Http\Controllers;

use App\Models\Company;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class PatientProfileController extends Controller
{
    public function edit($slug)
    {
        $company = Company::where('slug', $slug)->firstOrFail();
        $patient = auth('patient')->user();

        return view('patient.profile', compact('company', 'patient'));
    }

    public function update(Request $request, $slug)
    {
        $company = Company::where('slug', $slug)->firstOrFail();
        $patient = auth('patient')->user();

        $data = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => [
                'required',
                'email',
                'max:255',
                Rule::unique('patients')->where(function ($query) use ($company) {
                    return $query->where('company_id', $company->id);
                })->ignore($patient->id),
            ],
            'phone' => 'required|string|max:20',
            'dni' => 'required|string|max:20',
            'insurance' => 'required|string|max:255',
            'address' => 'nullable|string|max:255',
            'zip_code' => 'nullable|string|max:10',
            'city' => 'nullable|string|max:100',
            'state' => 'nullable|string|max:100',
        ]);

        $patient->update($data);

        return back()->with('success', 'Tu perfil ha sido actualizado correctamente.');
    }

    public function updatePassword(Request $request, $slug)
    {
        $patient = auth('patient')->user();

        $request->validate([
            'current_password' => ['required', 'current_password:patient'],
            'password' => ['required', Password::defaults(), 'confirmed'],
        ]);

        $patient->update([
            'password' => Hash::make($request->password),
        ]);

        return back()->with('success', 'Contraseña actualizada con éxito.');
    }
}
