<?php

namespace App\Support\Palpites;

class Catalogo
{
    public function grupos(): array
    {
        return config('palpites.grupos', []);
    }

    public function modalidades(): array
    {
        return config('palpites.modalidades', []);
    }

    public function modalidade(string $id): ?array
    {
        foreach ($this->modalidades() as $modalidade) {
            if ($modalidade['id'] === $id) {
                return $modalidade;
            }
        }

        return null;
    }

    public function porGrupo(int $grupo): array
    {
        foreach ($this->grupos() as $item) {
            if ((int) $item['grupo'] === $grupo) {
                return $item;
            }
        }

        throw new \InvalidArgumentException('Grupo fora da faixa de 1 a 25.');
    }

    public function porDezena(int $dezena): array
    {
        if ($dezena < 0 || $dezena > 99) {
            throw new \InvalidArgumentException('Dezena inválida.');
        }

        if ($dezena === 0) {
            return $this->porGrupo(25);
        }

        return $this->porGrupo((int) ceil($dezena / 4));
    }

    public function dezenasDoGrupo(int $grupo): array
    {
        return array_map('intval', $this->porGrupo($grupo)['dezenas']);
    }
}
