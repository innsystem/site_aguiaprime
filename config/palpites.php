<?php

$nomes = [
    1 => 'Avestruz',
    2 => 'Águia',
    3 => 'Burro',
    4 => 'Borboleta',
    5 => 'Cachorro',
    6 => 'Cabra',
    7 => 'Carneiro',
    8 => 'Camelo',
    9 => 'Cobra',
    10 => 'Coelho',
    11 => 'Cavalo',
    12 => 'Elefante',
    13 => 'Galo',
    14 => 'Gato',
    15 => 'Jacaré',
    16 => 'Leão',
    17 => 'Macaco',
    18 => 'Porco',
    19 => 'Pavão',
    20 => 'Peru',
    21 => 'Touro',
    22 => 'Tigre',
    23 => 'Urso',
    24 => 'Veado',
    25 => 'Vaca',
];

$grupos = [];

foreach ($nomes as $numero => $nome) {
    $dezenas = $numero === 25
        ? [97, 98, 99, 0]
        : range(($numero - 1) * 4 + 1, ($numero - 1) * 4 + 4);

    $grupos[] = [
        'grupo' => $numero,
        'nome' => $nome,
        'dezenas' => array_values($dezenas),
    ];
}

return [

    'ttl_dias' => 14,

    'limite_historico' => 200,

    'titulo_padrao' => 'Palpites da Águia Prime',

    'api_gerar_habilitada' => (bool) env('PALPITES_API_GERAR', false),

    'storage' => [
        'config' => 'aguiaprime:palpites:config',
        'historico' => 'aguiaprime:palpites:historico',
    ],

    'grupos' => $grupos,

    'modalidades' => [
        ['id' => 'milhar', 'nome' => 'Milhar', 'dica' => '4 números', 'usa_bicho' => true],
        ['id' => 'centena', 'nome' => 'Centena', 'dica' => '3 números', 'usa_bicho' => true],
        ['id' => 'dezena', 'nome' => 'Dezena', 'dica' => '2 números', 'usa_bicho' => true],
        ['id' => 'grupo', 'nome' => 'Grupo', 'dica' => '1 bicho', 'usa_bicho' => true],
        ['id' => 'duque-grupo', 'nome' => 'Duque de grupo', 'dica' => '2 bichos', 'usa_bicho' => true],
        ['id' => 'terno-grupo', 'nome' => 'Terno de grupo', 'dica' => '3 bichos', 'usa_bicho' => true],
        ['id' => 'duque-dezena', 'nome' => 'Duque de dezena', 'dica' => '2 dezenas', 'usa_bicho' => false],
        ['id' => 'terno-dezena', 'nome' => 'Terno de dezena', 'dica' => '3 dezenas', 'usa_bicho' => false],
        ['id' => 'passe-vai', 'nome' => 'Passe vai', 'dica' => '2 bichos em ordem', 'usa_bicho' => true],
        ['id' => 'passe-vai-e-vem', 'nome' => 'Passe vai e vem', 'dica' => '2 bichos, qualquer ordem', 'usa_bicho' => true],
    ],

];
