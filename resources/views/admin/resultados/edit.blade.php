@extends('admin.base_admin')

@section('title', 'Editar resultado')
@section('heading', 'Editar slide de resultado')
@section('subtitle', 'Atualize imagem recortada, título e valor.')

@push('head')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.6.2/cropper.min.css">
@endpush

@section('content')
<form method="POST" action="{{ route('admin.resultados.update', $resultado) }}" enctype="multipart/form-data" class="admin-panel" data-resultado-form>
    @csrf
    @method('PUT')
    <div class="row g-3">
        <div class="col-md-6">
            <label class="form-label" for="titulo">Título</label>
            <input type="text" name="titulo" id="titulo" class="form-control" value="{{ old('titulo', $resultado->titulo) }}" required>
        </div>
        <div class="col-md-6">
            <label class="form-label" for="valor">Valor do resultado</label>
            <input type="text" name="valor" id="valor" class="form-control" value="{{ old('valor', $resultado->valor) }}" placeholder="R$ 0,00" data-mask-money inputmode="numeric" autocomplete="off">
            <small class="text-muted">Opcional. Se ficar em branco, o valor não aparece no site.</small>
        </div>
        <div class="col-12">
            <label class="form-label" for="imagem">Imagem</label>
            @if ($resultado->imagem)
                <div class="mb-2" data-current-image>
                    <img src="{{ asset('storage/' . $resultado->imagem) }}" alt="" class="admin-thumb admin-thumb--lg">
                    <small class="d-block text-muted mt-1">Imagem atual</small>
                </div>
            @endif
            <input
                type="file"
                name="imagem"
                id="imagem"
                class="form-control"
                accept="image/jpeg,image/png,image/webp"
                data-image-cropper
                data-crop-required="false"
            >
            <small class="text-muted">Deixe em branco para manter a atual, ou selecione outra e recorte no formato 16:10.</small>
            <div class="admin-cropper-preview mt-3" data-crop-preview hidden>
                <img src="" alt="Prévia recortada">
            </div>
        </div>
    </div>
    <div class="mt-4 d-flex gap-2">
        <button type="submit" class="btn btn-admin">Atualizar</button>
        <a href="{{ route('admin.resultados.index') }}" class="btn btn-outline-admin">Cancelar</a>
    </div>
</form>

@include('admin.partials.image-cropper')
@endsection

@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.6.2/cropper.min.js"></script>
<script src="{{ asset('js/admin.js') }}"></script>
@endpush
