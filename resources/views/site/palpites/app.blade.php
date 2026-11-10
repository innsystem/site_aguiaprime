@extends('site.palpites.layout')

@section('content')
<div class="shell">
    <header class="topo">
        <a class="marca" href="{{ route('home') }}">
            <img src="{{ asset('img/logo.png') }}" alt="" width="48" height="48">
            <span>{{ $siteName }}</span>
        </a>
        <h1 id="titulo-aba">Bem-vindo!</h1>
        <div class="filete" aria-hidden="true"></div>
    </header>

    <p class="idade">Ferramenta para maiores de 18 anos. Jogue com responsabilidade.</p>

    <main>
        <section data-painel="inicio" id="painel-inicio">
            <p id="aviso-expirado" class="aviso" hidden>Sua configuração expirou após 14 dias sem uso. Preencha de novo.</p>

            <form id="form-banca" class="card" novalidate>
                <h2>Configuração da banca</h2>
                <p class="muted">Essas informações entram no envio ao representante e na imagem dos palpites.</p>

                <label for="nome">Nome da banca</label>
                <input id="nome" name="nome" type="text" maxlength="80" autocomplete="organization" placeholder="Ex.: Águia Prime" required>
                <p id="erro-nome" class="erro" hidden></p>

                <label for="link">Link do app</label>
                <div class="campo-url">
                    <span class="campo-url__prefixo" aria-hidden="true">https://</span>
                    <input id="link" name="link" type="text" maxlength="200" inputmode="url" autocomplete="url" placeholder="app.suabanca.com.br" spellcheck="false">
                </div>
                <p class="ajuda">Se colar um link com http ou https, o prefixo não se repete.</p>
                <p id="erro-link" class="erro" hidden></p>

                <label for="whatsapp">WhatsApp do representante</label>
                <input id="whatsapp" name="whatsapp" type="tel" inputmode="tel" maxlength="22" autocomplete="tel" placeholder="Ex.: (16) 99180-0765" required>
                <p class="ajuda">É pra esse número que o jogo vai quando o cliente encaminhar.</p>
                <p id="erro-whatsapp" class="erro" hidden></p>

                <button id="salvar" class="btn btn-gold" type="submit">Salvar</button>
            </form>

            <div id="continuar" class="card continuar" hidden>
                <p>Configuração salva.</p>
                <button id="continuar-btn" class="btn btn-gold" type="button">Continuar</button>
            </div>

            <div class="card hoje">
                <div>
                    <p class="muted">Palpites gerados hoje</p>
                    <p id="total-hoje" class="hoje__numero">0</p>
                </div>
                <span class="hoje__icone" aria-hidden="true">✦</span>
            </div>

            <button id="ir-gerar" class="btn btn-claro" type="button" disabled>Gerar palpite agora →</button>
        </section>

        <section data-painel="gerador" id="painel-gerador" hidden>
            <p class="rotulo" id="rotulo-modalidade">Modalidade</p>
            <div class="grade" role="group" aria-labelledby="rotulo-modalidade">
                @foreach ($boot['modalidades'] as $modalidade)
                    <button
                        type="button"
                        class="mod {{ $loop->first ? 'is-active' : '' }}"
                        data-modalidade="{{ $modalidade['id'] }}"
                        data-usa-bicho="{{ $modalidade['usa_bicho'] ? '1' : '0' }}"
                        aria-pressed="{{ $loop->first ? 'true' : 'false' }}"
                    >
                        <span class="mod__nome">{{ $modalidade['nome'] }}</span>
                        <span class="mod__dica">{{ $modalidade['dica'] }}</span>
                    </button>
                @endforeach
            </div>

            <div id="campo-bicho">
                <label for="bicho">Bicho (opcional)</label>
                <select id="bicho">
                    <option value="">Qualquer bicho (sortear)</option>
                    @foreach ($boot['grupos'] as $grupo)
                        <option value="{{ $grupo['grupo'] }}">{{ str_pad((string) $grupo['grupo'], 2, '0', STR_PAD_LEFT) }} — {{ $grupo['nome'] }}</option>
                    @endforeach
                </select>
            </div>
            <p id="aviso-bicho" class="muted" hidden>Esta modalidade sorteia dezenas livres.</p>

            <button id="gerar" class="btn btn-gold" type="button">Gerar palpite</button>

            <div id="resultado" class="resultado" hidden>
                <p id="resultado-mod" class="resultado__mod"></p>
                <p id="resultado-linha" class="resultado__linha"></p>
                <p id="resultado-bichos" class="resultado__bichos"></p>
                <div class="acoes">
                    <button id="gerar-outro" class="btn btn-borda" type="button">Gerar outro</button>
                    <button id="adicionar-resultado" class="btn btn-escuro" type="button">Adicionar ao histórico</button>
                </div>
            </div>
        </section>

        <section data-painel="manual" id="painel-manual" hidden>
            <div class="card">
                <h2>Digitar milhar</h2>
                <p class="muted">Digite os 4 números — centena, dezena e grupo são calculados sozinhos.</p>
                <input id="milhar" class="digito" type="text" inputmode="numeric" maxlength="4" placeholder="0000" autocomplete="off" aria-label="Milhar com 4 números">
                <div id="milhar-derivados" class="derivados" hidden>
                    <div><span>Centena</span><strong id="der-centena"></strong></div>
                    <div><span>Dezena</span><strong id="der-dezena"></strong></div>
                    <div><span>Grupo</span><strong id="der-grupo"></strong></div>
                </div>
                <button id="add-milhar" class="btn btn-gold" type="button" disabled>Adicionar ao histórico</button>
            </div>

            <div class="card">
                <h2>Digitar dezena</h2>
                <input id="dezena" class="digito" type="text" inputmode="numeric" maxlength="2" placeholder="00" autocomplete="off" aria-label="Dezena com 2 números">
                <p id="dezena-grupo" class="centro" hidden>Grupo: <strong id="dezena-grupo-nome"></strong></p>
                <button id="add-dezena" class="btn btn-gold" type="button" disabled>Adicionar ao histórico</button>
            </div>
            <p id="feedback-manual" class="feedback" hidden></p>
        </section>

        <section data-painel="historico" id="painel-historico" hidden>
            <div id="historico-vazio" class="card centro">
                <p>Nenhum palpite ainda.</p>
                <p class="muted">Gere ou digite um palpite para começar.</p>
            </div>
            <div id="historico-cheio" hidden>
                <div class="barra-lista">
                    <button id="alternar-todos" class="link" type="button">Selecionar todos</button>
                    <button id="limpar" class="link link-perigo" type="button">Limpar tudo</button>
                </div>
                <div id="confirmar-limpar" class="card" hidden>
                    <p>Apagar todos os palpites deste aparelho? Essa ação não dá para desfazer.</p>
                    <div class="acoes">
                        <button id="limpar-cancelar" class="btn btn-borda" type="button">Cancelar</button>
                        <button id="limpar-confirmar" class="btn btn-perigo" type="button">Apagar</button>
                    </div>
                </div>
                <div id="lista-historico" class="lista"></div>
                <button id="ir-enviar" class="btn btn-gold" type="button" disabled>Enviar (0) selecionados</button>
            </div>
        </section>

        <section data-painel="enviar" id="painel-enviar" hidden>
            <div id="enviar-vazio" class="card centro">
                <p>Nenhum palpite selecionado.</p>
                <p class="muted">Selecione no Histórico ou use tudo de uma vez:</p>
                <button id="selecionar-tudo" class="btn btn-gold" type="button">Selecionar todo o histórico (0)</button>
            </div>
            <div id="enviar-cheio" hidden>
                <div class="card">
                    <h2 id="titulo-mensagem">Vai na mensagem (0)</h2>
                    <ul id="lista-envio" class="lista-envio"></ul>
                </div>
                <label for="previa">Prévia da mensagem</label>
                <textarea id="previa" rows="8"></textarea>
                <p id="aviso-telefone" class="aviso" hidden>
                    Informe o WhatsApp do representante no Início para encaminhar o jogo.
                    <button id="ir-inicio-telefone" class="link" type="button">Ir para Início</button>
                </p>
                <button id="encaminhar" class="btn btn-whats" type="button">Encaminhar jogo para o representante</button>
                <p class="ajuda centro">Abre o WhatsApp com o jogo pronto para enviar ao representante.</p>

                <div id="imagem-palpites" class="imagem" data-logo="{{ asset('img/logo.png') }}" data-marca="{{ $siteName }}">
                    <p class="rotulo">Imagem para divulgar</p>
                    <div class="formatos" role="group" aria-label="Tamanho da imagem">
                        <button id="formato-quadrado" class="formato is-on" type="button" aria-pressed="true">Quadrado</button>
                        <button id="formato-story" class="formato" type="button" aria-pressed="false">Story</button>
                    </div>
                    <p id="aviso-imagem" class="aviso" hidden></p>
                    <button id="gerar-imagem" class="btn btn-gold" type="button">Gerar imagem</button>
                    <div id="previa-imagem" class="previa-imagem" hidden>
                        <img id="previa-imagem-img" alt="Prévia do cartaz dos palpites">
                        <p id="legenda-imagem" class="ajuda centro"></p>
                    </div>
                    <button id="baixar-imagem" class="btn btn-claro" type="button" hidden>Baixar imagem</button>
                    <button id="compartilhar-imagem" class="btn btn-borda" type="button" hidden>Compartilhar imagem</button>
                    <p class="ajuda centro">O cartaz é para status e grupos. O registro do jogo continua no botão verde, só com o texto.</p>
                </div>
            </div>
        </section>
    </main>

    <p class="versao">Águia Prime · Palpites</p>
</div>

<nav class="nav" aria-label="Etapas do gerador">
    <button type="button" data-aba="inicio" class="is-active" aria-current="page">
        <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M4 10.5 12 4l8 6.5V20a1 1 0 0 1-1 1h-5v-6H10v6H5a1 1 0 0 1-1-1z"/></svg>
        Início
    </button>
    <button type="button" data-aba="gerador">
        <svg viewBox="0 0 24 24" aria-hidden="true"><rect x="4" y="4" width="16" height="16" rx="3"/><circle cx="9" cy="9" r="1.2"/><circle cx="15" cy="12" r="1.2"/><circle cx="10" cy="15" r="1.2"/></svg>
        Gerador
    </button>
    <button type="button" data-aba="manual">
        <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M4 20h4l10-10-4-4L4 16z"/><path d="m13 7 4 4"/></svg>
        Manual
    </button>
    <button type="button" data-aba="historico">
        <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M4 13a8 8 0 1 0 2.2-5.5"/><path d="M4 4v5h5"/><path d="M12 8v5l3 2"/></svg>
        Histórico
        <span id="badge-historico" class="badge" hidden>0</span>
    </button>
    <button type="button" data-aba="enviar">
        <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M4 12h10"/><path d="m12 6 6 6-6 6"/><path d="M18 5v14"/></svg>
        Enviar
        <span id="badge-enviar" class="badge" hidden>0</span>
    </button>
</nav>

<div id="toast" class="toast" hidden role="status"></div>

<script>
    window.PalpitesBoot = @json($boot);
</script>
<script src="{{ asset('js/palpites-gerador.js') }}?v=4"></script>
<script src="{{ asset('js/palpites-imagem.js') }}?v=3"></script>
<script src="{{ asset('js/palpites.js') }}?v=6"></script>
@endsection
