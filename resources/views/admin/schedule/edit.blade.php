@extends('layouts.admin')

@section('content')
<div class="mb-4 d-flex justify-content-between align-items-center">
    <div>
        <h2 class="fw-bold mb-0">Configuración de Agenda</h2>
        <p class="text-muted">Definí los días laborales, horarios y tipo de turno para cada jornada.</p>
    </div>
</div>

@if(auth()->user()->role !== 'doctor' && $professionals->isNotEmpty())
    <!-- Selector de Agenda (solo administradores) -->
    <div class="card mb-4 shadow-sm border-0 bg-white">
        <div class="card-body">
            <div class="row align-items-center">
                <div class="col-md-6">
                    <label class="form-label fw-bold small text-muted mb-1">Agenda a configurar</label>
                    <select class="form-select" onchange="window.location.href = '{{ route('admin.schedule.edit') }}?professional_id=' + this.value">
                        <option value="global" {{ is_null($selectedProfessionalId) ? 'selected' : '' }}>
                            Global (Para todos los profesionales sin agenda propia)
                        </option>
                        @foreach($professionals as $prof)
                            <option value="{{ $prof->id }}" {{ $selectedProfessionalId === $prof->id ? 'selected' : '' }}>
                                Profesional: {{ $prof->name }} ({{ $prof->specialty ?? 'General' }})
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-6 text-md-end mt-3 mt-md-0">
                    <span class="badge bg-light text-primary border border-primary-subtle p-2">
                        <i class="bi bi-info-circle me-1"></i>
                        @if(is_null($selectedProfessionalId))
                            Editando horarios generales de la clínica.
                        @else
                            Editando horarios específicos de un profesional.
                        @endif
                    </span>
                </div>
            </div>
        </div>
    </div>
@else
    <!-- Título de agenda de profesional médico -->
    <div class="alert alert-primary bg-primary-subtle border-primary-subtle text-primary-emphasis mb-4">
        <i class="bi bi-person-workspace me-2"></i>
        Estás editando tus horarios de agenda individuales.
    </div>
@endif

<form action="{{ route('admin.schedule.update') }}" method="POST">
    @csrf
    @method('PUT')
    
    <input type="hidden" name="professional_id" value="{{ $selectedProfessionalId ?? 'global' }}">

    <div class="card shadow-sm border-0">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table align-middle mb-0">
                    <thead class="bg-light text-muted small text-uppercase">
                        <tr>
                            <th class="px-4 py-3" style="width: 20%;">Día</th>
                            <th class="py-3" style="width: 15%;">Atención</th>
                            <th class="py-3" style="width: 65%;">Intervalos y Tipo de Turno</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($days as $index => $name)
                            @php 
                                $daySettings = $settings->get($index) ?? collect();
                                $activeIntervals = $daySettings->filter(fn($s) => $s->is_active);
                                $isDayActive = $daySettings->isNotEmpty() ? $daySettings->first()->is_active : false;
                            @endphp
                            <tr>
                                <td class="px-4 fw-bold text-dark">{{ $name }}</td>
                                <td>
                                    <div class="form-check form-switch">
                                        <input class="form-check-input day-toggle" type="checkbox" name="days[{{ $index }}][is_active]" id="toggle-{{ $index }}" {{ $isDayActive ? 'checked' : '' }} onchange="toggleDay({{ $index }})">
                                        <label class="form-check-label small text-muted" for="toggle-{{ $index }}">Activo</label>
                                    </div>
                                </td>
                                <td class="py-3">
                                    <div id="intervals-container-{{ $index }}" class="space-y-2">
                                        @if($activeIntervals->isEmpty())
                                            <!-- Default empty interval shown if no active interval exists -->
                                            <div class="row g-2 align-items-center interval-row">
                                                <div class="col-md-3 col-sm-6">
                                                    <div class="input-group input-group-sm">
                                                        <span class="input-group-text"><i class="bi bi-clock-fill text-muted"></i></span>
                                                        <input type="time" name="days[{{ $index }}][intervals][0][start_time]" class="form-control" value="08:00" required>
                                                    </div>
                                                </div>
                                                <div class="col-md-3 col-sm-6">
                                                    <div class="input-group input-group-sm">
                                                        <span class="input-group-text"><i class="bi bi-clock"></i></span>
                                                        <input type="time" name="days[{{ $index }}][intervals][0][end_time]" class="form-control" value="12:00" required>
                                                    </div>
                                                </div>
                                                <div class="col-md-4 col-sm-10">
                                                    <div class="input-group input-group-sm">
                                                        <span class="input-group-text"><i class="bi bi-calendar2-week"></i></span>
                                                        <select name="days[{{ $index }}][intervals][0][appointment_type_id]" class="form-select">
                                                            @foreach($appointmentTypes as $type)
                                                                <option value="{{ $type->id }}">{{ $type->name }} ({{ $type->duration }} min)</option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="col-md-2 col-sm-2 text-end">
                                                    <button type="button" class="btn btn-sm btn-outline-danger border-0" onclick="removeIntervalRow(this)">
                                                        <i class="bi bi-trash3-fill"></i>
                                                    </button>
                                                </div>
                                            </div>
                                        @else
                                            @foreach($activeIntervals->values() as $key => $interval)
                                                <div class="row g-2 align-items-center interval-row {{ $key > 0 ? 'mt-2' : '' }}">
                                                    <div class="col-md-3 col-sm-6">
                                                        <div class="input-group input-group-sm">
                                                            <span class="input-group-text"><i class="bi bi-clock-fill text-muted"></i></span>
                                                            <input type="time" name="days[{{ $index }}][intervals][{{ $key }}][start_time]" class="form-control" value="{{ Carbon\Carbon::parse($interval->start_time)->format('H:i') }}" required>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-3 col-sm-6">
                                                        <div class="input-group input-group-sm">
                                                            <span class="input-group-text"><i class="bi bi-clock"></i></span>
                                                            <input type="time" name="days[{{ $index }}][intervals][{{ $key }}][end_time]" class="form-control" value="{{ Carbon\Carbon::parse($interval->end_time)->format('H:i') }}" required>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-4 col-sm-10">
                                                        <div class="input-group input-group-sm">
                                                            <span class="input-group-text"><i class="bi bi-calendar2-week"></i></span>
                                                            <select name="days[{{ $index }}][intervals][{{ $key }}][appointment_type_id]" class="form-select">
                                                                @foreach($appointmentTypes as $type)
                                                                    <option value="{{ $type->id }}" {{ $interval->appointment_type_id == $type->id ? 'selected' : '' }}>
                                                                        {{ $type->name }} ({{ $type->duration }} min)
                                                                    </option>
                                                                @endforeach
                                                            </select>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-2 col-sm-2 text-end">
                                                        <button type="button" class="btn btn-sm btn-outline-danger border-0" onclick="removeIntervalRow(this)">
                                                            <i class="bi bi-trash3-fill"></i>
                                                        </button>
                                                    </div>
                                                </div>
                                            @endforeach
                                        @endif
                                    </div>
                                    <button type="button" class="btn btn-link btn-sm text-primary p-0 mt-2 fw-semibold text-decoration-none add-interval-btn" id="add-btn-{{ $index }}" onclick="addIntervalRow({{ $index }})">
                                        <i class="bi bi-plus-lg me-1"></i> Agregar Horario
                                    </button>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        <div class="card-footer bg-white p-3 text-end border-0">
            <button type="submit" class="btn btn-primary px-5 fw-bold" style="border-radius: 8px;">Guardar Cambios</button>
        </div>
    </div>
</form>

<script>
// Serializar tipos de turnos activos para inyectar dinámicamente
const appointmentTypes = @json($appointmentTypes);

function addIntervalRow(dayIndex) {
    const container = document.getElementById('intervals-container-' + dayIndex);
    const rowCount = container.getElementsByClassName('interval-row').length;
    
    let optionsHtml = '';
    appointmentTypes.forEach(type => {
        optionsHtml += `<option value="${type.id}">${type.name} (${type.duration} min)</option>`;
    });

    const newRow = document.createElement('div');
    newRow.className = 'row g-2 align-items-center interval-row mt-2';
    newRow.innerHTML = `
        <div class="col-md-3 col-sm-6">
            <div class="input-group input-group-sm">
                <span class="input-group-text"><i class="bi bi-clock-fill text-muted"></i></span>
                <input type="time" name="days[${dayIndex}][intervals][${rowCount}][start_time]" class="form-control" value="08:00" required>
            </div>
        </div>
        <div class="col-md-3 col-sm-6">
            <div class="input-group input-group-sm">
                <span class="input-group-text"><i class="bi bi-clock"></i></span>
                <input type="time" name="days[${dayIndex}][intervals][${rowCount}][end_time]" class="form-control" value="12:00" required>
            </div>
        </div>
        <div class="col-md-4 col-sm-10">
            <div class="input-group input-group-sm">
                <span class="input-group-text"><i class="bi bi-calendar2-week"></i></span>
                <select name="days[${dayIndex}][intervals][${rowCount}][appointment_type_id]" class="form-select" required>
                    ${optionsHtml}
                </select>
            </div>
        </div>
        <div class="col-md-2 col-sm-2 text-end">
            <button type="button" class="btn btn-sm btn-outline-danger border-0" onclick="removeIntervalRow(this)">
                <i class="bi bi-trash3-fill"></i>
            </button>
        </div>
    `;
    container.appendChild(newRow);
    toggleDay(dayIndex);
}

function removeIntervalRow(button) {
    const row = button.closest('.interval-row');
    const container = row.parentNode;
    
    row.remove();
    
    // Reindexar inputs
    const rows = container.getElementsByClassName('interval-row');
    const dayIndex = container.id.replace('intervals-container-', '');
    
    Array.from(rows).forEach((r, index) => {
        r.querySelector('input[name*="[start_time]"]').name = `days[${dayIndex}][intervals][${index}][start_time]`;
        r.querySelector('input[name*="[end_time]"]').name = `days[${dayIndex}][intervals][${index}][end_time]`;
        r.querySelector('select[name*="[appointment_type_id]"]').name = `days[${dayIndex}][intervals][${index}][appointment_type_id]`;
    });
}

function toggleDay(dayIndex) {
    const isChecked = document.getElementById('toggle-' + dayIndex).checked;
    const container = document.getElementById('intervals-container-' + dayIndex);
    const addBtn = document.getElementById('add-btn-' + dayIndex);
    
    const inputs = container.querySelectorAll('input, select');
    inputs.forEach(input => {
        input.disabled = !isChecked;
    });
    
    if (addBtn) {
        if (isChecked) {
            addBtn.classList.remove('disabled');
            addBtn.style.pointerEvents = 'auto';
            addBtn.style.opacity = '1';
        } else {
            addBtn.classList.add('disabled');
            addBtn.style.pointerEvents = 'none';
            addBtn.style.opacity = '0.5';
        }
    }
}

document.addEventListener('DOMContentLoaded', function() {
    for (let i = 0; i <= 6; i++) {
        toggleDay(i);
    }
});
</script>
@endsection
