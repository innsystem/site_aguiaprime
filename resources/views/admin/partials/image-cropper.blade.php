{{-- Cropper modal compartilhado para resultados (16:10) --}}
<div class="modal fade" id="imageCropperModal" tabindex="-1" aria-labelledby="imageCropperModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content admin-cropper-modal">
            <div class="modal-header">
                <h2 class="modal-title h5" id="imageCropperModalLabel">Recortar imagem</h2>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Fechar"></button>
            </div>
            <div class="modal-body">
                <p class="admin-cropper-hint mb-3">Ajuste o enquadramento no formato <strong>16:10</strong> usado no site.</p>
                <div class="admin-cropper-stage">
                    <img id="imageCropperSource" alt="Pré-visualização para recorte" src="">
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-admin" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-admin" id="imageCropperConfirm">Aplicar recorte</button>
            </div>
        </div>
    </div>
</div>
