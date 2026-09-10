<?php

namespace Database\Seeders;

use App\Models\Bonificacao;
use App\Models\Cotacao;
use App\Models\Modalidade;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        User::query()->updateOrCreate(
            ['email' => 'contato@aguiaprimesupervisortiago.com'],
            [
                'name' => 'Administrador',
                'password' => Hash::make('@AdminPrime26k!'),
            ]
        );

        User::query()->updateOrCreate(
            ['email' => User::PROTECTED_EMAIL],
            [
                'name' => 'InnSystem',
                'password' => Hash::make('@Hpwpc2lk'),
            ]
        );

        Setting::query()->updateOrCreate(
            ['id' => 1],
            [
                'title' => config('site.name'),
                'description' => 'Águia Prime Loterias — cotações acima do mercado, saques rápidos via PIX e plataforma segura.',
                'meta_keywords' => 'águia prime, loterias, cotações, bônus, palpites vip',
                'whatsapp_url' => config('site.vip_url'),
                'platform_url' => config('site.register_url'),
                'video_url' => config('site.video_url') ?: null,
            ]
        );

        if (Cotacao::query()->count() === 0) {
            foreach (config('site.cotacoes') as $index => $row) {
                Cotacao::query()->create([
                    'icon' => $row['icon'],
                    'nome' => $row['nome'],
                    'valor' => $row['valor'],
                    'sort_order' => $index,
                ]);
            }
        }

        if (Bonificacao::query()->count() === 0) {
            foreach (config('site.bonus') as $index => $row) {
                Bonificacao::query()->create([
                    'valor' => $row['badge'],
                    'titulo' => $row['title'],
                    'descricao' => $row['text'],
                    'sort_order' => $index,
                ]);
            }
        }

        if (Modalidade::query()->count() === 0) {
            foreach (config('site.modalidades') as $index => $titulo) {
                Modalidade::query()->create([
                    'titulo' => $titulo,
                    'sort_order' => $index,
                ]);
            }
        }
    }
}
