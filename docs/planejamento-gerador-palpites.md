# Planejamento — Gerador de Palpites

Referência analisada em 05/11/2026: [palpitesdasorte.portodesigner.com.br](https://palpitesdasorte.portodesigner.com.br/) (v1.3.0, app de página única feito no Lovable).

Este documento descreve como recriar o fluxo desse gerador dentro do site Águia Prime já existente, como uma interface exclusiva. A landing atual permanece. Um botão **Gerar Palpites** leva para essa interface.

A geração da imagem fica reservada para uma etapa seguinte. O restante do fluxo, inclusive o texto do WhatsApp, entra nesta entrega.

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

### Início

- Título: **Bem-vindo!**
- Card **Configuração da banca**. Texto: “Essas informações aparecem na imagem dos palpites. Configure uma vez só.”
- Campos:
  - Nome da banca — placeholder `Ex.: Banca Paraíso`
  - Link do app — placeholder `Ex.: app.suabanca.com.br`
  - WhatsApp do representante — placeholder `Ex.: +55 16 99180-0765`, teclado numérico
- Ajuda do WhatsApp: “É pra esse número que o jogo vai quando o cliente encaminhar.”
- Botão **Salvar**. Depois do clique, o rótulo vira **Salvo!** por 2,5 segundos.
- Card **Palpites gerados hoje**, com o total de itens do histórico cuja data (dia/mês) é a de hoje.
- Botão **Gerar palpite agora →**, que abre a aba Gerador.
- Rodapé: `Palpites da Águia Prime · v1.3.0`

Chaves reais do navegador:

- `palpites:config` → `{ nome, link, representante }`
- `palpites:historico` → lista, máximo de 200 itens, o mais novo primeiro

Não há TTL. O dado fica até o usuário limpar o site ou clicar em **Limpar tudo**. O número `5516991800765` está gravado no código como fallback de teste. Esse número não entra no Águia Prime.

### Gerador

Grade de 2 colunas com as modalidades. A selecionada fica em destaque. Trocar a modalidade apaga o palpite que estava na tela.

Select **Bicho (opcional)**:

- vazio = `Qualquer bicho (sortear)`
- `01 — Avestruz` até `25 — Vaca`

Botão **Gerar palpite**. O resultado aparece abaixo, em card claro:

- nome da modalidade
- linha sorteada em destaque
- nome do bicho (ou dos bichos)
- **Gerar outro** — sorteia de novo e substitui o card
- **Histórico** — grava o palpite e abre a aba Histórico

### Manual

Dois cards independentes.

**Digitar milhar.** Aceita só dígitos, no máximo 4. Com 4 dígitos, mostra três derivados e libera o botão:

| Derivado | Regra | Exemplo para `2860` |
| --- | --- | --- |
| Centena | 3 últimos dígitos | `860` |
| Dezena | 2 últimos dígitos | `60` |
| Grupo | bicho da dezena | `Jacaré` |

Só a milhar entra no histórico. Centena, dezena e grupo são só conferência.

**Digitar dezena.** Aceita só dígitos, no máximo 2. Com 2 dígitos, mostra `Grupo: Jacaré` e libera o botão.

Os botões ficam desabilitados até o tamanho certo. Feedback: `Adicionado ao histórico!` ou `Verifique os números digitados.`, por 2,5 segundos. A tela manual não muda de aba.

### Histórico

Vazio:

- “Nenhum palpite ainda.”
- “Gere ou digite um palpite para começar.”

Com itens:

- **Selecionar todos** / **Desmarcar todos**
- **Limpar tudo**
- Cada linha: marcador circular, linha do palpite, `Modalidade · bichos · dd/mm`, botão excluir
- Clicar na linha ou no marcador alterna a seleção
- Item marcado fica com fundo claro
- **Enviar (n) selecionados** — desabilitado com zero marcados; abre a aba Enviar

A seleção vive só na memória da aba. Recarregar a página mantém o histórico e zera a marcação.

### Enviar

Sem seleção:

- “Nenhum palpite selecionado.”
- “Selecione no Histórico ou use tudo de uma vez:”
- **Selecionar todo o histórico (n)**

Com seleção:

- Lista **Vai na imagem (n)**, com linha, modalidade e bichos
- **Encaminhar jogo para o representante** — abre o WhatsApp em nova aba
- Texto auxiliar: “Abre o WhatsApp com o jogo pronto para enviar ao representante.”
- **Gerar imagem** — no site de referência desenha um PNG. Nesta entrega o botão fica reservado até o layout da imagem ser definido.

## 2. Como isso entra no Águia Prime

O site público hoje é uma landing Blade (`resources/views/site`), com header, footer e botão flutuante de WhatsApp. Bootstrap 5 e `public/css/site.css` / `public/js/site.js`. Laravel 10, PHP 8.1, sem Livewire e sem Alpine. As páginas públicas não passam pelo Vite.

A interface de palpites não reutiliza header, footer nem o botão flutuante. Ela tem casca própria, no mesmo espírito do app de referência: coluna estreita, navegação inferior, uma etapa por vez.

A identidade visual segue a Águia Prime (fundo `#070b14`, dourado `#e0b84a`, fontes Cinzel e Outfit, logo já existente). O fluxo, os textos e as regras vêm do app de referência. O verde `#0a3524` da referência não é copiado.

### Botão na landing

**Gerar Palpites** aponta para a rota `palpites`.

- Menu desktop, ao lado de **Cadastrar agora**
- Menu mobile (offcanvas), acima de **Cadastrar agora**
- Hero, como segundo botão, ao lado de **Cadastrar agora**

Na interface exclusiva, o logo volta para a home do site.

## 3. Decisões

| Tema | Decisão |
| --- | --- |
| Banco de dados | Nenhum. Sem migration, sem model, sem admin nesta entrega |
| Onde persiste | `localStorage` do navegador |
| Prazo | 14 dias, contados do último salvamento. A referência não expira; o prazo atende ao pedido de 7 ou 14 dias |
| Quem sorteia | PHP, numa classe testável. A tela chama um endpoint JSON |
| Quem guarda histórico e banca | O navegador. O servidor não recebe a lista de palpites |
| Primeira tela | Início, quando ainda não há configuração válida. Gerador, quando nome e WhatsApp já estão salvos |
| Número de teste da referência | Não copiar. Sem WhatsApp salvo, o envio pede para voltar ao Início |
| Imagem | Fora desta entrega. O botão fica visível e desabilitado, com o texto “Imagem em breve” |
| PWA e service worker da referência | Fora. Não há ganho dentro do site já hospedado |
| Stack da tela | Blade + CSS próprio + JavaScript próprio, no mesmo padrão da landing |

O prazo de 14 dias é deslizante: cada **Salvar**, cada palpite novo e cada exclusão renovam `savedAt`. Passados 14 dias sem uso, configuração e histórico são descartados na próxima abertura.

## 4. Persistência no navegador

Chaves próprias, para não colidir com nada do domínio de referência se alguém testar os dois:

- `aguiaprime:palpites:config`
- `aguiaprime:palpites:historico`

Configuração:

```json
{
  "nome": "Águia Prime",
  "link": "https://app.exemplo.com",
  "representante": "5516999999999",
  "savedAt": 1730822400000
}
```

Item de histórico:

```json
{
  "id": "m5k2ab-x7p1q",
  "modalidade": "milhar",
  "linha": "2860",
  "bichos": "Jacaré",
  "createdAt": 1730822400000
}
```

Regras:

- Máximo de 200 itens. Ao passar disso, sai o mais antigo.
- Ordem: mais recente primeiro.
- `representante` é guardado só com dígitos.
- Nome e link são gravados com `trim`.
- Leitura com JSON inválido ou prazo vencido volta ao estado vazio, sem quebrar a tela.

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

Fonte única: `config/palpites.php`. A classe PHP lê esse arquivo. A tela manual, que precisa mostrar o grupo enquanto a pessoa digita, recebe o mesmo catálogo em JSON no HTML. Não existe uma segunda tabela espalhada no JavaScript.

## 6. Modalidades e formato da linha

| ID | Nome | Dica | Linha | Campo `bichos` |
| --- | --- | --- | --- | --- |
| `milhar` | Milhar | 4 números | `2860` | `Jacaré` |
| `centena` | Centena | 3 números | `860` | `Jacaré` |
| `dezena` | Dezena | 2 números | `60` | `Jacaré` |
| `grupo` | Grupo | 1 bicho | `15 · Jacaré` | `Jacaré` |
| `duque-grupo` | Duque de grupo | 2 bichos | `15 · 03` | `Jacaré + Burro` |
| `terno-grupo` | Terno de grupo | 3 bichos | `15 · 03 · 07` | `Jacaré + Burro + Carneiro` |
| `duque-dezena` | Duque de dezena | 2 dezenas | `57 · 12` | `Jacaré + Burro` |
| `terno-dezena` | Terno de dezena | 3 dezenas | `57 · 12 · 25` | `Jacaré + Burro + Carneiro` |
| `passe-vai` | Passe vai | 2 bichos em ordem | `15 → 03` | `Jacaré → Burro` |
| `passe-vai-e-vem` | Passe vai e vem | 2 bichos, qualquer ordem | `15 ⇄ 03` | `Jacaré ⇄ Burro` |

Números sempre com zero à esquerda: milhar com 4, centena com 3, dezena e grupo com 2.

### Sorteio sem bicho escolhido

- Milhar: inteiro de `0000` a `9999`. O bicho sai dos 2 últimos dígitos.
- Centena: inteiro de `000` a `999`. O bicho sai dos 2 últimos dígitos.
- Dezena: inteiro de `00` a `99`.
- Grupo: um grupo entre os 25.
- Duque e terno de grupo, passe vai e passe vai e vem: grupos distintos, sorteados sem reposição.
- Duque e terno de dezena: dezenas distintas, de `00` a `99`, sem reposição. Cada dezena mostra o próprio bicho.

No passe vai a ordem sorteada é a ordem do jogo. No passe vai e vem a ordem exibida é só a ordem do sorteio; o significado do jogo é qualquer ordem, e isso já fica claro pelo separador `⇄`.

### Sorteio com bicho escolhido

O grupo escolhido entra no resultado. Os outros grupos ou dezenas são sorteados fora dele.

- Dezena: uma das 4 dezenas do grupo.
- Centena: um dígito aleatório (`0–9`) na frente dessa dezena. Continua com 3 dígitos.
- Milhar: dois dígitos aleatórios (`00–99`) na frente dessa dezena. Fica com 4 dígitos.
- Grupo: o próprio grupo, no formato `15 · Jacaré`.
- Duque de grupo e os dois passes: o grupo escolhido primeiro, mais um grupo diferente.
- Terno de grupo: o grupo escolhido primeiro, mais dois grupos diferentes.
- Duque e terno de dezena ignoram o bicho escolhido e usam o sorteio livre. É o que a referência faz hoje (`default` cai no sorteio sem filtro).

### Correção em relação à referência

No app analisado, milhar com bicho escolhido sai com 3 dígitos, porque a fórmula da centena foi reaproveitada. Aqui a milhar com bicho fica com 4 dígitos.

No sorteio livre, duque de grupo usa o mesmo separador do passe vai e vem (`⇄`). Aqui duque de grupo usa sempre `·` na linha e `+` nos bichos. `→` fica só no passe vai. `⇄` fica só no passe vai e vem.

## 7. Mensagem de WhatsApp

O link segue o formato pedido:

```text
https://api.whatsapp.com/send/?phone=5516991800765&text=...&type=phone_number&app_absent=0
```

O `text` é a mensagem abaixo, com encode de URL. O nome da banca entra no título. Se o nome estiver vazio, o título usa `Palpites da Águia Prime`. A data é a do dia, em `dd/mm/aaaa`. Uma linha por palpite selecionado, na ordem do histórico (mais recente primeiro).

```text
🎲 *Jogo para registro* — Palpites da Águia Prime
📅 05/11/2026

• Milhar: 2860 (Jacaré)

Aguardo confirmação!
```

Com vários palpites, cada um vira um bullet, separados por uma quebra de linha simples:

```text
🎲 *Jogo para registro* — Águia Prime
📅 05/11/2026

• Milhar: 2860 (Jacaré)
• Dezena: 60 (Jacaré)

Aguardo confirmação!
```

O link do app não entra nesse texto. Na referência ele só aparece na imagem.

Telefone:

- remove tudo que não for dígito
- com 10 ou 11 dígitos, prefixa `55`
- com DDI já incluso, mantém como está
- vazio ou curto demais: não abre o WhatsApp; a tela pede para preencher o representante no Início

## 8. Endpoint Laravel

`POST /palpites/gerar`, nome `palpites.gerar`.

Corpo JSON:

```json
{ "modalidade": "milhar", "grupo": 15 }
```

`grupo` é opcional. `null` ou ausente significa qualquer bicho.

Resposta `200`:

```json
{
  "id": "m5k2ab-x7p1q",
  "modalidade": "milhar",
  "linha": "2860",
  "bichos": "Jacaré",
  "createdAt": 1730822400000
}
```

Resposta `422` quando a modalidade não existe ou o grupo está fora de 1–25.

O histórico manual não precisa desse endpoint. Milhar e dezena digitadas são validadas no navegador com o catálogo já impresso na página. A mesma regra de grupo do PHP fica coberta por teste, e o JavaScript só lê a tabela.

CSRF: a página de palpites inclui o token. O JavaScript envia `X-CSRF-TOKEN`.

## 9. Arquivos previstos

Novos:

- `config/palpites.php` — grupos, modalidades, TTL de 14 dias, limite de 200, rótulo padrão do título
- `app/Support/Palpites/Catalogo.php` — consulta de grupo por dezena e lista de dezenas de um grupo
- `app/Support/Palpites/GeradorDePalpites.php` — sorteio das 10 modalidades
- `app/Http/Controllers/PalpiteController.php` — `index` e `gerar`
- `resources/views/site/palpites/layout.blade.php` — HTML próprio, sem header/footer da landing
- `resources/views/site/palpites/app.blade.php` — as cinco etapas no mesmo documento
- `public/css/palpites.css`
- `public/js/palpites.js`
- `tests/Unit/GeradorDePalpitesTest.php`
- `tests/Feature/PalpitePageTest.php`

Alterados:

- `routes/web.php` — `GET /palpites` e `POST /palpites/gerar`
- `resources/views/site/partials/header.blade.php` — botão no menu desktop e no offcanvas
- `resources/views/site/pages/home.blade.php` — botão no hero

Nada em `database/`. Nada no admin.

## 10. Comportamento da interface

Uma única página. As abas só mostram e escondem seções. A barra inferior fica fixa, com Início, Gerador, Manual, Histórico e Enviar. A aba ativa usa o dourado da marca.

Largura máxima do miolo: 32rem, centralizado. No celular ocupa a tela. Espaço inferior para a barra não cobrir o último botão.

Acessibilidade mínima: rótulos nos campos, botão de seleção com nome, foco visível, alvos de toque com pelo menos 44px.

### Estados que precisam funcionar

- Primeira visita, armazenamento vazio, abre em Início
- Salvar e ver **Salvo!**
- Voltar no dia seguinte e encontrar a banca preenchida
- Passar de 14 dias e cair de novo no Início vazio
- Gerar, gerar outro, adicionar ao histórico e ver o item no topo
- Gerar com cada modalidade, com e sem bicho
- Manual com milhar incompleta (botão desligado) e completa (derivados visíveis)
- Manual com dezena e grupo calculado, inclusive `00` → Vaca
- Marcar, desmarcar, selecionar todos, excluir um, limpar tudo
- Enviar com zero itens desabilitado
- Enviar abre o WhatsApp com o texto e o telefone corretos
- Enviar sem telefone explica o que falta, em vez de abrir link vazio
- Contador “Palpites gerados hoje” ignora itens de outros dias
- Recarregar mantém banca e histórico e perde só a seleção

## 11. Testes

`GeradorDePalpitesTest` cobre, com semente fixa quando der para fixar o aleatório:

- tamanho e zero à esquerda de milhar, centena e dezena
- `00` e `60` mapeiam para Vaca e Jacaré
- milhar com grupo 15 termina numa dezena de 57 a 60 e tem 4 caracteres
- centena com grupo 15 tem 3 caracteres e termina na dezena do grupo
- duque, terno e passes não repetem grupo
- duque e terno de dezena não repetem dezena
- passe vai usa `→`; passe vai e vem usa `⇄`; duque de grupo usa `·` e `+`
- grupo fora de 1–25 e modalidade desconhecida falham

`PalpitePageTest`:

- `GET /palpites` responde 200 e mostra as cinco abas e o select de bichos
- `POST /palpites/gerar` com modalidade válida devolve JSON com `linha` e `bichos`
- `POST` com modalidade inválida devolve 422

A conferência visual e de clique fica no navegador, na rota local do Laragon, desktop e largura de celular: home até o gerador, salvar, sortear, histórico, abrir o link do WhatsApp sem enviar de fato.

## 12. Ordem de implementação

1. Catálogo, gerador PHP e testes de unidade.
2. Rotas, controller e página com as cinco abas navegáveis, ainda sem sorteio.
3. Início com `localStorage` e prazo de 14 dias.
4. Gerador consumindo `POST /palpites/gerar`, com card de resultado.
5. Manual, com derivados na hora da digitação.
6. Histórico: marcar, excluir, limpar, contador do dia.
7. Enviar: montar o texto e abrir o WhatsApp. Botão de imagem presente e desabilitado.
8. Botões na landing.
9. Passada no navegador, desktop e celular.

## 13. Imagem — etapa posterior

Não desenhar a arte nesta entrega. Quando o layout for passado, a aba Enviar já terá a lista selecionada, o nome da banca e o link do app. A geração pode ser no navegador, em canvas, no tamanho 1080×1920 que a referência usa, ou no servidor. Isso se decide junto com a arte.

Só para não perder o que existe hoje na referência, o PNG atual é este, e será substituído:

- fundo em degradê verde, barra dourada no topo, dois círculos dourados suaves
- nome da banca em caixa alta, título `PALPITES DO DIA`, data `dd/mm/aaaa`
- cartão claro com até 14 palpites: linha em destaque e, abaixo, `Modalidade · bichos`
- link do app e o fecho `BOA SORTE!`
- em seguida, compartilhar o arquivo pelo `navigator.share` ou baixar `palpites.png`

O botão **Baixar imagem** da referência está ligado na função de gerar de novo, não na de download. Quando a imagem entrar, baixar e compartilhar serão ações separadas.
