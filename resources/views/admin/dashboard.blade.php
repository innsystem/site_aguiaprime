@extends('admin.base_admin')

@section('title', 'Dashboard')
@section('heading', 'Dashboard')
@section('subtitle', 'Visão geral do conteúdo gerenciado no site.')

@section('content')
<div class="row g-3">
    <div class="col-md-6 col-xl-3">
        <a href="{{ route('admin.cotacoes.index') }}" class="admin-stat-card">
            <span>Cotações</span>
            <strong>{{ $counts['cotacoes'] }}</strong>
        </a>
    </div>
    <div class="col-md-6 col-xl-3">
        <a href="{{ route('admin.bonificacoes.index') }}" class="admin-stat-card">
            <span>Bonificações</span>
            <strong>{{ $counts['bonificacoes'] }}</strong>
        </a>
    </div>
    <div class="col-md-6 col-xl-3">
        <a href="{{ route('admin.modalidades.index') }}" class="admin-stat-card">
            <span>Modalidades</span>
            <strong>{{ $counts['modalidades'] }}</strong>
        </a>
    </div>
    <div class="col-md-6 col-xl-3">
        <a href="{{ route('admin.resultados.index') }}" class="admin-stat-card">
            <span>Resultados</span>
            <strong>{{ $counts['resultados'] }}</strong>
        </a>
    </div>
</div>

<div class="admin-panel mt-4">
    <h2>Atalhos</h2>
    <div class="d-flex flex-wrap gap-2">
        <a href="{{ route('admin.settings.edit') }}" class="btn btn-outline-admin">Settings</a>
        <a href="{{ route('admin.cotacoes.index') }}" class="btn btn-outline-admin">Gerenciar cotações</a>
        <a href="{{ route('admin.bonificacoes.index') }}" class="btn btn-outline-admin">Gerenciar bonificações</a>
        <a href="{{ route('admin.modalidades.index') }}" class="btn btn-outline-admin">Gerenciar modalidades</a>
        <a href="{{ route('admin.resultados.create') }}" class="btn btn-admin">Novo slide de resultado</a>
    </div>
</div>
@endsection
