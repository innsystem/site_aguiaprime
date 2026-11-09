# Planejamento — Imagem dos palpites

Complementa `docs/planejamento-gerador-palpites.md`. A ferramenta de sorteio, histórico e texto do WhatsApp já existe. Esta etapa liga o botão **Imagem em breve** da aba Enviar.

A imagem é uma peça para divulgar. O texto que abre o WhatsApp do representante continua separado: ele registra o jogo. A imagem é o cartaz que a pessoa salva e manda como foto, no status ou num grupo.

Nada disso passa pelo servidor. O desenho acontece no navegador, no canvas, a partir dos palpites já selecionados. Não há upload, fila nem banco.

## 1. O que entra na imagem

Só os palpites **selecionados** no histórico, na mesma ordem da mensagem: do mais antigo para o mais recente.

Cada item mostra:

- a linha em destaque (`2458`, `15 · Jacaré`, `15 → 03`)
- embaixo, menor: `Milhar · Jacaré`

Também entram, quando existirem:

- nome da banca
- data local `dd/mm/aaaa`
- link do app, se tiver sido preenchido no Início
- marca Águia Prime e o logo já usado no site

Não entram:

- o telefone do representante
- o texto editado na prévia do WhatsApp
- palpites que não estão marcados
- a frase “Jogo para registro” (essa fica só na mensagem de texto)

Se a pessoa mudar a seleção depois de gerar a imagem, a prévia some e o download espera uma nova geração. Editar a prévia do texto não altera o cartaz.

## 2. Formato, à escolha de quem usa

Dois tamanhos. O padrão é o quadrado.

| Opção | Proporção | Pixels | Uso |
| --- | --- | --- | --- |
| Quadrado | 1:1 | 1080 × 1080 | conversa, grupo, feed. É o padrão |
| Story | 9:16 | 1080 × 1920 | status do WhatsApp e stories |

A escolha fica na aba Enviar, acima do botão de gerar: dois botões, **Quadrado** e **Story**. O último usado é lembrado no navegador, na chave `aguiaprime:palpites:formato`, sem prazo próprio. Se não houver nada salvo, abre em Quadrado.

O arquivo exportado tem sempre esses pixels, independente da tela do celular. WhatsApp comprime a foto; 1080 px chega nítido sem ficar pesado. Formato PNG.

Nome do arquivo:

- `palpites-aguia-prime-2026-11-09.png` no quadrado
- `palpites-aguia-prime-2026-11-09-story.png` no story

## 3. Como o cartaz é desenhado

Fundo azul-noite `#070b14`, com um brilho dourado suave no topo. Filete dourado `#e0b84a` na borda de cima. Texto claro `#f4f1e8`. Títulos na Cinzel, já carregada na página. O miolo da lista fica num cartão branco, para o número ler de longe no celular.

### Quadrado 1:1

```text
┌──────────────────────── 1080 × 1080 ────────────────────────┐
│ ▔▔▔▔▔▔▔▔▔▔▔▔▔ filete dourado ▔▔▔▔▔▔▔▔▔▔▔▔▔▔▔▔▔▔▔▔▔▔▔▔▔▔▔ │
│                         [logo]                               │
│                      ÁGUIA PRIME                             │
│                   PALPITES DO DIA                           │
│                      09/11/2026                             │
│  ┌──────────────────────────────────────────────────────┐  │
│  │  2458                                                │  │
│  │  Milhar · Jacaré                                     │  │
│  │  ──────────────────────────────────────────────────  │  │
│  │  00                                                  │  │
│  │  Dezena · Vaca                                       │  │
│  │  ... até 6 palpites                                  │  │
│  └──────────────────────────────────────────────────────┘  │
│              app.aguiaprime.test                            │
│                   BOA SORTE                                 │
│              Águia Prime Loterias                           │
└─────────────────────────────────────────────────────────────┘
```

Margem interna de 72 px. O cartão ocupa o meio. Seis palpites cabem com a linha grande e a legenda legível. Com 1 a 3 itens, o tipo cresce. Com 4 a 6, o tipo padrão. Não aperta além disso.

### Story 9:16

O mesmo cartaz, alongado. O que muda é o respiro e a quantidade.

```text
┌────────────── 1080 × 1920 ──────────────┐
│                                          │
│         zona livre ~220 px               │
│         (status não cobre a marca)       │
│                 [logo]                   │
│              ÁGUIA PRIME                 │
│           PALPITES DO DIA                │
│              09/11/2026                  │
│  ┌────────────────────────────────────┐  │
│  │  palpites, até 12                   │  │
│  │  linha grande                       │  │
│  │  modalidade · bichos                │  │
│  └────────────────────────────────────┘  │
│           link do app                    │
│             BOA SORTE                    │
│                                          │
│         zona livre ~260 px               │
│         (nome e data não colam           │
│          na barra do status)             │
└──────────────────────────────────────────┘
```

Status do WhatsApp e stories cobrem topo e rodapé. Logo, título e a última linha útil ficam na faixa central, entre 220 px do topo e 260 px da base. O link e o “Boa sorte” também ficam nessa faixa, não na zona que some.

### Quando a seleção não cabe

| Formato | Máximo no cartaz |
| --- | --- |
| Quadrado | 6 |
| Story | 12 |

Se a pessoa marcou mais do que cabe, a imagem leva os primeiros da ordem cronológica e o rodapé do cartão ganha uma linha: `+ 3 palpites na mensagem`. A tela avisa antes de gerar: “O quadrado mostra 6 de 9. Os outros continuam no texto do WhatsApp.” Não gera várias páginas.

## 4. Como a pessoa usa

Na aba Enviar, com pelo menos um palpite marcado, a ordem fica:

1. Lista **Vai na mensagem**
2. Prévia editável do texto (já existe)
3. **Encaminhar jogo para o representante** (já existe, abre o WhatsApp com o texto)
4. Escolha **Quadrado** ou **Story**
5. **Gerar imagem**
6. Prévia da imagem na própria tela, na largura da coluna
7. **Baixar imagem**
8. **Compartilhar imagem**, só se o celular oferecer o compartilhamento de arquivo

Gerar imagem desenha e mostra. Baixar grava o PNG. São ações separadas: baixar não gera de novo, compartilhar não substitui o download.

Sem seleção, os botões de imagem ficam desabilitados, junto com o texto que já existe pedindo para marcar no histórico.

### Divulgar no WhatsApp

Dois caminhos, e os dois convivem:

- **Registro.** O botão verde manda o texto para o representante. A imagem não entra nesse link. WhatsApp não aceita anexo por URL.
- **Divulgação.** A pessoa baixa o PNG e anexa na conversa, no grupo ou no status. No celular, **Compartilhar imagem** abre a folha do sistema; se o WhatsApp aparecer lá, ela escolhe o contato ou o status e a foto já vai junto. Se o navegador não deixar compartilhar arquivo, o botão some e fica só o download. É o caso comum no computador.

A prévia na tela serve para conferir antes de salvar. Não é a imagem final em tamanho cheio: o arquivo baixado é que tem 1080 px.

## 5. Desenho técnico

Arquivo novo: `public/js/palpites-imagem.js`. Ele só lê o catálogo visual e os itens que a tela já tem. Não chama `POST /palpites/gerar`.

Passos:

1. Esperar `document.fonts.ready` para Cinzel e Outfit. Se a fonte falhar, usa a sans do sistema. O cartaz ainda sai.
2. Carregar `img/logo.png` do próprio site. Se falhar, o título em texto ocupa o lugar.
3. Criar um canvas no tamanho escolhido, desenhar e exportar com `toBlob('image/png')`.
4. Mostrar a prévia com `URL.createObjectURL`. Ao gerar de novo ou trocar o formato, revogar a URL anterior.
5. Baixar com um link temporário e o nome de arquivo da seção 2.
6. Compartilhar com `navigator.share` só quando `canShare({ files })` for verdadeiro. O arquivo é um `File` PNG. Se a pessoa cancelar a folha, não é erro.

Trocar Quadrado/Story não baixa sozinho. Atualiza a escolha e, se já havia prévia, pede para gerar de novo com o outro tamanho. Assim ninguém salva um story achando que é quadrado.

### Segurança

O canvas não usa HTML do usuário. Nome, link e linhas entram por `fillText`. Texto longo demais é cortado com reticências: nome da banca em uma linha, link em uma linha, linha do palpite em uma linha. Isso evita estouro e evita desenhar conteúdo arbitrário.

Não há endpoint novo. Mil cliques em gerar imagem gastam o celular de quem clicou, não o Laravel.

## 6. O que muda na tela atual

- `resources/views/site/palpites/app.blade.php`: no lugar do botão desabilitado, o seletor de formato, **Gerar imagem**, a área da prévia, **Baixar imagem** e **Compartilhar imagem**.
- `public/css/palpites.css`: o seletor no mesmo dourado da ferramenta e a prévia com cantos arredondados, sem estourar a coluna.
- `public/js/palpites.js`: liga os botões e descarta a prévia quando a seleção muda.
- `public/js/palpites-imagem.js`: o desenho.

Sem migration, sem rota nova, sem mudança no texto do WhatsApp.

## 7. Conferência

No celular e no computador, com a ferramenta já aberta:

- Sem seleção, gerar e baixar ficam desligados.
- Um palpite no quadrado: número grande, banca, data, logo, e o arquivo baixa com 1080 × 1080.
- O mesmo jogo em story: mais alto, marca longe das bordas, arquivo 1080 × 1920.
- Sete palpites no quadrado: o cartaz mostra 6 e avisa o que ficou de fora. O texto do WhatsApp continua com os 7.
- Link do app vazio: o cartaz não deixa um buraco, só não desenha a linha.
- Trocar a seleção apaga a prévia.
- Baixar não abre o WhatsApp. Encaminhar o texto não baixa a imagem.
- No celular compatível, compartilhar oferece o envio da foto. No desktop, esse botão não aparece.

Não há teste de pixel no PHPUnit. A conferência é visual, nos dois formatos, com 1 item e com a lista no limite.
