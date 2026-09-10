@extends('admin.base_admin')

@section('title', 'Resultados')
@section('heading', 'Resultados')
@section('subtitle', 'Slides do carrossel de prêmios/resultados.')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <p class="mb-0 text-muted">Imagem, título e valor de cada slide.</p>
    <a href="{{ route('admin.resultados.create') }}" class="btn btn-admin">Novo slide</a>
</div>

<div class="admin-panel">
    <div class="table-responsive">
        <table class="table admin-table align-middle">
            <thead>
                <tr>
                    <th>Imagem</th>
                    <th>Título</th>
                    <th>Valor</th>
                    <th class="text-end">Ações</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($resultados as $resultado)
                    <tr>
                        <td>
                            @if ($resultado->imagem)
                                <img src="{{ asset('storage/' . $resultado->imagem) }}" alt="" class="admin-thumb">
                            @else
                                <span class="text-muted">—</span>
                            @endif
                        </td>
                        <td>{{ $resultado->titulo }}</td>
                        <td>{{ $resultado->valor ?: '—' }}</td>
                        <td class="text-end">
                            <a href="{{ route('admin.resultados.edit', $resultado) }}" class="btn btn-sm btn-outline-admin">Editar</a>
                            <form method="POST" action="{{ route('admin.resultados.destroy', $resultado) }}" class="d-inline" onsubmit="return confirm('Remover este slide?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-outline-danger">Excluir</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="text-center text-muted py-4">Nenhum slide cadastrado.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
