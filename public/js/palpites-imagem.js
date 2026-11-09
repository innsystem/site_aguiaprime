(function () {
    'use strict';

    var CHAVE = 'aguiaprime:palpites:formato';
    var LIMITES = { quadrado: 6, story: 12 };
    var TAMANHOS = {
        quadrado: { w: 1080, h: 1080 },
        story: { w: 1080, h: 1920 },
    };

    function formatoSalvo() {
        try {
            return localStorage.getItem(CHAVE) === 'story' ? 'story' : 'quadrado';
        } catch (erro) {
            return 'quadrado';
        }
    }

    function salvarFormato(formato) {
        try {
            localStorage.setItem(CHAVE, formato === 'story' ? 'story' : 'quadrado');
        } catch (erro) {
            /* o cartaz segue sem lembrar a escolha */
        }
    }

    function umaLinha(valor, maximo) {
        return String(valor || '')
            .replace(/[\u0000-\u001F\u007F]+/g, ' ')
            .replace(/\s+/g, ' ')
            .trim()
            .slice(0, maximo);
    }

    function cortar(ctx, texto, largura) {
        if (!texto || ctx.measureText(texto).width <= largura) return texto;
        var reticencias = '…';
        while (texto.length > 1 && ctx.measureText(texto + reticencias).width > largura) {
            texto = texto.slice(0, -1);
        }
        return texto + reticencias;
    }

    function limparLink(valor) {
        return umaLinha(valor, 200).replace(/^https?:\/\//i, '').replace(/\/+$/, '');
    }

    function fontesProntas() {
        if (!document.fonts || !document.fonts.load) return Promise.resolve();
        return Promise.all([
            document.fonts.load('700 42px Cinzel'),
            document.fonts.load('800 64px Outfit'),
        ]).catch(function () {
            return undefined;
        }).then(function () {
            return document.fonts.ready;
        }).catch(function () {
            return undefined;
        });
    }

    function carregarLogo(url) {
        return new Promise(function (resolve) {
            if (!url) {
                resolve(null);
                return;
            }
            var img = new Image();
            img.onload = function () { resolve(img.naturalWidth ? img : null); };
            img.onerror = function () { resolve(null); };
            img.src = url;
        });
    }

    function roundRect(ctx, x, y, w, h, r) {
        var raio = Math.min(r, w / 2, h / 2);
        ctx.beginPath();
        ctx.moveTo(x + raio, y);
        ctx.arcTo(x + w, y, x + w, y + h, raio);
        ctx.arcTo(x + w, y + h, x, y + h, raio);
        ctx.arcTo(x, y + h, x, y, raio);
        ctx.arcTo(x, y, x + w, y, raio);
        ctx.closePath();
    }

    function desenharLogo(ctx, img, x, y, size) {
        var iw = img.naturalWidth || img.width;
        var ih = img.naturalHeight || img.height;
        var escala = Math.min(size / iw, size / ih);
        var dw = iw * escala;
        var dh = ih * escala;
        ctx.drawImage(img, x + (size - dw) / 2, y + (size - dh) / 2, dw, dh);
    }

    function medidas(area, quantidade, story) {
        var linha = quantidade <= 3 ? (story ? 120 : 108) : (quantidade <= 6 ? (story ? 64 : 52) : 42);
        var detalhe = Math.round(linha * 0.42);
        var folga = Math.round(linha * 0.34);
        var bloco = linha + 8 + detalhe + folga;
        var total = quantidade * bloco;
        while (total > area && linha > 28) {
            linha -= 2;
            detalhe = Math.max(16, Math.round(linha * 0.42));
            folga = Math.max(8, Math.round(linha * 0.28));
            bloco = linha + 8 + detalhe + folga;
            total = quantidade * bloco;
        }
        return { linha: linha, detalhe: detalhe, bloco: bloco, total: total };
    }

    function pintar(ctx, poster) {
        var w = poster.w;
        var h = poster.h;
        var story = poster.formato === 'story';

        ctx.fillStyle = '#070b14';
        ctx.fillRect(0, 0, w, h);

        var brilho = ctx.createRadialGradient(w / 2, 0, 20, w / 2, 0, w * 0.72);
        brilho.addColorStop(0, 'rgba(224, 184, 74, 0.30)');
        brilho.addColorStop(1, 'rgba(224, 184, 74, 0)');
        ctx.fillStyle = brilho;
        ctx.fillRect(0, 0, w, Math.round(h * 0.42));

        ctx.fillStyle = '#e0b84a';
        ctx.fillRect(0, 0, w, story ? 0 : 16);

        var top = story ? 220 : 72;
        var bottom = story ? h - 260 : h - 64;
        var margem = 72;
        var miolo = w - margem * 2;
        var y = top;

        ctx.textAlign = 'center';
        ctx.textBaseline = 'top';

        if (story) {
            ctx.fillRect((w - 180) / 2, y, 180, 6);
            y += 28;
        }

        if (poster.logo) {
            var logo = story ? 112 : 96;
            desenharLogo(ctx, poster.logo, (w - logo) / 2, y, logo);
            y += logo + 22;
        }

        ctx.fillStyle = '#e0b84a';
        ctx.font = '700 40px Cinzel, Georgia, serif';
        ctx.fillText('ÁGUIA PRIME', w / 2, y);
        y += 52;

        ctx.fillStyle = '#f4f1e8';
        ctx.font = '700 32px Cinzel, Georgia, serif';
        ctx.fillText('PALPITES DO DIA', w / 2, y);
        y += 46;

        ctx.fillStyle = '#f3d27a';
        ctx.font = '700 30px Outfit, sans-serif';
        ctx.fillText(cortar(ctx, umaLinha(poster.nome, 80), miolo), w / 2, y);
        y += 42;

        ctx.fillStyle = 'rgba(244, 241, 232, 0.76)';
        ctx.font = '500 26px Outfit, sans-serif';
        ctx.fillText(umaLinha(poster.data, 20), w / 2, y);
        y += 46;

        var link = limparLink(poster.link);
        var rodape = 20 + (link ? 38 : 0) + 50 + 36;
        var cardTop = y;
        var cardH = Math.max(160, bottom - rodape - cardTop);
        var sobraAltura = poster.sobra > 0 ? 52 : 0;
        var padding = 36;
        var area = cardH - padding * 2 - sobraAltura;
        var n = poster.itens.length;
        var caixa = medidas(Math.max(area, 80), n, story);
        var inicio = cardTop + padding + Math.max(0, (area - caixa.total) / 2);

        roundRect(ctx, margem, cardTop, miolo, cardH, 36);
        ctx.fillStyle = '#f7f4ec';
        ctx.fill();
        ctx.lineWidth = 6;
        ctx.strokeStyle = '#e0b84a';
        ctx.stroke();

        ctx.save();
        roundRect(ctx, margem + 12, cardTop + 12, miolo - 24, Math.max(40, cardH - 24 - sobraAltura), 24);
        ctx.clip();

        poster.itens.forEach(function (item, indice) {
            var iy = inicio + indice * caixa.bloco;
            ctx.fillStyle = '#141820';
            ctx.font = '800 ' + caixa.linha + 'px Outfit, sans-serif';
            ctx.fillText(cortar(ctx, umaLinha(item.linha, 80), miolo - padding * 2), w / 2, iy);
            ctx.fillStyle = '#6b5a3c';
            ctx.font = '600 ' + caixa.detalhe + 'px Outfit, sans-serif';
            ctx.fillText(cortar(ctx, umaLinha(item.detalhe, 90), miolo - padding * 2), w / 2, iy + caixa.linha + 8);
            if (indice < n - 1) {
                var linhaY = iy + caixa.bloco - 6;
                ctx.strokeStyle = 'rgba(20, 24, 32, 0.12)';
                ctx.lineWidth = 2;
                ctx.beginPath();
                ctx.moveTo(margem + padding, linhaY);
                ctx.lineTo(margem + miolo - padding, linhaY);
                ctx.stroke();
            }
        });
        ctx.restore();

        if (poster.sobra > 0) {
            var extra = '+ ' + poster.sobra + (poster.sobra === 1 ? ' palpite na mensagem' : ' palpites na mensagem');
            ctx.fillStyle = '#8a6412';
            ctx.font = '700 26px Outfit, sans-serif';
            ctx.fillText(extra, w / 2, cardTop + cardH - padding - 4);
        }

        var fy = cardTop + cardH + 22;
        if (link) {
            ctx.fillStyle = '#f3d27a';
            ctx.font = '600 26px Outfit, sans-serif';
            ctx.fillText(cortar(ctx, link, miolo), w / 2, fy);
            fy += 38;
        }
        ctx.fillStyle = '#e0b84a';
        ctx.font = '700 34px Cinzel, Georgia, serif';
        ctx.fillText('BOA SORTE', w / 2, fy);
        fy += 46;
        ctx.fillStyle = 'rgba(244, 241, 232, 0.72)';
        ctx.font = '500 24px Outfit, sans-serif';
        ctx.fillText(cortar(ctx, umaLinha(poster.marca, 80), miolo), w / 2, fy);
    }

    function desenhar(opcoes) {
        var formato = opcoes.formato === 'story' ? 'story' : 'quadrado';
        var tamanho = TAMANHOS[formato];
        var itens = Array.isArray(opcoes.itens) ? opcoes.itens.slice(0, 200) : [];
        var limite = LIMITES[formato];
        var visiveis = itens.slice(0, limite);

        return fontesProntas().then(function () {
            return carregarLogo(opcoes.logoUrl);
        }).then(function (logo) {
            var canvas = document.createElement('canvas');
            canvas.width = tamanho.w;
            canvas.height = tamanho.h;
            var ctx = canvas.getContext('2d');
            if (!ctx) throw new Error('canvas');
            pintar(ctx, {
                w: tamanho.w,
                h: tamanho.h,
                formato: formato,
                logo: logo,
                nome: opcoes.nome || '',
                marca: opcoes.marca || 'Águia Prime Loterias',
                link: opcoes.link || '',
                data: opcoes.data || '',
                itens: visiveis,
                sobra: Math.max(0, itens.length - visiveis.length),
            });
            return new Promise(function (resolve, reject) {
                canvas.toBlob(function (blob) {
                    canvas.width = 0;
                    canvas.height = 0;
                    if (!blob) {
                        reject(new Error('blob'));
                        return;
                    }
                    resolve({
                        blob: blob,
                        width: tamanho.w,
                        height: tamanho.h,
                        exibidos: visiveis.length,
                        total: itens.length,
                        formato: formato,
                    });
                }, 'image/png');
            });
        });
    }

    window.PalpitesImagem = {
        limites: LIMITES,
        formatoSalvo: formatoSalvo,
        salvarFormato: salvarFormato,
        desenhar: desenhar,
    };
}());
