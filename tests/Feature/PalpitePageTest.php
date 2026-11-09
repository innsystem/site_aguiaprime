<?php

namespace Tests\Feature;

use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Tests\TestCase;

class PalpitePageTest extends TestCase
{
    public function test_pagina_responde_sem_indexar(): void
    {
        $this->withServerVariables(['REMOTE_ADDR' => '203.0.113.5']);

        $response = $this->get('/palpites');

        $response->assertOk();
        $response->assertSee('Início', false);
        $response->assertSee('Gerador', false);
        $response->assertSee('Manual', false);
        $response->assertSee('Histórico', false);
        $response->assertSee('Enviar', false);
        $response->assertSee('Avestruz', false);
        $response->assertSee('PalpitesBoot', false);
        $response->assertSee('noindex, nofollow', false);
        $response->assertSee('Gerar imagem', false);
        $response->assertSee('Baixar imagem', false);
        $response->assertSee('Quadrado', false);
        $response->assertSee('Story', false);
        $response->assertDontSee('Imagem em breve', false);
        $response->assertHeader('X-Robots-Tag', 'noindex, nofollow');
        $response->assertHeader('Cache-Control', 'no-store, private');
    }

    public function test_post_desligado_retorna_404(): void
    {
        config(['palpites.api_gerar_habilitada' => false]);
        $this->withoutMiddleware(VerifyCsrfToken::class);
        $this->withServerVariables(['REMOTE_ADDR' => '203.0.113.6']);

        $this->postJson('/palpites/gerar', ['modalidade' => 'dezena'])->assertNotFound();
    }

    public function test_post_valido_devolve_o_contrato(): void
    {
        config(['palpites.api_gerar_habilitada' => true]);
        $this->withoutMiddleware(VerifyCsrfToken::class);
        $this->withServerVariables(['REMOTE_ADDR' => '203.0.113.7']);

        $this->postJson('/palpites/gerar', ['modalidade' => 'milhar', 'grupo' => 15])
            ->assertOk()
            ->assertJsonPath('modalidade', 'milhar')
            ->assertJsonPath('bichos', 'Jacaré')
            ->assertJsonStructure(['id', 'modalidade', 'linha', 'bichos', 'createdAt']);
    }

    public function test_post_invalido_devolve_422(): void
    {
        config(['palpites.api_gerar_habilitada' => true]);
        $this->withoutMiddleware(VerifyCsrfToken::class);
        $this->withServerVariables(['REMOTE_ADDR' => '203.0.113.8']);

        $this->postJson('/palpites/gerar', ['modalidade' => 'foo'])->assertStatus(422);
        $this->postJson('/palpites/gerar', ['modalidade' => 'milhar', 'grupo' => 99])->assertStatus(422);
    }

    public function test_post_esta_no_grupo_web_com_csrf(): void
    {
        $rota = app('router')->getRoutes()->getByName('palpites.gerar');

        $this->assertNotNull($rota);
        $this->assertContains('web', $rota->gatherMiddleware());
        $this->assertContains('throttle:palpites-gerar', $rota->gatherMiddleware());
    }

    public function test_post_respeita_o_limite_por_minuto(): void
    {
        config(['palpites.api_gerar_habilitada' => true]);
        $this->withoutMiddleware(VerifyCsrfToken::class);
        $this->withServerVariables(['REMOTE_ADDR' => '203.0.113.21']);

        for ($i = 0; $i < 20; $i++) {
            $this->postJson('/palpites/gerar', ['modalidade' => 'grupo'])->assertOk();
        }

        $this->postJson('/palpites/gerar', ['modalidade' => 'grupo'])
            ->assertStatus(429)
            ->assertJsonPath('message', 'Muitas tentativas. Aguarde um minuto.');
    }

    public function test_home_tem_atalho_para_o_gerador(): void
    {
        $this->get('/')
            ->assertOk()
            ->assertSee('Gerar Palpites', false)
            ->assertSee('/palpites', false);
    }
}
