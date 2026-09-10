<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex,nofollow">
    <title>@yield('title', 'Admin') — Águia Prime</title>
    <link rel="icon" href="{{ asset('img/logo.png') }}" type="image/png">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/admin.css') }}">
    <script src="https://kit.fontawesome.com/927ac8bf3f.js" crossorigin="anonymous"></script>
    @stack('head')
</head>
<body class="admin-body">
    <div class="admin-shell">
        <aside class="admin-sidebar">
            <div class="admin-brand">
                <img src="{{ asset('img/logo.png') }}" alt="Águia Prime" width="36" height="36">
                <div>
                    <strong>Águia Prime</strong>
                    <span>Painel Admin</span>
                </div>
            </div>

            <nav class="admin-nav">
                <a href="{{ route('admin.dashboard') }}" class="{{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                    <i class="fa-solid fa-gauge-high"></i> Dashboard
                </a>
                <a href="{{ route('admin.settings.edit') }}" class="{{ request()->routeIs('admin.settings.*') ? 'active' : '' }}">
                    <i class="fa-solid fa-gear"></i> Configurações	
                </a>
                <a href="{{ route('admin.cotacoes.index') }}" class="{{ request()->routeIs('admin.cotacoes.*') ? 'active' : '' }}">
                    <i class="fa-solid fa-coins"></i> Cotações
                </a>
                <a href="{{ route('admin.bonificacoes.index') }}" class="{{ request()->routeIs('admin.bonificacoes.*') ? 'active' : '' }}">
                    <i class="fa-solid fa-gift"></i> Bonificações
                </a>
                <a href="{{ route('admin.modalidades.index') }}" class="{{ request()->routeIs('admin.modalidades.*') ? 'active' : '' }}">
                    <i class="fa-solid fa-ticket"></i> Modalidades
                </a>
                <a href="{{ route('admin.resultados.index') }}" class="{{ request()->routeIs('admin.resultados.*') ? 'active' : '' }}">
                    <i class="fa-solid fa-images"></i> Resultados
                </a>
                <a href="{{ route('admin.users.index') }}" class="{{ request()->routeIs('admin.users.*') ? 'active' : '' }}">
                    <i class="fa-solid fa-users"></i> Usuários
                </a>
            </nav>

            <div class="admin-sidebar__footer">
                <a href="{{ route('home') }}" target="_blank" rel="noopener">
                    <i class="fa-solid fa-arrow-up-right-from-square"></i> Ver site
                </a>
                <form method="POST" action="{{ route('admin.logout') }}">
                    @csrf
                    <button type="submit"><i class="fa-solid fa-right-from-bracket"></i> Sair</button>
                </form>
            </div>
        </aside>

        <div class="admin-main">
            <header class="admin-topbar">
                <div>
                    <h1>@yield('heading', 'Dashboard')</h1>
                    @hasSection('subtitle')
                        <p>@yield('subtitle')</p>
                    @endif
                </div>
                <div class="admin-user">
                    <span>{{ auth()->user()->name }}</span>
                    <small>{{ auth()->user()->email }}</small>
                </div>
            </header>

            <div class="admin-content">
                @include('admin.partials.alerts')
                @yield('content')
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    @stack('scripts')
</body>
</html>
