@extends('layouts.admin')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="fw-bold mb-0">Gestión de Turnos</h2>
    <div class="d-flex gap-2">
        <a href="{{ route('admin.appointments.create') }}" class="btn btn-success">
            <i class="bi bi-plus-lg"></i> Nuevo Turno
        </a>
        <a href="{{ route('admin.appointments.export', request()->all()) }}" class="btn btn-outline-primary">
            <i class="bi bi-file-earmark-pdf"></i> Exportar Historial (PDF)
        </a>
    </div>
</div>

<!-- Filtros -->
<div class="card mb-4">
    <div class="card-body">
        <form action="{{ route('admin.appointments.index') }}" method="GET" class="row g-3">
            <div class="col-md-3">
                <label class="small fw-bold">Desde</label>
                <input type="date" name="start_date" class="form-control" value="{{ request('start_date') }}">
            </div>
            <div class="col-md-3">
                <label class="small fw-bold">Hasta</label>
                <input type="date" name="end_date" class="form-control" value="{{ request('end_date') }}">
            </div>
            <div class="col-md-3">
                <label class="small fw-bold">Estado</label>
                <select name="status" class="form-select">
                    <option value="">Todos</option>
                    <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Activos</option>
                    <option value="cancelled" {{ request('status') === 'cancelled' ? 'selected' : '' }}>Cancelados</option>
                    <option value="rescheduled" {{ request('status') === 'rescheduled' ? 'selected' : '' }}>Reprogramados</option>
                </select>
            </div>
            <div class="col-md-3 d-flex align-items-end">
                <button type="submit" class="btn btn-secondary w-100">Filtrar</button>
            </div>
        </form>
    </div>
</div>

<!-- Listado -->
<div class="card" id="appointments-card">
    <div class="card-body p-0">
        <form action="{{ route('admin.appointments.batch_delete') }}" method="POST" id="batch-form">
            @csrf
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light text-muted small">
                        <tr>
                            <th class="px-4" style="width: 40px;"><input type="checkbox" id="select-all"></th>
                            <th class="px-4">Paciente</th>
                            <th>Fecha</th>
                            <th>Hora</th>
                            <th>Estado</th>
                            <th class="text-end px-4">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($appointments as $app)
                        <tr>
                            <td class="px-4">
                                <input type="checkbox" name="ids[]" value="{{ $app->id }}" class="item-checkbox">
                            </td>
                            <td class="px-4">
                                <div class="fw-bold">{{ $app->full_patient_name }}</div>
                                <div class="text-muted small">{{ $app->patient_email }} | {{ $app->patient_phone }}</div>
                            </td>
                            <td>{{ $app->date->format('d/m/Y') }}</td>
                            <td>{{ \Carbon\Carbon::parse($app->time)->format('H:i') }} hs</td>
                            <td>
                                <span class="badge rounded-pill bg-{{ $app->status === 'active' ? 'primary' : ($app->status === 'cancelled' ? 'danger' : 'warning') }}">
                                    {{ $app->status }}
                                </span>
                            </td>
                            <td class="text-end px-4">
                                <div class="dropdown">
                                    <button class="btn btn-sm btn-light border" type="button" data-bs-toggle="dropdown">
                                        <i class="bi bi-three-dots-vertical"></i>
                                    </button>
                                    <ul class="dropdown-menu dropdown-menu-end">
                                        <li><a class="dropdown-item" href="{{ route('admin.appointments.reschedule.form', $app) }}">Reprogramar</a></li>
                                        <li>
                                            <form action="{{ route('admin.appointments.cancel', $app) }}" method="POST">
                                                @csrf
                                                <button type="submit" class="dropdown-item text-danger">Cancelar</button>
                                            </form>
                                        </li>
                                    </ul>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="py-5 text-center text-muted">No se encontraron turnos.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            @if($appointments->count() > 0)
            <div class="p-3 border-top d-flex justify-content-between align-items-center">
                <button type="submit" class="btn btn-outline-danger btn-sm" onclick="return confirm('¿Borrar seleccionados?')">
                    Eliminar Seleccionados
                </button>
                <div>{{ $appointments->links() }}</div>
            </div>
            @endif
        </form>
    </div>
</div>
@endsection

@section('scripts')
<script>
    function attachListeners() {
        const selectAll = document.getElementById('select-all');
        if (selectAll) {
            selectAll.addEventListener('click', function(e) {
                document.querySelectorAll('.item-checkbox').forEach(cb => cb.checked = e.target.checked);
            });
        }
    }

    attachListeners();

    // Polling cada 15 segundos para buscar nuevos turnos registrados
    setInterval(function() {
        // Evitar actualizar si el usuario tiene elementos seleccionados o un dropdown abierto
        const anyChecked = document.querySelectorAll('.item-checkbox:checked').length > 0;
        const dropdownOpen = document.querySelectorAll('.dropdown-menu.show').length > 0;
        
        if (anyChecked || dropdownOpen) {
            return;
        }

        fetch(window.location.href)
            .then(response => response.text())
            .then(html => {
                const parser = new DOMParser();
                const doc = parser.parseFromString(html, 'text/html');
                const newCard = doc.getElementById('appointments-card');
                const currentCard = document.getElementById('appointments-card');
                
                if (newCard && currentCard) {
                    currentCard.innerHTML = newCard.innerHTML;
                    attachListeners();
                }
            })
            .catch(error => console.error('Error al actualizar la lista de turnos:', error));
    }, 15000); // 15 segundos
</script>
@endsection
