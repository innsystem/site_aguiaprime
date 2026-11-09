/* Espelha app/Support/Palpites/GeradorDePalpites.php. O catálogo vem de PalpitesBoot. */
(function () {
    'use strict';

    function boot() {
        return window.PalpitesBoot || { grupos: [], modalidades: [] };
    }

    function pad(numero, tamanho) {
        return String(numero).padStart(tamanho, '0');
    }

    function aleatorio(limite) {
        return Math.floor(Math.random() * limite);
    }

    function porGrupo(numero) {
        const grupo = boot().grupos.find(function (item) {
            return Number(item.grupo) === Number(numero);
        });
        if (!grupo) {
            throw new Error('Grupo fora da faixa de 1 a 25.');
        }
        return grupo;
    }

    function porDezena(dezena) {
        const valor = Number(dezena);
        if (valor < 0 || valor > 99 || Number.isNaN(valor)) {
            throw new Error('Dezena inválida.');
        }
        if (valor === 0) {
            return porGrupo(25);
        }
        return porGrupo(Math.ceil(valor / 4));
    }

    function modalidade(id) {
        return boot().modalidades.find(function (item) {
            return item.id === id;
        }) || null;
    }

    function sample(pool, quantidade) {
        const copia = pool.slice();
        const saida = [];
        for (let i = 0; i < quantidade; i += 1) {
            const indice = aleatorio(copia.length);
            saida.push(copia.splice(indice, 1)[0]);
        }
        return saida;
    }

    function novoId() {
        return Date.now().toString(36) + '-' + Math.random().toString(36).slice(2, 8);
    }

    function embrulho(modalidadeId, linha, bichos) {
        return {
            id: novoId(),
            modalidade: modalidadeId,
            linha: linha,
            bichos: bichos,
            createdAt: Date.now(),
        };
    }

    function escolherDezena(grupo) {
        const dezenas = grupo.dezenas.map(Number);
        return dezenas[aleatorio(dezenas.length)];
    }

    function milhar(escolhido) {
        if (!escolhido) {
            const linha = pad(aleatorio(10000), 4);
            return embrulho('milhar', linha, porDezena(Number(linha.slice(-2))).nome);
        }
        const dezena = escolherDezena(escolhido);
        return embrulho('milhar', pad(aleatorio(100), 2) + pad(dezena, 2), escolhido.nome);
    }

    function centena(escolhido) {
        if (!escolhido) {
            const linha = pad(aleatorio(1000), 3);
            return embrulho('centena', linha, porDezena(Number(linha.slice(-2))).nome);
        }
        const dezena = escolherDezena(escolhido);
        return embrulho('centena', String(aleatorio(10)) + pad(dezena, 2), escolhido.nome);
    }

    function dezena(escolhido) {
        const numero = escolhido ? escolherDezena(escolhido) : aleatorio(100);
        return embrulho('dezena', pad(numero, 2), porDezena(numero).nome);
    }

    function grupo(escolhido) {
        const item = escolhido || boot().grupos[aleatorio(boot().grupos.length)];
        return embrulho('grupo', pad(item.grupo, 2) + ' · ' + item.nome, item.nome);
    }

    function combinarGrupos(id, quantidade, escolhido, sepLinha, sepBicho) {
        let itens;
        if (!escolhido) {
            itens = sample(boot().grupos, quantidade);
        } else {
            const resto = boot().grupos.filter(function (item) {
                return Number(item.grupo) !== Number(escolhido.grupo);
            });
            itens = [escolhido].concat(sample(resto, quantidade - 1));
        }
        return embrulho(
            id,
            itens.map(function (item) { return pad(item.grupo, 2); }).join(sepLinha),
            itens.map(function (item) { return item.nome; }).join(sepBicho)
        );
    }

    function combinarDezenas(id, quantidade) {
        const dezenas = sample(Array.from({ length: 100 }, function (_, indice) { return indice; }), quantidade);
        return embrulho(
            id,
            dezenas.map(function (numero) { return pad(numero, 2); }).join(' · '),
            dezenas.map(function (numero) { return porDezena(numero).nome; }).join(' + ')
        );
    }

    function gerar(modalidadeId, grupoNumero) {
        const atual = modalidade(modalidadeId);
        if (!atual) {
            throw new Error('Modalidade desconhecida.');
        }

        let escolhido = null;
        if (grupoNumero !== null && grupoNumero !== undefined && grupoNumero !== '') {
            const numero = Number(grupoNumero);
            if (numero < 1 || numero > 25) {
                throw new Error('Grupo fora da faixa de 1 a 25.');
            }
            if (atual.usa_bicho) {
                escolhido = porGrupo(numero);
            }
        }

        switch (modalidadeId) {
            case 'milhar': return milhar(escolhido);
            case 'centena': return centena(escolhido);
            case 'dezena': return dezena(escolhido);
            case 'grupo': return grupo(escolhido);
            case 'duque-grupo': return combinarGrupos(modalidadeId, 2, escolhido, ' · ', ' + ');
            case 'terno-grupo': return combinarGrupos(modalidadeId, 3, escolhido, ' · ', ' + ');
            case 'duque-dezena': return combinarDezenas(modalidadeId, 2);
            case 'terno-dezena': return combinarDezenas(modalidadeId, 3);
            case 'passe-vai': return combinarGrupos(modalidadeId, 2, escolhido, ' → ', ' → ');
            case 'passe-vai-e-vem': return combinarGrupos(modalidadeId, 2, escolhido, ' ⇄ ', ' ⇄ ');
            default: throw new Error('Modalidade desconhecida.');
        }
    }

    function manualMilhar(valor) {
        if (!/^\d{4}$/.test(valor)) return null;
        return embrulho('milhar', valor, porDezena(Number(valor.slice(2))).nome);
    }

    function manualDezena(valor) {
        if (!/^\d{2}$/.test(valor)) return null;
        return embrulho('dezena', valor, porDezena(Number(valor)).nome);
    }

    window.PalpitesGerador = {
        gerar: gerar,
        porDezena: porDezena,
        manualMilhar: manualMilhar,
        manualDezena: manualDezena,
    };
}());
