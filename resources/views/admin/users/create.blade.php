@extends('admin.base_admin')

@section('title', 'Novo usuário')
@section('heading', 'Novo usuário')
@section('subtitle', 'Cadastre um novo acesso ao painel admin.')

@section('content')
<form method="POST" action="{{ route('admin.users.store') }}" class="admin-panel">
    @csrf
    <div class="row g-3">
        <div class="col-md-6">
            <label class="form-label" for="name">Nome</label>
            <input type="text" name="name" id="name" class="form-control" value="{{ old('name') }}" required>
        </div>
        <div class="col-md-6">
            <label class="form-label" for="email">E-mail</label>
            <input type="email" name="email" id="email" class="form-control" value="{{ old('email') }}" required>
        </div>
        <div class="col-md-6">
            <label class="form-label" for="password">Senha</label>
            <input type="password" name="password" id="password" class="form-control" required>
        </div>
        <div class="col-md-6">
            <label class="form-label" for="password_confirmation">Confirmar senha</label>
            <input type="password" name="password_confirmation" id="password_confirmation" class="form-control" required>
        </div>
    </div>
    <div class="mt-4 d-flex gap-2">
        <button type="submit" class="btn btn-admin">Salvar</button>
        <a href="{{ route('admin.users.index') }}" class="btn btn-outline-admin">Cancelar</a>
    </div>
</form>
@endsection
