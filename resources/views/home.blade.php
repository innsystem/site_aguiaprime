@extends('layouts.app')

@section('title', $site['name'] . ' — Cotações e saques rápidos')

@section('content')
{{-- HERO --}}
<section class="hero" id="inicio">
    <div class="hero__media" data-parallax>
        <img src="{{ asset('img/eagle-exec.webp') }}" alt="" class="hero__img" fetchpriority="high">
        <div class="hero__veil"></div>
    </div>
    <div class="container hero__content">
        <p class="hero__eyebrow reveal">Águia Prime Loterias</p>
        <h1 class="hero__title reveal">{{ $site['tagline'] }}</h1>
        <p class="hero__lead reveal">{{ $site['subtitle'] }}</p>
        <div class="hero__pills reveal">
            <span><i class="fa-solid fa-bolt"></i> Saques rápidos</span>
            <span><i class="fa-solid fa-gem"></i> Altas cotações</span>
            <span><i class="fa-solid fa-shield-halved"></i> Plataforma segura</span>
        </div>
        <div class="hero__cta reveal">
            <a class="btn btn-gold btn-lg" href="{{ $site['register_url'] }}" target="_blank" rel="noopener">
                Cadastrar agora <i class="fa-solid fa-arrow-right ms-2"></i>
            </a>
            <a class="btn btn-ghost btn-lg" href="#video">Assistir apresentação</a>
        </div>
    </div>
    <a href="#video" class="hero__scroll" aria-label="Rolar para baixo">
        <i class="fa-solid fa-chevron-down"></i>
    </a>
</section>

{{-- VÍDEO --}}
<section class="section section--dark" id="video">
    <div class="container">
        <div class="section-head text-center reveal">
            <span class="section-label">Apresentação</span>
            <h2>Conheça a Águia Prime</h2>
            <p>Veja como funciona a plataforma e por que milhares de jogadores escolhem a Águia.</p>
        </div>
        <div class="video-frame reveal">
            @if (!empty($site['video_url']))
                <div class="ratio ratio-16x9">
                    <iframe src="{{ $site['video_url'] }}" title="Vídeo Águia Prime Loterias" allowfullscreen loading="lazy" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"></iframe>
                </div>
            @else
                <div class="video-placeholder">
                    <i class="fa-solid fa-circle-play"></i>
                    <p>Espaço reservado para o vídeo de apresentação</p>
                    <small>Configure <code>AGUIA_VIDEO_URL</code> no arquivo .env</small>
                </div>
            @endif
        </div>
    </div>
</section>

{{-- COTAÇÕES --}}
<section class="section" id="cotacoes">
    <div class="container">
        <div class="section-head text-center reveal">
            <span class="section-label">Cotações</span>
            <h2>As melhores cotações do mercado</h2>
            <p>Cotações acima do mercado e saques rápidos para quem joga com estratégia.</p>
        </div>

        @php
            $cotacoesLeft = array_slice($site['cotacoes'], 0, 8);
            $cotacoesRight = array_slice($site['cotacoes'], 8);
        @endphp
        <div class="row g-3 g-lg-4 align-items-start">
            @foreach ([$cotacoesLeft, $cotacoesRight] as $chunk)
                <div class="col-12 col-lg-6">
                    <div class="cotacao-board reveal">
                        @foreach ($chunk as $row)
                            <div class="cotacao-row">
                                <div class="cotacao-row__main">
                                    <span class="cotacao-row__icon" aria-hidden="true">
                                        @if (($row['icon'] ?? '') === 'badge-2')
                                            <span class="cotacao-badge-2">2</span>
                                        @else
                                            <i class="fa-solid {{ $row['icon'] }}"></i>
                                        @endif
                                    </span>
                                    <span class="cotacao-row__name">{{ $row['nome'] }}</span>
                                </div>
                                <div class="cotacao-row__value">{{ $row['valor'] }}</div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endforeach
        </div>

        <div class="text-center mt-4 reveal">
            <a class="btn btn-gold" href="{{ $site['register_url'] }}" target="_blank" rel="noopener">Começar agora</a>
        </div>
    </div>
</section>

{{-- VANTAGENS --}}
<section class="section section--alt" id="vantagens">
    <div class="container">
        <div class="section-head text-center reveal">
            <span class="section-label">Por que jogar</span>
            <h2>Por que jogar na Águia Prime?</h2>
        </div>
        <div class="row g-4">
            @foreach ($site['vantagens'] as $item)
                <div class="col-6 col-md-4 col-lg">
                    <div class="feature-tile reveal">
                        <div class="feature-tile__icon"><i class="fa-solid {{ $item['icon'] }}"></i></div>
                        <h3>{{ $item['title'] }}</h3>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</section>

{{-- BÔNUS --}}
<section class="section" id="bonus">
    <div class="container">
        <div class="section-head text-center reveal">
            <span class="section-label">Vantagens</span>
            <h2>Bonificações exclusivas</h2>
            <p>Aumente sua banca com benefícios pensados para quem joga sério.</p>
        </div>
        <div class="row g-4">
            @foreach ($site['bonus'] as $b)
                <div class="col-md-4">
                    <article class="bonus-card reveal">
                        <div class="bonus-card__badge">{{ $b['badge'] }}</div>
                        <h3>{{ $b['title'] }}</h3>
                        <p>{{ $b['text'] }}</p>
                    </article>
                </div>
            @endforeach
        </div>
        <div class="text-center mt-4 reveal">
            <a class="btn btn-gold" href="{{ $site['register_url'] }}" target="_blank" rel="noopener">Garantir meu bônus</a>
        </div>
    </div>
</section>

{{-- MODALIDADES --}}
<section class="section section--alt" id="modalidades">
    <div class="container">
        <div class="section-head text-center reveal">
            <span class="section-label">Modalidades</span>
            <h2>As principais loterias</h2>
            <p>Com sorteios o dia todo.</p>
        </div>
        <div class="modalidade-grid reveal">
            @foreach ($site['modalidades'] as $mod)
                <span class="modalidade-chip"><i class="fa-solid fa-ticket me-2"></i>{{ $mod }}</span>
            @endforeach
        </div>
    </div>
</section>

{{-- GANHOU SACOU --}}
<section class="cta-band" id="saques">
    <div class="container text-center reveal">
        <span class="section-label">Pagamentos 24h</span>
        <h2>Ganhou? Sacou!</h2>
        <p>Receba seus prêmios sem complicação, com saques rápidos via PIX.</p>
        <a class="btn btn-gold btn-lg" href="{{ $site['register_url'] }}" target="_blank" rel="noopener">Cadastre-se agora</a>
    </div>
</section>

{{-- VIP --}}
<section class="section" id="vip">
    <div class="container">
        <div class="vip-panel reveal">
            <div class="row g-0 align-items-stretch">
                <div class="col-lg-5">
                    <div class="vip-panel__media">
                        <img src="{{ asset('img/vip-cover.jpg') }}" alt="Grupo VIP Prime Palpites" loading="lazy">
                        <span class="vip-tag">Exclusivo</span>
                    </div>
                </div>
                <div class="col-lg-7">
                    <div class="vip-panel__body">
                        <span class="section-label">Palpites</span>
                        <h2>Entre para o grupo de palpites VIP</h2>
                        <p>Receba palpites diários e dicas exclusivas para aumentar suas chances. Conteúdo selecionado para quem quer jogar com mais informação.</p>
                        <a class="btn btn-gold" href="{{ $site['vip_url'] }}" target="_blank" rel="noopener">
                            <i class="fab fa-whatsapp me-2"></i>Quero participar
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

{{-- PRÊMIOS CAROUSEL --}}
<section class="section section--alt" id="premios">
    <div class="container">
        <div class="section-head text-center reveal">
            <span class="section-label">Resultados reais</span>
            <h2>Ganhou, sacou. Mais de 3.000 clientes lucrando diariamente</h2>
            <p>Quem aposta na Águia Prime joga com segurança e recebe sem complicação.</p>
        </div>

        <div id="premiosCarousel" class="carousel slide premio-carousel reveal" data-bs-ride="carousel" data-bs-interval="4500">
            <div class="carousel-indicators">
                @foreach ($site['premios'] as $i => $premio)
                    <button type="button" data-bs-target="#premiosCarousel" data-bs-slide-to="{{ $i }}" class="{{ $i === 0 ? 'active' : '' }}" aria-current="{{ $i === 0 ? 'true' : 'false' }}" aria-label="Prêmio {{ $i + 1 }}"></button>
                @endforeach
            </div>
            <div class="carousel-inner">
                @foreach ($site['premios'] as $i => $premio)
                    <div class="carousel-item {{ $i === 0 ? 'active' : '' }}">
                        <div class="premio-slide">
                            <div class="premio-slide__visual">
                                @php $imgPath = public_path('img/premios/premio-' . ($i + 1) . '.jpg'); @endphp
                                @if (file_exists($imgPath))
                                    <img src="{{ asset('img/premios/premio-' . ($i + 1) . '.jpg') }}" alt="{{ $premio['label'] }} {{ $premio['valor'] }}" loading="lazy">
                                @else
                                    <div class="premio-slide__placeholder">
                                        <i class="fa-solid fa-trophy"></i>
                                        <span>Espaço para comprovante</span>
                                    </div>
                                @endif
                            </div>
                            <div class="premio-slide__info">
                                <span class="premio-slide__label">{{ $premio['label'] }}</span>
                                <strong class="premio-slide__valor">{{ $premio['valor'] }}</strong>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
            <button class="carousel-control-prev" type="button" data-bs-target="#premiosCarousel" data-bs-slide="prev">
                <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                <span class="visually-hidden">Anterior</span>
            </button>
            <button class="carousel-control-next" type="button" data-bs-target="#premiosCarousel" data-bs-slide="next">
                <span class="carousel-control-next-icon" aria-hidden="true"></span>
                <span class="visually-hidden">Próximo</span>
            </button>
        </div>

        <div class="text-center mt-5 reveal">
            <a class="btn btn-gold btn-lg" href="{{ $site['register_url'] }}" target="_blank" rel="noopener">Quero fazer parte</a>
        </div>
    </div>
</section>
@endsection
