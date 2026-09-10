<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Bonificacao extends Model
{
    protected $table = 'bonificacoes';

    protected $fillable = [
        'valor',
        'titulo',
        'descricao',
        'sort_order',
    ];
}
