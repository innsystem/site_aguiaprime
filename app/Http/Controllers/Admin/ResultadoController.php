<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Resultado;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ResultadoController extends Controller
{
    public function index()
    {
        return view('admin.resultados.index', [
            'resultados' => Resultado::query()->orderBy('sort_order')->orderBy('id')->get(),
        ]);
    }

    public function create()
    {
        return view('admin.resultados.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'titulo' => ['required', 'string', 'max:120'],
            'valor' => ['nullable', 'string', 'max:60'],
            'imagem' => ['required', 'image', 'mimes:jpeg,jpg,png,webp', 'max:5120'],
        ]);

        $path = $request->file('imagem')->store('resultados', 'public');

        Resultado::query()->create([
            'titulo' => $data['titulo'],
            'valor' => $this->normalizeValor($data['valor'] ?? null),
            'imagem' => $path,
            'sort_order' => (int) Resultado::query()->max('sort_order') + 1,
        ]);

        return redirect()
            ->route('admin.resultados.index')
            ->with('success', 'Slide de resultado criado com sucesso.');
    }

    public function edit(Resultado $resultado)
    {
        return view('admin.resultados.edit', compact('resultado'));
    }

    public function update(Request $request, Resultado $resultado)
    {
        $data = $request->validate([
            'titulo' => ['required', 'string', 'max:120'],
            'valor' => ['nullable', 'string', 'max:60'],
            'imagem' => ['nullable', 'image', 'mimes:jpeg,jpg,png,webp', 'max:5120'],
        ]);

        $data['valor'] = $this->normalizeValor($data['valor'] ?? null);

        if ($request->hasFile('imagem')) {
            if ($resultado->imagem) {
                Storage::disk('public')->delete($resultado->imagem);
            }
            $data['imagem'] = $request->file('imagem')->store('resultados', 'public');
        } else {
            unset($data['imagem']);
        }

        $resultado->update($data);

        return redirect()
            ->route('admin.resultados.index')
            ->with('success', 'Slide de resultado atualizado com sucesso.');
    }

    public function destroy(Resultado $resultado)
    {
        if ($resultado->imagem) {
            Storage::disk('public')->delete($resultado->imagem);
        }

        $resultado->delete();

        return redirect()
            ->route('admin.resultados.index')
            ->with('success', 'Slide de resultado removido com sucesso.');
    }

    private function normalizeValor(?string $valor): ?string
    {
        $valor = trim((string) $valor);

        return $valor === '' || $valor === 'R$' ? null : $valor;
    }
}
