<?php

namespace App\Http\Controllers;

use App\Support\Palpites\Catalogo;
use App\Support\Palpites\GeradorDePalpites;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Validation\Rule;

class PalpiteController extends Controller
{
    public function index(Catalogo $catalogo): Response
    {
        $boot = [
            'grupos' => $catalogo->grupos(),
            'modalidades' => $catalogo->modalidades(),
            'ttlDias' => (int) config('palpites.ttl_dias'),
            'limiteHistorico' => (int) config('palpites.limite_historico'),
            'tituloPadrao' => config('palpites.titulo_padrao'),
            'storageConfig' => config('palpites.storage.config'),
            'storageHistorico' => config('palpites.storage.historico'),
        ];

        return response()
            ->view('site.palpites.app', [
                'boot' => $boot,
                'siteName' => config('site.name'),
            ])
            ->header('Referrer-Policy', 'strict-origin-when-cross-origin')
            ->header('X-Content-Type-Options', 'nosniff')
            ->header('X-Robots-Tag', 'noindex, nofollow')
            ->header('Cache-Control', 'no-store, private');
    }

    public function gerar(Request $request, GeradorDePalpites $gerador): JsonResponse
    {
        if (! config('palpites.api_gerar_habilitada')) {
            abort(404);
        }

        $ids = array_column(config('palpites.modalidades'), 'id');

        $dados = $request->validate([
            'modalidade' => ['required', 'string', Rule::in($ids)],
            'grupo' => ['nullable', 'integer', 'between:1,25'],
        ], [
            'modalidade.required' => 'Informe a modalidade.',
            'modalidade.in' => 'Modalidade desconhecida.',
            'grupo.integer' => 'Grupo inválido.',
            'grupo.between' => 'Grupo fora da faixa de 1 a 25.',
        ]);

        try {
            $palpite = $gerador->gerar(
                $dados['modalidade'],
                isset($dados['grupo']) ? (int) $dados['grupo'] : null
            );
        } catch (\InvalidArgumentException $e) {
            return response()->json(['message' => $e->getMessage()], 422);
        }

        return response()->json($palpite);
    }
}
