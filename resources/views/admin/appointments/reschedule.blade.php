@extends('layouts.admin')

@section('content')
<div class="mb-4">
    <h2 class="fw-bold mb-0">Reprogramar Turno</h2>
    <p class="text-muted">Seleccioná una nueva fecha y hora para el paciente <strong>{{ $appointment->full_patient_name }}</strong>.</p>
</div>

<div class="row">
    <div class="col-md-6">
        <div class="card">
            <div class="card-body">
                <form action="{{ route('admin.appointments.reschedule', $appointment) }}" method="POST">
                    @csrf
                    @method('PUT')
                    
                    <div class="mb-4">
                        <label class="form-label small fw-bold">Nueva Fecha</label>
                        <input type="date" id="reschedule-date" name="date" class="form-control" required min="{{ date('Y-m-d') }}">
                    </div>

                    <div class="mb-4">
                        <label class="form-label small fw-bold">Nuevo Horario Disponibilidad</label>
                        <div id="slots-container" class="row g-2">
                            <div class="col-12 text-muted small">Seleccioná una fecha para ver horarios disponibles.</div>
                        </div>
                        <input type="hidden" name="time" id="reschedule-time">
                    </div>

                    <div class="d-flex gap-2">
                        <a href="{{ route('admin.appointments.index') }}" class="btn btn-light px-4">Cancelar</a>
                        <button type="submit" class="btn btn-primary px-4" id="btn-submit" disabled>Confirmar Cambio</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="col-md-6">
        <div class="card bg-light border-0">
            <div class="card-body">
                <h6 class="fw-bold">Datos Actuales</h6>
                <hr>
                <div class="mb-2"><span class="text-muted small">Paciente:</span><br>{{ $appointment->full_patient_name }}</div>
                <div class="mb-2"><span class="text-muted small">Turno Original:</span><br>{{ $appointment->date->format('d/m/Y') }} — {{ \Carbon\Carbon::parse($appointment->time)->format('H:i') }} hs</div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    document.getElementById('reschedule-date').addEventListener('change', async function(e) {
        const date = e.target.value;
        const container = document.getElementById('slots-container');
        container.innerHTML = '<div class="col-12 small text-muted">Cargando...</div>';
        
        try {
            const response = await fetch(`{{ route('booking.slots', $appointment->company->slug) }}?date=${date}`);
            const data = await response.json();
            
            container.innerHTML = '';
            if(data.slots.length === 0) {
                container.innerHTML = '<div class="col-12 text-danger small">No hay horarios disponibles para esta fecha.</div>';
            } else {
                data.slots.forEach(slot => {
                    const div = document.createElement('div');
                    div.className = 'col-3';
                    div.innerHTML = `<button type="button" class="btn btn-sm btn-outline-primary w-100 slot-btn" onclick="selectTime(this, '${slot}')">${slot}</button>`;
                    container.appendChild(div);
                });
            }
        } catch (error) {
            container.innerHTML = '<div class="col-12 text-danger small">Error al cargar horarios.</div>';
        }
    });

    function selectTime(btn, time) {
        document.querySelectorAll('.slot-btn').forEach(b => b.classList.replace('btn-primary', 'btn-outline-primary'));
        btn.classList.replace('btn-outline-primary', 'btn-primary');
        document.getElementById('reschedule-time').value = time;
        document.getElementById('btn-submit').disabled = false;
    }
</script>
@endsection
