<?php

namespace App\Support;

class YoutubeUrl
{
    /**
     * Converte qualquer URL válida do YouTube para o formato embed.
     */
    public static function toEmbed(?string $url): ?string
    {
        $url = trim((string) $url);

        if ($url === '') {
            return null;
        }

        $videoId = self::extractId($url);

        if (! $videoId) {
            return null;
        }

        return 'https://www.youtube.com/embed/'.$videoId;
    }

    /**
     * Extrai o ID do vídeo a partir de formatos comuns do YouTube.
     */
    public static function extractId(?string $url): ?string
    {
        $url = trim((string) $url);

        if ($url === '') {
            return null;
        }

        // Já é só o ID (11 caracteres)
        if (preg_match('/^[a-zA-Z0-9_-]{11}$/', $url)) {
            return $url;
        }

        $parts = parse_url($url);

        if ($parts === false || empty($parts['host'])) {
            return null;
        }

        $host = strtolower($parts['host']);
        $path = $parts['path'] ?? '';

        // youtu.be/{id}
        if (str_contains($host, 'youtu.be')) {
            $id = trim($path, '/');
            $id = explode('/', $id)[0] ?? '';

            return self::isValidId($id) ? $id : null;
        }

        if (! str_contains($host, 'youtube.com') && ! str_contains($host, 'youtube-nocookie.com')) {
            return null;
        }

        // youtube.com/watch?v={id}
        if (! empty($parts['query'])) {
            parse_str($parts['query'], $query);
            if (! empty($query['v']) && self::isValidId($query['v'])) {
                return $query['v'];
            }
        }

        // /embed/{id}, /shorts/{id}, /live/{id}, /v/{id}
        if (preg_match('#/(?:embed|shorts|live|v)/([a-zA-Z0-9_-]{11})#', $path, $matches)) {
            return $matches[1];
        }

        return null;
    }

    private static function isValidId(string $id): bool
    {
        return (bool) preg_match('/^[a-zA-Z0-9_-]{11}$/', $id);
    }
}
