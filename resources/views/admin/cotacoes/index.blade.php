@extends('admin.base_admin')

@section('title', 'Cotações')
@section('heading', 'Cotações')
@section('subtitle', 'Crie e edite linhas com ícone Font Awesome, nome e valor.')

@section('content')
<div class="admin-panel mb-4">
    <h2 class="h5 mb-3">Nova cotação</h2>
    <form method="POST" action="{{ route('admin.cotacoes.store') }}" class="row g-3 align-items-end">
        @csrf
        <div class="col-md-4">
            <label class="form-label">Ícone</label>
            <div class="icon-picker" data-icon-picker>
                <input type="hidden" name="icon" value="{{ old('icon', 'fa-coins') }}" data-icon-input>
                <button type="button" class="icon-picker__trigger" data-icon-trigger>
                    <span data-icon-preview>
                        <i class="fa-solid fa-coins"></i>
                    </span>
                    <span>Escolher ícone</span>
                </button>
                <div class="icon-picker__gallery" hidden data-icon-gallery>
                    @foreach ($icons as $icon)
                        <button type="button" class="icon-option" data-icon-value="{{ $icon }}" title="{{ $icon }}">
                            @if ($icon === 'badge-2')
                                <span class="cotacao-badge-2">2</span>
                            @else
                                <i class="fa-solid {{ $icon }}"></i>
                            @endif
                        </button>
                    @endforeach
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <label class="form-label" for="nome">Nome</label>
            <input type="text" name="nome" id="nome" class="form-control" value="{{ old('nome') }}" required>
        </div>
        <div class="col-md-3">
            <label class="form-label" for="valor">Valor</label>
            <input type="text" name="valor" id="valor" class="form-control" value="{{ old('valor') }}" required>
        </div>
        <div class="col-md-1">
            <button type="submit" class="btn btn-admin w-100">+</button>
        </div>
    </form>
</div>

<div class="admin-panel">
    <h2 class="h5 mb-3">Cotações cadastradas</h2>
    <div class="d-flex flex-column gap-3">
        @forelse ($cotacoes as $cotacao)
            <div class="admin-card-block">
                <form method="POST" action="{{ route('admin.cotacoes.update', $cotacao) }}" class="row g-2 align-items-end">
                    @csrf
                    @method('PUT')
                    <div class="col-md-3">
                        <label class="form-label">Ícone</label>
                        <div class="icon-picker" data-icon-picker>
                            <input type="hidden" name="icon" value="{{ $cotacao->icon }}" data-icon-input>
                            <button type="button" class="icon-picker__trigger" data-icon-trigger>
                                <span data-icon-preview>
                                    @if ($cotacao->icon === 'badge-2')
                                        <span class="cotacao-badge-2">2</span>
                                    @else
                                        <i class="fa-solid {{ $cotacao->icon }}"></i>
                                    @endif
                                </span>
                                <span>Ícone</span>
                            </button>
                            <div class="icon-picker__gallery" hidden data-icon-gallery>
                                @foreach ($icons as $icon)
                                    <button type="button" class="icon-option {{ $cotacao->icon === $icon ? 'active' : '' }}" data-icon-value="{{ $icon }}" title="{{ $icon }}">
                                        @if ($icon === 'badge-2')
                                            <span class="cotacao-badge-2">2</span>
                                        @else
                                            <i class="fa-solid {{ $icon }}"></i>
                                        @endif
                                    </button>
                                @endforeach
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Nome</label>
                        <input type="text" name="nome" class="form-control" value="{{ $cotacao->nome }}" required>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Valor</label>
                        <input type="text" name="valor" class="form-control" value="{{ $cotacao->valor }}" required>
                    </div>
                    <div class="col-md-2">
                        <button type="submit" class="btn btn-admin w-100">Salvar</button>
                    </div>
                </form>
                <form method="POST" action="{{ route('admin.cotacoes.destroy', $cotacao) }}" class="mt-2" onsubmit="return confirm('Remover esta cotação?')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-sm btn-outline-danger">Excluir</button>
                </form>
            </div>
        @empty
            <p class="text-muted mb-0">Nenhuma cotação cadastrada.</p>
        @endforelse
    </div>
</div>
@endsection

@push('scripts')
<script src="{{ asset('js/admin.js') }}"></script>
@endpush
