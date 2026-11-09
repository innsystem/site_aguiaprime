(function () {
    'use strict';

    const boot = window.PalpitesBoot;
    const gerador = window.PalpitesGerador;
    if (!boot || !gerador) return;

    const abas = ['inicio', 'gerador', 'manual', 'historico', 'enviar'];
    const titulos = {
        inicio: 'Bem-vindo!',
        gerador: 'Gerador de palpites',
        manual: 'Palpite manual',
        historico: 'Histórico',
        enviar: 'Enviar palpites',
    };

    const estado = {
        config: null,
        itens: [],
        updatedAt: null,
        selecionados: new Set(),
        expirado: false,
        modalidade: 'milhar',
        resultado: null,
        resultadoNoHistorico: false,
        pendingHash: null,
        previewSuja: false,
        assinatura: '',
    };

    const imagem = {
        formato: 'quadrado',
        blob: null,
        url: null,
        nomeArquivo: '',
        pedido: 0,
        gerando: false,
        avisoExtra: '',
    };

    let toastTimer = null;
    let feedbackTimer = null;
    let salvoTimer = null;

    const $ = function (id) { return document.getElementById(id); };

    function lerJson(chave) {
        try {
            const bruto = localStorage.getItem(chave);
            if (!bruto) return null;
            return JSON.parse(bruto);
        } catch (erro) {
            return null;
        }
    }

    function normalizarTelefone(valor) {
        let digitos = String(valor || '').replace(/\D/g, '');
        if (digitos.length > 20) return null;
        if (digitos.length === 10 || digitos.length === 11) digitos = '55' + digitos;
        if ((digitos.length === 12 || digitos.length === 13) && digitos.indexOf('55') === 0) return digitos;
        return null;
    }

    function mascaraTelefone(digitos) {
        const resto = digitos.slice(2);
        const ddd = resto.slice(0, 2);
        const numero = resto.slice(2);
        if (numero.length === 9) return '(' + ddd + ') ' + numero.slice(0, 5) + '-' + numero.slice(5);
        if (numero.length === 8) return '(' + ddd + ') ' + numero.slice(0, 4) + '-' + numero.slice(4);
        return digitos;
    }

    function configUtilizavel(config) {
        if (!config || typeof config.nome !== 'string') return false;
        if (config.nome.trim().length < 2 || config.nome.trim().length > 80) return false;
        return normalizarTelefone(config.representante || '') !== null;
    }

    function itemValido(item) {
        return item
            && typeof item.id === 'string'
            && typeof item.modalidade === 'string'
            && typeof item.linha === 'string'
            && typeof item.bichos === 'string'
            && typeof item.createdAt === 'number';
    }

    function diaLocal(timestamp) {
        const data = new Date(timestamp);
        if (Number.isNaN(data.getTime())) return '';
        const mes = String(data.getMonth() + 1).padStart(2, '0');
        const dia = String(data.getDate()).padStart(2, '0');
        return data.getFullYear() + '-' + mes + '-' + dia;
    }

    function nomeModalidade(id) {
        const encontrada = boot.modalidades.find(function (item) { return item.id === id; });
        return encontrada ? encontrada.nome : id;
    }

    function modalidadeAtual() {
        return boot.modalidades.find(function (item) { return item.id === estado.modalidade; }) || boot.modalidades[0];
    }

    function carregar() {
        const config = lerJson(boot.storageConfig);
        const historico = lerJson(boot.storageHistorico);
        const tempos = [];
        if (config && typeof config.updatedAt === 'number') tempos.push(config.updatedAt);
        if (historico && typeof historico.updatedAt === 'number') tempos.push(historico.updatedAt);
        const recente = tempos.length ? Math.max.apply(null, tempos) : null;
        const ttl = boot.ttlDias * 24 * 60 * 60 * 1000;

        if (recente && Date.now() - recente > ttl) {
            localStorage.removeItem(boot.storageConfig);
            localStorage.removeItem(boot.storageHistorico);
            estado.expirado = true;
            return;
        }

        estado.config = configUtilizavel(config) ? config : null;
        estado.itens = historico && Array.isArray(historico.itens)
            ? historico.itens.filter(itemValido).slice(0, boot.limiteHistorico)
            : [];
        estado.updatedAt = recente;
    }

    function gravarConfig(renovarHistorico) {
        localStorage.setItem(boot.storageConfig, JSON.stringify(estado.config));
        if (renovarHistorico) {
            localStorage.setItem(boot.storageHistorico, JSON.stringify({
                updatedAt: estado.config.updatedAt,
                itens: estado.itens,
            }));
            estado.updatedAt = estado.config.updatedAt;
        }
    }

    function gravarHistorico(renovar) {
        const agora = Date.now();
        if (renovar) {
            estado.updatedAt = agora;
            if (estado.config) {
                estado.config.updatedAt = agora;
                localStorage.setItem(boot.storageConfig, JSON.stringify(estado.config));
            }
        }
        const updatedAt = estado.updatedAt || (estado.config && estado.config.updatedAt) || agora;
        localStorage.setItem(boot.storageHistorico, JSON.stringify({
            updatedAt: updatedAt,
            itens: estado.itens.slice(0, boot.limiteHistorico),
        }));
    }

    function adicionarItem(parcial, origem) {
        const antesSelecao = assinaturaSelecao();
        const item = {
            id: parcial.id,
            modalidade: parcial.modalidade,
            linha: parcial.linha,
            bichos: parcial.bichos,
            createdAt: parcial.createdAt,
            origem: origem,
        };
        estado.itens.unshift(item);
        if (estado.itens.length > boot.limiteHistorico) {
            estado.itens.slice(boot.limiteHistorico).forEach(function (antigo) {
                estado.selecionados.delete(antigo.id);
            });
            estado.itens = estado.itens.slice(0, boot.limiteHistorico);
        }
        estado.selecionados.add(item.id);
        gravarHistorico(true);
        atualizarBadges();
        atualizarContador();
        marcarMudancaDeSelecao(antesSelecao);
    }

    function assinaturaSelecao() {
        return selecionados().map(function (item) { return item.id; }).join(',');
    }

    function selecionados() {
        return estado.itens
            .filter(function (item) { return estado.selecionados.has(item.id); })
            .sort(function (a, b) { return a.createdAt - b.createdAt; });
    }

    function mostrarErro(id, mensagem) {
        const el = $(id);
        el.textContent = mensagem || '';
        el.hidden = !mensagem;
    }

    function validarFormulario() {
        const nome = $('nome').value.trim();
        const link = $('link').value.trim();
        const telefone = normalizarTelefone($('whatsapp').value);
        let foco = null;

        if (nome.length < 2) {
            mostrarErro('erro-nome', 'Informe o nome da banca (mínimo de 2 caracteres).');
            foco = foco || $('nome');
        } else {
            mostrarErro('erro-nome', '');
        }

        if (link.length > 200) {
            mostrarErro('erro-link', 'O link ficou longo demais.');
            foco = foco || $('link');
        } else {
            mostrarErro('erro-link', '');
        }

        if (!telefone) {
            mostrarErro('erro-whatsapp', 'Informe um WhatsApp válido com DDD.');
            foco = foco || $('whatsapp');
        } else {
            mostrarErro('erro-whatsapp', '');
        }

        if (foco) {
            foco.focus();
            return null;
        }

        return { nome: nome, link: link, representante: telefone };
    }

    function preencherFormulario() {
        if (!estado.config) return;
        $('nome').value = estado.config.nome || '';
        $('link').value = estado.config.link || '';
        const telefone = normalizarTelefone(estado.config.representante || '');
        $('whatsapp').value = telefone ? mascaraTelefone(telefone) : (estado.config.representante || '');
        atualizarBotaoGerar();
    }

    function atualizarBotaoGerar() {
        $('ir-gerar').disabled = !configUtilizavel(estado.config);
    }

    function atualizarContador() {
        const hoje = diaLocal(Date.now());
        const total = estado.itens.filter(function (item) {
            return diaLocal(item.createdAt) === hoje;
        }).length;
        $('total-hoje').textContent = String(total);
    }

    function atualizarBadges() {
        const historico = $('badge-historico');
        const enviar = $('badge-enviar');
        historico.textContent = String(estado.itens.length);
        historico.hidden = estado.itens.length === 0;
        enviar.textContent = String(estado.selecionados.size);
        enviar.hidden = estado.selecionados.size === 0;
    }

    function toast(mensagem, acao) {
        const el = $('toast');
        el.replaceChildren();
        const texto = document.createElement('span');
        texto.textContent = mensagem;
        el.appendChild(texto);
        if (acao) {
            const botao = document.createElement('button');
            botao.type = 'button';
            botao.textContent = acao.rotulo;
            botao.addEventListener('click', function () {
                el.hidden = true;
                acao.aoClicar();
            });
            el.appendChild(botao);
        }
        el.hidden = false;
        clearTimeout(toastTimer);
        toastTimer = setTimeout(function () { el.hidden = true; }, 2500);
    }

    function feedbackManual(mensagem, erro) {
        const el = $('feedback-manual');
        el.textContent = mensagem;
        el.hidden = false;
        el.style.color = erro ? 'var(--danger)' : '';
        clearTimeout(feedbackTimer);
        feedbackTimer = setTimeout(function () { el.hidden = true; }, 2500);
    }

    function mostrarAba(id, opcoes) {
        opcoes = opcoes || {};
        document.querySelectorAll('[data-painel]').forEach(function (painel) {
            painel.hidden = painel.getAttribute('data-painel') !== id;
        });
        document.querySelectorAll('.nav [data-aba]').forEach(function (botao) {
            const ativo = botao.getAttribute('data-aba') === id;
            botao.classList.toggle('is-active', ativo);
            if (ativo) botao.setAttribute('aria-current', 'page');
            else botao.removeAttribute('aria-current');
        });
        $('titulo-aba').textContent = titulos[id] || titulos.inicio;
        if (opcoes.atualizarHash !== false) {
            estado.pendingHash = null;
            $('continuar').hidden = true;
            if (location.hash !== '#' + id) {
                history.replaceState(null, '', '#' + id);
            }
        }
        if (id === 'historico') renderHistorico();
        if (id === 'enviar') renderEnviar();
        if (id === 'inicio') atualizarContador();
        window.scrollTo(0, 0);
    }

    function atualizarBicho() {
        const usa = Boolean(modalidadeAtual() && modalidadeAtual().usa_bicho);
        $('campo-bicho').hidden = !usa;
        $('aviso-bicho').hidden = usa;
        if (!usa) $('bicho').value = '';
    }

    function marcarModalidade() {
        document.querySelectorAll('[data-modalidade]').forEach(function (botao) {
            const ativo = botao.getAttribute('data-modalidade') === estado.modalidade;
            botao.classList.toggle('is-active', ativo);
            botao.setAttribute('aria-pressed', ativo ? 'true' : 'false');
        });
    }

    function renderResultado() {
        const caixa = $('resultado');
        if (!estado.resultado) {
            caixa.hidden = true;
            return;
        }
        $('resultado-mod').textContent = nomeModalidade(estado.resultado.modalidade);
        $('resultado-linha').textContent = estado.resultado.linha;
        $('resultado-bichos').textContent = estado.resultado.bichos;
        $('adicionar-resultado').disabled = estado.resultadoNoHistorico;
        caixa.hidden = false;
        const nav = document.querySelector('.nav');
        const folga = (nav ? nav.offsetHeight : 72) + 16;
        const fundo = caixa.getBoundingClientRect().bottom;
        const limite = window.innerHeight - folga;
        if (fundo > limite) {
            window.scrollTo({ top: window.scrollY + (fundo - limite), behavior: 'auto' });
        }
    }

    function sortear() {
        const atual = modalidadeAtual();
        const grupo = atual && atual.usa_bicho && $('bicho').value !== '' ? Number($('bicho').value) : null;
        try {
            estado.resultado = gerador.gerar(estado.modalidade, grupo);
            estado.resultadoNoHistorico = false;
        } catch (erro) {
            toast('Não foi possível gerar o palpite.');
            return;
        }
        renderResultado();
    }

    function dataCurta(timestamp) {
        const data = new Date(timestamp);
        const dia = String(data.getDate()).padStart(2, '0');
        const mes = String(data.getMonth() + 1).padStart(2, '0');
        return dia + '/' + mes;
    }

    function renderHistorico() {
        const vazio = estado.itens.length === 0;
        $('historico-vazio').hidden = !vazio;
        $('historico-cheio').hidden = vazio;
        if (vazio) return;

        const todos = estado.selecionados.size === estado.itens.length && estado.itens.length > 0;
        $('alternar-todos').textContent = todos ? 'Desmarcar todos' : 'Selecionar todos';
        $('ir-enviar').textContent = 'Enviar (' + estado.selecionados.size + ') selecionados';
        $('ir-enviar').disabled = estado.selecionados.size === 0;

        const lista = $('lista-historico');
        lista.replaceChildren();
        estado.itens.forEach(function (item) {
            const marcado = estado.selecionados.has(item.id);
            const linha = document.createElement('div');
            linha.className = 'item' + (marcado ? ' is-on' : '');

            const marcar = document.createElement('button');
            marcar.type = 'button';
            marcar.className = 'marca-sel';
            marcar.setAttribute('aria-label', 'Selecionar palpite');
            marcar.textContent = marcado ? '✓' : '';
            marcar.addEventListener('click', function () { alternarSelecao(item.id); });

            const corpo = document.createElement('button');
            corpo.type = 'button';
            corpo.className = 'item-corpo';
            const forte = document.createElement('strong');
            forte.textContent = item.linha;
            const detalhe = document.createElement('span');
            detalhe.textContent = nomeModalidade(item.modalidade) + ' · ' + item.bichos + ' · ' + dataCurta(item.createdAt);
            corpo.appendChild(forte);
            corpo.appendChild(detalhe);
            corpo.addEventListener('click', function () { alternarSelecao(item.id); });

            const excluir = document.createElement('button');
            excluir.type = 'button';
            excluir.className = 'excluir';
            excluir.setAttribute('aria-label', 'Excluir palpite');
            excluir.textContent = '✕';
            excluir.addEventListener('click', function () { excluirItem(item.id); });

            linha.appendChild(marcar);
            linha.appendChild(corpo);
            linha.appendChild(excluir);
            lista.appendChild(linha);
        });
    }

    function alternarSelecao(id) {
        const antes = assinaturaSelecao();
        if (estado.selecionados.has(id)) estado.selecionados.delete(id);
        else estado.selecionados.add(id);
        estado.previewSuja = false;
        atualizarBadges();
        renderHistorico();
        marcarMudancaDeSelecao(antes);
    }

    function excluirItem(id) {
        const antes = assinaturaSelecao();
        estado.itens = estado.itens.filter(function (item) { return item.id !== id; });
        estado.selecionados.delete(id);
        gravarHistorico(false);
        estado.previewSuja = false;
        atualizarBadges();
        atualizarContador();
        renderHistorico();
        marcarMudancaDeSelecao(antes);
    }

    function montarMensagem(itens) {
        const nome = estado.config && estado.config.nome.trim()
            ? estado.config.nome.trim()
            : boot.tituloPadrao;
        const data = new Date().toLocaleDateString('pt-BR');
        const linhas = itens.map(function (item) {
            return '• ' + nomeModalidade(item.modalidade) + ': ' + item.linha + ' (' + item.bichos + ')';
        });
        return '🎲 *Jogo para registro* — ' + nome + '\n📅 ' + data + '\n\n' + linhas.join('\n') + '\n\nAguardo confirmação!';
    }

    function renderEnviar() {
        const itens = selecionados();
        const vazio = itens.length === 0;
        $('enviar-vazio').hidden = !vazio;
        $('enviar-cheio').hidden = vazio;
        $('selecionar-tudo').textContent = 'Selecionar todo o histórico (' + estado.itens.length + ')';
        $('selecionar-tudo').disabled = estado.itens.length === 0;
        $('aviso-telefone').hidden = true;
        if (vazio) return;

        $('titulo-mensagem').textContent = 'Vai na mensagem (' + itens.length + ')';
        const lista = $('lista-envio');
        lista.replaceChildren();
        itens.forEach(function (item) {
            const li = document.createElement('li');
            const linha = document.createElement('span');
            const detalhe = document.createElement('span');
            linha.textContent = item.linha;
            detalhe.textContent = nomeModalidade(item.modalidade) + ' · ' + item.bichos;
            li.appendChild(linha);
            li.appendChild(detalhe);
            lista.appendChild(li);
        });

        const assinatura = itens.map(function (item) { return item.id; }).join(',');
        if (assinatura !== estado.assinatura) {
            estado.assinatura = assinatura;
            estado.previewSuja = false;
        }
        if (!estado.previewSuja) {
            $('previa').value = montarMensagem(itens);
        }
        atualizarAvisoImagem();
    }

    function encaminhar() {
        const telefone = estado.config ? normalizarTelefone(estado.config.representante) : null;
        if (!telefone) {
            $('aviso-telefone').hidden = false;
            return;
        }
        const url = 'https://api.whatsapp.com/send/?phone=' + telefone
            + '&text=' + encodeURIComponent($('previa').value)
            + '&type=phone_number&app_absent=0';
        window.open(url, '_blank', 'noopener,noreferrer');
    }

    function dataImagem() {
        const data = new Date();
        const dia = String(data.getDate()).padStart(2, '0');
        const mes = String(data.getMonth() + 1).padStart(2, '0');
        const ano = String(data.getFullYear());
        return { texto: dia + '/' + mes + '/' + ano, arquivo: ano + '-' + mes + '-' + dia };
    }

    function marcarFormato() {
        const story = imagem.formato === 'story';
        $('formato-quadrado').classList.toggle('is-on', !story);
        $('formato-story').classList.toggle('is-on', story);
        $('formato-quadrado').setAttribute('aria-pressed', story ? 'false' : 'true');
        $('formato-story').setAttribute('aria-pressed', story ? 'true' : 'false');
    }

    function atualizarAvisoImagem() {
        const el = $('aviso-imagem');
        if (!el || !window.PalpitesImagem) return;
        const total = selecionados().length;
        const limite = window.PalpitesImagem.limites[imagem.formato] || 6;
        const partes = [];
        if (imagem.avisoExtra === 'formato') {
            partes.push('O tamanho mudou. Gere a imagem de novo para baixar este formato.');
        } else if (imagem.avisoExtra === 'selecao') {
            partes.push('A seleção mudou. Gere a imagem de novo.');
        }
        if (total > limite) {
            const nome = imagem.formato === 'story' ? 'O story' : 'O quadrado';
            partes.push(nome + ' mostra ' + limite + ' de ' + total + '. Os outros continuam no texto do WhatsApp.');
        }
        el.textContent = partes.join(' ');
        el.hidden = partes.length === 0;
    }

    function liberarGeracao(pedido) {
        if (pedido !== imagem.pedido) return;
        imagem.gerando = false;
        const botao = $('gerar-imagem');
        botao.disabled = selecionados().length === 0;
        botao.textContent = 'Gerar imagem';
    }

    function descartarImagem(motivo) {
        const tinha = Boolean(imagem.blob) || imagem.gerando;
        imagem.pedido += 1;
        imagem.gerando = false;
        if (imagem.url) URL.revokeObjectURL(imagem.url);
        imagem.url = null;
        imagem.blob = null;
        imagem.nomeArquivo = '';
        $('previa-imagem').hidden = true;
        $('previa-imagem-img').removeAttribute('src');
        $('baixar-imagem').hidden = true;
        $('compartilhar-imagem').hidden = true;
        $('gerar-imagem').disabled = selecionados().length === 0;
        $('gerar-imagem').textContent = 'Gerar imagem';
        imagem.avisoExtra = tinha && motivo ? motivo : '';
        atualizarAvisoImagem();
    }

    function marcarMudancaDeSelecao(antes) {
        if (assinaturaSelecao() === antes) {
            atualizarAvisoImagem();
            return;
        }
        if (imagem.blob || imagem.gerando) descartarImagem('selecao');
        else atualizarAvisoImagem();
    }

    function escolherFormato(formato) {
        const proximo = formato === 'story' ? 'story' : 'quadrado';
        if (imagem.formato === proximo) return;
        imagem.formato = proximo;
        if (window.PalpitesImagem) window.PalpitesImagem.salvarFormato(proximo);
        marcarFormato();
        if (imagem.blob || imagem.gerando) descartarImagem('formato');
        else atualizarAvisoImagem();
    }

    function podeCompartilharArquivo(arquivo) {
        try {
            return Boolean(navigator.canShare && navigator.canShare({ files: [arquivo] }));
        } catch (erro) {
            return false;
        }
    }

    function gerarImagem() {
        const desenho = window.PalpitesImagem;
        if (!desenho || imagem.gerando) return;
        const itens = selecionados();
        if (!itens.length) return;
        const formato = imagem.formato;
        const pedido = ++imagem.pedido;
        imagem.gerando = true;
        $('gerar-imagem').disabled = true;
        $('gerar-imagem').textContent = 'Gerando…';
        const data = dataImagem();
        const nome = estado.config && estado.config.nome.trim()
            ? estado.config.nome.trim()
            : boot.tituloPadrao;
        const link = estado.config && estado.config.link ? estado.config.link.trim() : '';
        const caixa = $('imagem-palpites');
        desenho.desenhar({
            formato: formato,
            nome: nome,
            marca: caixa ? caixa.getAttribute('data-marca') : 'Águia Prime Loterias',
            link: link,
            data: data.texto,
            logoUrl: caixa ? caixa.getAttribute('data-logo') : '',
            itens: itens.map(function (item) {
                return {
                    linha: item.linha,
                    detalhe: nomeModalidade(item.modalidade) + ' · ' + item.bichos,
                };
            }),
        }).then(function (resultado) {
            if (pedido !== imagem.pedido) return;
            if (imagem.url) URL.revokeObjectURL(imagem.url);
            imagem.blob = resultado.blob;
            imagem.url = URL.createObjectURL(resultado.blob);
            imagem.nomeArquivo = 'palpites-aguia-prime-' + data.arquivo + (formato === 'story' ? '-story' : '') + '.png';
            imagem.avisoExtra = '';
            $('previa-imagem-img').src = imagem.url;
            $('previa-imagem-img').alt = 'Cartaz ' + (formato === 'story' ? 'em story' : 'quadrado') + ' com ' + resultado.exibidos + ' palpites de ' + data.texto;
            $('legenda-imagem').textContent = (formato === 'story' ? 'Story' : 'Quadrado') + ' · ' + resultado.width + ' × ' + resultado.height;
            $('previa-imagem').hidden = false;
            $('baixar-imagem').hidden = false;
            let arquivo = null;
            try {
                arquivo = new File([resultado.blob], imagem.nomeArquivo, { type: 'image/png' });
            } catch (erro) {
                arquivo = null;
            }
            $('compartilhar-imagem').hidden = !arquivo || !podeCompartilharArquivo(arquivo);
            atualizarAvisoImagem();
        }).catch(function () {
            if (pedido !== imagem.pedido) return;
            toast('Não foi possível gerar a imagem neste aparelho.');
        }).then(function () {
            liberarGeracao(pedido);
        });
    }

    function baixarImagem() {
        if (!imagem.blob || !imagem.nomeArquivo) return;
        const url = URL.createObjectURL(imagem.blob);
        const link = document.createElement('a');
        link.href = url;
        link.download = imagem.nomeArquivo;
        document.body.appendChild(link);
        link.click();
        link.remove();
        setTimeout(function () { URL.revokeObjectURL(url); }, 1500);
    }

    function compartilharImagem() {
        if (!imagem.blob || !imagem.nomeArquivo || !navigator.share) return;
        let arquivo;
        try {
            arquivo = new File([imagem.blob], imagem.nomeArquivo, { type: 'image/png' });
        } catch (erro) {
            return;
        }
        if (!podeCompartilharArquivo(arquivo)) return;
        navigator.share({ files: [arquivo], title: 'Palpites do dia' }).catch(function (erro) {
            if (erro && erro.name === 'AbortError') return;
            toast('Não foi possível compartilhar. Baixe a imagem.');
        });
    }

    function definirModalidade(id) {
        estado.modalidade = id;
        estado.resultado = null;
        estado.resultadoNoHistorico = false;
        marcarModalidade();
        atualizarBicho();
        renderResultado();
    }

    function ligarEventos() {
        document.querySelectorAll('.nav [data-aba]').forEach(function (botao) {
            botao.addEventListener('click', function () {
                mostrarAba(botao.getAttribute('data-aba'));
            });
        });

        $('form-banca').addEventListener('submit', function (evento) {
            evento.preventDefault();
            const dados = validarFormulario();
            if (!dados) return;
            const agora = Date.now();
            estado.config = {
                nome: dados.nome,
                link: dados.link,
                representante: dados.representante,
                updatedAt: agora,
            };
            gravarConfig(estado.itens.length > 0 || estado.updatedAt !== null);
            $('whatsapp').value = mascaraTelefone(dados.representante);
            atualizarBotaoGerar();
            const botao = $('salvar');
            botao.textContent = 'Salvo!';
            clearTimeout(salvoTimer);
            salvoTimer = setTimeout(function () { botao.textContent = 'Salvar'; }, 2500);
            if (estado.pendingHash) $('continuar').hidden = false;
        });

        $('whatsapp').addEventListener('blur', function () {
            const telefone = normalizarTelefone($('whatsapp').value);
            if (telefone) $('whatsapp').value = mascaraTelefone(telefone);
        });

        $('ir-gerar').addEventListener('click', function () {
            if (!configUtilizavel(estado.config)) {
                validarFormulario();
                return;
            }
            mostrarAba('gerador');
        });

        $('continuar-btn').addEventListener('click', function () {
            const destino = estado.pendingHash;
            estado.pendingHash = null;
            $('continuar').hidden = true;
            if (abas.indexOf(destino) !== -1) mostrarAba(destino);
        });

        document.querySelectorAll('[data-modalidade]').forEach(function (botao) {
            botao.addEventListener('click', function () {
                definirModalidade(botao.getAttribute('data-modalidade'));
            });
        });

        $('gerar').addEventListener('click', sortear);
        $('gerar-outro').addEventListener('click', sortear);
        $('adicionar-resultado').addEventListener('click', function () {
            if (!estado.resultado || estado.resultadoNoHistorico) return;
            adicionarItem(estado.resultado, 'gerador');
            estado.resultadoNoHistorico = true;
            renderResultado();
            toast('Adicionado ao histórico.', {
                rotulo: 'Ver histórico',
                aoClicar: function () { mostrarAba('historico'); },
            });
        });

        $('milhar').addEventListener('input', function () {
            $('milhar').value = $('milhar').value.replace(/\D/g, '').slice(0, 4);
            const completo = $('milhar').value.length === 4;
            $('add-milhar').disabled = !completo;
            $('milhar-derivados').hidden = !completo;
            if (!completo) return;
            $('der-centena').textContent = $('milhar').value.slice(1);
            $('der-dezena').textContent = $('milhar').value.slice(2);
            $('der-grupo').textContent = gerador.porDezena(Number($('milhar').value.slice(2))).nome;
        });

        $('dezena').addEventListener('input', function () {
            $('dezena').value = $('dezena').value.replace(/\D/g, '').slice(0, 2);
            const completo = $('dezena').value.length === 2;
            $('add-dezena').disabled = !completo;
            $('dezena-grupo').hidden = !completo;
            if (completo) {
                $('dezena-grupo-nome').textContent = gerador.porDezena(Number($('dezena').value)).nome;
            }
        });

        $('add-milhar').addEventListener('click', function () {
            const item = gerador.manualMilhar($('milhar').value);
            if (!item) {
                feedbackManual('Verifique os números digitados.', true);
                return;
            }
            adicionarItem(item, 'manual');
            $('milhar').value = '';
            $('milhar-derivados').hidden = true;
            $('add-milhar').disabled = true;
            feedbackManual('Adicionado ao histórico!', false);
        });

        $('add-dezena').addEventListener('click', function () {
            const item = gerador.manualDezena($('dezena').value);
            if (!item) {
                feedbackManual('Verifique os números digitados.', true);
                return;
            }
            adicionarItem(item, 'manual');
            $('dezena').value = '';
            $('dezena-grupo').hidden = true;
            $('add-dezena').disabled = true;
            feedbackManual('Adicionado ao histórico!', false);
        });

        $('alternar-todos').addEventListener('click', function () {
            const antes = assinaturaSelecao();
            if (estado.selecionados.size === estado.itens.length) estado.selecionados.clear();
            else estado.itens.forEach(function (item) { estado.selecionados.add(item.id); });
            estado.previewSuja = false;
            atualizarBadges();
            renderHistorico();
            marcarMudancaDeSelecao(antes);
        });

        $('limpar').addEventListener('click', function () {
            $('confirmar-limpar').hidden = false;
        });
        $('limpar-cancelar').addEventListener('click', function () {
            $('confirmar-limpar').hidden = true;
        });
        $('limpar-confirmar').addEventListener('click', function () {
            const antes = assinaturaSelecao();
            estado.itens = [];
            estado.selecionados.clear();
            estado.previewSuja = false;
            gravarHistorico(false);
            $('confirmar-limpar').hidden = true;
            atualizarBadges();
            atualizarContador();
            renderHistorico();
            marcarMudancaDeSelecao(antes);
        });

        $('ir-enviar').addEventListener('click', function () {
            mostrarAba('enviar');
        });

        $('selecionar-tudo').addEventListener('click', function () {
            const antes = assinaturaSelecao();
            estado.itens.forEach(function (item) { estado.selecionados.add(item.id); });
            estado.previewSuja = false;
            atualizarBadges();
            marcarMudancaDeSelecao(antes);
            mostrarAba('enviar');
        });

        $('previa').addEventListener('input', function () {
            estado.previewSuja = true;
        });

        $('encaminhar').addEventListener('click', encaminhar);
        $('formato-quadrado').addEventListener('click', function () { escolherFormato('quadrado'); });
        $('formato-story').addEventListener('click', function () { escolherFormato('story'); });
        $('gerar-imagem').addEventListener('click', gerarImagem);
        $('baixar-imagem').addEventListener('click', baixarImagem);
        $('compartilhar-imagem').addEventListener('click', compartilharImagem);
        $('ir-inicio-telefone').addEventListener('click', function () {
            mostrarAba('inicio');
            $('whatsapp').focus();
        });
    }

    function abaInicial() {
        const hash = (location.hash || '').replace('#', '');
        if (abas.indexOf(hash) !== -1) {
            if (!configUtilizavel(estado.config) && hash !== 'inicio') {
                estado.pendingHash = hash;
                mostrarAba('inicio', { atualizarHash: false });
                return;
            }
            mostrarAba(hash, { atualizarHash: false });
            return;
        }
        mostrarAba(configUtilizavel(estado.config) ? 'gerador' : 'inicio');
    }

    carregar();
    if (window.PalpitesImagem) imagem.formato = window.PalpitesImagem.formatoSalvo();
    marcarFormato();
    $('aviso-expirado').hidden = !estado.expirado;
    preencherFormulario();
    atualizarBotaoGerar();
    atualizarContador();
    atualizarBadges();
    atualizarBicho();
    ligarEventos();
    abaInicial();

    window.addEventListener('hashchange', function () {
        const hash = (location.hash || '').replace('#', '');
        if (abas.indexOf(hash) === -1) return;
        if (!configUtilizavel(estado.config) && hash !== 'inicio') {
            estado.pendingHash = hash;
            mostrarAba('inicio', { atualizarHash: false });
            return;
        }
        mostrarAba(hash, { atualizarHash: false });
    });
}());
