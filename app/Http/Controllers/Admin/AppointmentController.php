<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Appointment;
use App\Services\CalendarService;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;

class AppointmentController extends Controller
{
    protected $calendarService;

    public function __construct(CalendarService $calendarService)
    {
        $this->calendarService = $calendarService;
    }

    public function index(Request $request)
    {
        $query = Appointment::query();

        if ($request->filled('start_date')) {
            $query->whereDate('date', '>=', $request->start_date);
        }

        if ($request->filled('end_date')) {
            $query->whereDate('date', '<=', $request->end_date);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $appointments = $query->orderBy('date', 'desc')
            ->orderBy('time', 'desc')
            ->paginate(15);

        return view('admin.appointments.index', compact('appointments'));
    }

    public function create()
    {
        $company = auth()->user()->company;
        return view('admin.appointments.create', compact('company'));
    }

    public function store(Request $request)
    {
        $company = auth()->user()->company;

        $request->validate([
            'date' => 'required|date|after_or_equal:today',
            'time' => 'required',
            'patient_first_name' => 'required|string|max:255',
            'patient_last_name' => 'required|string|max:255',
            'patient_dni' => 'required|string|max:20',
            'patient_phone' => 'required|string|max:20',
            'patient_email' => 'nullable|email|max:255',
            'patient_insurance' => 'required|string|max:255',
            'payment_status' => 'required|in:paid,pending',
        ]);

        // Validar disponibilidad preventiva
        $date = Carbon::parse($request->date);
        $available = $this->calendarService->getAvailableSlots($company, $date);

        if (!in_array($request->time, $available)) {
            return back()->withErrors(['time' => 'El horario seleccionado ya no está disponible.'])->withInput();
        }

        // Definir status, payment_method y source
        $status = $request->payment_status === 'paid' ? 'active' : 'pending_payment';
        $paymentMethod = $request->payment_status === 'paid' ? 'cash' : null;

        Appointment::create([
            'date' => $request->date,
            'time' => $request->time,
            'patient_first_name' => $request->patient_first_name,
            'patient_last_name' => $request->patient_last_name,
            'patient_dni' => $request->patient_dni,
            'patient_phone' => $request->patient_phone,
            'patient_email' => $request->patient_email,
            'patient_insurance' => $request->patient_insurance,
            'status' => $status,
            'payment_method' => $paymentMethod,
            'source' => 'admin',
            'notes' => 'Registrado manualmente desde el panel de administración.',
            'cancel_token' => \Illuminate\Support\Str::random(64),
        ]);

        return redirect()->route('admin.appointments.index')->with('success', 'Turno creado correctamente.');
    }

    public function rescheduleForm(Appointment $appointment)
    {
        return view('admin.appointments.reschedule', compact('appointment'));
    }

    public function reschedule(Request $request, Appointment $appointment)
    {
        $request->validate([
            'date' => 'required|date|after_or_equal:today',
            'time' => 'required',
        ]);

        // Store history
        $appointment->update([
            'original_date' => $appointment->date,
            'original_time' => $appointment->time,
            'date' => $request->date,
            'time' => $request->time,
            'status' => 'rescheduled',
            'rescheduled_by' => auth()->id(),
            'rescheduled_at' => now(),
        ]);

        return redirect()->route('admin.appointments.index')->with('success', 'Turno reprogramado correctamente.');
    }

    public function cancel(Request $request, Appointment $appointment)
    {
        $appointment->update([
            'status' => 'cancelled',
            'cancelled_at' => now(),
            'cancellation_reason' => $request->reason ?? 'Cancelado por administración',
        ]);

        return back()->with('success', 'Turno cancelado.');
    }

    public function exportPdf(Request $request)
    {
        $query = Appointment::query();

        if ($request->filled('start_date')) {
            $query->whereDate('date', '>=', $request->start_date);
        }

        if ($request->filled('end_date')) {
            $query->whereDate('date', '<=', $request->end_date);
        }

        $appointments = $query->orderBy('date')->get();
        $company = auth()->user()->company;

        $pdf = Pdf::loadView('admin.appointments.pdf-history', compact('appointments', 'company', 'request'));
        
        return $pdf->download('historial-turnos.pdf');
    }

    public function destroyBatch(Request $request)
    {
        $request->validate(['ids' => 'required|array']);
        Appointment::whereIn('id', $request->ids)->delete();

        return back()->with('success', 'Registros eliminados.');
    }
}
