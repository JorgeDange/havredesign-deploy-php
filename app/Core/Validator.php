<?php

declare(strict_types=1);

namespace App\Core;

/**
 * Validator — validação server-side com mensagens em português.
 *
 * Regras: required, email, min, max, numeric, integer, date, date_format,
 *         in, unique, confirmed, nullable, string, url, phone, honeypot
 */
final class Validator
{
    /** @var array<string, string> erros por campo */
    private array $erros = [];

    /** @var array<string, mixed> dados limpos (cast aplicado) */
    private array $limpos = [];

    /**
     * @param array<string, mixed> $dados   input do utilizador
     * @param array<string, string> $regras "campo" => "required|email|max:255"
     * @param array<string, string> $nomes  rótulos legíveis (opcional)
     */
    private function __construct(
        private array $dados,
        array $regras,
        private array $nomes = [],
    ) {
        foreach ($regras as $campo => $regraString) {
            $this->validarCampo($campo, $regraString);
        }
    }

    public static function fazer(array $dados, array $regras, array $nomes = []): self
    {
        return new self($dados, $regras, $nomes);
    }

    // ------------------------------------------------------------------
    // Resultado
    // ------------------------------------------------------------------

    public static function falhou(array $dados, array $regras, array $nomes = []): bool
    {
        return !self::fazer($dados, $regras, $nomes)->passou();
    }

    public function passou(): bool
    {
        return $this->erros === [];
    }

    /**
     * @return array<string, string> erros por campo (mensagem PT)
     */
    public function erros(): array
    {
        return $this->erros;
    }

    /**
     * Primeira mensagem de erro (para flash genérico).
     */
    public function primeiroErro(): ?string
    {
        return $this->erros === [] ? null : (string) reset($this->erros);
    }

    /**
     * Dados validados (com casts: inteiros normalizados).
     *
     * @return array<string, mixed>
     */
    public function limpos(): array
    {
        return $this->limpos !== [] ? $this->limpos : $this->dados;
    }

    public function valor(string $campo, mixed $padrao = null): mixed
    {
        return $this->limpos[$campo] ?? $this->dados[$campo] ?? $padrao;
    }

    // ------------------------------------------------------------------
    // Motor
    // ------------------------------------------------------------------

    private function rotulo(string $campo): string
    {
        return $this->nomes[$campo] ?? $campo;
    }

    public function erro(string $campo, string $mensagem): void
    {
        if (!isset($this->erros[$campo])) {
            $this->erros[$campo] = $mensagem;
        }
    }

    private function valorDoDado(string $campo): mixed
    {
        return $this->dados[$campo] ?? null;
    }

    private function presente(mixed $valor): bool
    {
        if ($valor === null) {
            return false;
        }
        if (is_string($valor)) {
            return trim($valor) !== '';
        }

        return true;
    }

    private function validarCampo(string $campo, string $regraString): void
    {
        $regras = explode('|', $regraString);
        $valor  = $this->valorDoDado($campo);
        $rotulo = $this->rotulo($campo);

        $obrigatorio = in_array('required', $regras, true);
        $nullable    = in_array('nullable', $regras, true);

        // Campo em falta
        if (!$this->presente($valor)) {
            if ($obrigatorio) {
                $this->erro($campo, "O campo {$rotulo} é obrigatório.");
            } elseif ($nullable || !$obrigatorio) {
                $this->limpos[$campo] = null;
            }

            return;
        }

        // String normalizada
        $texto = is_string($valor) ? trim($valor) : $valor;
        $this->limpos[$campo] = $texto;

        foreach ($regras as $regra) {
            [$nome, $parametro] = array_pad(explode(':', $regra, 2), 2, null);

            switch ($nome) {
                case 'required':
                case 'nullable':
                case 'string':
                    break;

                case 'email':
                    if (!filter_var((string) $texto, FILTER_VALIDATE_EMAIL)) {
                        $this->erro($campo, "O campo {$rotulo} deve ser um e-mail válido.");
                    }
                    break;

                case 'min':
                    if (mb_strlen((string) $texto) < (int) $parametro) {
                        $this->erro($campo, "O campo {$rotulo} deve ter pelo menos {$parametro} caracteres.");
                    }
                    break;

                case 'max':
                    if (mb_strlen((string) $texto) > (int) $parametro) {
                        $this->erro($campo, "O campo {$rotulo} não pode ter mais de {$parametro} caracteres.");
                    }
                    break;

                case 'numeric':
                    if (!is_numeric($texto)) {
                        $this->erro($campo, "O campo {$rotulo} deve ser numérico.");
                    }
                    break;

                case 'integer':
                    $inteiro = filter_var($texto, FILTER_VALIDATE_INT);
                    if ($inteiro === false) {
                        $this->erro($campo, "O campo {$rotulo} deve ser um número inteiro.");
                    } else {
                        $this->limpos[$campo] = $inteiro;
                    }
                    break;

                case 'date':
                    $d = \DateTime::createFromFormat('Y-m-d', (string) $texto);
                    if ($d === false || $d->format('Y-m-d') !== $texto) {
                        $this->erro($campo, "O campo {$rotulo} deve ser uma data válida (AAAA-MM-DD).");
                    }
                    break;

                case 'date_format':
                    $d = \DateTime::createFromFormat((string) $parametro, (string) $texto);
                    if ($d === false || $d->format((string) $parametro) !== $texto) {
                        $this->erro($campo, "O campo {$rotulo} não está no formato esperado ({$parametro}).");
                    }
                    break;

                case 'in':
                    $opcoes = explode(',', (string) $parametro);
                    if (!in_array((string) $texto, $opcoes, true)) {
                        $this->erro($campo, "O campo {$rotulo} tem um valor inválido.");
                    }
                    break;

                case 'confirmed':
                    $outro = $this->valorDoDado($campo . '_confirmation');
                    if ((string) $texto !== (string) $outro) {
                        $this->erro($campo, "A confirmação do campo {$rotulo} não coincide.");
                    }
                    break;

                case 'unique':
                    // unique:tabela,coluna[,id_excluir]
                    $partes = explode(',', (string) $parametro);
                    $tabela = $partes[0] ?? '';
                    $coluna = $partes[1] ?? $campo;
                    $excluir = $partes[2] ?? null;

                    if ($tabela !== '' && preg_match('/^[a-zA-Z_][a-zA-Z0-9_]*$/', $tabela) && preg_match('/^[a-zA-Z_][a-zA-Z0-9_]*$/', $coluna)) {
                        $sql    = "SELECT COUNT(*) FROM `{$tabela}` WHERE `{$coluna}` = ?";
                        $params = [$texto];
                        if ($excluir !== null && $excluir !== '') {
                            $sql .= ' AND id != ?';
                            $params[] = $excluir;
                        }
                        $total = (int) Database::escalar($sql, $params);
                        if ($total > 0) {
                            $this->erro($campo, "O valor do campo {$rotulo} já está a ser usado.");
                        }
                    }
                    break;

                case 'url':
                    if (!filter_var((string) $texto, FILTER_VALIDATE_URL)) {
                        $this->erro($campo, "O campo {$rotulo} deve ser um URL válido.");
                    }
                    break;

                case 'honeypot':
                    // Campo armadilha: tem de ficar vazio (max:0 no Laravel)
                    if ((string) $texto !== '') {
                        $this->erro($campo, 'Submissão rejeitada.');
                        Logger::aviso('spam.honeypot', ['campo' => $campo]);
                    }
                    break;

                // Regra desconhecida — ignorada (compatibilidade progressiva)
            }
        }
    }
}
