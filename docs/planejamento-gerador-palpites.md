# Planejamento — Gerador de Palpites

Referência analisada em 05/11/2026: [palpitesdasorte.portodesigner.com.br](https://palpitesdasorte.portodesigner.com.br/) (v1.3.0, app de página única feito no Lovable). Revisado e aprofundado em 09/11/2026.

Este documento descreve como recriar o fluxo desse gerador dentro do site Águia Prime já existente, como uma interface exclusiva. A landing atual permanece. Um botão **Gerar Palpites** leva para essa interface.

A geração da imagem fica reservada para uma etapa seguinte. O restante do fluxo, inclusive o texto do WhatsApp, entra nesta entrega.

## 0. Aprofundamento — o que mudou neste revisão

Antes de iniciar a implementação, o planejamento original foi revisado. Os pontos abaixo corrigem bugs herdados da referência, fecham ambiguidades e melhoram o fluxo real no celular.

| Tema | Antes | Agora |
| --- | --- | --- |
| Onde sorteia | Só no PHP (`POST` a cada clique) | Sorteio no navegador, a partir do catálogo injetado pela Blade. O PHP guarda as mesmas regras em classe testável e um `POST` opcional só para testes/API futura |
| Contador “hoje” | Referência compara só `dd/mm` (bug entre anos) | Compara data local completa `YYYY-MM-DD` |
| TTL | `savedAt` renovado até em exclusão | Relógio único `updatedAt`; só renovar em uso positivo (salvar banca ou adicionar palpite) |
| Salvar | Sem regra clara de obrigatoriedade | Nome e WhatsApp obrigatórios; link opcional |
| Após gerar | Botão “Histórico” muda de aba | **Adicionar ao histórico** permanece no Gerador, com feedback; badge sobe na aba Histórico |
| Seleção | Manual demais | Todo palpite novo entra já marcado |
| Limpar tudo | Sem confirmação | Confirmação explícita |
| Enviar | Só abre o WhatsApp | Prévia editável da mensagem + botão de abrir |
| Ordem no WhatsApp | Mais recente primeiro | Cronológica crescente (mais antigo primeiro), melhor para registrar o jogo |
| Bicho + dezenas | Select ativo, mas o filtro era ignorado | Select some ou fica desabilitado com texto quando a modalidade não usa bicho |
| Navegação | Só estado em memória | Hash `#inicio`, `#gerador`, `#manual`, `#historico`, `#enviar` |
| SEO da ferramenta | Não definido | `noindex, nofollow` em `/palpites` |
| iPhone | Não citado | `safe-area-inset-bottom` na barra inferior |
| Flood / abuso | Quase só “throttle leve” no POST | UI sem POST; API desligada por padrão em produção; limiter 20/min/IP; sem DB para palpites (§9.1) |

O restante do documento já incorpora essas decisões. Não há uma “lista paralela”: isto é o plano oficial.

## 1. O que o site de referência faz

É um aplicativo mobile-first, sem login e sem banco de dados. Tudo fica no `localStorage` do navegador, sem prazo de expiração. Há cinco abas fixas no rodapé.

| Aba | Função |
| --- | --- |
| Início | Nome da banca, link do app, WhatsApp do representante e contador do dia |
| Gerador | Escolhe modalidade e bicho opcional, sorteia e mostra o resultado |
| Manual | Digita milhar ou dezena e grava no histórico |
| Histórico | Lista, marca, desmarca, exclui e envia a seleção |
| Enviar | Monta a mensagem, abre o WhatsApp e (lá) gera uma imagem 1080×1920 |

O app de referência abre direto na aba **Gerador**. A aba Início existe, mas não é a primeira tela.

### Início (referência)

- Título: **Bem-vindo!**
- Card **Configuração da banca**. Texto: “Essas informações aparecem na imagem dos palpites. Configure uma vez só.”
- Campos: Nome da banca, Link do app, WhatsApp do representante
- Botão **Salvar** → rótulo **Salvo!** por 2,5 s
- Card **Palpites gerados hoje**
- Botão **Gerar palpite agora →**
- Rodapé de versão

Chaves da referência: `palpites:config`, `palpites:historico` (máx. 200). Sem TTL. Número de teste `5516991800765` no código — **não entra** no Águia Prime.

### Gerador / Manual / Histórico / Enviar (referência)

Comportamento descrito nas seções 10 e seguintes, com as melhorias da seção 0 já aplicadas no Águia Prime. Diferenças conscientes em relação à referência ficam na seção 6.3.

## 2. Como isso entra no Águia Prime

O site público hoje é uma landing Blade (`resources/views/site`), com header, footer e botão flutuante de WhatsApp. Bootstrap 5 e `public/css/site.css` / `public/js/site.js`. Laravel 10, PHP 8.1, sem Livewire e sem Alpine. As páginas públicas não passam pelo Vite.

A interface de palpites **não** reutiliza header, footer nem o botão flutuante. Casca própria: coluna estreita, navegação inferior, uma etapa por vez.

Identidade visual Águia Prime (fundo `#070b14`, dourado `#e0b84a`, Cinzel + Outfit, logo existente). O verde `#0a3524` da referência **não** é copiado.

### Botão na landing

**Gerar Palpites** → rota `palpites`.

- Menu desktop, ao lado de **Cadastrar agora**
- Menu mobile (offcanvas), acima de **Cadastrar agora**
- Hero, como segundo botão, ao lado de **Cadastrar agora**

Na interface exclusiva, o logo volta para a home do site.

### Meta e SEO

- `robots`: `noindex, nofollow` — é ferramenta de uso, não página de conteúdo
- `theme-color`: `#070b14`
- Título: `Gerar Palpites — Águia Prime Loterias`
- Canonical: `route('palpites')`

## 3. Decisões

| Tema | Decisão |
| --- | --- |
| Banco de dados | Nenhum nesta entrega |
| Persistência | `localStorage` com chaves próprias |
| Prazo | 14 dias deslizantes a partir do último **uso positivo** |
| Quem sorteia na UI | JavaScript no navegador (instantâneo, funciona offline) |
| Regras canônicas | `config/palpites.php` + classes PHP espelhando o algoritmo, cobertas por PHPUnit |
| Endpoint `POST` | Existe para testes Feature; UI **não depende** dele; em produção nasce **desligado** (`PALPITES_API_GERAR=false`) + throttle 20/min/IP (§9.1) |
| Configuração válida | `nome` não vazio **e** telefone com DDI válido (ver §7) |
| Primeira aba | Hash da URL, se válido; senão Início sem config válida; senão Gerador |
| Número de teste da referência | Não copiar |
| Imagem | Fora desta entrega. Botão visível, desabilitado, texto “Imagem em breve” |
| PWA / service worker | Fora |
| Stack | Blade + `palpites.css` + `palpites.js` (+ módulo de gerador), padrão da landing |

### Relógio de 14 dias

Campo único `updatedAt` dentro de `aguiaprime:palpites:config` (e espelhado no envelope do histórico, ver §4).

**Renova** quando:

- o usuário salva a banca com sucesso;
- um palpite novo entra no histórico (gerador ou manual).

**Não renova** quando:

- só navega entre abas;
- só marca/desmarca;
- exclui um item;
- limpa tudo (aí apaga mesmo).

Na abertura da página: se `now - updatedAt > 14 dias`, apaga config + histórico e trata como primeira visita. Aviso discreto uma vez: “Sua configuração expirou após 14 dias sem uso. Preencha de novo.”

## 4. Persistência no navegador

Chaves:

- `aguiaprime:palpites:config`
- `aguiaprime:palpites:historico`

Configuração:

```json
{
  "nome": "Águia Prime",
  "link": "https://app.exemplo.com",
  "representante": "5516999999999",
  "updatedAt": 1730822400000
}
```

Histórico (envelope, para o TTL não depender só da config):

```json
{
  "updatedAt": 1730822400000,
  "itens": [
    {
      "id": "m5k2ab-x7p1q",
      "modalidade": "milhar",
      "linha": "2860",
      "bichos": "Jacaré",
      "createdAt": 1730822400000,
      "origem": "gerador"
    }
  ]
}
```

`origem` é `"gerador"` ou `"manual"` — útil no histórico e em analytics futuras; não aparece na mensagem do WhatsApp.

Regras:

- Máximo de **200** itens; passa disso, remove o mais antigo.
- Ordem de armazenamento: mais recente primeiro.
- `representante`: só dígitos.
- `nome` e `link`: `trim`. `link` pode ser vazio.
- JSON inválido ou prazo vencido → estado vazio, sem quebrar a tela.
- Seleção de IDs: **só em memória** (`Set`). Recarregar zera a marcação, mantém histórico.
- Palpite novo: entra no topo **e** já vai para o conjunto de selecionados.

### Contador “Palpites gerados hoje”

```js
const diaLocal = (ts) => new Date(ts).toLocaleDateString('en-CA'); // YYYY-MM-DD no fuso local
itens.filter((i) => diaLocal(i.createdAt) === diaLocal(Date.now())).length
```

Não usar só `dd/mm` (bug da referência entre anos). Não usar UTC puro sem converter, para não “virar o dia” cedo/tarde demais no Brasil.

## 5. Catálogo dos 25 grupos

A dezena `00` pertence à Vaca. Nos demais, o grupo é `ceil(dezena / 4)`.

| Grupo | Bicho | Dezenas |
| --- | --- | --- |
| 01 | Avestruz | 01–04 |
| 02 | Águia | 05–08 |
| 03 | Burro | 09–12 |
| 04 | Borboleta | 13–16 |
| 05 | Cachorro | 17–20 |
| 06 | Cabra | 21–24 |
| 07 | Carneiro | 25–28 |
| 08 | Camelo | 29–32 |
| 09 | Cobra | 33–36 |
| 10 | Coelho | 37–40 |
| 11 | Cavalo | 41–44 |
| 12 | Elefante | 45–48 |
| 13 | Galo | 49–52 |
| 14 | Gato | 53–56 |
| 15 | Jacaré | 57–60 |
| 16 | Leão | 61–64 |
| 17 | Macaco | 65–68 |
| 18 | Porco | 69–72 |
| 19 | Pavão | 73–76 |
| 20 | Peru | 77–80 |
| 21 | Touro | 81–84 |
| 22 | Tigre | 85–88 |
| 23 | Urso | 89–92 |
| 24 | Veado | 93–96 |
| 25 | Vaca | 97, 98, 99, 00 |

Fonte única: `config/palpites.php`.

- PHP: `App\Support\Palpites\Catalogo` lê o config.
- Blade: imprime `window.PalpitesBoot = { grupos, modalidades, ttlDias, limiteHistorico, ... }` a partir do mesmo config.
- JS: **não** redefine a tabela dos 25 bichos à mão.

## 6. Modalidades e formato da linha

| ID | Nome | Dica | Usa bicho? | Linha | Campo `bichos` |
| --- | --- | --- | --- | --- | --- |
| `milhar` | Milhar | 4 números | sim | `2860` | `Jacaré` |
| `centena` | Centena | 3 números | sim | `860` | `Jacaré` |
| `dezena` | Dezena | 2 números | sim | `60` | `Jacaré` |
| `grupo` | Grupo | 1 bicho | sim | `15 · Jacaré` | `Jacaré` |
| `duque-grupo` | Duque de grupo | 2 bichos | sim | `15 · 03` | `Jacaré + Burro` |
| `terno-grupo` | Terno de grupo | 3 bichos | sim | `15 · 03 · 07` | `Jacaré + Burro + Carneiro` |
| `duque-dezena` | Duque de dezena | 2 dezenas | **não** | `57 · 12` | `Jacaré + Burro` |
| `terno-dezena` | Terno de dezena | 3 dezenas | **não** | `57 · 12 · 25` | `Jacaré + Burro + Carneiro` |
| `passe-vai` | Passe vai | 2 bichos em ordem | sim | `15 → 03` | `Jacaré → Burro` |
| `passe-vai-e-vem` | Passe vai e vem | 2 bichos, qualquer ordem | sim | `15 ⇄ 03` | `Jacaré ⇄ Burro` |

Números sempre com zero à esquerda: milhar 4, centena 3, dezena e grupo 2.

### 6.1 Sorteio sem bicho escolhido

- Milhar: `0000`–`9999`. Bicho pelos 2 últimos dígitos.
- Centena: `000`–`999`. Bicho pelos 2 últimos.
- Dezena: `00`–`99`.
- Grupo: um dos 25.
- Duque/terno de grupo e passes: grupos distintos, sem reposição.
- Duque/terno de dezena: dezenas distintas `00`–`99`, sem reposição.

Passe vai: ordem sorteada = ordem do jogo (`→`).  
Passe vai e vem: ordem exibida é a do sorteio; o jogo aceita qualquer ordem (`⇄`).

### 6.2 Sorteio com bicho escolhido

O grupo escolhido entra no resultado; os demais saem fora dele.

- Dezena: uma das 4 dezenas do grupo.
- Centena: 1 dígito aleatório + dezena do grupo (3 dígitos).
- Milhar: 2 dígitos aleatórios + dezena do grupo (**4 dígitos**).
- Grupo: `15 · Jacaré`.
- Duque de grupo / passes: grupo escolhido primeiro + 1 distinto.
- Terno de grupo: escolhido primeiro + 2 distintos.
- Duque/terno de dezena: o select de bicho **não aparece** (ver §10). Sorteio livre.

### 6.3 Correções conscientes vs. referência

1. Milhar com bicho: **4 dígitos** (na referência saía 3, bug da fórmula da centena).
2. Duque de grupo: separadores `·` e `+` — nunca `⇄` (que fica só no passe vai e vem).
3. Contador do dia: inclui o ano.
4. Fluxo pós-gerar: não obriga mudar de aba.

## 7. WhatsApp e telefone

### Normalização do telefone

1. Remover tudo que não for dígito.
2. Se começar com `0`, remover zeros à esquerda de tronco local só depois de analisar tamanho — regra prática:
   - 10 ou 11 dígitos → prefixar `55`
   - 12 ou 13 dígitos começando com `55` → manter
   - demais tamanhos → inválido
3. Válido para salvar e enviar: após normalizar, `^55\d{10,11}$` (Brasil com DDD + 8 ou 9 dígitos).

No input do Início:

- `inputmode="tel"`
- Placeholder: `Ex.: (16) 99180-0765`
- Ao sair do campo (`blur`), mostrar máscara amigável se válido; gravar só dígitos no `localStorage`
- Erro claro se inválido no Salvar

### Link do WhatsApp

Formato pedido:

```text
https://api.whatsapp.com/send/?phone=5516999999999&text=...&type=phone_number&app_absent=0
```

### Texto da mensagem

Nome da banca no título; se vazio, `Palpites da Águia Prime`.  
Data do dia em `dd/mm/aaaa` (fuso local).  
Itens na **ordem cronológica crescente** (mais antigo → mais novo), para o registro do jogo ler na sequência em que foram criados.  
Só entram os **selecionados**.

```text
🎲 *Jogo para registro* — Águia Prime
📅 09/11/2026

• Milhar: 2860 (Jacaré)
• Dezena: 60 (Jacaré)

Aguardo confirmação!
```

O link do app **não** entra no texto (na referência só aparece na imagem).

### Prévia na aba Enviar

Antes de abrir o WhatsApp:

1. Card **Prévia da mensagem** com o texto exato em `<textarea>` (editável).
2. Edição na prévia **não** altera o histórico; só o que será enviado naquele clique.
3. Botão **Encaminhar jogo para o representante** monta a URL com o conteúdo atual da prévia.
4. Se o telefone estiver inválido/ausente: não abre; alerta + atalho “Ir para Início”.
5. Se zero selecionados: estado vazio com **Selecionar todo o histórico (n)**.

## 8. Arquitetura do sorteio

### Por que o sorteio principal é no cliente

A ferramenta é offline-friendly por natureza (tudo no `localStorage`). Exigir `POST` a cada “Gerar palpite” adiciona latência, falha sem rede e atrito no celular — sem ganho de negócio (não há anti-fraude nem auditoria server-side).

### Como evitar duas verdades divergentes

1. `config/palpites.php` é a única tabela de grupos/modalidades.
2. `App\Support\Palpites\Catalogo` e `GeradorDePalpites` implementam as regras em PHP.
3. PHPUnit cobre o gerador PHP de ponta a ponta (fonte canônica das regras).
4. A Blade injeta o catálogo; `public/js/palpites-gerador.js` implementa o **mesmo** contrato de entrada/saída documentado abaixo.
5. Checklist de implementação: qualquer mudança de regra atualiza PHP + teste + JS na mesma PR.
6. `POST /palpites/gerar` continua existindo, chama a classe PHP, e serve para Feature tests. A UI não usa esse POST no fluxo normal.

### Contrato do gerador (PHP e JS)

Entrada:

```json
{ "modalidade": "milhar", "grupo": 15 }
```

`grupo` opcional (`null` = qualquer).

Saída:

```json
{
  "id": "m5k2ab-x7p1q",
  "modalidade": "milhar",
  "linha": "2860",
  "bichos": "Jacaré",
  "createdAt": 1730822400000
}
```

Erros: modalidade desconhecida ou grupo fora de 1–25.

IDs: `Date.now().toString(36) + '-' + random` (equivalente nos dois lados).

## 9. Endpoint Laravel

| Método | Rota | Nome | Uso |
| --- | --- | --- | --- |
| `GET` | `/palpites` | `palpites` | Página da ferramenta |
| `POST` | `/palpites/gerar` | `palpites.gerar` | API do gerador PHP (testes / uso controlado) |

`POST` responde 200 com o JSON do contrato ou 422 em validação. A página `GET` já entrega `PalpitesBoot` com catálogo completo para o JS.

Proteções obrigatórias deste endpoint e da página estão na **§9.1**. Em resumo: a UI não chama o `POST` no fluxo normal; o `POST` nasce atrás de throttle agressivo e, em produção, pode ficar desligado por config.

### 9.1 Segurança, flood e uso mal-intencionado

#### O que realmente custa no servidor

Com o sorteio no navegador, **clicar mil vezes em “Gerar palpite” não gera mil hits de CPU/DB no Laravel**. Histórico, banca e seleção ficam no `localStorage` do aparelho do usuário — não há gravação no MySQL, não há fila, não há disco do servidor por palpite.

Superfícies HTTP reais nesta entrega:

| Superfície | Risco se abusada | Impacto |
| --- | --- | --- |
| `GET /palpites` | Flood de página | CPU/HTML leve; sem query pesada |
| `POST /palpites/gerar` | Flood de API | CPU trivial por request, mas é o alvo óbvio de bot |
| Assets `palpites.css` / `js` | Hotlink / crawl | Cacheável; impacto baixo |
| Imagem server-side (futuro) | Geração PNG em massa | **Alto** — tratar à parte (§14) |

Não há risco de “encher o banco” com palpites: não existe tabela de palpites.

#### Decisões desta entrega

1. **UI gera só no cliente** — o caminho feliz não depende de `POST`. Isso é a principal mitigação de flood de sorteio.
2. **`POST /palpites/gerar` não é público “aberto”**
   - Middleware: `web` (CSRF) + rate limiter dedicado `palpites-gerar`.
   - Limite inicial: **20 req/min por IP** (`ThrottleRequests`). Resposta `429` com mensagem curta.
   - Flag em `config/palpites.php`: `api_gerar_habilitada` (env `PALPITES_API_GERAR=false` em produção por padrão).
   - Com a flag `false`, a rota responde `404` ou `403` estável — Feature tests ligam a flag no `phpunit.xml` / `before`.
   - Corpo mínimo: só `modalidade` (string allowlist) e `grupo` (int|null, 1–25). Nada de HTML, arquivos ou blobs.
   - Sem log de payload completo em `info`; falhas de validação não gravam PII.
3. **`GET /palpites`**
   - Sem autenticação (é a ferramenta pública).
   - Sem consultas N+1: no máximo `Setting::current()` se a página precisar da marca — preferir só `config('site')` + `config('palpites')` para esta view, evitando DB no hot path.
   - Throttle opcional mais folgado, ex.: `throttle:120,1` por IP, só se o host não tiver WAF/Cloudflare na frente.
   - `Cache-Control` para HTML: `no-store` ou curto (a página carrega CSRF fresco); assets estáticos com cache longo + fingerprint/query se necessário.
4. **CSRF** no `POST` obrigatório. Sem rota irmã em `routes/api.php` sem CSRF nesta fase.
5. **Validação estrita** — Form Request ou `$request->validate([...])` com `Rule::in` das modalidades do config. Grupo fora do range → 422, sem executar sorteio.
6. **XSS / injeção na UI**
   - Blade: `{{ }}` em qualquer eco de setting.
   - JS: não usar `innerHTML` com nome da banca, link ou texto da prévia; preferir `textContent` / valor de `textarea`.
   - A prévia editável só alimenta `encodeURIComponent` na URL do WhatsApp — não volta para o servidor.
7. **WhatsApp**
   - Abrir `api.whatsapp.com` no cliente não sobrecarrega o Laravel.
   - Telefone normalizado só com dígitos; rejeitar strings longas (>20 dígitos após limpar) para evitar URLs absurdas.
   - Abuso de spam no WhatsApp é problema da plataforma/destinatário, não de armazenamento nosso; mesmo assim a UI não envia em loop automático (um clique = uma aba).
8. **Cliente malicioso local**
   - Pode zerar/forjar `localStorage` à vontade — impacto só no próprio navegador.
   - Limite de 200 itens e TTL de 14 dias protegem o aparelho do usuário, não o servidor.
9. **Cabeçalhos básicos** na layout de palpites (ou herdados do app): `Referrer-Policy`, `X-Content-Type-Options: nosniff`. CSP rígida pode ficar para depois se o kit Font Awesome / CDNs da landing complicarem; não bloquear o go-live por CSP perfeita.
10. **Observabilidade**
    - Contar `429` do limiter `palpites-gerar` (log warning amostrado ou métrica).
    - Não criar job, mail ou webhook por geração.

#### O que fica de fora (de propósito)

- Captcha no gerador: atrito alto, benefício baixo enquanto o sorteio for client-side.
- Auth obrigatória: muda o produto.
- Banimento por IP no app: melhor no proxy (Cloudflare / nginx) se um dia houver ataque de `GET`.
- Rate limit “por usuário logado”: não há usuário nesta ferramenta.

#### Quando a imagem entrar (§14)

Gerar PNG no servidor é o único ponto realmente caro. Regras já reservadas:

- Preferir canvas no cliente (como a referência), **ou**
- Se for server-side: throttle bem mais baixo (ex. 5/min/IP), tamanho máximo de lista (já há teto de 14 na referência), timeout curto, sem fila ilimitada, e nunca aceitar HTML arbitrário para renderizar.

#### Checklist de segurança na implementação

- [ ] `PALPITES_API_GERAR=false` no `.env` de produção
- [ ] Limiter `palpites-gerar` registrado em `RouteServiceProvider` (20/min/IP)
- [ ] `POST` com CSRF + Form Request allowlist
- [ ] `GET /palpites` sem query pesada
- [ ] JS sem `innerHTML` de dados do usuário
- [ ] Feature test: com flag off → não gera; com flag on + 21º request → 429 (opcional, se o teste de throttle for estável no CI)

## 10. Comportamento da interface

Uma página. Abas = seções. Barra inferior fixa: Início · Gerador · Manual · Histórico · Enviar.

- Largura máxima do miolo: `32rem`
- Padding inferior ≥ altura da barra + `env(safe-area-inset-bottom)`
- Aba ativa em dourado
- Hash na URL sincronizado com a aba (`#gerador` etc.). Trocar aba atualiza o hash; carregar com hash abre a aba (se Início exigir config e não houver, redireciona visualmente para Início sem apagar o hash desejado — após salvar, oferece “Continuar”)
- Badge numérico na aba **Histórico** = total de itens; na aba **Enviar** = quantidade selecionada (oculto quando 0)
- Alvos de toque ≥ 44px; foco visível; labels em todos os campos
- Faixa discreta 18+: “Ferramenta para maiores de 18 anos. Jogue com responsabilidade.”

### 10.1 Início

- Validar no **Salvar**:
  - Nome obrigatório (mín. 2 caracteres)
  - WhatsApp obrigatório e válido (§7)
  - Link opcional; se preenchido, aceitar domínio com ou sem `https://` (normalizar gravando como o usuário digitou, trim)
- Erros inline abaixo do campo, sem alert nativo
- Sucesso: **Salvo!** 2,5 s
- Contador do dia (§4)
- **Gerar palpite agora →** só habilita com config válida; senão, foco no primeiro campo inválido
- Texto do card: manter a ideia “configure uma vez só”, ajustando para a marca: “Essas informações entram no envio ao representante e na imagem dos palpites (em breve).”

### 10.2 Gerador

- Grade 2 colunas de modalidades
- Trocar modalidade limpa o card de resultado
- Select de bicho:
  - Visível quando `usa bicho = sim`
  - Oculto (ou desabilitado + texto “Esta modalidade sorteia dezenas livres”) quando `duque-dezena` / `terno-dezena`
  - Ao esconder, zerar o valor selecionado
- **Gerar palpite**: imediato no cliente; botão com estado `Gerando...` só se no futuro voltarmos ao POST
- Card de resultado:
  - Modalidade, linha, bichos
  - **Gerar outro**
  - **Adicionar ao histórico** — grava, marca como selecionado, toast “Adicionado”, **permanece no Gerador**
- Atalho secundário no toast: “Ver histórico”

### 10.3 Manual

- Milhar: 4 dígitos; derivados centena / dezena / grupo
- Dezena: 2 dígitos; mostra grupo (inclui `00` → Vaca)
- Botões desabilitados até tamanho certo
- Feedback 2,5 s; **não** muda de aba
- Item novo já selecionado

### 10.4 Histórico

- Vazio: copy da referência
- **Selecionar todos** / **Desmarcar todos**
- **Limpar tudo** → confirmação: “Apagar todos os palpites deste aparelho? Essa ação não dá para desfazer.”
- Excluir um item: sem confirmação (ação local e reversível só gerando de novo)
- Linha: checkbox circular, `linha`, `Modalidade · bichos · dd/mm`, excluir
- **Enviar (n) selecionados** → `#enviar`

### 10.5 Enviar

- Estados vazios e com seleção (§7)
- Lista “Vai na mensagem (n)” (renomear em relação à referência, já que a imagem ainda não existe; quando a imagem entrar, o título pode voltar a “Vai na imagem”)
- Prévia editável
- Encaminhar WhatsApp
- **Gerar imagem** desabilitado: “Imagem em breve”

### 10.6 Estados que precisam funcionar

- Primeira visita → Início
- Config válida + visita seguinte → Gerador (ou hash)
- Salvar inválido → erros inline
- TTL 14 dias → wipe + aviso
- Offline → gerar/manual/histórico/enviar texto funcionam; só o `POST` de teste falharia
- Gerar todas as modalidades com/sem bicho
- Manual `00` → Vaca
- Auto-seleção ao adicionar
- Confirmar limpar tudo
- Prévia editada reflete no link do WhatsApp
- Telefone inválido não abre aba vazia
- Contador do dia com ano
- Recarregar: mantém banca/histórico, perde só seleção (e re-lê hash)
- iPhone com home indicator: barra não cobre botões

## 11. Arquivos previstos

Novos:

- `config/palpites.php` — grupos, modalidades, TTL, flag `api_gerar_habilitada`, limites
- `app/Support/Palpites/Catalogo.php`
- `app/Support/Palpites/GeradorDePalpites.php`
- `app/Http/Controllers/PalpiteController.php`
- `resources/views/site/palpites/layout.blade.php`
- `resources/views/site/palpites/app.blade.php`
- `public/css/palpites.css`
- `public/js/palpites-gerador.js` — algoritmo espelhado
- `public/js/palpites.js` — UI, storage, abas, WhatsApp
- `tests/Unit/CatalogoTest.php`
- `tests/Unit/GeradorDePalpitesTest.php`
- `tests/Feature/PalpitePageTest.php`

Alterados:

- `routes/web.php`
- `resources/views/site/partials/header.blade.php`
- `resources/views/site/pages/home.blade.php`

Nada em `database/`. Nada no admin.

## 12. Testes

### Unit — `CatalogoTest`

- `00` → Vaca (25); `60` → Jacaré (15); `01` → Avestruz
- Dezenas do grupo 15 = 57..60; grupo 25 = 97,98,99,0

### Unit — `GeradorDePalpitesTest`

- Tamanhos e padding de milhar/centena/dezena
- Milhar com grupo 15: 4 chars, sufixo em 57–60
- Centena com grupo 15: 3 chars, sufixo em 57–60
- Duque/terno/passes sem grupos repetidos
- Duque/terno de dezena sem dezenas repetidas
- Separadores: `→`, `⇄`, `·`, `+`
- Modalidade inválida / grupo inválido falham

### Feature — `PalpitePageTest`

- `GET /palpites` 200, cinco abas, `PalpitesBoot`, `noindex`
- `POST /palpites/gerar` válido → JSON do contrato
- `POST` inválido → 422
- Landing contém link para `route('palpites')`

### Manual no navegador (Laragon)

Desktop + largura ~390px: landing → ferramenta → salvar → gerar → adicionar → histórico → prévia → abrir WhatsApp (sem enviar) → limpar com confirmação → hash `#enviar`.

## 13. Ordem de implementação

1. `config/palpites.php` + `Catalogo` + `GeradorDePalpites` + testes unitários
2. Rotas + controller + layout/app com abas + hash + `PalpitesBoot`
3. Storage (config/histórico/TTL) + Início com validação
4. `palpites-gerador.js` alinhado aos testes PHP + tela Gerador
5. Manual
6. Histórico (seleção, badges, confirmar limpar)
7. Enviar (prévia + WhatsApp); botão de imagem desabilitado
8. Botões na landing + `noindex`
9. Passada visual desktop/mobile + safe-area

## 14. Imagem — etapa posterior

Não desenhar a arte nesta entrega. A aba Enviar já terá seleção, nome da banca e link do app.

Referência atual (será substituída quando a arte Águia Prime existir):

- Canvas 1080×1920, degradê, barra dourada, círculos suaves
- Nome da banca, `PALPITES DO DIA`, data
- Até 14 itens: linha + `Modalidade · bichos`
- Link do app + `BOA SORTE!`
- `navigator.share` ou download `palpites.png`

Quando entrar: baixar e compartilhar como ações **separadas** (na referência o botão “Baixar” estava ligado ao gerar de novo).

## 15. Critérios para considerar a entrega pronta

- [ ] Fluxo completo sem banco e sem imagem
- [ ] Regras de sorteio cobertas por PHPUnit
- [ ] UI usável offline após o primeiro `GET /palpites`
- [ ] WhatsApp com telefone normalizado e prévia fiel
- [ ] TTL 14 dias e contador do dia corretos
- [ ] Landing com entrada clara para a ferramenta
- [ ] Mobile com barra inferior sem cobrir conteúdo
- [ ] `POST /palpites/gerar` desligado em produção + throttle quando ligado (§9.1)
