<header class="site-header" id="topo">
    <nav class="navbar navbar-expand-lg navbar-dark">
        <div class="container">
            <a class="navbar-brand d-flex align-items-center gap-2" href="{{ route('home') }}">
                <img src="{{ asset('img/logo.png') }}" alt="Águia Prime Loterias" class="brand-logo" width="48" height="48">
                <span class="brand-text d-none d-sm-inline">Águia Prime</span>
            </a>

            <button class="navbar-toggler border-0" type="button" data-bs-toggle="offcanvas" data-bs-target="#navOffcanvas" aria-controls="navOffcanvas" aria-label="Abrir menu">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse d-none d-lg-flex">
                <ul class="navbar-nav ms-auto align-items-lg-center gap-lg-1">
                    <li class="nav-item"><a class="nav-link" href="#cotacoes">Cotações</a></li>
                    <li class="nav-item"><a class="nav-link" href="#vantagens">Vantagens</a></li>
                    <li class="nav-item"><a class="nav-link" href="#bonus">Bônus</a></li>
                    <li class="nav-item"><a class="nav-link" href="#modalidades">Loterias</a></li>
                    <li class="nav-item"><a class="nav-link" href="#premios">Prêmios</a></li>
                    <li class="nav-item"><a class="nav-link" href="#vip">VIP</a></li>
                    <li class="nav-item ms-lg-2">
                        <a class="btn btn-gold btn-sm px-3" href="{{ config('site.register_url') }}" target="_blank" rel="noopener">Cadastrar agora</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>
</header>

<div class="offcanvas offcanvas-end nav-offcanvas" tabindex="-1" id="navOffcanvas" aria-labelledby="navOffcanvasLabel">
    <div class="offcanvas-header">
        <h5 class="offcanvas-title" id="navOffcanvasLabel">
            <img src="{{ asset('img/logo.png') }}" alt="" width="36" height="36" class="me-2">
            Menu
        </h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="offcanvas" aria-label="Fechar"></button>
    </div>
    <div class="offcanvas-body">
        <ul class="navbar-nav gap-2">
            <li class="nav-item"><a class="nav-link" data-bs-dismiss="offcanvas" href="#cotacoes">Cotações</a></li>
            <li class="nav-item"><a class="nav-link" data-bs-dismiss="offcanvas" href="#vantagens">Vantagens</a></li>
            <li class="nav-item"><a class="nav-link" data-bs-dismiss="offcanvas" href="#bonus">Bônus</a></li>
            <li class="nav-item"><a class="nav-link" data-bs-dismiss="offcanvas" href="#modalidades">Loterias</a></li>
            <li class="nav-item"><a class="nav-link" data-bs-dismiss="offcanvas" href="#premios">Prêmios</a></li>
            <li class="nav-item"><a class="nav-link" data-bs-dismiss="offcanvas" href="#vip">VIP</a></li>
        </ul>
        <a class="btn btn-gold w-100 mt-4" href="{{ config('site.register_url') }}" target="_blank" rel="noopener">
            <i class="fa-solid fa-rocket me-2"></i>Cadastrar agora
        </a>
    </div>
</div>
