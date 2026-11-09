<?php

namespace App\Support\Palpites;

class GeradorDePalpites
{
    public function __construct(private Catalogo $catalogo)
    {
    }

    public function gerar(string $modalidadeId, ?int $grupo = null): array
    {
        $modalidade = $this->catalogo->modalidade($modalidadeId);

        if ($modalidade === null) {
            throw new \InvalidArgumentException('Modalidade desconhecida.');
        }

        $escolhido = null;

        if ($grupo !== null) {
            if ($grupo < 1 || $grupo > 25) {
                throw new \InvalidArgumentException('Grupo fora da faixa de 1 a 25.');
            }

            if ($modalidade['usa_bicho']) {
                $escolhido = $this->catalogo->porGrupo($grupo);
            }
        }

        $resultado = match ($modalidadeId) {
            'milhar' => $this->milhar($escolhido),
            'centena' => $this->centena($escolhido),
            'dezena' => $this->dezena($escolhido),
            'grupo' => $this->grupo($escolhido),
            'duque-grupo' => $this->combinarGrupos(2, $escolhido, ' · ', ' + '),
            'terno-grupo' => $this->combinarGrupos(3, $escolhido, ' · ', ' + '),
            'duque-dezena' => $this->combinarDezenas(2),
            'terno-dezena' => $this->combinarDezenas(3),
            'passe-vai' => $this->combinarGrupos(2, $escolhido, ' → ', ' → '),
            'passe-vai-e-vem' => $this->combinarGrupos(2, $escolhido, ' ⇄ ', ' ⇄ '),
            default => throw new \InvalidArgumentException('Modalidade desconhecida.'),
        };

        return [
            'id' => $this->novoId(),
            'modalidade' => $modalidadeId,
            'linha' => $resultado['linha'],
            'bichos' => $resultado['bichos'],
            'createdAt' => (int) floor(microtime(true) * 1000),
        ];
    }

    private function milhar(?array $escolhido): array
    {
        if ($escolhido === null) {
            $linha = $this->pad($this->int(10000), 4);

            return [
                'linha' => $linha,
                'bichos' => $this->catalogo->porDezena((int) substr($linha, -2))['nome'],
            ];
        }

        $dezena = $this->escolherDezena($escolhido);

        return [
            'linha' => $this->pad($this->int(100), 2).$this->pad($dezena, 2),
            'bichos' => $escolhido['nome'],
        ];
    }

    private function centena(?array $escolhido): array
    {
        if ($escolhido === null) {
            $linha = $this->pad($this->int(1000), 3);

            return [
                'linha' => $linha,
                'bichos' => $this->catalogo->porDezena((int) substr($linha, -2))['nome'],
            ];
        }

        $dezena = $this->escolherDezena($escolhido);

        return [
            'linha' => (string) $this->int(10).$this->pad($dezena, 2),
            'bichos' => $escolhido['nome'],
        ];
    }

    private function dezena(?array $escolhido): array
    {
        $dezena = $escolhido === null
            ? $this->int(100)
            : $this->escolherDezena($escolhido);

        return [
            'linha' => $this->pad($dezena, 2),
            'bichos' => $this->catalogo->porDezena($dezena)['nome'],
        ];
    }

    private function grupo(?array $escolhido): array
    {
        $item = $escolhido ?? $this->grupoAleatorio();

        return [
            'linha' => $this->pad((int) $item['grupo'], 2).' · '.$item['nome'],
            'bichos' => $item['nome'],
        ];
    }

    private function combinarGrupos(int $quantidade, ?array $escolhido, string $sepLinha, string $sepBicho): array
    {
        if ($escolhido === null) {
            $itens = $this->sample($this->catalogo->grupos(), $quantidade);
        } else {
            $resto = array_values(array_filter(
                $this->catalogo->grupos(),
                fn (array $item) => (int) $item['grupo'] !== (int) $escolhido['grupo']
            ));
            $itens = array_merge([$escolhido], $this->sample($resto, $quantidade - 1));
        }

        return [
            'linha' => implode($sepLinha, array_map(
                fn (array $item) => $this->pad((int) $item['grupo'], 2),
                $itens
            )),
            'bichos' => implode($sepBicho, array_map(
                fn (array $item) => $item['nome'],
                $itens
            )),
        ];
    }

    private function combinarDezenas(int $quantidade): array
    {
        $dezenas = $this->sample(range(0, 99), $quantidade);

        return [
            'linha' => implode(' · ', array_map(fn (int $dezena) => $this->pad($dezena, 2), $dezenas)),
            'bichos' => implode(' + ', array_map(
                fn (int $dezena) => $this->catalogo->porDezena($dezena)['nome'],
                $dezenas
            )),
        ];
    }

    private function escolherDezena(array $grupo): int
    {
        $dezenas = array_map('intval', $grupo['dezenas']);

        return $dezenas[$this->int(count($dezenas))];
    }

    private function grupoAleatorio(): array
    {
        $grupos = $this->catalogo->grupos();

        return $grupos[$this->int(count($grupos))];
    }

    private function sample(array $pool, int $quantidade): array
    {
        $pool = array_values($pool);

        if ($quantidade > count($pool)) {
            throw new \InvalidArgumentException('Não há itens suficientes para o sorteio.');
        }

        $saida = [];

        for ($i = 0; $i < $quantidade; $i++) {
            $indice = $this->int(count($pool));
            $saida[] = $pool[$indice];
            array_splice($pool, $indice, 1);
        }

        return $saida;
    }

    private function int(int $limite): int
    {
        if ($limite < 1) {
            throw new \InvalidArgumentException('Limite inválido.');
        }

        return random_int(0, $limite - 1);
    }

    private function pad(int $numero, int $tamanho): string
    {
        return str_pad((string) $numero, $tamanho, '0', STR_PAD_LEFT);
    }

    private function novoId(): string
    {
        $ms = (int) floor(microtime(true) * 1000);

        return base_convert((string) $ms, 10, 36).'-'.substr(bin2hex(random_bytes(3)), 0, 6);
    }
}
