<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="utf-8" />
    <meta content="width=device-width, initial-scale=1.0" name="viewport" />
    <title>TurneroMédico - Sistema de gestión de turnos médicos online</title>
    <!-- Tailwind CSS v3 with Plugins -->
    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        'brand-teal': '#009BA4',
                        'brand-dark': '#0A2540',
                        'brand-gray': '#F7FAFC',
                        'brand-blue-deep': '#005F6B',
                    },
                    fontFamily: {
                        sans: ['Inter', 'ui-sans-serif', 'system-ui', '-apple-system', 'BlinkMacSystemFont', 'Segoe UI', 'Roboto', 'Helvetica Neue', 'Arial', 'sans-serif'],
                    },
                }
            }
        }
    </script>
    <style data-purpose="custom-animations">
        .accordion-content {
            max-height: 0;
            overflow: hidden;
            transition: max-height 0.3s ease-out;
        }

        .accordion-item.active .accordion-content {
            max-height: 200px;
        }

        .accordion-item.active .chevron {
            transform: rotate(180deg);
        }
    </style>
</head>

<body class="bg-white text-brand-dark font-sans antialiased">

    <!-- BEGIN: Navigation -->
    <nav class="sticky top-0 z-50 bg-white/95 backdrop-blur-sm border-b border-gray-100 transition-all duration-300"
        data-purpose="main-navigation">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-20">
                <div class="flex items-center">
                    <div class="flex items-center gap-2">
                        <div class="w-8 h-8 bg-brand-teal rounded-full flex items-center justify-center">
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewbox="0 0 24 24">
                                <path
                                    d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"
                                    stroke-linecap="round" stroke-linejoin="round" stroke-width="2"></path>
                            </svg>
                        </div>
                        <span class="font-bold text-xl tracking-tight text-brand-dark">Turnero<span
                                class="text-brand-teal">Médico</span></span>
                    </div>
                </div>

                <div class="hidden md:flex items-center space-x-8 text-sm font-medium text-gray-600">
                    <a class="hover:text-brand-teal transition" href="#caracteristicas">Características</a>
                    <a class="hover:text-brand-teal transition" href="#como-funciona">Cómo funciona</a>
                    <a class="hover:text-brand-teal transition" href="#faq">Preguntas</a>
                    <a class="hover:text-brand-teal transition" href="#contacto">Contacto</a>
                </div>

                <div class="flex items-center gap-3">
                    @auth
                        <a class="border-2 border-brand-teal text-brand-teal px-5 py-2 rounded-full font-semibold text-sm hover:bg-teal-50 transition"
                            href="{{ route('admin.appointments.index') }}">Panel Admin</a>
                    @else
                        <!--<a class="border-2 border-brand-teal text-brand-teal px-5 py-2 rounded-full font-semibold text-sm hover:bg-teal-50 transition"
                                                        href="{{ route('login') }}">Ingresar</a>-->
                    @endauth
                    <a class="bg-brand-teal text-white px-6 py-2.5 rounded-full font-semibold text-sm hover:bg-brand-blue-deep transition animate-pulse"
                        href="https://wa.me/542920503974" target="_blank" rel="noopener noreferrer">Solicitar demo</a>
                </div>
            </div>
        </div>
    </nav>
    <!-- END: Navigation -->

    <!-- BEGIN: Hero Section -->
    <section class="pt-8 pb-12 overflow-hidden" data-purpose="hero-section">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid lg:grid-cols-2 gap-12 items-center">
                <!-- Text Content -->
                <div class="z-10">
                    <div
                        class="inline-flex items-center gap-2 px-4 py-1 rounded-full bg-teal-50 border border-teal-100 text-brand-teal text-xs font-bold mb-6">
                        <svg class="w-3 h-3" fill="currentColor" viewbox="0 0 20 20">
                            <path
                                d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z">
                            </path>
                        </svg>
                        Sistema #1 en gestión de turnos médicos online
                    </div>
                    <h1 class="text-5xl lg:text-6xl font-extrabold text-brand-dark leading-tight mb-6">
                        Turnos online para tu consultorio, <span class="text-brand-teal">simples y en tiempo
                            real.</span>
                    </h1>
                    <p class="text-lg text-gray-600 mb-10 max-w-lg leading-relaxed">
                        Mejorá la experiencia de tus pacientes y administrá tu agenda profesional de forma inteligente.
                        Recibí reservas por <strong>WhatsApp</strong>, desde tu <strong>URL pública</strong> o el equipo
                        los carga desde el panel. Vos los ves al instante.
                    </p>
                    <div class="flex flex-wrap gap-4 mb-12">
                        <a class="bg-brand-teal text-white px-8 py-4 rounded-full font-bold hover:bg-brand-blue-deep transition shadow-lg shadow-teal-200/50"
                            href="https://wa.me/542920503974" target="_blank" rel="noopener noreferrer">Quiero una demo
                            gratis</a>
                        <a class="border-2 border-brand-teal text-brand-teal px-8 py-4 rounded-full font-bold hover:bg-teal-50 transition flex items-center gap-2"
                            href="https://wa.me/542920503974" target="_blank" rel="noopener noreferrer">
                            <svg class="w-5 h-5" fill="currentColor" viewbox="0 0 24 24">
                                <path
                                    d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347z">
                                </path>
                            </svg>
                            Escribinos por WhatsApp
                        </a>
                    </div>
                    <div class="grid grid-cols-3 gap-8">
                        <div>
                            <div class="text-2xl font-bold text-brand-dark">+2.4k</div>
                            <div class="text-xs text-gray-500 uppercase font-semibold">Turnos gestionados / mes</div>
                        </div>
                        <div>
                            <div class="text-2xl font-bold text-brand-dark">24/7</div>
                            <div class="text-xs text-gray-500 uppercase font-semibold">Reservas automáticas</div>
                        </div>
                        <div>
                            <div class="text-2xl font-bold text-brand-dark">100%</div>
                            <div class="text-xs text-gray-500 uppercase font-semibold">Configurable</div>
                        </div>
                    </div>
                </div>
                <!-- Graphic Asset -->
                <div class="relative flex justify-center">
                    <div class="w-full max-w-md bg-[#00818A] rounded-2xl p-6 shadow-2xl relative">
                        <div class="flex justify-between items-center mb-6">
                            <div class="text-white">
                                <div class="text-xs opacity-80">AGENDA DE HOY</div>
                                <div class="font-bold text-lg">Dr. García</div>
                            </div>
                            <div
                                class="bg-white/20 text-white text-[10px] px-2 py-0.5 rounded uppercase tracking-wider">
                                En vivo</div>
                        </div>
                        <div class="space-y-3">
                            <!-- Item 1 -->
                            <div
                                class="bg-white rounded-lg p-3 flex items-center justify-between border-l-4 border-teal-500">
                                <div class="flex gap-3">
                                    <div class="text-xs font-bold text-brand-dark">09:00</div>
                                    <div>
                                        <div class="text-xs font-bold text-brand-dark">María López</div>
                                        <div class="text-[10px] text-gray-500">Consulta general</div>
                                    </div>
                                </div>
                                <div
                                    class="w-4 h-4 rounded-full border border-gray-300 flex items-center justify-center">
                                    <svg class="w-2.5 h-2.5 text-gray-400" fill="none" stroke="currentColor"
                                        viewbox="0 0 24 24">
                                        <path d="M9 5l7 7-7 7"></path>
                                    </svg>
                                </div>
                            </div>
                            <!-- Item 2 -->
                            <div
                                class="bg-white rounded-lg p-3 flex items-center justify-between border-l-4 border-blue-400">
                                <div class="flex gap-3">
                                    <div class="text-xs font-bold text-brand-dark">09:30</div>
                                    <div>
                                        <div class="text-xs font-bold text-brand-dark">Juan Pérez</div>
                                        <div class="text-[10px] text-gray-500">Control anual</div>
                                    </div>
                                </div>
                                <div
                                    class="w-4 h-4 rounded-full border border-gray-300 flex items-center justify-center">
                                    <svg class="w-2.5 h-2.5 text-gray-400" fill="none" stroke="currentColor"
                                        viewbox="0 0 24 24">
                                        <path d="M9 5l7 7-7 7"></path>
                                    </svg>
                                </div>
                            </div>
                            <!-- Item 3 -->
                            <div
                                class="bg-white rounded-lg p-3 flex items-center justify-between border-l-4 border-yellow-400">
                                <div class="flex gap-3">
                                    <div class="text-xs font-bold text-brand-dark">10:00</div>
                                    <div>
                                        <div class="text-xs font-bold text-brand-dark">— Nueva reserva WhatsApp</div>
                                        <div class="text-[10px] text-teal-600 font-semibold">Confirmando...</div>
                                    </div>
                                </div>
                                <div
                                    class="w-4 h-4 rounded-full border border-gray-300 flex items-center justify-center">
                                    <svg class="w-2.5 h-2.5 text-gray-400" fill="none" stroke="currentColor"
                                        viewbox="0 0 24 24">
                                        <path d="M9 5l7 7-7 7"></path>
                                    </svg>
                                </div>
                            </div>
                            <!-- Item 4 -->
                            <div
                                class="bg-white rounded-lg p-3 flex items-center justify-between border-l-4 border-teal-500">
                                <div class="flex gap-3">
                                    <div class="text-xs font-bold text-brand-dark">10:30</div>
                                    <div>
                                        <div class="text-xs font-bold text-brand-dark">Ana Suárez</div>
                                        <div class="text-[10px] text-gray-500">Estudios</div>
                                    </div>
                                </div>
                                <div
                                    class="w-4 h-4 rounded-full border border-gray-300 flex items-center justify-center">
                                    <svg class="w-2.5 h-2.5 text-gray-400" fill="none" stroke="currentColor"
                                        viewbox="0 0 24 24">
                                        <path d="M9 5l7 7-7 7"></path>
                                    </svg>
                                </div>
                            </div>
                        </div>
                        <!-- WhatsApp Badge -->
                        <div
                            class="absolute -bottom-4 left-1/2 -translate-x-1/2 md:left-auto md:translate-x-0 md:-left-12 bg-white rounded-lg p-3 shadow-xl flex items-center gap-3 border border-gray-100 w-max">
                            <div class="w-8 h-8 bg-green-500 rounded-full flex items-center justify-center">
                                <svg class="w-5 h-5 text-white" fill="currentColor" viewbox="0 0 24 24">
                                    <path
                                        d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347z">
                                    </path>
                                </svg>
                            </div>
                            <div>
                                <div class="text-[10px] text-gray-400">Nuevo turno vía WhatsApp</div>
                                <div class="text-xs font-bold text-brand-dark">10:00 — <span
                                        class="text-green-500">Confirmado ✓</span></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- END: Hero Section -->

    <!-- BEGIN: Targets Section -->
    <section class="bg-[#F8FAFC] pt-8 pb-12 border-y border-gray-100" data-purpose="targets-grid">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16">
                <h3 class="text-3xl font-extrabold text-brand-dark">Adaptado a la medida de tu especialidad</h3>
                <p class="text-gray-500 text-sm max-w-xl mx-auto mt-2">Nuestra plataforma se configura de forma
                    independiente según el flujo y las necesidades de tu rama profesional.</p>
            </div>

            <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6 gap-6">
                <!-- 1. Odontología -->
                <div
                    class="bg-white rounded-2xl p-6 border border-gray-200 shadow-[0_10px_30px_rgba(0,0,0,0.08)] hover:shadow-[0_20px_50px_rgba(0,155,164,0.15)] hover:-translate-y-2 hover:border-brand-teal/30 transition-all duration-300 flex flex-col items-center text-center group">
                    <div
                        class="w-16 h-16 bg-teal-50 text-brand-teal rounded-full flex items-center justify-center mb-4 group-hover:bg-brand-teal group-hover:text-white transition-colors duration-300">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M12 2C9 2 7 3.5 7 6.5C7 9.5 9 10.5 9 12.5C9 14.5 7 16.5 7 19.5C7 21 8.5 22 12 22C15.5 22 17 21 17 19.5C17 16.5 15 14.5 15 12.5C15 10.5 17 9.5 17 6.5C17 3.5 15 2 12 2Z" />
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M9 12.5C9 12.5 11 11.5 12 11.5C13 11.5 15 12.5 15 12.5" />
                        </svg>
                    </div>
                    <span class="text-xs font-bold text-brand-dark">Odontología</span>
                </div>

                <!-- 2. Nutrición -->
                <div
                    class="bg-white rounded-2xl p-6 border border-gray-200 shadow-[0_10px_30px_rgba(0,0,0,0.08)] hover:shadow-[0_20px_50px_rgba(0,155,164,0.15)] hover:-translate-y-2 hover:border-brand-teal/30 transition-all duration-300 group flex flex-col items-center text-center">
                    <div
                        class="w-16 h-16 bg-teal-50 text-brand-teal rounded-full flex items-center justify-center mb-4 group-hover:bg-brand-teal group-hover:text-white transition-colors duration-300">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M12 5C10.5 3 7 3 5.5 5.5C4 8 4 12 6.5 15.5C9 19 11 20 12 20C13 20 15 19 17.5 15.5C20 12 20 8 18.5 5.5C17 3 13.5 3 12 5Z" />
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 5C12 3 13 2 14 2" />
                        </svg>
                    </div>
                    <span class="text-xs font-bold text-brand-dark">Nutricionistas</span>
                </div>

                <!-- 3. Psicología -->
                <div
                    class="bg-white rounded-2xl p-6 border border-gray-200 shadow-[0_10px_30px_rgba(0,0,0,0.08)] hover:shadow-[0_20px_50px_rgba(0,155,164,0.15)] hover:-translate-y-2 hover:border-brand-teal/30 transition-all duration-300 group flex flex-col items-center text-center">
                    <div
                        class="w-16 h-16 bg-teal-50 text-brand-teal rounded-full flex items-center justify-center mb-4 group-hover:bg-brand-teal group-hover:text-white transition-colors duration-300">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M9.5 2C7.5 2 6 3.5 6 5.5C6 7 7 8 8 9C7 10 6 11.5 6 13C6 15 7.5 16.5 9.5 16.5M14.5 2C16.5 2 18 3.5 18 5.5C18 7 17 8 16 9C17 10 18 11.5 18 13C18 15 16.5 16.5 14.5 16.5" />
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 5V16.5" />
                        </svg>
                    </div>
                    <span class="text-xs font-bold text-brand-dark">Psicólogos</span>
                </div>

                <!-- 4. Clínica Médica -->
                <div
                    class="bg-white rounded-2xl p-6 border border-gray-200 shadow-[0_10px_30px_rgba(0,0,0,0.08)] hover:shadow-[0_20px_50px_rgba(0,155,164,0.15)] hover:-translate-y-2 hover:border-brand-teal/30 transition-all duration-300 group flex flex-col items-center text-center">
                    <div
                        class="w-16 h-16 bg-teal-50 text-brand-teal rounded-full flex items-center justify-center mb-4 group-hover:bg-brand-teal group-hover:text-white transition-colors duration-300">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M4.5 3V10C4.5 14.14 7.86 17.5 12 17.5C16.14 17.5 19.5 14.14 19.5 10V3" />
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 17.5V21M10 21H14" />
                        </svg>
                    </div>
                    <span class="text-xs font-bold text-brand-dark">Clínicas Médicas</span>
                </div>

                <!-- 5. Kinesiología -->
                <div
                    class="bg-white rounded-2xl p-6 border border-gray-200 shadow-[0_10px_30px_rgba(0,0,0,0.08)] hover:shadow-[0_20px_50px_rgba(0,155,164,0.15)] hover:-translate-y-2 hover:border-brand-teal/30 transition-all duration-300 group flex flex-col items-center text-center">
                    <div
                        class="w-16 h-16 bg-teal-50 text-brand-teal rounded-full flex items-center justify-center mb-4 group-hover:bg-brand-teal group-hover:text-white transition-colors duration-300">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                            <circle cx="12" cy="6" r="2" />
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M12 8V16M8 10h8M9 16l-2 5M15 16l2 5" />
                        </svg>
                    </div>
                    <span class="text-xs font-bold text-brand-dark">Kinesiólogos</span>
                </div>

                <!-- 6. Estética & Salud -->
                <div
                    class="bg-white rounded-2xl p-6 border border-gray-200 shadow-[0_10px_30px_rgba(0,0,0,0.08)] hover:shadow-[0_20px_50px_rgba(0,155,164,0.15)] hover:-translate-y-2 hover:border-brand-teal/30 transition-all duration-300 group flex flex-col items-center text-center">
                    <div
                        class="w-16 h-16 bg-teal-50 text-brand-teal rounded-full flex items-center justify-center mb-4 group-hover:bg-brand-teal group-hover:text-white transition-colors duration-300">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M12 3V21M3 12H21M5.64 5.64L18.36 18.36M5.64 18.36L18.36 5.64" />
                        </svg>
                    </div>
                    <span class="text-xs font-bold text-brand-dark">Estética &amp; Salud</span>
                </div>

                <!-- 7. Pediatría -->
                <div
                    class="bg-white rounded-2xl p-6 border border-gray-200 shadow-[0_10px_30px_rgba(0,0,0,0.08)] hover:shadow-[0_20px_50px_rgba(0,155,164,0.15)] hover:-translate-y-2 hover:border-brand-teal/30 transition-all duration-300 group flex flex-col items-center text-center">
                    <div
                        class="w-16 h-16 bg-teal-50 text-brand-teal rounded-full flex items-center justify-center mb-4 group-hover:bg-brand-teal group-hover:text-white transition-colors duration-300">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                            <circle cx="12" cy="12" r="8" />
                            <circle cx="9" cy="10" r="1" fill="currentColor" />
                            <circle cx="15" cy="10" r="1" fill="currentColor" />
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M9.5 14C10.5 15.5 13.5 15.5 14.5 14" />
                        </svg>
                    </div>
                    <span class="text-xs font-bold text-brand-dark">Pediatría</span>
                </div>

                <!-- 8. Ginecología -->
                <div
                    class="bg-white rounded-2xl p-6 border border-gray-200 shadow-[0_10px_30px_rgba(0,0,0,0.08)] hover:shadow-[0_20px_50px_rgba(0,155,164,0.15)] hover:-translate-y-2 hover:border-brand-teal/30 transition-all duration-300 group flex flex-col items-center text-center">
                    <div
                        class="w-16 h-16 bg-teal-50 text-brand-teal rounded-full flex items-center justify-center mb-4 group-hover:bg-brand-teal group-hover:text-white transition-colors duration-300">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                            <circle cx="12" cy="9" r="5" />
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 14V21M9 18H15" />
                        </svg>
                    </div>
                    <span class="text-xs font-bold text-brand-dark">Ginecología</span>
                </div>

                <!-- 9. Oftalmología -->
                <div
                    class="bg-white rounded-2xl p-6 border border-gray-200 shadow-[0_10px_30px_rgba(0,0,0,0.08)] hover:shadow-[0_20px_50px_rgba(0,155,164,0.15)] hover:-translate-y-2 hover:border-brand-teal/30 transition-all duration-300 group flex flex-col items-center text-center">
                    <div
                        class="w-16 h-16 bg-teal-50 text-brand-teal rounded-full flex items-center justify-center mb-4 group-hover:bg-brand-teal group-hover:text-white transition-colors duration-300">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M1 12S5 4 12 4s11 8 11 8-4 8-11 8-11-8-11-8z" />
                            <circle cx="12" cy="12" r="3" />
                        </svg>
                    </div>
                    <span class="text-xs font-bold text-brand-dark">Oftalmología</span>
                </div>

                <!-- 10. Cardiología -->
                <div
                    class="bg-white rounded-2xl p-6 border border-gray-200 shadow-[0_10px_30px_rgba(0,0,0,0.08)] hover:shadow-[0_20px_50px_rgba(0,155,164,0.15)] hover:-translate-y-2 hover:border-brand-teal/30 transition-all duration-300 group flex flex-col items-center text-center">
                    <div
                        class="w-16 h-16 bg-teal-50 text-brand-teal rounded-full flex items-center justify-center mb-4 group-hover:bg-brand-teal group-hover:text-white transition-colors duration-300">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                        </svg>
                    </div>
                    <span class="text-xs font-bold text-brand-dark">Cardiología</span>
                </div>

                <!-- 11. Dermatología -->
                <div
                    class="bg-white rounded-2xl p-6 border border-gray-200 shadow-[0_10px_30px_rgba(0,0,0,0.08)] hover:shadow-[0_20px_50px_rgba(0,155,164,0.15)] hover:-translate-y-2 hover:border-brand-teal/30 transition-all duration-300 group flex flex-col items-center text-center">
                    <div
                        class="w-16 h-16 bg-teal-50 text-brand-teal rounded-full flex items-center justify-center mb-4 group-hover:bg-brand-teal group-hover:text-white transition-colors duration-300">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M12 2C8 6 4 10 4 14C4 18.42 7.58 22 12 22C16.42 22 20 18.42 20 14C20 10 16 6 12 2Z" />
                        </svg>
                    </div>
                    <span class="text-xs font-bold text-brand-dark">Dermatología</span>
                </div>

                <!-- 12. Traumatología -->
                <div
                    class="bg-white rounded-2xl p-6 border border-gray-200 shadow-[0_10px_30px_rgba(0,0,0,0.08)] hover:shadow-[0_20px_50px_rgba(0,155,164,0.15)] hover:-translate-y-2 hover:border-brand-teal/30 transition-all duration-300 group flex flex-col items-center text-center">
                    <div
                        class="w-16 h-16 bg-teal-50 text-brand-teal rounded-full flex items-center justify-center mb-4 group-hover:bg-brand-teal group-hover:text-white transition-colors duration-300">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M18.4 18.4L20 20M14.5 14.5L18.4 18.4M9.5 14.5L5.6 18.4M18.4 5.6L14.5 9.5M5.6 5.6L9.5 9.5M9.5 9.5L14.5 9.5M14.5 9.5L14.5 14.5M14.5 14.5L9.5 14.5M9.5 14.5L9.5 9.5" />
                        </svg>
                    </div>
                    <span class="text-xs font-bold text-brand-dark">Traumatología</span>
                </div>
            </div>
        </div>
    </section>
    <!-- END: Targets Section -->

    <!-- BEGIN: Features Grid -->
    <section id="caracteristicas" class="pt-8 pb-12" data-purpose="features">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-20">
                <div class="text-brand-teal text-xs font-bold uppercase tracking-widest mb-4">CARACTERÍSTICAS</div>
                <h2 class="text-4xl font-bold text-brand-dark mb-4">Todo lo que tu consultorio<br />necesita para
                    funcionar mejor</h2>
                <p class="text-gray-500 max-w-2xl mx-auto">Un sistema pensado por y para profesionales de la salud.
                    Simple para el paciente, potente para vos.</p>
            </div>
            <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-8">
                <!-- Feature 1 -->
                <div
                    class="p-6 rounded-2xl bg-white border border-gray-300 shadow-[0_10px_30px_rgba(0,0,0,0.08)] hover:shadow-[0_20px_50px_rgba(0,155,164,0.15)] hover:-translate-y-2 hover:border-brand-teal/30 transition-all duration-300 group">
                    <div
                        class="w-16 h-16 bg-teal-50 text-brand-teal rounded-full flex items-center justify-center mb-6 group-hover:bg-brand-teal group-hover:text-white transition-colors duration-300">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold mb-3">Turnos en tiempo real</h3>
                    <p class="text-gray-600 text-base leading-relaxed">Vé al instante cada turno que reserva el público.
                        Sin refrescar, sin demoras. Notificaciones inmediatas.</p>
                </div>
                <!-- Feature 2 -->
                <div
                    class="p-6 rounded-2xl bg-white border border-gray-300 shadow-[0_10px_30px_rgba(0,0,0,0.08)] hover:shadow-[0_20px_50px_rgba(0,155,164,0.15)] hover:-translate-y-2 hover:border-brand-teal/30 transition-all duration-300 group">
                    <div
                        class="w-16 h-16 bg-teal-50 text-brand-teal rounded-full flex items-center justify-center mb-6 group-hover:bg-brand-teal group-hover:text-white transition-colors duration-300">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z">
                            </path>
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z">
                            </path>
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold mb-3">100% configurable</h3>
                    <p class="text-gray-600 text-base leading-relaxed">Horarios, duración de consultas, días de
                        atención,
                        precios, coberturas y feriados. Todo se adapta a vos.</p>
                </div>
                <!-- Feature 3 -->
                <div
                    class="p-6 rounded-2xl bg-white border border-gray-300 shadow-[0_10px_30px_rgba(0,0,0,0.08)] hover:shadow-[0_20px_50px_rgba(0,155,164,0.15)] hover:-translate-y-2 hover:border-brand-teal/30 transition-all duration-300 group">
                    <div
                        class="w-16 h-16 bg-teal-50 text-brand-teal rounded-full flex items-center justify-center mb-6 group-hover:bg-brand-teal group-hover:text-white transition-colors duration-300">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z">
                            </path>
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold mb-3">Multi-profesional</h3>
                    <p class="text-gray-600 text-base leading-relaxed">Un consultorio con varios profesionales, cada uno
                        con su agenda independiente y su URL pública.</p>
                </div>
                <!-- Feature 4 -->
                <div
                    class="p-6 rounded-2xl bg-white border border-gray-300 shadow-[0_10px_30px_rgba(0,0,0,0.08)] hover:shadow-[0_20px_50px_rgba(0,155,164,0.15)] hover:-translate-y-2 hover:border-brand-teal/30 transition-all duration-300 group">
                    <div
                        class="w-16 h-16 bg-teal-50 text-brand-teal rounded-full flex items-center justify-center mb-6 group-hover:bg-brand-teal group-hover:text-white transition-colors duration-300">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z">
                            </path>
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold mb-3">Datos seguros</h3>
                    <p class="text-gray-600 text-base leading-relaxed">Información de pacientes protegida, backups
                        automáticos y acceso por roles (médico / secretaria).</p>
                </div>
                <!-- Feature 5 -->
                <div
                    class="p-6 rounded-2xl bg-white border border-gray-300 shadow-[0_10px_30px_rgba(0,0,0,0.08)] hover:shadow-[0_20px_50px_rgba(0,155,164,0.15)] hover:-translate-y-2 hover:border-brand-teal/30 transition-all duration-300 group">
                    <div
                        class="w-16 h-16 bg-teal-50 text-brand-teal rounded-full flex items-center justify-center mb-6 group-hover:bg-brand-teal group-hover:text-white transition-colors duration-300">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z">
                            </path>
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold mb-3">Reportes claros</h3>
                    <p class="text-gray-600 text-base leading-relaxed">Estadísticas de ocupación, ingresos, ausencias y
                        mejores horarios para optimizar tu agenda.</p>
                </div>
                <!-- Feature 6 -->
                <div
                    class="p-6 rounded-2xl bg-white border border-gray-300 shadow-[0_10px_30px_rgba(0,0,0,0.08)] hover:shadow-[0_20px_50px_rgba(0,155,164,0.15)] hover:-translate-y-2 hover:border-brand-teal/30 transition-all duration-300 group">
                    <div
                        class="w-16 h-16 bg-teal-50 text-brand-teal rounded-full flex items-center justify-center mb-6 group-hover:bg-brand-teal group-hover:text-white transition-colors duration-300">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9">
                            </path>
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold mb-3">Recordatorios automáticos</h3>
                    <p class="text-gray-600 text-base leading-relaxed">Reducí el ausentismo con avisos automáticos a los
                        pacientes por WhatsApp / email antes del turno.</p>
                </div>
            </div>
        </div>
    </section>
    <!-- END: Features Grid -->

    <!-- BEGIN: How It Works -->
    <section id="como-funciona" class="bg-brand-dark pt-8 pb-12 text-white overflow-hidden" data-purpose="how-it-works">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center mb-16">
            <div class="text-brand-teal text-xs font-bold uppercase tracking-widest mb-4">CÓMO FUNCIONA</div>
            <h2 class="text-4xl font-bold mb-4">3 formas simples de que<br />tus pacientes saquen turno</h2>
            <p class="text-gray-400 max-w-xl mx-auto">Elegí las que mejor se adapten a tu consultorio. Todas se
                sincronizan en la misma agenda en tiempo real.</p>
        </div>
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid md:grid-cols-3 gap-8">
                <!-- Step 1 -->
                <div class="bg-white/5 border border-white/10 rounded-2xl p-6 hover:bg-white/10 transition">
                    <div class="flex justify-between items-start mb-8">
                        <div class="w-12 h-12 bg-white/10 rounded-full flex items-center justify-center">
                            <svg class="w-8 h-8 text-brand-teal" fill="currentColor" viewbox="0 0 24 24">
                                <path
                                    d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347z">
                                </path>
                            </svg>
                        </div>
                        <span class="bg-brand-teal text-[10px] font-bold px-2 py-0.5 rounded uppercase">El más
                            usado</span>
                    </div>
                    <h3 class="text-xl font-bold mb-3">1. Por WhatsApp</h3>
                    <p class="text-gray-300 text-base leading-relaxed">Tu paciente escribe a un número de WhatsApp y
                        reserva su turno guiado por el sistema. Sin apps, sin instalaciones.</p>
                </div>
                <!-- Step 2 -->
                <div class="bg-white/5 border border-white/10 rounded-2xl p-6 hover:bg-white/10 transition">
                    <div class="flex justify-between items-start mb-8">
                        <div class="w-12 h-12 bg-white/10 rounded-full flex items-center justify-center">
                            <svg class="w-8 h-8 text-brand-teal" fill="none" stroke="currentColor" viewbox="0 0 24 24">
                                <path
                                    d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9m-9 9a9 9 0 019-9">
                                </path>
                            </svg>
                        </div>
                        <span class="bg-white/10 text-[10px] font-bold px-2 py-0.5 rounded uppercase">24/7</span>
                    </div>
                    <h3 class="text-xl font-bold mb-3">2. Desde tu URL pública</h3>
                    <p class="text-gray-300 text-base leading-relaxed">Compartís un enlace personalizado de tu
                        consultorio. El paciente elige día, horario, paga si corresponde y listo.</p>
                </div>
                <!-- Step 3 -->
                <div class="bg-white/5 border border-white/10 rounded-2xl p-6 hover:bg-white/10 transition">
                    <div class="flex justify-between items-start mb-8">
                        <div class="w-12 h-12 bg-white/10 rounded-full flex items-center justify-center">
                            <svg class="w-8 h-8 text-brand-teal" fill="none" stroke="currentColor" viewbox="0 0 24 24">
                                <path
                                    d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z">
                                </path>
                            </svg>
                        </div>
                        <span class="bg-white/10 text-[10px] font-bold px-2 py-0.5 rounded uppercase">Control
                            total</span>
                    </div>
                    <h3 class="text-xl font-bold mb-3">3. Desde el panel administrador</h3>
                    <p class="text-gray-300 text-base leading-relaxed">Vos o tu secretaria cargan turnos manualmente
                        desde
                        el panel, con control total sobre bloques, pausas y reprogramaciones.</p>
                </div>
            </div>
        </div>
    </section>
    <!-- END: How It Works -->

    <!-- BEGIN: Dashboard Preview Section -->
    <section class="pt-8 pb-12 bg-white" data-purpose="admin-preview">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid lg:grid-cols-2 gap-16 items-center">
                <!-- Content List -->
                <div>
                    <div class="text-brand-teal text-xs font-bold uppercase tracking-widest mb-4">BENEFICIOS</div>
                    <h2 class="text-4xl font-bold text-brand-dark mb-8 leading-tight">Mejor servicio al paciente. Mejor
                        administración para vos.</h2>
                    <ul class="space-y-6">
                        <li class="flex gap-4">
                            <div
                                class="flex-shrink-0 w-6 h-6 rounded-full bg-teal-100 flex items-center justify-center mt-1">
                                <svg class="w-4 h-4 text-brand-teal" fill="currentColor" viewbox="0 0 20 20">
                                    <path
                                        d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z">
                                    </path>
                                </svg>
                            </div>
                            <div>
                                <h4 class="font-bold text-brand-dark">Tu paciente reserva en 1 minuto</h4>
                                <p class="text-sm text-gray-500">Sin llamadas, sin esperas, sin idas y vueltas. La
                                    experiencia que hoy esperan tus pacientes.</p>
                            </div>
                        </li>
                        <li class="flex gap-4">
                            <div
                                class="flex-shrink-0 w-6 h-6 rounded-full bg-teal-100 flex items-center justify-center mt-1">
                                <svg class="w-4 h-4 text-brand-teal" fill="currentColor" viewbox="0 0 20 20">
                                    <path
                                        d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z">
                                    </path>
                                </svg>
                            </div>
                            <div>
                                <h4 class="font-bold text-brand-dark">Ves los turnos al instante</h4>
                                <p class="text-sm text-gray-500">Cada reserva que el público hace aparece
                                    automáticamente en tu panel, sin refrescar.</p>
                            </div>
                        </li>
                        <li class="flex gap-4">
                            <div
                                class="flex-shrink-0 w-6 h-6 rounded-full bg-teal-100 flex items-center justify-center mt-1">
                                <svg class="w-4 h-4 text-brand-teal" fill="currentColor" viewbox="0 0 20 20">
                                    <path
                                        d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z">
                                    </path>
                                </svg>
                            </div>
                            <div>
                                <h4 class="font-bold text-brand-dark">Menos ausentismo</h4>
                                <p class="text-sm text-gray-500">Recordatorios automáticos, confirmaciones y
                                    cancelaciones controladas antes del turno.</p>
                            </div>
                        </li>
                        <li class="flex gap-4">
                            <div
                                class="flex-shrink-0 w-6 h-6 rounded-full bg-teal-100 flex items-center justify-center mt-1">
                                <svg class="w-4 h-4 text-brand-teal" fill="currentColor" viewbox="0 0 20 20">
                                    <path
                                        d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z">
                                    </path>
                                </svg>
                            </div>
                            <div>
                                <h4 class="font-bold text-brand-dark">Agenda inteligente</h4>
                                <p class="text-sm text-gray-500">Bloquear horarios, tomarte vacaciones o dejar espacio a
                                    urgencias con un clic.</p>
                            </div>
                        </li>
                        <li class="flex gap-4">
                            <div
                                class="flex-shrink-0 w-6 h-6 rounded-full bg-teal-100 flex items-center justify-center mt-1">
                                <svg class="w-4 h-4 text-brand-teal" fill="currentColor" viewbox="0 0 20 20">
                                    <path
                                        d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z">
                                    </path>
                                </svg>
                            </div>
                            <div>
                                <h4 class="font-bold text-brand-dark">Cobros con Mercado Pago</h4>
                                <p class="text-sm text-gray-500">Cobrá seña o consulta completa al momento de reservar.
                                    Menos ausencias, más ingresos.</p>
                            </div>
                        </li>
                    </ul>
                </div>
                <!-- Dashboard UI Mockup -->
                <div class="relative">
                    <div class="bg-gray-50 rounded-2xl p-6 border border-gray-200 shadow-xl">
                        <div class="flex justify-between items-center mb-8">
                            <h3 class="text-lg md:text-xl font-bold text-brand-dark">Panel administrador</h3>
                            <span
                                class="bg-green-100 text-green-600 text-[10px] font-bold px-2 py-1 rounded-full flex items-center gap-1">
                                <span class="w-1 h-1 bg-green-600 rounded-full animate-pulse"></span>
                                En línea
                            </span>
                        </div>
                        <div class="grid grid-cols-2 gap-4 mb-6">
                            <div class="bg-white p-4 rounded-xl border border-gray-100 shadow-sm">
                                <svg class="w-5 h-5 text-brand-teal mb-3" fill="none" stroke="currentColor"
                                    viewbox="0 0 24 24">
                                    <path
                                        d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z">
                                    </path>
                                </svg>
                                <div class="text-2xl font-bold text-brand-dark">18</div>
                                <div class="text-xs font-semibold text-gray-600">Turnos hoy</div>
                            </div>
                            <div class="bg-white p-4 rounded-xl border border-gray-100 shadow-sm">
                                <svg class="w-5 h-5 text-brand-teal mb-3" fill="none" stroke="currentColor"
                                    viewbox="0 0 24 24">
                                    <path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                <div class="text-2xl font-bold text-brand-dark">15</div>
                                <div class="text-xs font-semibold text-gray-600">Confirmados</div>
                            </div>
                            <div class="bg-white p-4 rounded-xl border border-gray-100 shadow-sm">
                                <svg class="w-5 h-5 text-brand-teal mb-3" fill="none" stroke="currentColor"
                                    viewbox="0 0 24 24">
                                    <path
                                        d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z">
                                    </path>
                                </svg>
                                <div class="text-2xl font-bold text-brand-dark">$142k</div>
                                <div class="text-xs font-semibold text-gray-600">Pagos MP</div>
                            </div>
                            <div class="bg-white p-4 rounded-xl border border-gray-100 shadow-sm">
                                <svg class="w-5 h-5 text-brand-teal mb-3" fill="none" stroke="currentColor"
                                    viewbox="0 0 24 24">
                                    <path d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z">
                                    </path>
                                </svg>
                                <div class="text-2xl font-bold text-brand-dark">6</div>
                                <div class="text-xs font-semibold text-gray-600">Nuevos pacientes</div>
                            </div>
                        </div>
                        <div class="bg-white p-4 rounded-xl border border-gray-100 shadow-sm">
                            <div class="text-xs font-bold text-brand-teal uppercase mb-2">CONFIGURABLE</div>
                            <p class="text-sm text-gray-600 leading-relaxed">Horarios, precios, duración de consultas,
                                coberturas, feriados y mucho más.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- END: Dashboard Preview Section -->

    <!-- BEGIN: Mercado Pago Integration -->
    <section class="pt-8 pb-12" data-purpose="payment-integration">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
            <div
                class="bg-brand-gray rounded-[2.5rem] p-8 lg:p-16 flex flex-col lg:flex-row items-center gap-12 border border-gray-100">
                <div class="lg:w-1/2">
                    <div class="text-brand-teal text-xs font-bold uppercase mb-4">COBROS INTEGRADOS</div>
                    <h2 class="text-4xl font-bold text-brand-dark mb-6">Cobrá con <span class="text-[#00B1EA]">Mercado
                            Pago</span> al reservar</h2>
                    <p class="text-gray-600 mb-8 leading-relaxed">El paciente paga la seña o la consulta completa al
                        sacar el turno. Menos cancelaciones de último momento y una caja más ordenada. Configurable por
                        profesional, servicio o cobertura.</p>
                    <ul class="space-y-4">
                        <li class="flex items-center gap-3 text-sm font-semibold text-gray-700">
                            <svg class="w-5 h-5 text-brand-teal" fill="none" stroke="currentColor" viewbox="0 0 24 24">
                                <path d="M5 13l4 4L19 7"></path>
                            </svg>
                            Cobro de seña o total
                        </li>
                        <li class="flex items-center gap-3 text-sm font-semibold text-gray-700">
                            <svg class="w-5 h-5 text-brand-teal" fill="none" stroke="currentColor" viewbox="0 0 24 24">
                                <path d="M5 13l4 4L19 7"></path>
                            </svg>
                            Reembolsos y cancelaciones
                        </li>
                        <li class="flex items-center gap-3 text-sm font-semibold text-gray-700">
                            <svg class="w-5 h-5 text-brand-teal" fill="none" stroke="currentColor" viewbox="0 0 24 24">
                                <path d="M5 13l4 4L19 7"></path>
                            </svg>
                            Reportes por servicio y profesional
                        </li>
                    </ul>
                </div>
                <div class="lg:w-1/2 w-full">
                    <div class="bg-brand-blue-deep rounded-2xl p-8 text-white shadow-2xl relative overflow-hidden">
                        <div class="absolute top-0 right-0 w-32 h-32 bg-white/5 rounded-full -mr-16 -mt-16"></div>
                        <div class="relative z-10">
                            <svg class="w-10 h-10 mb-8" fill="none" stroke="currentColor" viewbox="0 0 24 24">
                                <path
                                    d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"
                                    stroke-linecap="round" stroke-linejoin="round" stroke-width="2"></path>
                            </svg>
                            <div class="text-xs opacity-70 mb-1">Seña de reserva</div>
                            <div class="text-3xl font-bold mb-4">$ 5.000,00</div>
                            <div class="text-xs opacity-70 mb-8">Pagando con Mercado Pago</div>
                            <div
                                class="w-full bg-white/10 border border-white/20 rounded-lg p-3 flex items-center gap-3">
                                <svg class="w-4 h-4 text-brand-teal" fill="currentColor" viewbox="0 0 20 20">
                                    <path
                                        d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z">
                                    </path>
                                </svg>
                                <span class="text-[10px] font-bold">Turno confirmado automáticamente al recibir el
                                    pago</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- END: Mercado Pago Integration -->

    <!-- BEGIN: FAQ Section -->
    <section id="faq" class="pt-8 pb-12 bg-white" data-purpose="faq">
        <div class="max-w-3xl mx-auto px-4">
            <div class="text-center mb-16">
                <div class="text-brand-teal text-xs font-bold uppercase tracking-widest mb-4">PREGUNTAS FRECUENTES</div>
                <h2 class="text-4xl font-bold text-brand-dark mb-4">Todo lo que querés<br />saber antes de empezar</h2>
            </div>
            <div class="space-y-4" id="faq-accordion">
                <!-- Accordion Items -->
                <div class="accordion-item border border-gray-300 rounded-xl px-4 bg-white transition-all">
                    <button
                        class="w-full flex justify-between items-center py-3 text-left font-semibold text-brand-dark hover:text-brand-teal transition">
                        <span>¿Sirve para cualquier especialidad médica?</span>
                        <svg class="chevron w-5 h-5 text-gray-400 transition-transform" fill="none"
                            stroke="currentColor" viewbox="0 0 24 24">
                            <path d="M19 9l-7 7-7-7" stroke-linecap="round" stroke-linejoin="round" stroke-width="2">
                            </path>
                        </svg>
                    </button>
                    <div class="accordion-content bg-gray-50/50 rounded-lg">
                        <p class="pb-3 text-base text-gray-600 leading-relaxed">Sí, el sistema es totalmente flexible y
                            se
                            adapta a las necesidades específicas de cada especialidad, permitiendo configurar duraciones
                            de consulta, tipos de servicio y mucho más.</p>
                    </div>
                </div>
                <div class="accordion-item border border-gray-300 rounded-xl px-4 bg-white transition-all">
                    <button
                        class="w-full flex justify-between items-center py-3 text-left font-semibold text-brand-dark hover:text-brand-teal transition">
                        <span>¿Cómo saca turno un paciente?</span>
                        <svg class="chevron w-5 h-5 text-gray-400 transition-transform" fill="none"
                            stroke="currentColor" viewbox="0 0 24 24">
                            <path d="M19 9l-7 7-7-7" stroke-linecap="round" stroke-linejoin="round" stroke-width="2">
                            </path>
                        </svg>
                    </button>
                    <div class="accordion-content bg-gray-50/50 rounded-lg">
                        <p class="pb-3 text-base text-gray-600 leading-relaxed">Puede hacerlo a través de un link
                            público o vía un flujo de
                            WhatsApp automatizado.</p>
                    </div>
                </div>
                <div class="accordion-item border border-gray-300 rounded-xl px-4 bg-white transition-all">
                    <button
                        class="w-full flex justify-between items-center py-3 text-left font-semibold text-brand-dark hover:text-brand-teal transition">
                        <span>¿Puedo ver los turnos en tiempo real?</span>
                        <svg class="chevron w-5 h-5 text-gray-400 transition-transform" fill="none"
                            stroke="currentColor" viewbox="0 0 24 24">
                            <path d="M19 9l-7 7-7-7" stroke-linecap="round" stroke-linejoin="round" stroke-width="2">
                            </path>
                        </svg>
                    </button>
                    <div class="accordion-content bg-gray-50/50 rounded-lg">
                        <p class="pb-3 text-base text-gray-600 leading-relaxed">Sí, el panel se actualiza
                            automáticamente sin necesidad de
                            recargar la página.</p>
                    </div>
                </div>
                <div class="accordion-item border border-gray-300 rounded-xl px-4 bg-white transition-all">
                    <button
                        class="w-full flex justify-between items-center py-3 text-left font-semibold text-brand-dark hover:text-brand-teal transition">
                        <span>¿Es configurable a mi forma de trabajar?</span>
                        <svg class="chevron w-5 h-5 text-gray-400 transition-transform" fill="none"
                            stroke="currentColor" viewbox="0 0 24 24">
                            <path d="M19 9l-7 7-7-7" stroke-linecap="round" stroke-linejoin="round" stroke-width="2">
                            </path>
                        </svg>
                    </button>
                    <div class="accordion-content bg-gray-50/50 rounded-lg">
                        <p class="pb-3 text-base text-gray-600 leading-relaxed">Absolutamente, podés definir horarios,
                            descansos,
                            sobreturnos y bloqueos.</p>
                    </div>
                </div>
                <div class="accordion-item border border-gray-300 rounded-xl px-4 bg-white transition-all">
                    <button
                        class="w-full flex justify-between items-center py-3 text-left font-semibold text-brand-dark hover:text-brand-teal transition">
                        <span>¿Qué medios de pago acepta?</span>
                        <svg class="chevron w-5 h-5 text-gray-400 transition-transform" fill="none"
                            stroke="currentColor" viewbox="0 0 24 24">
                            <path d="M19 9l-7 7-7-7" stroke-linecap="round" stroke-linejoin="round" stroke-width="2">
                            </path>
                        </svg>
                    </button>
                    <div class="accordion-content bg-gray-50/50 rounded-lg">
                        <p class="pb-3 text-base text-gray-600 leading-relaxed">Integración directa con Mercado Pago
                            para tarjetas y dinero
                            en cuenta.</p>
                    </div>
                </div>
                <div class="accordion-item border border-gray-300 rounded-xl px-4 bg-white transition-all">
                    <button
                        class="w-full flex justify-between items-center py-3 text-left font-semibold text-brand-dark hover:text-brand-teal transition">
                        <span>¿Necesito instalar algo?</span>
                        <svg class="chevron w-5 h-5 text-gray-400 transition-transform" fill="none"
                            stroke="currentColor" viewbox="0 0 24 24">
                            <path d="M19 9l-7 7-7-7" stroke-linecap="round" stroke-linejoin="round" stroke-width="2">
                            </path>
                        </svg>
                    </button>
                    <div class="accordion-content bg-gray-50/50 rounded-lg">
                        <p class="pb-3 text-base text-gray-600 leading-relaxed">No, es un servicio basado en la nube.
                            Accedés desde
                            cualquier navegador.</p>
                    </div>
                </div>
                <div class="accordion-item border border-gray-300 rounded-xl px-4 bg-white transition-all">
                    <button
                        class="w-full flex justify-between items-center py-3 text-left font-semibold text-brand-dark hover:text-brand-teal transition">
                        <span>¿Puede usarlo mi secretaria además de mí?</span>
                        <svg class="chevron w-5 h-5 text-gray-400 transition-transform" fill="none"
                            stroke="currentColor" viewbox="0 0 24 24">
                            <path d="M19 9l-7 7-7-7" stroke-linecap="round" stroke-linejoin="round" stroke-width="2">
                            </path>
                        </svg>
                    </button>
                    <div class="accordion-content bg-gray-50/50 rounded-lg">
                        <p class="pb-3 text-base text-gray-600 leading-relaxed">Sí, podés crear múltiples accesos para
                            que varias personas administren tu consultorio.</p>
                    </div>
                </div>
                <div class="accordion-item border border-gray-300 rounded-xl px-4 bg-white transition-all">
                    <button
                        class="w-full flex justify-between items-center py-3 text-left font-semibold text-brand-dark hover:text-brand-teal transition">
                        <span>¿Sirve para consultorios con varios profesionales?</span>
                        <svg class="chevron w-5 h-5 text-gray-400 transition-transform" fill="none"
                            stroke="currentColor" viewbox="0 0 24 24">
                            <path d="M19 9l-7 7-7-7" stroke-linecap="round" stroke-linejoin="round" stroke-width="2">
                            </path>
                        </svg>
                    </button>
                    <div class="accordion-content bg-gray-50/50 rounded-lg">
                        <p class="pb-3 text-base text-gray-600 leading-relaxed">Sí, cada profesional puede tener su
                            propia agenda y
                            configuraciones individuales.</p>
                    </div>
                </div>
                <div class="accordion-item border border-gray-300 rounded-xl px-4 bg-white transition-all">
                    <button
                        class="w-full flex justify-between items-center py-3 text-left font-semibold text-brand-dark hover:text-brand-teal transition">
                        <span>¿Cómo se reducen los ausentismos?</span>
                        <svg class="chevron w-5 h-5 text-gray-400 transition-transform" fill="none"
                            stroke="currentColor" viewbox="0 0 24 24">
                            <path d="M19 9l-7 7-7-7" stroke-linecap="round" stroke-linejoin="round" stroke-width="2">
                            </path>
                        </svg>
                    </button>
                    <div class="accordion-content bg-gray-50/50 rounded-lg">
                        <p class="pb-3 text-base text-gray-600 leading-relaxed">A través de recordatorios automáticos
                            vía WhatsApp y el
                            cobro anticipado.</p>
                    </div>
                </div>
                <div class="accordion-item border border-gray-300 rounded-xl px-4 bg-white transition-all">
                    <button
                        class="w-full flex justify-between items-center py-3 text-left font-semibold text-brand-dark hover:text-brand-teal transition">
                        <span>¿Cómo empiezo?</span>
                        <svg class="chevron w-5 h-5 text-gray-400 transition-transform" fill="none"
                            stroke="currentColor" viewbox="0 0 24 24">
                            <path d="M19 9l-7 7-7-7" stroke-linecap="round" stroke-linejoin="round" stroke-width="2">
                            </path>
                        </svg>
                    </button>
                    <div class="accordion-content bg-gray-50/50 rounded-lg">
                        <p class="pb-3 text-base text-gray-600 leading-relaxed">Haciendo clic en el botón de demo o
                            contactándonos por
                            WhatsApp.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- END: FAQ Section -->

    <!-- BEGIN: Contact Section -->
    <section id="contacto" class="pt-8 pb-12 bg-brand-gray" data-purpose="contact">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid lg:grid-cols-2 gap-16">
                <!-- Contact Info -->
                <div>
                    <div class="text-brand-teal text-xs font-bold uppercase tracking-widest mb-4">CONTACTO</div>
                    <h2 class="text-4xl font-bold text-brand-dark mb-6">Hablemos de<br />tu consultorio</h2>
                    <p class="text-gray-500 mb-12 text-lg">Contanos qué necesitás. Te respondemos con una propuesta
                        personalizada para tu especialidad y forma de trabajar.</p>
                    <div class="space-y-8">
                        <a class="flex items-center gap-5 hover:opacity-85 transition group"
                            href="https://wa.me/542920503974" target="_blank" rel="noopener noreferrer">
                            <div
                                class="w-12 h-12 bg-white rounded-full flex items-center justify-center text-brand-teal shadow-sm group-hover:bg-brand-teal group-hover:text-white transition-all duration-300">
                                <svg class="w-6 h-6" fill="currentColor" viewbox="0 0 24 24">
                                    <path
                                        d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347z">
                                    </path>
                                </svg>
                            </div>
                            <div>
                                <div class="text-xs text-gray-400 font-bold uppercase tracking-wider">WHATSAPP</div>
                                <div
                                    class="text-base md:text-lg font-bold text-brand-dark group-hover:text-brand-teal transition">
                                    +54 2920 50-3974</div>
                            </div>
                        </a>
                        <div class="flex items-center gap-5">
                            <div
                                class="w-12 h-12 bg-white rounded-full flex items-center justify-center text-brand-teal shadow-sm">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewbox="0 0 24 24">
                                    <path
                                        d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z">
                                    </path>
                                </svg>
                            </div>
                            <div>
                                <div class="text-xs text-gray-400 font-bold uppercase tracking-wider">EMAIL</div>
                                <div class="text-base md:text-lg font-bold text-brand-dark">hola@vetrixweb.com</div>
                            </div>
                        </div>
                        <div class="flex items-center gap-5">
                            <div
                                class="w-12 h-12 bg-white rounded-full flex items-center justify-center text-brand-teal shadow-sm">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewbox="0 0 24 24">
                                    <path
                                        d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z">
                                    </path>
                                    <path d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                </svg>
                            </div>
                            <div>
                                <div class="text-xs text-gray-400 font-bold uppercase tracking-wider">ZONA</div>
                                <div class="text-base md:text-lg font-bold text-brand-dark">Argentina — Atención 100%
                                    online</div>
                            </div>
                        </div>
                    </div>
                    <div class="mt-12">
                        <a class="inline-flex items-center gap-2 bg-brand-teal text-white px-6 py-3 rounded-full font-bold text-sm hover:bg-brand-blue-deep transition"
                            href="https://wa.me/542920503974" target="_blank">
                            <svg class="w-4 h-4" fill="currentColor" viewbox="0 0 24 24">
                                <path
                                    d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347z">
                                </path>
                            </svg>
                            Hablar por WhatsApp ahora
                        </a>
                    </div>
                </div>
                <!-- Contact Form Card -->
                <div class="bg-white rounded-[2rem] p-8 lg:p-12 shadow-xl border border-gray-100">
                    <div class="mb-8">
                        <h3 class="text-2xl font-bold text-brand-dark mb-1">Escribinos</h3>
                        <p class="text-sm text-gray-500 font-medium">Respondemos en menos de 24 hs hábiles.</p>
                    </div>
                    <form class="space-y-6">
                        <div class="grid md:grid-cols-2 gap-6">
                            <div class="space-y-2">
                                <label class="text-xs font-bold text-gray-500 uppercase ml-1">Nombre y apellido
                                    <span class="text-red-400">*</span></label>
                                <input
                                    class="w-full bg-gray-50 border-gray-200 rounded-xl px-4 py-3.5 text-base focus:ring-brand-teal focus:border-brand-teal text-brand-dark placeholder:text-gray-400"
                                    placeholder="Juan Pérez" type="text" />
                            </div>
                            <div class="space-y-2">
                                <label class="text-xs font-bold text-gray-500 uppercase ml-1">Email <span
                                        class="text-red-400">*</span></label>
                                <input
                                    class="w-full bg-gray-50 border-gray-200 rounded-xl px-4 py-3.5 text-base focus:ring-brand-teal focus:border-brand-teal text-brand-dark placeholder:text-gray-400"
                                    placeholder="tu@email.com" type="email" />
                            </div>
                        </div>
                        <div class="grid md:grid-cols-2 gap-6">
                            <div class="space-y-2">
                                <label class="text-xs font-bold text-gray-500 uppercase ml-1">Teléfono</label>
                                <input
                                    class="w-full bg-gray-50 border-gray-200 rounded-xl px-4 py-3.5 text-base focus:ring-brand-teal focus:border-brand-teal text-brand-dark placeholder:text-gray-400"
                                    placeholder="+54 9..." type="tel" />
                            </div>
                            <div class="space-y-2">
                                <label class="text-xs font-bold text-gray-500 uppercase ml-1">Especialidad</label>
                                <input
                                    class="w-full bg-gray-50 border-gray-200 rounded-xl px-4 py-3.5 text-base focus:ring-brand-teal focus:border-brand-teal text-brand-dark placeholder:text-gray-400"
                                    placeholder="Odontología, nutrición..." type="text" />
                            </div>
                        </div>
                        <div class="space-y-2">
                            <label class="text-xs font-bold text-gray-500 uppercase ml-1">Mensaje <span
                                    class="text-red-400">*</span></label>
                            <textarea
                                class="w-full bg-gray-50 border-gray-200 rounded-xl px-4 py-3.5 text-base focus:ring-brand-teal focus:border-brand-teal text-brand-dark placeholder:text-gray-400"
                                placeholder="Contanos brevemente cómo trabajás y qué necesitás..." rows="4"></textarea>
                        </div>
                        <button
                            class="w-full bg-brand-teal text-white font-bold py-4 rounded-xl hover:bg-brand-blue-deep transition flex items-center justify-center gap-2 text-base md:text-lg shadow-md"
                            type="submit">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewbox="0 0 24 24">
                                <path d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path>
                            </svg>
                            Enviar mensaje
                        </button>
                        <p class="text-xs text-gray-400 text-center">Al enviar aceptás ser contactado por email o
                            WhatsApp para responder tu consulta.</p>
                    </form>
                </div>
            </div>
        </div>
    </section>
    <!-- END: Contact Section -->

    <!-- BEGIN: Footer -->
    <footer class="bg-white pt-16 pb-8 border-t border-gray-200 shadow-[0_-8px_30px_rgba(0,0,0,0.05)] relative z-10"
        data-purpose="main-footer">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid md:grid-cols-3 gap-12 mb-8">
                <div>
                    <div class="flex items-center gap-2 mb-6">
                        <div class="w-8 h-8 bg-brand-teal rounded-full flex items-center justify-center">
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewbox="0 0 24 24">
                                <path
                                    d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"
                                    stroke-linecap="round" stroke-linejoin="round" stroke-width="2"></path>
                            </svg>
                        </div>
                        <span class="font-bold text-xl tracking-tight text-brand-dark">Turnero<span
                                class="text-brand-teal">Médico</span></span>
                    </div>
                    <p class="text-sm text-gray-500 leading-relaxed max-w-xs">Sistema de turnos online configurable para
                        profesionales de la salud. WhatsApp, URL pública, panel administrador y pagos con Mercado Pago.
                    </p>
                </div>
                <div>
                    <h4 class="font-bold text-brand-dark mb-6">Navegación</h4>
                    <ul class="space-y-3 text-sm text-gray-600">
                        <li><a class="hover:text-brand-teal transition" href="#caracteristicas">Características</a></li>
                        <li><a class="hover:text-brand-teal transition" href="#como-funciona">Cómo funciona</a></li>
                        <li><a class="hover:text-brand-teal transition" href="#faq">Preguntas frecuentes</a></li>
                        <li><a class="hover:text-brand-teal transition" href="#contacto">Contacto</a></li>
                    </ul>
                </div>
                <div>
                    <h4 class="font-bold text-brand-dark mb-6">Contactanos</h4>
                    <ul class="space-y-3 text-sm text-gray-600">
                        <li class="flex items-center gap-2.5">
                            <a class="flex items-center gap-2.5 hover:text-brand-teal transition"
                                href="https://wa.me/542920503974" target="_blank" rel="noopener noreferrer">
                                <svg class="w-5 h-5 text-brand-teal" fill="currentColor" viewbox="0 0 24 24">
                                    <path
                                        d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347z">
                                    </path>
                                </svg>
                                +54 2920 50-3974
                            </a>
                        </li>
                        <li class="flex items-center gap-2.5">
                            <a class="flex items-center gap-2.5 hover:text-brand-teal transition"
                                href="mailto:hola@vetrixweb.com">
                                <svg class="w-5 h-5 text-brand-teal" fill="none" stroke="currentColor"
                                    viewbox="0 0 24 24">
                                    <path
                                        d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z">
                                    </path>
                                </svg>
                                hola@vetrixweb.com
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
            <div
                class="pt-8 border-t border-gray-100 flex flex-col md:flex-row justify-between items-center gap-4 text-[13px] text-gray-400 font-medium">
                <p class="flex items-center gap-1.5 flex-wrap justify-center md:justify-start">
                    <svg class="w-5 h-3.5 rounded-sm inline-block shadow-sm" viewBox="0 0 3 2">
                        <rect width="3" height="2" fill="#74acdf" />
                        <rect y="0.667" width="3" height="0.667" fill="#ffffff" />
                        <circle cx="1.5" cy="1" r="0.12" fill="#f6b426" />
                    </svg>
                    ©2026 TurneroMédico. Todos los derechos reservados. - <a
                        class="hover:text-brand-teal transition font-bold" href="https://vetrixweb.com" target="_blank"
                        rel="noopener noreferrer">VETRIXWEB</a>
                </p>
            </div>
        </div>
    </footer>
    <!-- END: Footer -->

    <!-- BEGIN: Interactive Scripts -->
    <script>
        // Logic for the FAQ accordion
        document.querySelectorAll('.accordion-item button').forEach(button => {
            button.addEventListener('click', () => {
                const item = button.parentElement;
                const isActive = item.classList.contains('active');

                // Close all items
                document.querySelectorAll('.accordion-item').forEach(i => i.classList.remove('active'));

                // Toggle current item
                if (!isActive) {
                    item.classList.add('active');
                }
            });
        });

        // Sticky header shadow & border transition on scroll
        const navElement = document.querySelector('nav[data-purpose="main-navigation"]');
        window.addEventListener('scroll', () => {
            if (window.scrollY > 10) {
                navElement.classList.add('shadow-md', 'border-gray-200');
                navElement.classList.remove('border-gray-100');
            } else {
                navElement.classList.remove('shadow-md', 'border-gray-200');
                navElement.classList.add('border-gray-100');
            }
        });
    </script>
    <!-- END: Interactive Scripts -->

    <!-- Floating WhatsApp Button -->
    <a href="https://wa.me/542920503974" target="_blank" rel="noopener noreferrer"
        class="fixed bottom-6 right-6 z-50 bg-[#25D366] text-white p-4 rounded-full shadow-2xl hover:bg-[#128C7E] transition-all duration-300 hover:scale-110 flex items-center justify-center group"
        aria-label="Contactar por WhatsApp">
        <svg class="w-7 h-7" fill="currentColor" viewBox="0 0 24 24">
            <path fill-rule="evenodd" clip-rule="evenodd"
                d="M12.004 2C6.48 2 2 6.48 2 12.004c0 1.762.455 3.417 1.25 4.87L2 22l5.303-1.392c1.396.762 2.99 1.196 4.701 1.196 5.523 0 10.004-4.48 10.004-10.004C22.008 6.48 17.527 2 12.004 2zm5.726 14.195c-.246.69-1.206 1.254-1.743 1.32-.486.06-1.12.09-1.803-.13-.42-.135-.95-.316-1.61-.597-2.812-1.196-4.636-4.062-4.776-4.25-.14-.188-1.144-1.52-1.144-2.898 0-1.378.718-2.05.975-2.316.257-.266.56-.332.748-.332.188 0 .374.004.537.012.169.008.397-.064.62.464.225.534.77 1.88.837 2.016.067.135.112.294.022.474-.09.18-.135.294-.27.452-.134.158-.28.35-.4.47-.134.136-.275.284-.118.553.157.27.7 1.15 1.5 1.86.8.71 1.474.93 1.68 1.033.206.103.327.087.447-.052.12-.138.514-.6.653-.804.14-.204.28-.17.472-.098.192.072 1.217.574 1.427.68.21.106.35.158.4.246.05.088.05.508-.196 1.198z" />
        </svg>
        <span
            class="absolute right-16 bg-white text-brand-dark text-xs font-semibold px-3 py-1.5 rounded-lg shadow-md border border-gray-100 whitespace-nowrap opacity-0 group-hover:opacity-100 transition-opacity duration-300 pointer-events-none">
            ¿Necesitás ayuda? Escribinos
        </span>
    </a>

</body>

</html>