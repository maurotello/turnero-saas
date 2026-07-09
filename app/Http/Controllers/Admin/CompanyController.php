<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class CompanyController extends Controller
{
    public function edit()
    {
        $company = auth()->user()->company;
        return view('admin.company.edit', compact('company'));
    }
    public function update(Request $request)
    {
        $company = auth()->user()->company;
        
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'address' => 'nullable|string',
            'city' => 'nullable|string',
            'state' => 'nullable|string',
            'phone' => 'nullable|string',
            'email' => 'nullable|email',
            'primary_color' => 'nullable|string|size:7',
            'logo' => 'nullable|image|max:2048',
            'logo_cropped' => 'nullable|string',
            'mp_public_key' => 'nullable|string|max:255',
            'mp_access_token' => 'nullable|string|max:500',
        ]);

        unset($data['logo']);
        unset($data['logo_cropped']);

        $disk = config('filesystems.default');

        try {
            if ($request->filled('logo_cropped')) {
                $logoCropped = $request->input('logo_cropped');
                if (preg_match('/^data:image\/(\w+);base64,/', $logoCropped, $type)) {
                    $image = substr($logoCropped, strpos($logoCropped, ',') + 1);
                    $type = strtolower($type[1]); // jpg, png, gif, jpeg, webp

                    if (in_array($type, ['jpg', 'jpeg', 'png', 'gif', 'webp'])) {
                        $image = base64_decode($image);

                        if ($image !== false) {
                            $fileName = 'logos/' . Str::random(40) . '.' . $type;
                            
                            // Eliminar el logo anterior si existe
                            if ($company->logo) {
                                \Illuminate\Support\Facades\Storage::disk($disk)->delete($company->logo);
                            }

                            \Illuminate\Support\Facades\Storage::disk($disk)->put($fileName, $image);
                            $data['logo'] = $fileName;
                        }
                    }
                }
            } elseif ($request->hasFile('logo')) {
                // Eliminar el logo anterior si existe
                if ($company->logo) {
                    \Illuminate\Support\Facades\Storage::disk($disk)->delete($company->logo);
                }
                $path = $request->file('logo')->store('logos', $disk);
                $data['logo'] = $path;
            }
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::error('Error de subida a R2 (Logo Empresa): ' . $e->getMessage());
            return back()->with('error', 'No se pudo subir el logo. Por favor, intenta de nuevo.')->withInput();
        }

        unset($data['logo_cropped']);

        $company->update($data);

        return back()->with('success', 'Datos de la empresa actualizados.');
    }
}
