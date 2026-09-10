<!DOCTYPE html>
<html lang="pt-BR" prefix="og: https://ogp.me/ns#">
@php
    $siteName = $settings->title ?? config('site.name');
    $pageTitle = trim($__env->yieldContent('title', $siteName));
    $metaDescription = trim($__env->yieldContent(
        'meta_description',
        $settings->description
            ?? 'Águia Prime Loterias — cotações acima do mercado, saques rápidos via PIX e plataforma segura.'
    ));
    $metaKeywords = trim($__env->yieldContent('meta_keywords', $settings->meta_keywords ?? ''));
    $canonicalUrl = trim($__env->yieldContent('canonical', url()->current()));
    $ogImage = trim($__env->yieldContent('og_image', asset('img/eagle-exec.webp')));
    $ogType = trim($__env->yieldContent('og_type', 'website'));
    $robots = trim($__env->yieldContent('robots', 'index, follow, max-image-preview:large, max-snippet:-1, max-video-preview:-1'));
    $videoEmbed = $settings->video_embed_url ?? null;
    $videoId = $videoEmbed ? \App\Support\YoutubeUrl::extractId($videoEmbed) : null;
    $whatsappUrl = $settings->whatsapp_url ?? config('site.vip_url');
    $platformUrl = $settings->platform_url ?? config('site.register_url');
@endphp
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="theme-color" content="#0B1220">
    <meta name="color-scheme" content="dark">
    <meta name="format-detection" content="telephone=no">

    <title>{{ $pageTitle }}</title>
    <meta name="description" content="{{ $metaDescription }}">
    @if ($metaKeywords !== '')
        <meta name="keywords" content="{{ $metaKeywords }}">
    @endif
    <meta name="author" content="{{ $siteName }}">
    <meta name="robots" content="{{ $robots }}">
    <meta name="googlebot" content="{{ $robots }}">
    <meta name="bingbot" content="index, follow">
    <meta name="language" content="Portuguese">
    <meta name="revisit-after" content="7 days">
    <link rel="canonical" href="{{ $canonicalUrl }}">
    <link rel="sitemap" type="application/xml" title="Sitemap" href="{{ url('/sitemap.xml') }}">

    {{-- Open Graph --}}
    <meta property="og:locale" content="pt_BR">
    <meta property="og:type" content="{{ $ogType }}">
    <meta property="og:site_name" content="{{ $siteName }}">
    <meta property="og:title" content="{{ $pageTitle }}">
    <meta property="og:description" content="{{ $metaDescription }}">
    <meta property="og:url" content="{{ $canonicalUrl }}">
    <meta property="og:image" content="{{ $ogImage }}">
    <meta property="og:image:secure_url" content="{{ $ogImage }}">
    <meta property="og:image:alt" content="{{ $siteName }}">
    <meta property="og:image:type" content="image/webp">
    @if ($videoId)
        <meta property="og:video" content="https://www.youtube.com/embed/{{ $videoId }}">
        <meta property="og:video:url" content="https://www.youtube.com/watch?v={{ $videoId }}">
        <meta property="og:video:type" content="text/html">
        <meta property="og:video:width" content="1280">
        <meta property="og:video:height" content="720">
    @endif

    {{-- Twitter Card --}}
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="{{ $pageTitle }}">
    <meta name="twitter:description" content="{{ $metaDescription }}">
    <meta name="twitter:image" content="{{ $ogImage }}">
    <meta name="twitter:image:alt" content="{{ $siteName }}">

    {{-- Ícones --}}
    <link rel="icon" href="{{ asset('img/logo.png') }}" type="image/png">
    <link rel="apple-touch-icon" href="{{ asset('img/logo.png') }}">
    <link rel="mask-icon" href="{{ asset('img/logo.png') }}" color="#D4AF37">

    {{-- Preconnect / assets --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cinzel:wght@500;600;700&family=Outfit:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css">
    <link rel="stylesheet" href="{{ asset('css/site.css') }}">
    <script src="https://kit.fontawesome.com/927ac8bf3f.js" crossorigin="anonymous"></script>

    {{-- JSON-LD --}}
    <script type="application/ld+json">
        {!! json_encode([
            '@context' => 'https://schema.org',
            '@graph' => array_values(array_filter([
                [
                    '@type' => 'Organization',
                    '@id' => url('/').'/#organization',
                    'name' => $siteName,
                    'url' => url('/'),
                    'logo' => asset('img/logo.png'),
                    'image' => $ogImage,
                    'description' => $metaDescription,
                    'sameAs' => array_values(array_filter([
                        $whatsappUrl ?: null,
                        $platformUrl ?: null,
                    ])),
                ],
                [
                    '@type' => 'WebSite',
                    '@id' => url('/').'/#website',
                    'url' => url('/'),
                    'name' => $siteName,
                    'description' => $metaDescription,
                    'publisher' => [
                        '@id' => url('/').'/#organization',
                    ],
                    'inLanguage' => 'pt-BR',
                ],
                [
                    '@type' => 'WebPage',
                    '@id' => $canonicalUrl.'#webpage',
                    'url' => $canonicalUrl,
                    'name' => $pageTitle,
                    'description' => $metaDescription,
                    'isPartOf' => [
                        '@id' => url('/').'/#website',
                    ],
                    'about' => [
                        '@id' => url('/').'/#organization',
                    ],
                    'primaryImageOfPage' => [
                        '@type' => 'ImageObject',
                        'url' => $ogImage,
                    ],
                    'inLanguage' => 'pt-BR',
                ],
                $videoId ? [
                    '@type' => 'VideoObject',
                    'name' => $pageTitle,
                    'description' => $metaDescription,
                    'thumbnailUrl' => 'https://i.ytimg.com/vi/'.$videoId.'/hqdefault.jpg',
                    'embedUrl' => 'https://www.youtube.com/embed/'.$videoId,
                    'contentUrl' => 'https://www.youtube.com/watch?v='.$videoId,
                    'uploadDate' => now()->toAtomString(),
                    'publisher' => [
                        '@id' => url('/').'/#organization',
                    ],
                ] : null,
            ])),
        ], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT) !!}
    </script>

    @stack('head')
</head>
<body>
    @include('site.partials.header')

    <main id="conteudo-principal">
        @yield('content')
    </main>

    @include('site.partials.footer')

    <div class="whatsapp_futuante pulsaDelay animate__animated animate__tada">
        <a href="{{ $whatsappUrl }}" target="_blank" rel="noopener noreferrer" aria-label="Grupo VIP WhatsApp">
            <i class="fab fa-whatsapp"></i>
        </a>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="{{ asset('js/site.js') }}"></script>
    @stack('scripts')
</body>
</html>
