<footer class="site-footer">
    <div class="container">
        <div class="row align-items-center gy-4">
            <div class="col-md-6 text-center text-md-start">
                <div class="d-flex align-items-center justify-content-center justify-content-md-start gap-3 mb-3">
                    <img src="{{ asset('img/logo.png') }}" alt="Águia Prime" width="52" height="52">
                    <div>
                        <strong class="footer-brand">Águia Prime Loterias</strong>
                        <p class="mb-0 small text-muted-gold">Entretenimento responsável</p>
                    </div>
                </div>
            </div>
            <div class="col-md-6 text-center text-md-end">
                <a class="btn btn-outline-gold btn-sm me-2 mb-2" href="{{ $settings->platform_url ?? config('site.register_url') }}" target="_blank" rel="noopener">Cadastrar</a>
                <a class="btn btn-gold btn-sm mb-2" href="{{ $settings->whatsapp_url ?? config('site.vip_url') }}" target="_blank" rel="noopener">Grupo VIP</a>
            </div>
        </div>
        <hr class="footer-divider">
        <p class="footer-disclaimer text-center mb-2">
            Jogue com responsabilidade. Proibido para menores de 18 anos.
            A Águia Prime Loterias é uma plataforma de entretenimento para maiores de idade.
        </p>
        <p class="text-center small mb-0 copyright">
            &copy; {{ date('Y') }} Águia Prime Loterias. Todos os direitos reservados.
        </p>
    </div>
</footer>
