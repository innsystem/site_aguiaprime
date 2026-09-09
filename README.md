# Águia Prime Loterias

Landing page institucional da **Águia Prime Loterias**, construída em **Laravel 10** com Bootstrap 5, Font Awesome e visual premium (navy + gold).

## Requisitos

- PHP 8.1+
- Composer 2
- Extensões PHP padrão do Laravel

## Instalação

```bash
composer install
cp .env.example .env
php artisan key:generate
```

Configure no `.env`:

```env
AGUIA_REGISTER_URL=https://app.aguiaprime119000.com/pr/zFhVdeku
AGUIA_VIP_URL=https://chat.whatsapp.com/J0RROFoeBTxFwAzx77Lt7M
AGUIA_VIDEO_URL=
```

`AGUIA_VIDEO_URL` aceita URL de embed (YouTube/Vimeo). Sem valor, a seção exibe placeholder.

## Desenvolvimento

```bash
php artisan serve
```

Acesse `http://127.0.0.1:8000`.

Em Laragon: `http://localhost/innsystem_clientes/aguiaprime/public`

## Estrutura do site

- Hero full-bleed com CTA externo
- Vídeo de apresentação
- Tabela de cotações
- Vantagens, bônus e modalidades
- Grupo VIP (WhatsApp)
- Carousel de prêmios/comprovantes (`public/img/premios/premio-1.jpg` …)

Conteúdo configurável em `config/site.php`.

## Assets de comprovantes

Coloque as imagens dos ganhos em:

```
public/img/premios/premio-1.jpg
public/img/premios/premio-2.jpg
public/img/premios/premio-3.jpg
public/img/premios/premio-4.jpg
```
