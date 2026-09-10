@extends('admin.base_admin')

@section('title', 'Usuários')
@section('heading', 'Usuários Admin')
@section('subtitle', 'Gerencie os acessos ao painel administrativo.')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <p class="mb-0 text-muted">Adicione, edite ou remova usuários do admin.</p>
    <a href="{{ route('admin.users.create') }}" class="btn btn-admin">Novo usuário</a>
</div>

<div class="admin-panel">
    <div class="table-responsive">
        <table class="table admin-table align-middle">
            <thead>
                <tr>
                    <th>Nome</th>
                    <th>E-mail</th>
                    <th>Proteção</th>
                    <th class="text-end">Ações</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($users as $user)
                    @php $canManage = $user->canBeManagedBy(auth()->user()); @endphp
                    <tr>
                        <td>{{ $user->name }}</td>
                        <td>{{ $user->email }}</td>
                        <td>
                            @if ($user->isProtected())
                                <span class="badge text-bg-warning">Protegido</span>
                            @else
                                <span class="text-muted">—</span>
                            @endif
                        </td>
                        <td class="text-end">
                            @if ($canManage)
                                <a href="{{ route('admin.users.edit', $user) }}" class="btn btn-sm btn-outline-admin">Editar</a>
                                @if ($user->id !== auth()->id() || $user->isProtected())
                                    <form method="POST" action="{{ route('admin.users.destroy', $user) }}" class="d-inline" onsubmit="return confirm('Excluir este usuário?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger">Excluir</button>
                                    </form>
                                @endif
                            @else
                                <span class="text-muted small">Somente o próprio usuário</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="text-center text-muted py-4">Nenhum usuário cadastrado.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
