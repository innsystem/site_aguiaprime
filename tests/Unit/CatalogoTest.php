<?php

namespace Tests\Unit;

use App\Support\Palpites\Catalogo;
use Tests\TestCase;

class CatalogoTest extends TestCase
{
    public function test_dezenas_mapeiam_para_o_bicho_certo(): void
    {
        $catalogo = new Catalogo;

        $this->assertSame('Vaca', $catalogo->porDezena(0)['nome']);
        $this->assertSame(25, $catalogo->porDezena(0)['grupo']);
        $this->assertSame('Jacaré', $catalogo->porDezena(60)['nome']);
        $this->assertSame(15, $catalogo->porDezena(60)['grupo']);
        $this->assertSame('Avestruz', $catalogo->porDezena(1)['nome']);
    }

    public function test_dezenas_dos_grupos_15_e_25(): void
    {
        $catalogo = new Catalogo;

        $this->assertSame([57, 58, 59, 60], $catalogo->dezenasDoGrupo(15));
        $this->assertSame([97, 98, 99, 0], $catalogo->dezenasDoGrupo(25));
    }
}
