@extends('admin.base_admin')

@section('title', 'Bonificações')
@section('heading', 'Bonificações')
@section('subtitle', 'Gerencie os 3 cards de bônus exibidos no site.')

@section('content')
<form method="POST" action="{{ route('admin.bonificacoes.update') }}" class="admin-panel">
    @csrf
    @method('PUT')

    <div class="row g-4">
        @foreach ($bonificacoes as $index => $item)
            <div class="col-lg-4">
                <div class="admin-card-block">
                    <h2 class="h6 mb-3">Card {{ $index + 1 }}</h2>
                    <input type="hidden" name="items[{{ $index }}][id]" value="{{ $item->id }}">
                    <div class="mb-3">
                        <label class="form-label">Valor do bônus</label>
                        <input type="text" name="items[{{ $index }}][valor]" class="form-control" value="{{ old('items.'.$index.'.valor', $item->valor) }}" required placeholder="25%">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Título</label>
                        <input type="text" name="items[{{ $index }}][titulo]" class="form-control" value="{{ old('items.'.$index.'.titulo', $item->titulo) }}" required>
                    </div>
                    <div class="mb-0">
                        <label class="form-label">Descrição</label>
                        <textarea name="items[{{ $index }}][descricao]" rows="4" class="form-control">{{ old('items.'.$index.'.descricao', $item->descricao) }}</textarea>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    <div class="mt-4">
        <button type="submit" class="btn btn-admin">Salvar</button>
    </div>
</form>
@endsection
