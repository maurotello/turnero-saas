# 🏗️ Plan de Implementación — SaaS Turnero Médico/Nutrición

## Stack Tecnológico
- **Framework**: Laravel 12
- **Frontend**: Blade + Bootstrap 5 + JavaScript Vanilla
- **Base de Datos**: MySQL
- **PDF**: DomPDF (barryvdh/laravel-dompdf)
- **Email**: Laravel Mail con Mailable classes
- **Auth**: Laravel Breeze (simple, Blade-based)

---

## 📐 Arquitectura Multi-Tenant

### Modelo: Tenant por "Company"
- Cada profesional crea su **Company** (empresa)
- Todos los datos (turnos, configuración, pacientes) están asociados a `company_id`
- Se usa un **Global Scope** para filtrar automáticamente por company
- URL pública del turnero: `/booking/{company_slug}`

### Tabla `companies`
| Campo | Tipo |
|-------|------|
| id | bigint PK |
| name | varchar(255) |
| slug | varchar(100) unique |
| logo | varchar(500) nullable |
| address | varchar(255) |
| city | varchar(100) |
| state | varchar(100) |
| country | varchar(100) |
| phone | varchar(50) |
| email | varchar(255) |
| website | varchar(255) nullable |
| professional_name | varchar(255) |
| professional_title | varchar(255) |
| specialty | varchar(255) |
| license_number | varchar(100) nullable |
| consultation_price | decimal(10,2) nullable |
| timezone | varchar(50) default 'America/Argentina/Buenos_Aires' |
| primary_color | varchar(7) default '#0d6efd' |
| created_at, updated_at | timestamps |

---

## 👥 Roles y Permisos

### Roles
1. **super_admin** — Administrador de plataforma (futuro)
2. **admin** — Administrador del profesional (dueño de la empresa)
3. **staff** — Administrativo

### Implementación
- Campo `role` en tabla `users` (enum: admin, staff)
- Middleware personalizado `CheckRole`
- Policies para cada modelo
- Gate definitions en `AuthServiceProvider`

---

## 📦 Modelos y Migraciones

### 1. `users`
| Campo | Tipo |
|-------|------|
| id | bigint PK |
| company_id | bigint FK |
| name | varchar |
| email | varchar unique |
| password | varchar |
| role | enum(admin, staff) |
| timestamps | |

### 2. `schedule_settings`
| Campo | Tipo |
|-------|------|
| id | bigint PK |
| company_id | bigint FK |
| day_of_week | tinyint (0=domingo, 6=sábado) |
| start_time | time |
| end_time | time |
| slot_duration | int (minutos) |
| is_active | boolean |
| timestamps | |

### 3. `blocked_days`
| Campo | Tipo |
|-------|------|
| id | bigint PK |
| company_id | bigint FK |
| date | date |
| reason | varchar nullable |
| timestamps | |

### 4. `appointments`
| Campo | Tipo |
|-------|------|
| id | bigint PK |
| company_id | bigint FK |
| date | date |
| time | time |
| patient_first_name | varchar |
| patient_last_name | varchar |
| patient_phone | varchar |
| patient_email | varchar |
| patient_insurance | varchar nullable (obra social) |
| status | enum(active, cancelled, rescheduled) |
| cancel_token | varchar(64) unique |
| lock_token | varchar(64) nullable |
| locked_until | timestamp nullable |
| original_date | date nullable |
| original_time | time nullable |
| rescheduled_by | bigint FK nullable |
| rescheduled_at | timestamp nullable |
| cancelled_at | timestamp nullable |
| cancellation_reason | varchar nullable |
| notes | text nullable |
| timestamps | |

---

## 🗄️ Vistas y Procedimientos MySQL

### Vista: `v_available_slots`
Genera slots disponibles basándose en configuración de horarios,
turnos existentes, y días bloqueados.

### Procedimiento: `sp_lock_slot`
Bloquea temporalmente un slot (5 minutos) para evitar 
doble reserva durante el proceso de completar datos.

### Procedimiento: `sp_confirm_appointment`
Confirma un turno verificando disponibilidad y lock.

### Procedimiento: `sp_release_expired_locks`
Libera locks expirados (ejecutar periódicamente).

---

## 🛣️ Rutas

### Públicas (`web.php`)
```
GET  /booking/{slug}                    → CalendarController@show
GET  /booking/{slug}/slots/{date}       → CalendarController@slots (JSON)
POST /booking/{slug}/lock               → CalendarController@lockSlot (JSON)
POST /booking/{slug}/confirm            → CalendarController@confirm
GET  /booking/{slug}/success/{id}       → CalendarController@success
GET  /cancel/{token}                    → AppointmentController@cancelForm
POST /cancel/{token}                    → AppointmentController@cancel
```

### Autenticadas (`web.php` con middleware `auth`)
```
GET    /admin/dashboard                 → AdminController@dashboard
GET    /admin/appointments              → AdminController@appointments
GET    /admin/appointments/history      → AdminController@history
POST   /admin/appointments/export       → AdminController@exportHistory
DELETE /admin/appointments/{id}         → AdminController@destroyAppointment
GET    /admin/appointments/{id}/reschedule → AdminController@rescheduleForm
PUT    /admin/appointments/{id}/reschedule → AdminController@reschedule
POST   /admin/appointments/{id}/cancel  → AdminController@cancelAppointment
GET    /admin/schedule                  → ScheduleController@edit
PUT    /admin/schedule                  → ScheduleController@update
GET    /admin/blocked-days              → BlockedDayController@index
POST   /admin/blocked-days              → BlockedDayController@store
DELETE /admin/blocked-days/{id}         → BlockedDayController@destroy
GET    /admin/company                   → CompanyController@edit
PUT    /admin/company                   → CompanyController@update
GET    /admin/users                     → UserController@index (solo admin)
POST   /admin/users                     → UserController@store (solo admin)
DELETE /admin/users/{id}                → UserController@destroy (solo admin)
```

### API (`api.php`)
```
GET /api/booking/{slug}/available-dates/{month} → slots por mes
GET /api/booking/{slug}/available-slots/{date}  → slots por día
POST /api/booking/{slug}/lock-slot              → bloquear slot
POST /api/booking/{slug}/release-lock           → liberar slot
```

---

## 🎨 Vistas Blade

### Layout
- `layouts/app.blade.php` — Panel admin
- `layouts/booking.blade.php` — Landing pública

### Admin
- `admin/dashboard.blade.php`
- `admin/appointments/index.blade.php`
- `admin/appointments/history.blade.php`
- `admin/appointments/reschedule.blade.php`
- `admin/schedule/edit.blade.php`
- `admin/blocked-days/index.blade.php`
- `admin/company/edit.blade.php`
- `admin/users/index.blade.php`

### Público
- `booking/calendar.blade.php` — Calendario + pasos wizard
- `booking/success.blade.php`
- `booking/cancel.blade.php`

### Email
- `emails/appointment-confirmation.blade.php`

### PDF
- `pdf/appointment.blade.php`

---

## 🔒 Seguridad

1. **CSRF**: Automático en Laravel forms
2. **Rate Limiting**: En rutas de booking (60/min por IP)
3. **Validación**: FormRequest classes
4. **Prepared Statements**: Eloquent por defecto
5. **Tokens firmados**: cancel_token con hash SHA-256
6. **Policies**: AppointmentPolicy, CompanyPolicy
7. **Middleware**: CheckRole, EnsureCompanyOwnership

---

## 📋 Orden de Implementación

### Fase 1: Setup Base
1. Crear proyecto Laravel 12
2. Configurar .env y MySQL
3. Crear migraciones
4. Crear modelos con relaciones
5. Implementar auth con Breeze
6. Crear seeders

### Fase 2: Backend Core
7. Middleware de roles
8. Policies
9. Vistas/Procedimientos MySQL
10. Controllers admin
11. Controllers público
12. Form Requests (validación)

### Fase 3: Panel Admin
13. Layout admin con Bootstrap
14. Dashboard con estadísticas
15. Gestión de agenda/horarios
16. Gestión de días bloqueados
17. Lista de turnos + filtros
18. Reprogramación de turnos
19. Historial + exportación PDF
20. Gestión de empresa
21. Gestión de usuarios

### Fase 4: Landing Pública
22. Layout público
23. Calendario mensual dinámico
24. Wizard de reserva (4 pasos)
25. Locking de slots en tiempo real
26. Confirmación y validación

### Fase 5: Comunicaciones
27. Email de confirmación
28. PDF del turno
29. Link de cancelación con token

### Fase 6: Polish
30. Responsive móvil
31. Rate limiting
32. Optimizaciones MySQL
33. Tests básicos
