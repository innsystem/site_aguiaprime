<?php

namespace App\Models;

use App\Support\YoutubeUrl;
use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    protected $fillable = [
        'title',
        'description',
        'meta_keywords',
        'whatsapp_url',
        'platform_url',
        'video_url',
    ];

    public static function current(): self
    {
        return static::query()->first() ?? new static([
            'title' => config('site.name'),
            'description' => 'Águia Prime Loterias — cotações acima do mercado, saques rápidos via PIX e plataforma segura.',
            'meta_keywords' => null,
            'whatsapp_url' => config('site.vip_url'),
            'platform_url' => config('site.register_url'),
            'video_url' => config('site.video_url'),
        ]);
    }

    public function getVideoEmbedUrlAttribute(): ?string
    {
        return YoutubeUrl::toEmbed($this->video_url)
            ?? YoutubeUrl::toEmbed(config('site.video_url'));
    }
}
