<?php

namespace Tests\Unit;

use App\Support\Palpites\GeradorDePalpites;
use InvalidArgumentException;
use Tests\TestCase;

class GeradorDePalpitesTest extends TestCase
{
    public function test_milhar_centena_e_dezena_tem_tamanho_e_zero_a_esquerda(): void
    {
        $gerador = app(GeradorDePalpites::class);

        for ($i = 0; $i < 30; $i++) {
            $milhar = $gerador->gerar('milhar');
            $centena = $gerador->gerar('centena');
            $dezena = $gerador->gerar('dezena');

            $this->assertMatchesRegularExpression('/^\d{4}$/', $milhar['linha']);
            $this->assertMatchesRegularExpression('/^\d{3}$/', $centena['linha']);
            $this->assertMatchesRegularExpression('/^\d{2}$/', $dezena['linha']);
            $this->assertNotSame('', $milhar['bichos']);
            $this->assertNotSame('', $centena['bichos']);
            $this->assertNotSame('', $dezena['bichos']);
        }
    }

    public function test_milhar_com_grupo_15_tem_quatro_digitos_do_jacare(): void
    {
        $gerador = app(GeradorDePalpites::class);

        for ($i = 0; $i < 40; $i++) {
            $palpite = $gerador->gerar('milhar', 15);

            $this->assertSame(4, strlen($palpite['linha']));
            $this->assertSame('Jacaré', $palpite['bichos']);
            $this->assertContains((int) substr($palpite['linha'], -2), [57, 58, 59, 60]);
        }
    }

    public function test_centena_com_grupo_15_tem_tres_digitos_do_jacare(): void
    {
        $gerador = app(GeradorDePalpites::class);

        for ($i = 0; $i < 40; $i++) {
            $palpite = $gerador->gerar('centena', 15);

            $this->assertSame(3, strlen($palpite['linha']));
            $this->assertSame('Jacaré', $palpite['bichos']);
            $this->assertContains((int) substr($palpite['linha'], -2), [57, 58, 59, 60]);
        }
    }

    public function test_dezena_da_vaca_pode_sair_00(): void
    {
        $gerador = app(GeradorDePalpites::class);
        $viuZero = false;

        for ($i = 0; $i < 80; $i++) {
            $palpite = $gerador->gerar('dezena', 25);
            $this->assertSame('Vaca', $palpite['bichos']);
            $this->assertContains($palpite['linha'], ['97', '98', '99', '00']);
            if ($palpite['linha'] === '00') {
                $viuZero = true;
            }
        }

        $this->assertTrue($viuZero);
    }

    public function test_grupos_e_dezenas_nao_se_repetem(): void
    {
        $gerador = app(GeradorDePalpites::class);

        foreach (['duque-grupo', 'terno-grupo', 'passe-vai', 'passe-vai-e-vem'] as $modalidade) {
            $palpite = $gerador->gerar($modalidade);
            $partes = preg_split('/ · | → | ⇄ /', $palpite['linha']);
            $this->assertGreaterThan(1, count($partes));
            $this->assertSame($partes, array_values(array_unique($partes)));
        }

        foreach (['duque-dezena', 'terno-dezena'] as $modalidade) {
            $palpite = $gerador->gerar($modalidade);
            $partes = explode(' · ', $palpite['linha']);
            $esperado = $modalidade === 'duque-dezena' ? 2 : 3;
            $this->assertCount($esperado, $partes);
            $this->assertSame($partes, array_values(array_unique($partes)));
        }
    }

    public function test_separadores_de_duque_e_passes(): void
    {
        $gerador = app(GeradorDePalpites::class);

        $duque = $gerador->gerar('duque-grupo', 15);
        $this->assertStringStartsWith('15 · ', $duque['linha']);
        $this->assertStringNotContainsString('⇄', $duque['linha']);
        $this->assertStringNotContainsString('→', $duque['linha']);
        $this->assertStringStartsWith('Jacaré + ', $duque['bichos']);

        $vai = $gerador->gerar('passe-vai', 15);
        $this->assertStringContainsString(' → ', $vai['linha']);
        $this->assertStringContainsString(' → ', $vai['bichos']);
        $this->assertStringNotContainsString('⇄', $vai['linha']);

        $vem = $gerador->gerar('passe-vai-e-vem', 15);
        $this->assertStringContainsString(' ⇄ ', $vem['linha']);
        $this->assertStringContainsString(' ⇄ ', $vem['bichos']);

        $terno = $gerador->gerar('terno-grupo', 15);
        $this->assertCount(3, explode(' · ', $terno['linha']));
        $this->assertStringContainsString(' + ', $terno['bichos']);
    }

    public function test_modalidade_e_grupo_invalidos_falham(): void
    {
        $gerador = app(GeradorDePalpites::class);

        try {
            $gerador->gerar('nao-existe');
            $this->fail('Modalidade desconhecida deveria falhar.');
        } catch (InvalidArgumentException $e) {
            $this->assertSame('Modalidade desconhecida.', $e->getMessage());
        }

        try {
            $gerador->gerar('milhar', 99);
            $this->fail('Grupo 99 deveria falhar.');
        } catch (InvalidArgumentException $e) {
            $this->assertSame('Grupo fora da faixa de 1 a 25.', $e->getMessage());
        }
    }
}
