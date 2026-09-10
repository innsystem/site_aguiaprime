<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Modalidade;
use Illuminate\Http\Request;

class ModalidadeController extends Controller
{
    public function index()
    {
        return view('admin.modalidades.index', [
            'modalidades' => Modalidade::query()->orderBy('sort_order')->orderBy('id')->get(),
        ]);
    }

    public function update(Request $request)
    {
        $data = $request->validate([
            'titulos' => ['nullable', 'array'],
            'titulos.*' => ['nullable', 'string', 'max:120'],
        ]);

        $titulos = collect($data['titulos'] ?? [])
            ->map(fn ($titulo) => trim((string) $titulo))
            ->filter()
            ->values();

        Modalidade::query()->delete();

        foreach ($titulos as $index => $titulo) {
            Modalidade::query()->create([
                'titulo' => $titulo,
                'sort_order' => $index,
            ]);
        }

        return redirect()
            ->route('admin.modalidades.index')
            ->with('success', 'Modalidades atualizadas com sucesso.');
    }
}
