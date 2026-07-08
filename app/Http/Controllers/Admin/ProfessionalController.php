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
            $data['avatar'] = $request->file('avatar')->store('avatars', 'public');
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
            // Delete old avatar
            if ($professional->avatar) {
                Storage::disk('public')->delete($professional->avatar);
            }
            $data['avatar'] = $request->file('avatar')->store('avatars', 'public');
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
            Storage::disk('public')->delete($professional->avatar);
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
