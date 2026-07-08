<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Reserva de Turnos - {{ $company->name }}</title>
    
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com?plugins=forms"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        'primary': '{{ $company->primary_color ?? '#009BA4' }}',
                        'primary-dark': 'color-mix(in srgb, {{ $company->primary_color ?? '#009BA4' }} 85%, black)',
                        'brand-gray': '#F7FAFC',
                    },
                    fontFamily: {
                        sans: ['Inter', 'sans-serif'],
                    }
                }
            }
        }
    </script>
    
    <style>
        body { background-color: #F7FAFC; }
        .step-card {
            display: none;
            opacity: 0;
            transform: translateY(10px);
            transition: all 0.3s ease-out;
        }
        .step-card.active {
            display: block;
            opacity: 1;
            transform: translateY(0);
        }
        ::-webkit-scrollbar { width: 6px; }
        ::-webkit-scrollbar-track { background: transparent; }
        ::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 4px; }
    </style>
    <!-- Dynamic Favicon -->
    @php
        $faviconCompany = null;
        if (isset($company)) {
            $faviconCompany = $company;
        } elseif (auth()->check() && auth()->user()->company) {
            $faviconCompany = auth()->user()->company;
        }
        
        $defaultSvg = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><rect width="100" height="100" rx="50" fill="#009BA4" /><path d="M30 40h40M35 25v10M65 25v10M30 80h40a10 10 0 0010-10V35a10 10 0 00-10-10H30a10 10 0 00-10 10v35a10 10 0 0010 10z" fill="none" stroke="#FFFFFF" stroke-linecap="round" stroke-linejoin="round" stroke-width="6"/></svg>';
        $base64DefaultSvg = base64_encode($defaultSvg);
    @endphp

    @if($faviconCompany)
        @if($faviconCompany->logo)
            <link rel="icon" type="image/{{ pathinfo($faviconCompany->logo, PATHINFO_EXTENSION) === 'svg' ? 'svg+xml' : 'png' }}" href="{{ asset('storage/' . $faviconCompany->logo) }}">
        @else
            @php
                $initial = strtoupper(substr($faviconCompany->name, 0, 1));
                $primaryColor = $faviconCompany->primary_color ?? '#009BA4';
                $svg = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><rect width="100" height="100" rx="20" fill="' . $primaryColor . '"/><text x="50%" y="55%" dominant-baseline="middle" text-anchor="middle" font-family="\'Inter\', sans-serif" font-weight="bold" font-size="60" fill="#FFFFFF">' . $initial . '</text></svg>';
                $base64Svg = base64_encode($svg);
            @endphp
            <link rel="icon" type="image/svg+xml" href="data:image/svg+xml;base64,{{ $base64Svg }}">
        @endif
    @else
        <link rel="icon" type="image/svg+xml" href="data:image/svg+xml;base64,{{ $base64DefaultSvg }}">
    @endif
</head>
<body class="text-gray-800 antialiased min-h-screen flex flex-col">

    <!-- Top Bar / Navbar -->
    <nav class="bg-white border-b border-gray-100 py-3 shadow-sm sticky top-0 z-50">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 flex justify-between items-center">
            <a href="{{ route('booking.show', $company->slug) }}" class="flex items-center gap-2 text-decoration-none">
                @if($company->logo)
                    <img src="{{ asset('storage/' . $company->logo) }}" class="rounded-full w-8 h-8 object-cover border border-gray-200">
                @else
                    <div class="w-8 h-8 rounded-full bg-primary text-white flex items-center justify-center font-bold text-xs">
                        {{ strtoupper(substr($company->name, 0, 1)) }}
                    </div>
                @endif
                <span class="font-bold text-gray-950 text-sm md:text-base">{{ $company->name }}</span>
            </a>

            <div class="flex items-center gap-3">
                @auth('patient')
                    <a href="{{ route('booking.dashboard', $company->slug) }}" class="text-gray-600 hover:text-primary text-xs md:text-sm font-semibold transition-colors flex items-center gap-1">
                        <i class="bi bi-calendar-check"></i> Mis Turnos
                    </a>
                    <a href="{{ route('booking.profile', $company->slug) }}" class="text-gray-600 hover:text-primary text-xs md:text-sm font-semibold transition-colors flex items-center gap-1">
                        <i class="bi bi-person"></i> Mi Perfil
                    </a>
                    <form method="POST" action="{{ route('booking.logout', $company->slug) }}" class="inline m-0">
                        @csrf
                        <button type="submit" class="text-red-500 hover:text-red-700 text-xs md:text-sm font-semibold transition-colors flex items-center gap-1 border-0 bg-transparent p-0">
                            <i class="bi bi-box-arrow-right"></i> Salir
                        </button>
                    </form>
                @else
                    <a href="{{ route('booking.login', $company->slug) }}" class="text-gray-600 hover:text-primary text-xs md:text-sm font-semibold transition-colors">
                        Iniciar Sesión
                    </a>
                    <a href="{{ route('booking.register', $company->slug) }}" class="bg-primary text-white px-3 py-1.5 rounded-full text-xs md:text-sm font-semibold hover:bg-primary-dark transition-colors shadow-sm">
                        Registrarse
                    </a>
                @endauth
            </div>
        </div>
    </nav>

    <!-- Header / Banner -->
    <div class="bg-primary h-32 md:h-48 w-full absolute top-0 left-0 -z-10 shadow-inner opacity-90"></div>

    <div class="max-w-3xl mx-auto w-full px-4 pt-8 md:pt-16 pb-12 flex-grow">
        
        <!-- Alerts -->
        @if(session('success'))
            <div class="bg-green-50 border-l-4 border-green-500 p-4 mb-6 rounded shadow-sm flex justify-between items-start">
                <div class="flex items-center">
                    <i class="bi bi-check-circle-fill text-green-500 text-xl mr-3"></i>
                    <p class="text-green-700 text-sm font-medium">{{ session('success') }}</p>
                </div>
            </div>
        @endif
        @if(session('error') || request()->query('payment_status') === 'failure')
            <div class="bg-red-50 border-l-4 border-red-500 p-4 mb-6 rounded shadow-sm">
                <div class="flex items-center">
                    <i class="bi bi-exclamation-triangle-fill text-red-500 text-xl mr-3"></i>
                    <p class="text-red-700 text-sm font-medium">{{ session('error') ?? 'El pago fue rechazado o cancelado. Podés intentar reservar de nuevo.' }}</p>
                </div>
            </div>
        @endif
        @if(session('warning') || request()->query('payment_status') === 'pending')
            <div class="bg-yellow-50 border-l-4 border-yellow-500 p-4 mb-6 rounded shadow-sm">
                <div class="flex items-center">
                    <i class="bi bi-exclamation-circle-fill text-yellow-500 text-xl mr-3"></i>
                    <p class="text-yellow-700 text-sm font-medium">{{ session('warning') ?? 'Tu pago está pendiente de acreditación.' }}</p>
                </div>
            </div>
        @endif
        @if($errors->any())
            <div class="bg-red-50 border-l-4 border-red-500 p-4 mb-6 rounded shadow-sm">
                <ul class="text-sm text-red-700 space-y-1">
                    @foreach($errors->all() as $error)
                        <li class="flex items-center"><i class="bi bi-x-circle-fill mr-2"></i> {{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <!-- Clinic Info Card -->
        <div class="bg-white rounded-2xl shadow-lg p-6 md:p-8 mb-6 border border-gray-100 flex flex-col md:flex-row items-center md:items-start gap-6 text-center md:text-left relative overflow-hidden">
            <div class="absolute top-0 right-0 w-32 h-32 bg-primary opacity-5 rounded-bl-full -z-0"></div>
            @if($company->logo)
                <img src="{{ asset('storage/' . $company->logo) }}" alt="Logo" class="w-24 h-24 md:w-32 md:h-32 object-cover rounded-2xl shadow-sm border border-gray-100 flex-shrink-0 z-10">
            @else
                <div class="w-24 h-24 md:w-32 md:h-32 bg-gray-50 rounded-2xl flex items-center justify-center flex-shrink-0 text-primary border border-gray-100 z-10">
                    <i class="bi bi-building text-4xl opacity-50"></i>
                </div>
            @endif
            <div class="flex-grow z-10">
                <div class="inline-block px-3 py-1 bg-blue-50 text-primary text-xs font-bold rounded-full mb-2 uppercase tracking-wide">
                    {{ $company->specialty ?? 'Salud' }}
                </div>
                <h1 class="text-2xl md:text-3xl font-bold text-gray-900 mb-1">{{ $company->professional_name }}</h1>
                <p class="text-gray-500 font-medium text-sm md:text-base mb-4">{{ $company->professional_title ?? $company->name }}</p>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-3 text-sm text-gray-600">
                    @if($company->email)
                    <div class="flex items-center justify-center md:justify-start gap-2">
                        <i class="bi bi-envelope text-gray-400"></i>
                        <span>{{ $company->email }}</span>
                    </div>
                    @endif
                    @if($company->phone)
                    <div class="flex items-center justify-center md:justify-start gap-2">
                        <i class="bi bi-whatsapp text-green-500"></i>
                        <span>{{ $company->phone }}</span>
                    </div>
                    @endif
                    @if($company->address)
                    <div class="flex items-center justify-center md:justify-start gap-2 md:col-span-2">
                        <i class="bi bi-geo-alt text-gray-400"></i>
                        <span>{{ $company->address }}{{ $company->city ? ', ' . $company->city : '' }}</span>
                    </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Booking Steps -->
        @if($professionals->isEmpty())
            <div class="bg-white rounded-2xl shadow-lg p-8 border border-gray-100 text-center">
                <div class="w-16 h-16 bg-yellow-50 text-yellow-500 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="bi bi-exclamation-triangle text-2xl"></i>
                </div>
                <h3 class="text-xl font-bold text-gray-900 mb-2">Turnero deshabilitado temporariamente</h3>
                <p class="text-gray-500 text-sm max-w-md mx-auto">No hay profesionales disponibles en este momento. Por favor, comunicate directamente con el consultorio para más información.</p>
            </div>
        @elseif($appointmentTypes->isEmpty())
            <div class="bg-white rounded-2xl shadow-lg p-8 border border-gray-100 text-center">
                <div class="w-16 h-16 bg-yellow-50 text-yellow-500 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="bi bi-exclamation-triangle text-2xl"></i>
                </div>
                <h3 class="text-xl font-bold text-gray-900 mb-2">Turnero deshabilitado temporariamente</h3>
                <p class="text-gray-500 text-sm max-w-md mx-auto">No hay tipos de consultas activos configurados. Por favor, comunicate directamente con el consultorio para más información.</p>
            </div>
        @else
            @php
                $steps = [];
                if ($professionals->count() > 1) {
                    $steps[] = ['card' => 'step0', 'label' => 'Profesional'];
                }
                if ($appointmentTypes->count() > 1) {
                    $steps[] = ['card' => 'step0a', 'label' => 'Tipo de Turno'];
                }
                $steps[] = ['card' => 'step1', 'label' => 'Fecha'];
                $steps[] = ['card' => 'step2', 'label' => 'Hora'];
                $steps[] = ['card' => 'step3', 'label' => 'Datos'];
            @endphp

            <div class="bg-white rounded-2xl shadow-[0_8px_30px_rgb(0,0,0,0.04)] border border-gray-100 overflow-hidden">
                
                <!-- Step Indicators -->
                <div class="flex border-b border-gray-100 bg-gray-50/50">
                    @foreach($steps as $key => $s)
                        <div class="flex-1 py-3 px-2 text-center text-[10px] md:text-sm font-semibold {{ $key === 0 ? 'text-primary border-b-2 border-primary' : 'text-gray-400 border-b-2 border-transparent' }} transition-all duration-200" 
                             id="indicator-{{ $s['card'] }}">
                            {{ $key + 1 }}. {{ $s['label'] }}
                        </div>
                    @endforeach
                </div>

                <div class="p-5 md:p-8">
                    @if($professionals->count() > 1)
                        <!-- Step 0: Selección de Profesional -->
                        <div class="step-card active" id="step0">
                            <h2 class="text-xl font-bold text-gray-900 mb-6 flex items-center gap-2">
                                <i class="bi bi-people text-primary"></i> Seleccioná un Profesional
                            </h2>
                            
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-6">
                                @foreach($professionals as $prof)
                                    <div onclick="selectProfessional({{ $prof->id }})" id="prof-card-{{ $prof->id }}"
                                         class="professional-card border border-gray-200 rounded-xl p-4 flex items-center gap-4 cursor-pointer hover:border-primary hover:bg-primary/5 transition-all shadow-sm">
                                        @if($prof->avatar)
                                            <img src="{{ asset('storage/' . $prof->avatar) }}" class="rounded-full w-14 h-14 object-cover border border-gray-200">
                                        @else
                                            <div class="w-14 h-14 rounded-full bg-primary text-white flex items-center justify-center font-bold text-sm">
                                                {{ strtoupper(substr($prof->name, 0, 2)) }}
                                            </div>
                                        @endif
                                        <div>
                                            <span class="font-bold text-gray-900 block">{{ $prof->name }}</span>
                                            <span class="text-xs text-gray-500">{{ $prof->specialty ?? 'General' }}</span>
                                        </div>
                                    </div>
                                @endforeach
                            </div>

                            <button type="button" id="btn-next-0" onclick="goToNext()" disabled class="w-full py-3.5 rounded-xl bg-primary text-white font-bold text-lg hover:bg-primary-dark transition-colors disabled:opacity-50 disabled:cursor-not-allowed shadow-lg shadow-primary/30 active:scale-[0.98]">
                                Siguiente paso
                            </button>
                        </div>
                    @endif

                    @if($appointmentTypes->count() > 1)
                        <!-- Step 0A: Selección de Tipo de Turno -->
                        <div class="step-card" id="step0a">
                            <h2 class="text-xl font-bold text-gray-900 mb-6 flex items-center gap-2">
                                <i class="bi bi-calendar2-week text-primary"></i> Seleccioná el Tipo de Consulta
                            </h2>
                            
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-6">
                                @foreach($appointmentTypes as $type)
                                    <div onclick="selectAppointmentType({{ $type->id }})" id="type-card-{{ $type->id }}"
                                         class="appointment-type-card border border-gray-200 rounded-xl p-4 flex items-center justify-between cursor-pointer hover:border-primary hover:bg-primary/5 transition-all shadow-sm">
                                        <div>
                                            <span class="font-bold text-gray-900 block text-sm md:text-base">{{ $type->name }}</span>
                                            <span class="text-xs text-gray-500"><i class="bi bi-clock me-1"></i> {{ $type->duration }} minutos</span>
                                        </div>
                                        @if($type->price > 0)
                                            <span class="text-xs font-bold text-green-700 bg-green-50 px-2 py-1 rounded border border-green-200">${{ number_format($type->price, 0, ',', '.') }}</span>
                                        @endif
                                    </div>
                                @endforeach
                            </div>

                            <div class="flex gap-3">
                                <button type="button" onclick="goToPrev()" class="w-1/3 py-3.5 rounded-xl bg-gray-100 text-gray-700 font-bold hover:bg-gray-200 transition-colors active:scale-[0.98]">Atrás</button>
                                <button type="button" id="btn-next-0a" onclick="goToNext()" disabled class="w-2/3 py-3.5 rounded-xl bg-primary text-white font-bold text-lg hover:bg-primary-dark transition-colors disabled:opacity-50 disabled:cursor-not-allowed shadow-lg shadow-primary/30 active:scale-[0.98]">
                                    Siguiente paso
                                </button>
                            </div>
                        </div>
                    @endif

                    <!-- Step 1: Fecha -->
                    <div class="step-card" id="step1">
                        <h2 class="text-xl font-bold text-gray-900 mb-6 flex items-center gap-2">
                            <i class="bi bi-calendar3 text-primary"></i> Seleccioná el día
                        </h2>
                        
                        <div class="bg-white border border-gray-100 rounded-xl p-4 md:p-6 shadow-sm mb-6">
                            <!-- Calendar Header -->
                            <div class="flex justify-between items-center mb-6">
                                <button type="button" id="prev-month-btn" class="w-10 h-10 rounded-full flex items-center justify-center bg-gray-50 hover:bg-gray-100 text-gray-600 transition-colors">
                                    <i class="bi bi-chevron-left"></i>
                                </button>
                                <h3 class="text-lg font-bold text-gray-800 capitalize" id="current-month-display">Cargando...</h3>
                                <button type="button" id="next-month-btn" class="w-10 h-10 rounded-full flex items-center justify-center bg-gray-50 hover:bg-gray-100 text-gray-600 transition-colors">
                                    <i class="bi bi-chevron-right"></i>
                                </button>
                            </div>
                            
                            <!-- Weekdays -->
                            <div class="grid grid-cols-7 text-center text-xs font-bold text-gray-400 mb-3">
                                <div>DO</div><div>LU</div><div>MA</div><div>MI</div><div>JU</div><div>VI</div><div>SA</div>
                            </div>
                            
                            <!-- Grid -->
                            <div id="calendar-days-grid" class="grid grid-cols-7 gap-1 md:gap-2 text-sm">
                                <!-- JS Generated -->
                            </div>
                        </div>

                        <div class="flex justify-center gap-4 text-xs font-medium text-gray-500 mb-6 flex-wrap">
                            <div class="flex items-center gap-1.5"><span class="w-2.5 h-2.5 rounded-full bg-gray-100 border border-gray-200"></span> Disponible</div>
                            <div class="flex items-center gap-1.5"><span class="w-2.5 h-2.5 rounded-full bg-red-50 border border-red-100"></span> Ocupado</div>
                            <div class="flex items-center gap-1.5"><span class="w-2.5 h-2.5 rounded-full bg-primary"></span> Seleccionado</div>
                        </div>

                        <div class="flex gap-3">
                            <button type="button" onclick="goToPrev()" class="w-1/3 py-3.5 rounded-xl bg-gray-100 text-gray-700 font-bold hover:bg-gray-200 transition-colors active:scale-[0.98]">Atrás</button>
                            <button type="button" id="btn-next-1" onclick="goToNext()" disabled class="w-2/3 py-3.5 rounded-xl bg-primary text-white font-bold text-lg hover:bg-primary-dark transition-colors disabled:opacity-50 disabled:cursor-not-allowed shadow-lg shadow-primary/30 active:scale-[0.98]">Siguiente paso</button>
                        </div>
                    </div>

                    <!-- Step 2: Hora -->
                    <div class="step-card" id="step2">
                        <div class="flex flex-col md:flex-row md:items-center justify-between mb-6 gap-3">
                            <h2 class="text-xl font-bold text-gray-900 flex items-center gap-2">
                                <i class="bi bi-clock text-primary"></i> Horarios
                            </h2>
                            <span class="bg-blue-50 text-primary px-3 py-1.5 rounded-lg text-sm font-semibold inline-block text-center" id="selected-date-display"></span>
                        </div>

                        <div id="slots-grid" class="grid grid-cols-3 md:grid-cols-4 gap-3 mb-8">
                            <!-- JS Generated -->
                        </div>

                        <div class="flex gap-3">
                            <button type="button" onclick="goToPrev()" class="w-1/3 py-3.5 rounded-xl bg-gray-100 text-gray-700 font-bold hover:bg-gray-200 transition-colors active:scale-[0.98]">Atrás</button>
                            <button type="button" id="btn-next-2" onclick="goToNext()" disabled class="w-2/3 py-3.5 rounded-xl bg-primary text-white font-bold text-lg hover:bg-primary-dark transition-colors disabled:opacity-50 disabled:cursor-not-allowed shadow-lg shadow-primary/30 active:scale-[0.98]">Continuar</button>
                        </div>
                    </div>

                    <!-- Step 3: Datos y Confirmación -->
                    <div class="step-card" id="step3">
                        <h2 class="text-xl font-bold text-gray-900 mb-2 flex items-center gap-2">
                            <i class="bi bi-person-lines-fill text-primary"></i> Confirmá tus datos
                        </h2>
                        
                        @auth('patient')
                            <p class="text-gray-500 text-sm mb-6">Hemos completado tus datos personales. Podés revisarlos antes de confirmar tu reserva.</p>

                            <form action="{{ route('booking.confirm', $company->slug, false) }}" method="POST" id="booking-form">
                                @csrf
                                <input type="hidden" name="date" id="final-date">
                                <input type="hidden" name="time" id="final-time">
                                <input type="hidden" name="professional_id" id="final-professional-id">
                                <input type="hidden" name="appointment_type_id" id="final-appointment-type-id">
                                
                                <div class="space-y-5 mb-8">
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                                        <div>
                                            <label class="block text-[11px] font-bold text-gray-500 uppercase tracking-wider mb-1.5">Nombre *</label>
                                            <input type="text" name="patient_first_name" required value="{{ auth('patient')->user()->first_name }}" class="w-full rounded-xl border-gray-200 focus:border-primary focus:ring focus:ring-primary/20 transition-all shadow-sm py-2.5">
                                        </div>
                                        <div>
                                            <label class="block text-[11px] font-bold text-gray-500 uppercase tracking-wider mb-1.5">Apellido *</label>
                                            <input type="text" name="patient_last_name" required value="{{ auth('patient')->user()->last_name }}" class="w-full rounded-xl border-gray-200 focus:border-primary focus:ring focus:ring-primary/20 transition-all shadow-sm py-2.5">
                                        </div>
                                    </div>
                                    
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                                        <div>
                                            <label class="block text-[11px] font-bold text-gray-500 uppercase tracking-wider mb-1.5">WhatsApp *</label>
                                            <div class="relative">
                                                <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                                                    <i class="bi bi-whatsapp text-green-500"></i>
                                                </div>
                                                <input type="tel" name="patient_phone" required value="{{ auth('patient')->user()->phone }}" placeholder="Ej: 2920123456" class="w-full pl-11 rounded-xl border-gray-200 focus:border-primary focus:ring focus:ring-primary/20 transition-all shadow-sm py-2.5">
                                            </div>
                                        </div>
                                        <div>
                                            <label class="block text-[11px] font-bold text-gray-500 uppercase tracking-wider mb-1.5">Email *</label>
                                            <input type="email" name="patient_email" required value="{{ auth('patient')->user()->email }}" class="w-full rounded-xl border-gray-200 focus:border-primary focus:ring focus:ring-primary/20 transition-all shadow-sm py-2.5">
                                        </div>
                                    </div>

                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                                        <div>
                                            <label class="block text-[11px] font-bold text-gray-500 uppercase tracking-wider mb-1.5">DNI *</label>
                                            <input type="text" name="patient_dni" required value="{{ auth('patient')->user()->dni }}" placeholder="Sin puntos" class="w-full rounded-xl border-gray-200 focus:border-primary focus:ring focus:ring-primary/20 transition-all shadow-sm py-2.5">
                                        </div>
                                        <div>
                                            <label class="block text-[11px] font-bold text-gray-500 uppercase tracking-wider mb-1.5">Obra Social / Prepaga *</label>
                                            <input type="text" name="patient_insurance" required value="{{ auth('patient')->user()->insurance }}" placeholder="Ej: OSDE, Swiss Medical..." class="w-full rounded-xl border-gray-200 focus:border-primary focus:ring focus:ring-primary/20 transition-all shadow-sm py-2.5">
                                        </div>
                                    </div>
                                </div>

                                <div class="flex gap-3">
                                    <button type="button" onclick="goToPrev()" class="w-1/3 py-3.5 rounded-xl bg-gray-100 text-gray-700 font-bold hover:bg-gray-200 transition-colors active:scale-[0.98]">Atrás</button>
                                    <button type="submit" class="w-2/3 py-3.5 rounded-xl bg-primary text-white font-bold text-lg hover:bg-primary-dark transition-colors shadow-lg shadow-primary/30 active:scale-[0.98]">Confirmar Turno</button>
                                </div>
                            </form>
                        @else
                            <p class="text-gray-500 text-sm mb-6">Para continuar con la reserva de tu turno, por favor iniciá sesión o registrate en nuestra plataforma.</p>
                            
                            <div class="bg-gray-50 rounded-xl p-6 border border-gray-100 text-center mb-8">
                                <i class="bi bi-shield-lock text-gray-400 text-4xl mb-3 block"></i>
                                <h4 class="font-bold text-gray-800 mb-2">Autenticación Requerida</h4>
                                <p class="text-gray-500 text-xs max-w-sm mx-auto mb-4">El registro te permitirá tener un historial de tus turnos, reprogramaciones y estados de pago de forma segura.</p>
                                
                                <div class="flex flex-col sm:flex-row justify-center gap-3">
                                    <a href="{{ route('booking.login', $company->slug) }}" id="login-btn" class="inline-block bg-primary text-white px-5 py-2.5 rounded-xl font-bold hover:bg-primary-dark transition-colors text-sm shadow-md shadow-primary/20">
                                        Iniciar Sesión
                                    </a>
                                    <a href="{{ route('booking.register', $company->slug) }}" id="register-btn" class="inline-block bg-white border border-gray-200 text-gray-700 px-5 py-2.5 rounded-xl font-bold hover:bg-gray-50 transition-colors text-sm">
                                        Registrarme
                                    </a>
                                </div>
                            </div>

                            <div class="flex gap-3">
                                <button type="button" onclick="goToPrev()" class="w-full py-3.5 rounded-xl bg-gray-100 text-gray-700 font-bold hover:bg-gray-200 transition-colors active:scale-[0.98]">Atrás</button>
                            </div>
                        @endauth
                    </div>
                </div>
            </div>
        @endif
        </div>
        
        <div class="text-center mt-8 pb-8">
            <p class="text-[11px] text-gray-400 font-medium uppercase tracking-wider">Desarrollado por <a href="https://vetrixweb.com" target="_blank" class="font-bold text-primary hover:underline">VETRIXWEB</a></p>
        </div>
    </div>

<script>
    let selectedDate = null;
    let selectedTime = null;

    const todayStr = "{{ \Carbon\Carbon::now($company->timezone ?? config('app.timezone'))->format('Y-m-d') }}";
    const todayParts = todayStr.split('-');
    const serverToday = new Date(parseInt(todayParts[0]), parseInt(todayParts[1]) - 1, parseInt(todayParts[2]));
    serverToday.setHours(0,0,0,0);

    let currentYear = serverToday.getFullYear();
    let currentMonth = serverToday.getMonth();

    const monthNames = [
        "enero", "febrero", "marzo", "abril", "mayo", "junio",
        "julio", "agosto", "septiembre", "octubre", "noviembre", "diciembre"
    ];

    const professionalsCount = {{ $professionals->count() }};
    const appointmentTypes = @json($appointmentTypes);
    const steps = @json($steps);
    let currentStepIndex = 0;

    let selectedProfessionalId = @json($professionals->count() === 1 ? $professionals->first()->id : null);
    let selectedAppointmentTypeId = @json($appointmentTypes->count() === 1 ? $appointmentTypes->first()->id : null);

    function updateIndicators(index) {
        steps.forEach((step, idx) => {
            const el = document.getElementById('indicator-' + step.card);
            if (!el) return;
            if (idx === index) {
                el.className = "flex-1 py-3 px-2 text-center text-xs md:text-sm font-bold text-primary border-b-2 border-primary transition-colors";
                el.removeAttribute('onclick');
            } else if (idx < index) {
                el.className = "flex-1 py-3 px-2 text-center text-xs md:text-sm font-semibold text-gray-700 border-b-2 border-transparent transition-colors cursor-pointer hover:bg-gray-100";
                el.setAttribute('onclick', `goToStepIndex(${idx})`);
            } else {
                el.className = "flex-1 py-3 px-2 text-center text-xs md:text-sm font-semibold text-gray-400 border-b-2 border-transparent transition-colors";
                el.removeAttribute('onclick');
            }
        });
    }

    function selectProfessional(id) {
        selectedProfessionalId = id;
        
        document.querySelectorAll('.professional-card').forEach(card => {
            card.classList.remove('border-primary', 'bg-primary/5', 'ring-2', 'ring-primary');
            card.classList.add('border-gray-200');
        });
        
        const selectedCard = document.getElementById('prof-card-' + id);
        if (selectedCard) {
            selectedCard.classList.remove('border-gray-200');
            selectedCard.classList.add('border-primary', 'bg-primary/5', 'ring-2', 'ring-primary');
        }
        
        const btnNext = document.getElementById('btn-next-0');
        if (btnNext) btnNext.disabled = false;
    }

    function selectAppointmentType(id) {
        selectedAppointmentTypeId = id;
        
        document.querySelectorAll('.appointment-type-card').forEach(card => {
            card.classList.remove('border-primary', 'bg-primary/5', 'ring-2', 'ring-primary');
            card.classList.add('border-gray-200');
        });
        
        const selectedCard = document.getElementById('type-card-' + id);
        if (selectedCard) {
            selectedCard.classList.remove('border-gray-200');
            selectedCard.classList.add('border-primary', 'bg-primary/5', 'ring-2', 'ring-primary');
        }
        
        const btnNext = document.getElementById('btn-next-0a');
        if (btnNext) btnNext.disabled = false;
    }

    function goToNext() {
        const currentStep = steps[currentStepIndex];
        if (currentStep.card === 'step0' && !selectedProfessionalId) return;
        if (currentStep.card === 'step0a' && !selectedAppointmentTypeId) return;
        if (currentStep.card === 'step1' && !selectedDate) return;
        if (currentStep.card === 'step2' && !selectedTime) return;
        
        document.getElementById(currentStep.card).classList.remove('active');
        currentStepIndex++;
        const nextStep = steps[currentStepIndex];
        document.getElementById(nextStep.card).classList.add('active');
        updateIndicators(currentStepIndex);
        
        if (nextStep.card === 'step1') {
            renderCalendar(currentYear, currentMonth);
        } else if (nextStep.card === 'step2') {
            loadSlots();
        } else if (nextStep.card === 'step3') {
            prepareConfirmation();
        }
    }

    function goToPrev() {
        const currentStep = steps[currentStepIndex];
        document.getElementById(currentStep.card).classList.remove('active');
        currentStepIndex--;
        const prevStep = steps[currentStepIndex];
        document.getElementById(prevStep.card).classList.add('active');
        updateIndicators(currentStepIndex);
    }

    function goToStepIndex(idx) {
        const currentStep = steps[currentStepIndex];
        document.getElementById(currentStep.card).classList.remove('active');
        currentStepIndex = idx;
        const targetStep = steps[currentStepIndex];
        document.getElementById(targetStep.card).classList.add('active');
        updateIndicators(currentStepIndex);
        
        if (targetStep.card === 'step1') {
            renderCalendar(currentYear, currentMonth);
        } else if (targetStep.card === 'step2') {
            loadSlots();
        }
    }

    async function renderCalendar(year, month) {
        if (!selectedProfessionalId || !selectedAppointmentTypeId) return;

        const firstDayIndex = new Date(year, month, 1).getDay();
        const totalDays = new Date(year, month + 1, 0).getDate();
        
        document.getElementById('current-month-display').innerText = `${monthNames[month]} ${year}`;
        
        const monthStr = `${year}-${String(month + 1).padStart(2, '0')}`;
        let availability = {};
        
        try {
            const response = await fetch(`{{ route('booking.month-availability', $company->slug, false) }}?month=${monthStr}&professional_id=${selectedProfessionalId}&appointment_type_id=${selectedAppointmentTypeId}`);
            availability = await response.json();
        } catch (e) {
            console.error("Error fetching availability:", e);
        }
        
        const grid = document.getElementById('calendar-days-grid');
        grid.innerHTML = '';
        
        for (let i = 0; i < firstDayIndex; i++) {
            grid.innerHTML += `<div></div>`;
        }
        
        for (let day = 1; day <= totalDays; day++) {
            const dateObj = new Date(year, month, day);
            dateObj.setHours(0,0,0,0);
            
            const dateStr = `${year}-${String(month + 1).padStart(2, '0')}-${String(day).padStart(2, '0')}`;
            
            let statusClass = 'bg-gray-50 text-gray-700 hover:border-primary hover:bg-white cursor-pointer'; 
            let isPast = dateObj < serverToday;
            let status = 'available';
            
            if (isPast) {
                status = 'past';
                statusClass = 'bg-transparent text-gray-300 cursor-not-allowed opacity-50';
            } else {
                const dayData = availability[dateStr];
                if (dayData && !dayData.has_slots || !dayData) {
                    status = 'unavailable';
                    statusClass = 'bg-red-50 text-red-300 border-red-50 cursor-not-allowed';
                }
            }
            
            const isSelected = selectedDate === dateStr;
            if (isSelected) {
                statusClass = 'bg-primary text-white shadow-md font-bold scale-[1.03] ring-2 ring-primary ring-offset-1';
            }
            
            grid.innerHTML += `
                <div class="py-2.5 md:py-3 flex items-center justify-center rounded-xl border-2 border-transparent transition-all duration-200 ${statusClass}" 
                     data-date="${dateStr}"
                     ${status === 'available' || status === 'selected' ? `onclick="onDaySelect('${dateStr}')"` : ''}>
                    ${day}
                </div>
            `;
        }
    }

    function onDaySelect(dateStr) {
        selectedDate = dateStr;
        document.getElementById('btn-next-1').disabled = false;
        renderCalendar(currentYear, currentMonth);
    }

    document.getElementById('prev-month-btn').addEventListener('click', () => {
        currentMonth--;
        if (currentMonth < 0) { currentMonth = 11; currentYear--; }
        renderCalendar(currentYear, currentMonth);
    });

    document.getElementById('next-month-btn').addEventListener('click', () => {
        currentMonth++;
        if (currentMonth > 11) { currentMonth = 0; currentYear++; }
        renderCalendar(currentYear, currentMonth);
    });

    async function loadSlots() {
        if (!selectedDate || !selectedProfessionalId || !selectedAppointmentTypeId) return;
        
        const response = await fetch(`{{ route('booking.slots', $company->slug, false) }}?date=${selectedDate}&professional_id=${selectedProfessionalId}&appointment_type_id=${selectedAppointmentTypeId}`);
        const data = await response.json();
        
        document.getElementById('selected-date-display').innerText = data.date_formatted;
        const grid = document.getElementById('slots-grid');
        grid.innerHTML = '';
        
        if (data.slots.length === 0) {
            grid.innerHTML = '<div class="col-span-3 md:col-span-4 text-center py-10 bg-gray-50 rounded-xl text-gray-500 font-medium">No hay horarios disponibles para este día.</div>';
        } else {
            data.slots.forEach(slot => {
                const isSelected = selectedTime === slot;
                const slotClass = isSelected 
                    ? 'bg-primary text-white border-primary shadow-md scale-[1.02]' 
                    : 'bg-white text-gray-700 border-gray-200 hover:border-primary hover:text-primary';
                    
                grid.innerHTML += `
                    <div class="border-2 rounded-xl py-3.5 text-center font-bold cursor-pointer transition-all duration-200 ${slotClass} slot-btn" onclick="selectTime('${slot}')">${slot}</div>
                `;
            });
        }
    }

    function selectTime(time) {
        selectedTime = time;
        document.getElementById('btn-next-2').disabled = false;
        loadSlots(); // Re-renderizar para marcar visualmente el seleccionado
    }

    function prepareConfirmation() {
        const loginBtn = document.getElementById('login-btn');
        const registerBtn = document.getElementById('register-btn');
        
        const queryParams = `?professional_id=${selectedProfessionalId}&appointment_type_id=${selectedAppointmentTypeId}&date=${selectedDate}&time=${selectedTime}`;
        
        if (loginBtn) {
            loginBtn.href = `{{ route('booking.login', $company->slug) }}` + queryParams;
        }
        if (registerBtn) {
            registerBtn.href = `{{ route('booking.register', $company->slug) }}` + queryParams;
        }
        
        if (document.getElementById('final-date')) document.getElementById('final-date').value = selectedDate;
        if (document.getElementById('final-time')) document.getElementById('final-time').value = selectedTime;
        if (document.getElementById('final-professional-id')) document.getElementById('final-professional-id').value = selectedProfessionalId;
        if (document.getElementById('final-appointment-type-id')) document.getElementById('final-appointment-type-id').value = selectedAppointmentTypeId;
    }

    // Inicializar flujo al cargar la página
    (function initFlow() {
        if (professionalsCount === 0) return;

        const urlParams = new URLSearchParams(window.location.search);
        const paramProfId = urlParams.get('professional_id');
        const paramTypeId = urlParams.get('appointment_type_id');
        const paramDate = urlParams.get('date');
        const paramTime = urlParams.get('time');

        if (paramProfId && paramTypeId && paramDate && paramTime) {
            selectedProfessionalId = parseInt(paramProfId);
            selectedAppointmentTypeId = parseInt(paramTypeId);
            selectedDate = paramDate;
            selectedTime = paramTime;
            
            document.querySelectorAll('.step-card').forEach(s => s.classList.remove('active'));
            document.getElementById('step3').classList.add('active');
            
            currentStepIndex = steps.findIndex(s => s.card === 'step3');
            updateIndicators(currentStepIndex);
            
            prepareConfirmation();
            
            if (professionalsCount > 1) {
                selectProfessional(selectedProfessionalId);
            }
            if (appointmentTypes.length > 1) {
                selectAppointmentType(selectedAppointmentTypeId);
            }
        } else {
            currentStepIndex = 0;
            const initialCard = steps[0].card;
            document.querySelectorAll('.step-card').forEach(s => s.classList.remove('active'));
            document.getElementById(initialCard).classList.add('active');
            updateIndicators(0);

            if (initialCard === 'step1') {
                renderCalendar(currentYear, currentMonth);
            }
        }
    })();
</script>
</body>
</html>
