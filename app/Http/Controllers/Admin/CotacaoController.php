<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Cotacao;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class CotacaoController extends Controller
{
    public function index()
    {
        return view('admin.cotacoes.index', [
            'cotacoes' => Cotacao::query()->orderBy('sort_order')->orderBy('id')->get(),
            'icons' => config('admin_icons'),
        ]);
    }

    public function store(Request $request)
    {
        $data = $this->validated($request);
        $data['sort_order'] = (int) Cotacao::query()->max('sort_order') + 1;

        Cotacao::query()->create($data);

        return redirect()
            ->route('admin.cotacoes.index')
            ->with('success', 'Cotação criada com sucesso.');
    }

    public function update(Request $request, Cotacao $cotacao)
    {
        $cotacao->update($this->validated($request));

        return redirect()
            ->route('admin.cotacoes.index')
            ->with('success', 'Cotação atualizada com sucesso.');
    }

    public function destroy(Cotacao $cotacao)
    {
        $cotacao->delete();

        return redirect()
            ->route('admin.cotacoes.index')
            ->with('success', 'Cotação removida com sucesso.');
    }

    private function validated(Request $request): array
    {
        return $request->validate([
            'icon' => ['required', 'string', Rule::in(config('admin_icons'))],
            'nome' => ['required', 'string', 'max:120'],
            'valor' => ['required', 'string', 'max:60'],
        ]);
    }
}
