@extends('admin.base_admin')

@section('title', 'Editar usuário')
@section('heading', 'Editar usuário')
@section('subtitle', 'Atualize os dados de acesso do usuário.')

@section('content')
<form method="POST" action="{{ route('admin.users.update', $user) }}" class="admin-panel">
    @csrf
    @method('PUT')
    <div class="row g-3">
        <div class="col-md-6">
            <label class="form-label" for="name">Nome</label>
            <input type="text" name="name" id="name" class="form-control" value="{{ old('name', $user->name) }}" required>
        </div>
        <div class="col-md-6">
            <label class="form-label" for="email">E-mail</label>
            <input
                type="email"
                name="email"
                id="email"
                class="form-control"
                value="{{ old('email', $user->email) }}"
                @if ($user->isProtected()) readonly @endif
                required
            >
            @if ($user->isProtected())
                <small class="text-muted">E-mail protegido não pode ser alterado.</small>
            @endif
        </div>
        <div class="col-md-6">
            <label class="form-label" for="password">Nova senha</label>
            <input type="password" name="password" id="password" class="form-control">
            <small class="text-muted">Deixe em branco para manter a senha atual.</small>
        </div>
        <div class="col-md-6">
            <label class="form-label" for="password_confirmation">Confirmar nova senha</label>
            <input type="password" name="password_confirmation" id="password_confirmation" class="form-control">
        </div>
    </div>
    <div class="mt-4 d-flex gap-2">
        <button type="submit" class="btn btn-admin">Atualizar</button>
        <a href="{{ route('admin.users.index') }}" class="btn btn-outline-admin">Cancelar</a>
    </div>
</form>
@endsection
