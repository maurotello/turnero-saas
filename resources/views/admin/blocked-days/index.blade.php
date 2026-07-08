@extends('layouts.admin')

@section('content')
<div class="mb-4">
    <h2 class="fw-bold mb-0">Días Bloqueados</h2>
    <p class="text-muted">Inhabilitá fechas completas (vacaciones, feriados, etc.).</p>
</div>

<div class="row">
    <div class="col-md-4">
        <div class="card">
            <div class="card-header bg-white py-3"><h5 class="mb-0 fw-bold">Bloquear Fecha</h5></div>
            <div class="card-body">
                <form action="{{ route('admin.blocked-days.store') }}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label small fw-bold">Fecha</label>
                        <input type="date" name="date" class="form-control" required min="{{ date('Y-m-d') }}">
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold">Motivo (Opcional)</label>
                        <input type="text" name="reason" class="form-control" placeholder="Ej: Vacaciones">
                    </div>
                    <div class="d-grid">
                        <button type="submit" class="btn btn-primary">Bloquear Día</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <div class="col-md-8">
        <div class="card">
            <div class="card-header bg-white py-3"><h5 class="mb-0 fw-bold">Próximos Días Bloqueados</h5></div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="bg-light text-muted small">
                            <tr>
                                <th class="px-4">Fecha</th>
                                <th>Motivo</th>
                                <th class="text-end px-4">Acción</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($blockedDays as $day)
                            <tr>
                                <td class="px-4 fw-bold">{{ $day->date->format('d/m/Y') }}</td>
                                <td>{{ $day->reason ?? '-' }}</td>
                                <td class="text-end px-4">
                                    <form action="{{ route('admin.blocked-days.destroy', $day) }}" method="POST">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger" onclick="return confirm('¿Desbloquear este día?')">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="3" class="py-5 text-center text-muted">No hay días bloqueados.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
