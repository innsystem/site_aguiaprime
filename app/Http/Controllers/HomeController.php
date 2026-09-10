<?php

namespace App\Http\Controllers;

use App\Models\Bonificacao;
use App\Models\Cotacao;
use App\Models\Modalidade;
use App\Models\Resultado;
use App\Models\Setting;

class HomeController extends Controller
{
    public function index()
    {
        return view('site.pages.home', [
            'site' => config('site'),
            'settings' => Setting::current(),
            'cotacoes' => Cotacao::query()->orderBy('sort_order')->orderBy('id')->get(),
            'bonificacoes' => Bonificacao::query()->orderBy('sort_order')->orderBy('id')->get(),
            'modalidades' => Modalidade::query()->orderBy('sort_order')->orderBy('id')->get(),
            'resultados' => Resultado::query()->orderBy('sort_order')->orderBy('id')->get(),
        ]);
    }
}
