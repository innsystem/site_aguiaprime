<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use App\Support\YoutubeUrl;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class SettingController extends Controller
{
    public function edit()
    {
        return view('admin.settings.edit', [
            'settings' => Setting::current(),
        ]);
    }

    public function update(Request $request)
    {
        $data = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:2000'],
            'meta_keywords' => ['nullable', 'string', 'max:500'],
            'whatsapp_url' => ['nullable', 'url', 'max:500'],
            'platform_url' => ['nullable', 'url', 'max:500'],
            'video_url' => ['nullable', 'string', 'max:500'],
        ]);

        $data['video_url'] = $this->normalizeVideoUrl($data['video_url'] ?? null);

        $settings = Setting::query()->first();

        if ($settings) {
            $settings->update($data);
        } else {
            Setting::query()->create($data);
        }

        return redirect()
            ->route('admin.settings.edit')
            ->with('success', 'Settings atualizadas com sucesso.');
    }

    private function normalizeVideoUrl(?string $url): ?string
    {
        $url = trim((string) $url);

        if ($url === '') {
            return null;
        }

        $embed = YoutubeUrl::toEmbed($url);

        if (! $embed) {
            throw ValidationException::withMessages([
                'video_url' => 'Informe um link válido do YouTube (watch, youtu.be, shorts ou embed).',
            ]);
        }

        return $embed;
    }
}
