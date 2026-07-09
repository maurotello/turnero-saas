@extends('layouts.admin')

@section('content')
<div class="mb-4">
    <h2 class="fw-bold mb-0">Datos de la Empresa</h2>
    <p class="text-muted">Personalizá tu perfil profesional y la apariencia de tu turnero.</p>
</div>

<form action="{{ route('admin.company.update') }}" method="POST" enctype="multipart/form-data">
    @csrf
    @method('PUT')
    
    <div class="row">
        <div class="col-lg-8">
            <div class="card mb-4">
                <div class="card-header bg-white py-3"><h5 class="mb-0 fw-bold">Información General</h5></div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">Nombre de la Empresa</label>
                            <input type="text" name="name" class="form-control" value="{{ $company->name }}" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">Email de Contacto</label>
                            <input type="email" name="email" class="form-control" value="{{ $company->email }}">
                        </div>
                        <div class="col-md-12">
                            <label class="form-label small fw-bold">Dirección</label>
                            <input type="text" name="address" class="form-control" value="{{ $company->address }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">Ciudad</label>
                            <input type="text" name="city" class="form-control" value="{{ $company->city }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">Teléfono</label>
                            <input type="text" name="phone" class="form-control" value="{{ $company->phone }}">
                        </div>
                    </div>
                </div>
            </div>



            <div class="card mb-4 shadow-sm border-0">
                <div class="card-header bg-white py-3 border-bottom"><h5 class="mb-0 fw-bold text-dark"><i class="bi bi-wallet2 text-primary me-2"></i>Pasarela de Pago (Mercado Pago)</h5></div>
                <div class="card-body">
                    <p class="text-muted small">Configura tu cuenta de Mercado Pago para poder cobrar las consultas de tus turnos de forma automática.</p>
                    <div class="row g-3">
                        <div class="col-md-12">
                            <label class="form-label small fw-bold text-muted">Public Key (Clave Pública)</label>
                            <input type="text" name="mp_public_key" class="form-control font-monospace" value="{{ $company->mp_public_key }}" placeholder="APP_USR-...">
                        </div>
                        <div class="col-md-12">
                            <label class="form-label small fw-bold text-muted">Access Token (Token de Acceso)</label>
                            <input type="password" name="mp_access_token" class="form-control font-monospace" value="{{ $company->mp_access_token }}" placeholder="APP_USR-...">
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card mb-4">
                <div class="card-header bg-white py-3"><h5 class="mb-0 fw-bold">Personalización</h5></div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label small fw-bold">Color Primario (Web)</label>
                        <input type="color" name="primary_color" class="form-control form-control-color w-100" value="{{ $company->primary_color }}">
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold">Logo</label>
                        <input type="file" id="logoInput" class="form-control" accept="image/*">
                        <input type="hidden" id="logoCropped" name="logo_cropped">
                        <div class="mt-3 text-center">
                            @if($company->logo)
                                <img src="{{ Storage::url($company->logo) }}" id="logoPreview" class="img-thumbnail rounded-circle bg-light shadow-sm" style="width: 120px; height: 120px; object-fit: cover;">
                            @else
                                <img src="https://placehold.co/400x400?text=Logo" id="logoPreview" class="img-thumbnail rounded-circle bg-light shadow-sm" style="width: 120px; height: 120px; object-fit: cover;">
                            @endif
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="d-grid gap-2">
                <button type="submit" class="btn btn-primary btn-lg">Guardar Todo</button>
            </div>
        </div>
    </div>
</form>

<!-- Modal de Recorte -->
<div class="modal fade" id="cropperModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="cropperModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title fw-bold" id="cropperModalLabel">Recortar Logo</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body text-center">
                <div class="img-container" style="max-height: 450px; overflow: hidden;">
                    <img id="imageToCrop" src="" style="max-width: 100%;">
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-primary" id="cropButton">Cortar y Usar</button>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<link href="https://cdn.jsdelivr.net/npm/cropperjs@1.6.1/dist/cropper.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/cropperjs@1.6.1/dist/cropper.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const logoInput = document.getElementById('logoInput');
        const logoCropped = document.getElementById('logoCropped');
        const logoPreview = document.getElementById('logoPreview');
        const imageToCrop = document.getElementById('imageToCrop');
        const cropButton = document.getElementById('cropButton');
        
        const modalEl = document.getElementById('cropperModal');
        const cropperModal = new bootstrap.Modal(modalEl);
        let cropper = null;

        logoInput.addEventListener('change', function(e) {
            const files = e.target.files;
            if (files && files.length > 0) {
                const file = files[0];
                const reader = new FileReader();
                reader.onload = function(event) {
                    imageToCrop.src = event.target.result;
                    cropperModal.show();
                };
                reader.readAsDataURL(file);
            }
        });

        modalEl.addEventListener('shown.bs.modal', function() {
            cropper = new Cropper(imageToCrop, {
                aspectRatio: 1,
                viewMode: 2,
                dragMode: 'move',
                autoCropArea: 1,
                restore: false,
                guides: true,
                center: true,
                highlight: false,
                cropBoxMovable: true,
                cropBoxResizable: true,
                toggleDragModeOnDblclick: false,
            });
        });

        modalEl.addEventListener('hidden.bs.modal', function() {
            if (cropper) {
                cropper.destroy();
                cropper = null;
            }
            // Limpiar el input de archivo por si el usuario canceló sin recortar
            if (!logoCropped.value) {
                logoInput.value = '';
            }
        });

        cropButton.addEventListener('click', function() {
            if (cropper) {
                const canvas = cropper.getCroppedCanvas({
                    width: 400,
                    height: 400,
                    imageSmoothingEnabled: true,
                    imageSmoothingQuality: 'high',
                });

                const base64Image = canvas.toDataURL('image/jpeg', 0.9);
                logoCropped.value = base64Image;
                logoPreview.src = base64Image;

                // Actualizar avatar del Topbar en tiempo real
                const topbarContainer = document.getElementById('topbarUserImageContainer');
                if (topbarContainer) {
                    topbarContainer.innerHTML = `<img src="${base64Image}" id="topbarUserLogo" class="rounded-circle bg-light border" style="width: 36px; height: 36px; object-fit: cover;">`;
                }

                // Actualizar logo del Sidebar en tiempo real
                const sidebarContainer = document.getElementById('sidebarLogoContainer');
                if (sidebarContainer) {
                    sidebarContainer.innerHTML = `<img src="${base64Image}" id="sidebarLogo" class="rounded-circle bg-light border" style="width: 32px; height: 32px; object-fit: cover;">`;
                }

                cropperModal.hide();
            }
        });
    });
</script>
@endsection
