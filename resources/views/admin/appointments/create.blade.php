@extends('layouts.admin')

@section('content')
<div class="mb-4">
    <h2 class="fw-bold mb-0">Nuevo Turno</h2>
    <p class="text-muted">Cargar manualmente un nuevo turno para la empresa/profesional.</p>
</div>

@if ($errors->any())
    <div class="alert alert-danger alert-dismissible fade show mb-4 rounded-3 shadow-sm" role="alert">
        <ul class="mb-0 list-unstyled">
            @foreach ($errors->all() as $error)
                <li><i class="bi bi-exclamation-triangle-fill me-2"></i> {{ $error }}</li>
            @endforeach
        </ul>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

<form action="{{ route('admin.appointments.store') }}" method="POST">
    @csrf

    <div class="row">
        <!-- Datos del Paciente -->
        <div class="col-md-6 mb-4">
            <div class="card h-100">
                <div class="card-body">
                    <h5 class="fw-bold mb-4"><i class="bi bi-person-fill me-2"></i> Datos del Paciente</h5>
                    
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">Nombre</label>
                            <input type="text" name="patient_first_name" class="form-control" value="{{ old('patient_first_name') }}" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">Apellido</label>
                            <input type="text" name="patient_last_name" class="form-control" value="{{ old('patient_last_name') }}" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">DNI del paciente</label>
                            <input type="text" name="patient_dni" class="form-control" value="{{ old('patient_dni') }}" placeholder="Ej: 35XXXXXX" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">Teléfono de contacto</label>
                            <input type="tel" name="patient_phone" class="form-control" value="{{ old('patient_phone') }}" placeholder="Ej: 2920XXXXXX" required>
                        </div>
                        <div class="col-12">
                            <label class="form-label small fw-bold">Email (Opcional)</label>
                            <input type="email" name="patient_email" class="form-control" value="{{ old('patient_email') }}">
                        </div>
                        <div class="col-12">
                            <label class="form-label small fw-bold">Obra Social / Prepaga</label>
                            <input type="text" name="patient_insurance" class="form-control" value="{{ old('patient_insurance') }}" placeholder="OSDE, Swiss, Particular, etc." required>
                            <div class="form-text text-muted small">Si no tiene obra social, escriba Particular.</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Fecha, Hora y Pago -->
        <div class="col-md-6 mb-4">
            <div class="card h-100">
                <div class="card-body d-flex flex-column">
                    <h5 class="fw-bold mb-4"><i class="bi bi-calendar-check-fill me-2"></i> Fecha, Horario y Pago</h5>
                    
                    <div class="mb-4">
                        <label class="form-label small fw-bold">Fecha del Turno</label>
                        <input type="date" id="appointment-date" name="date" class="form-control" value="{{ old('date') }}" required min="{{ date('Y-m-d') }}">
                    </div>

                    <div class="mb-4 flex-grow-1">
                        <label class="form-label small fw-bold">Horarios Disponibles</label>
                        <div id="slots-container" class="row g-2">
                            <div class="col-12 text-muted small">Seleccioná una fecha para ver horarios disponibles.</div>
                        </div>
                        <input type="hidden" name="time" id="appointment-time" value="{{ old('time') }}">
                    </div>

                    <div class="mb-4">
                        <label class="form-label small fw-bold d-block">Estado del Pago</label>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="payment_status" id="payment-paid" value="paid" {{ old('payment_status', 'paid') === 'paid' ? 'checked' : '' }} required>
                            <label class="form-check-label" for="payment-paid">
                                <span class="badge bg-success"><i class="bi bi-check-lg"></i> Pagado (Efectivo/Otro)</span>
                            </label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="payment_status" id="payment-pending" value="pending" {{ old('payment_status') === 'pending' ? 'checked' : '' }} required>
                            <label class="form-check-label" for="payment-pending">
                                <span class="badge bg-warning text-dark"><i class="bi bi-hourglass-split"></i> Dejar pendiente de pago</span>
                            </label>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="d-flex gap-2">
        <a href="{{ route('admin.appointments.index') }}" class="btn btn-light px-4">Cancelar</a>
        <button type="submit" class="btn btn-primary px-4" id="btn-submit" disabled>Crear Turno</button>
    </div>
</form>
@endsection

@section('scripts')
<script>
    const slotsContainer = document.getElementById('slots-container');
    const timeInput = document.getElementById('appointment-time');
    const submitBtn = document.getElementById('btn-submit');
    const dateInput = document.getElementById('appointment-date');

    async function loadSlots(date) {
        if (!date) return;
        
        slotsContainer.innerHTML = '<div class="col-12 small text-muted">Cargando horarios...</div>';
        submitBtn.disabled = true;
        timeInput.value = '';

        try {
            const response = await fetch(`{{ route('booking.slots', $company->slug) }}?date=${date}`);
            const data = await response.json();
            
            slotsContainer.innerHTML = '';
            if (data.slots.length === 0) {
                slotsContainer.innerHTML = '<div class="col-12 text-danger small">No hay horarios disponibles para esta fecha.</div>';
            } else {
                data.slots.forEach(slot => {
                    const div = document.createElement('div');
                    div.className = 'col-3';
                    div.innerHTML = `<button type="button" class="btn btn-sm btn-outline-primary w-100 slot-btn" onclick="selectTime(this, '${slot}')">${slot}</button>`;
                    slotsContainer.appendChild(div);
                });
                
                // Si había una hora seleccionada anteriormente por old() o redirección
                const oldTime = "{{ old('time') }}";
                if (oldTime) {
                    const matchingBtn = Array.from(document.querySelectorAll('.slot-btn'))
                        .find(btn => btn.innerText.trim() === oldTime);
                    if (matchingBtn) {
                        selectTime(matchingBtn, oldTime);
                    }
                }
            }
        } catch (error) {
            slotsContainer.innerHTML = '<div class="col-12 text-danger small">Error al cargar horarios.</div>';
        }
    }

    dateInput.addEventListener('change', function(e) {
        loadSlots(e.target.value);
    });

    function selectTime(btn, time) {
        document.querySelectorAll('.slot-btn').forEach(b => b.classList.replace('btn-primary', 'btn-outline-primary'));
        btn.classList.replace('btn-outline-primary', 'btn-primary');
        timeInput.value = time;
        submitBtn.disabled = false;
    }

    // Cargar slots si hay fecha inicial seleccionada (old() o recarga)
    if (dateInput.value) {
        loadSlots(dateInput.value);
    }
</script>
@endsection
