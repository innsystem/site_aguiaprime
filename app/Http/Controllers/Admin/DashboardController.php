<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Bonificacao;
use App\Models\Cotacao;
use App\Models\Modalidade;
use App\Models\Resultado;

class DashboardController extends Controller
{
    public function index()
    {
        return view('admin.dashboard', [
            'counts' => [
                'cotacoes' => Cotacao::count(),
                'bonificacoes' => Bonificacao::count(),
                'modalidades' => Modalidade::count(),
                'resultados' => Resultado::count(),
            ],
        ]);
    }
}
