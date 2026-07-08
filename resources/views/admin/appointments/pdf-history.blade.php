<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Historial de Turnos</title>
    <style>
        body { font-family: sans-serif; font-size: 12px; color: #333; }
        .header { text-align: center; margin-bottom: 30px; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #f2f2f2; }
        .status-badge { padding: 3px 6px; border-radius: 4px; font-size: 10px; }
    </style>
</head>
<body>
    <div class="header">
        <h2>Historial de Turnos</h2>
        <p><strong>{{ $company->name }}</strong><br>
        Periodo: {{ $request->start_date ?? 'Inicio' }} al {{ $request->end_date ?? 'Fin' }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th>Fecha</th>
                <th>Hora</th>
                <th>Paciente</th>
                <th>Teléfono</th>
                <th>Estado</th>
            </tr>
        </thead>
        <tbody>
            @foreach($appointments as $app)
            <tr>
                <td>{{ $app->date->format('d/m/Y') }}</td>
                <td>{{ \Carbon\Carbon::parse($app->time)->format('H:i') }}</td>
                <td>{{ $app->full_patient_name }}</td>
                <td>{{ $app->patient_phone }}</td>
                <td>{{ $app->status }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div style="margin-top: 20px; text-align: right;">
        <p>Total de turnos en el periodo: {{ $appointments->count() }}</p>
    </div>
</body>
</html>
