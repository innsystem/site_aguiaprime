<?php

return [

    'name' => 'Águia Prime Loterias',

    'tagline' => 'A SUA CHANCE DE GANHAR COMEÇA AGORA!',

    'subtitle' => 'Resultados de verdade começam com escolhas certas.',

    'register_url' => env('AGUIA_REGISTER_URL', 'https://app.aguiaprime119000.com/pr/zFhVdeku'),

    'vip_url' => env('AGUIA_VIP_URL', 'https://chat.whatsapp.com/J0RROFoeBTxFwAzx77Lt7M'),

    'video_url' => env('AGUIA_VIDEO_URL', ''),

    'cotacoes' => [
        ['nome' => 'Milhar', 'valor' => '8.000'],
        ['nome' => 'Milhar Centena', 'valor' => '8.800'],
        ['nome' => 'Centena', 'valor' => '800,00'],
        ['nome' => 'Dezena', 'valor' => '80,00'],
        ['nome' => 'Grupo', 'valor' => '80,00'],
        ['nome' => 'Duque de Dezena', 'valor' => '300,00'],
        ['nome' => 'Terno de Dezena', 'valor' => '5.000'],
        ['nome' => 'Terno de Grupo', 'valor' => '1.500'],
        ['nome' => 'Duque de Grupo', 'valor' => '180,00'],
        ['nome' => 'Milhar Invertida', 'valor' => '8.000'],
        ['nome' => 'Palpitão', 'valor' => '800,00'],
        ['nome' => 'Passe Vai', 'valor' => '90,00'],
        ['nome' => 'Passe Vai e Vem', 'valor' => '45,00'],
        ['nome' => 'Centena Esquerda', 'valor' => '800,00'],
        ['nome' => 'Dezena Esquerda', 'valor' => '80,00'],
        ['nome' => 'Grupo Meio', 'valor' => '20,00'],
    ],

    'vantagens' => [
        ['icon' => 'fa-bolt', 'title' => 'Saques rápidos via PIX'],
        ['icon' => 'fa-chart-line', 'title' => 'Cotações acima do mercado'],
        ['icon' => 'fa-mobile-screen', 'title' => 'Plataforma simples e fácil'],
        ['icon' => 'fa-headset', 'title' => 'Suporte todos os dias'],
        ['icon' => 'fa-lightbulb', 'title' => 'Palpites exclusivos'],
    ],

    'bonus' => [
        [
            'badge' => '25%',
            'title' => 'Bônus para novos clientes',
            'text' => 'Máximo de R$ 100,00. Válido para primeira recarga do dia na modalidade loterias.',
        ],
        [
            'badge' => '15%',
            'title' => 'Bônus nos dias de Federal',
            'text' => 'Promoções nos dias de Federal + recompensas extras toda semana.',
        ],
        [
            'badge' => '10%',
            'title' => 'Bônus diário',
            'text' => 'Promoção válida para primeira recarga do dia na modalidade loterias.',
        ],
    ],

    'modalidades' => [
        'Lotto Trivo',
        'Rio/Federal',
        'Maluquinha',
        'Capital',
        'São Paulo',
        'Look/Goiás',
        'Lotece/Lotep',
        'Bahia',
        'Minas Gerais',
        'Nacional',
        'Loteria Instantânea',
        'Quininha',
        'Seninha',
        'Super 15',
    ],

    'premios' => [
        ['valor' => 'R$ 20.000,00', 'label' => 'Comprovante de prêmio'],
        ['valor' => 'R$ 11.000,00', 'label' => 'Comprovante de prêmio'],
        ['valor' => 'R$ 25.000,00', 'label' => 'Comprovante de prêmio'],
        ['valor' => 'R$ 62.000,00', 'label' => 'Comprovante de prêmio'],
    ],

];
