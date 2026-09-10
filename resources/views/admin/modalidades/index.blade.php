@extends('admin.base_admin')

@section('title', 'Modalidades')
@section('heading', 'Modalidades')
@section('subtitle', 'Adicione ou remova badges (apenas título).')

@section('content')
<form method="POST" action="{{ route('admin.modalidades.update') }}" class="admin-panel" id="modalidades-form">
    @csrf
    @method('PUT')

    <div id="modalidades-rows" class="d-flex flex-column gap-2">
        @forelse ($modalidades as $modalidade)
            <div class="row g-2 align-items-center modalidade-row">
                <div class="col">
                    <input type="text" name="titulos[]" class="form-control" value="{{ $modalidade->titulo }}" placeholder="Nome da modalidade" required>
                </div>
                <div class="col-auto">
                    <button type="button" class="btn btn-outline-danger" data-remove-row>&times;</button>
                </div>
            </div>
        @empty
            <div class="row g-2 align-items-center modalidade-row">
                <div class="col">
                    <input type="text" name="titulos[]" class="form-control" placeholder="Nome da modalidade">
                </div>
                <div class="col-auto">
                    <button type="button" class="btn btn-outline-danger" data-remove-row>&times;</button>
                </div>
            </div>
        @endforelse
    </div>

    <div class="d-flex gap-2 mt-4">
        <button type="button" class="btn btn-outline-admin" id="add-modalidade-row">Adicionar linha</button>
        <button type="submit" class="btn btn-admin">Salvar</button>
    </div>
</form>

<template id="modalidade-row-template">
    <div class="row g-2 align-items-center modalidade-row">
        <div class="col">
            <input type="text" name="titulos[]" class="form-control" placeholder="Nome da modalidade">
        </div>
        <div class="col-auto">
            <button type="button" class="btn btn-outline-danger" data-remove-row>&times;</button>
        </div>
    </div>
</template>
@endsection

@push('scripts')
<script src="{{ asset('js/admin.js') }}"></script>
@endpush
