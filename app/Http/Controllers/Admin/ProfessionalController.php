<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreProfessionalRequest;
use App\Http\Requests\UpdateProfessionalRequest;
use App\Models\Professional;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ProfessionalController extends Controller
{
    public function index()
    {
        $company = auth()->user()->company;
        $professionals = $company->professionals()->orderBy('name')->get();

        return view('admin.professionals.index', compact('professionals'));
    }

    public function create()
    {
        return view('admin.professionals.create');
    }

    public function store(StoreProfessionalRequest $request)
    {
        $company = auth()->user()->company;
        $data = $request->validated();
        $data['company_id'] = $company->id;
        $data['is_active'] = $request->has('is_active');

        if ($request->hasFile('avatar')) {
            try {
                $disk = config('filesystems.default');
                $data['avatar'] = $request->file('avatar')->store('avatars', $disk);
            } catch (\Throwable $e) {
                \Illuminate\Support\Facades\Log::error('Error de subida a R2 (Crear Profesional): ' . $e->getMessage());
                return back()->with('error', 'No se pudo subir la foto de perfil. Por favor, intenta de nuevo.')->withInput();
            }
        }

        Professional::create($data);

        return redirect()->route('admin.professionals.index')
            ->with('success', 'Profesional creado con éxito.');
    }

    public function edit(Professional $professional)
    {
        $this->authorizeCompanyScope($professional);

        return view('admin.professionals.edit', compact('professional'));
    }

    public function update(UpdateProfessionalRequest $request, Professional $professional)
    {
        $this->authorizeCompanyScope($professional);

        $data = $request->validated();
        $data['is_active'] = $request->has('is_active');

        unset($data['avatar']);

        if ($request->hasFile('avatar')) {
            try {
                $disk = config('filesystems.default');
                // Delete old avatar
                if ($professional->avatar) {
                    Storage::disk($disk)->delete($professional->avatar);
                }
                $data['avatar'] = $request->file('avatar')->store('avatars', $disk);
            } catch (\Throwable $e) {
                \Illuminate\Support\Facades\Log::error('Error de subida a R2 (Actualizar Profesional): ' . $e->getMessage());
                return back()->with('error', 'No se pudo subir la nueva foto de perfil. Por favor, intenta de nuevo.')->withInput();
            }
        }

        $professional->update($data);

        return redirect()->route('admin.professionals.index')
            ->with('success', 'Profesional actualizado con éxito.');
    }

    public function destroy(Professional $professional)
    {
        $this->authorizeCompanyScope($professional);

        // Check if there are future appointments
        $hasFutureAppointments = $professional->appointments()
            ->where('date', '>=', now()->toDateString())
            ->whereIn('status', ['active', 'pending_payment', 'rescheduled'])
            ->exists();

        if ($hasFutureAppointments) {
            return back()->with('error', 'No se puede eliminar el profesional porque tiene turnos asignados a futuro. Por favor cancelá o reasigná los turnos antes de continuar.');
        }

        // Delete avatar if exists
        if ($professional->avatar) {
            try {
                $disk = config('filesystems.default');
                Storage::disk($disk)->delete($professional->avatar);
            } catch (\Throwable $e) {
                \Illuminate\Support\Facades\Log::warning('Error al eliminar avatar en R2 al borrar profesional: ' . $e->getMessage());
            }
        }

        $professional->delete();

        return redirect()->route('admin.professionals.index')
            ->with('success', 'Profesional eliminado con éxito.');
    }

    /**
     * Helper de seguridad para garantizar que el profesional pertenezca al tenant del usuario.
     */
    protected function authorizeCompanyScope(Professional $professional): void
    {
        if ($professional->company_id !== auth()->user()->company_id) {
            abort(403, 'No tienes permisos para acceder a este profesional.');
        }
    }
}
