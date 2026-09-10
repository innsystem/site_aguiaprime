<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Bonificacao;
use Illuminate\Http\Request;

class BonificacaoController extends Controller
{
    public function index()
    {
        $items = Bonificacao::query()->orderBy('sort_order')->orderBy('id')->get();

        while ($items->count() < 3) {
            $items->push(new Bonificacao([
                'valor' => '',
                'titulo' => '',
                'descricao' => '',
                'sort_order' => $items->count(),
            ]));
        }

        return view('admin.bonificacoes.index', [
            'bonificacoes' => $items->take(3),
        ]);
    }

    public function update(Request $request)
    {
        $data = $request->validate([
            'items' => ['required', 'array', 'size:3'],
            'items.*.id' => ['nullable', 'integer', 'exists:bonificacoes,id'],
            'items.*.valor' => ['required', 'string', 'max:40'],
            'items.*.titulo' => ['required', 'string', 'max:120'],
            'items.*.descricao' => ['nullable', 'string', 'max:1000'],
        ]);

        foreach ($data['items'] as $index => $item) {
            $payload = [
                'valor' => $item['valor'],
                'titulo' => $item['titulo'],
                'descricao' => $item['descricao'] ?? null,
                'sort_order' => $index,
            ];

            if (! empty($item['id'])) {
                Bonificacao::query()->whereKey($item['id'])->update($payload);
            } else {
                Bonificacao::query()->create($payload);
            }
        }

        return redirect()
            ->route('admin.bonificacoes.index')
            ->with('success', 'Bonificações atualizadas com sucesso.');
    }
}
