<?php

declare(strict_types=1);

namespace App\Core;

/**
 * Database — ligação PDO singleton com prepared statements.
 * REGRA DE OURO: nunca concatenar variáveis em SQL.
 */
final class Database
{
    private static ?\PDO $ligacao = null;

    private function __construct()
    {
    }

    /**
     * Devolve a ligação PDO (cria-a na primeira chamada).
     */
    public static function ligacao(): \PDO
    {
        if (self::$ligacao instanceof \PDO) {
            return self::$ligacao;
        }

        $dsn = sprintf(
            'mysql:host=%s;port=%d;dbname=%s;charset=%s',
            Config::obter('DB_HOST', '127.0.0.1'),
            Config::inteiro('DB_PORT', 3306),
            Config::obter('DB_NAME', 'havre_design'),
            Config::obter('DB_CHARSET', 'utf8mb4')
        );

        try {
            self::$ligacao = new \PDO($dsn, Config::obter('DB_USER', 'root'), Config::obter('DB_PASS'), [
                \PDO::ATTR_ERRMODE            => \PDO::ERRMODE_EXCEPTION,
                \PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_ASSOC,
                \PDO::ATTR_EMULATE_PREPARES   => false, // prepared statements reais
                \PDO::ATTR_STRINGIFY_FETCHES  => false,
            ]);
        } catch (\PDOException $e) {
            Logger::erro('db.ligacao_falhou', ['mensagem' => $e->getMessage()]);
            // Mensagem genérica — nunca expor detalhes da BD ao utilizador
            throw new \RuntimeException('Erro de ligação à base de dados.', 0, $e);
        }

        return self::$ligacao;
    }

    /**
     * Executa uma query com parâmetros ligados (prepared statement).
     *
     * @param array<string|int, mixed> $parametros  valores ligados por posição (?) ou nome (:nome)
     * @return int linhas afetadas (INSERT devolve o id com ultimoId())
     */
    public static function executar(string $sql, array $parametros = []): int
    {
        $stmt = self::ligacao()->prepare($sql);
        $stmt->execute($parametros);

        return $stmt->rowCount();
    }

    /**
     * Devolve todas as linhas de uma query.
     *
     * @return array<int, array<string, mixed>>
     */
    public static function todos(string $sql, array $parametros = []): array
    {
        $stmt = self::ligacao()->prepare($sql);
        $stmt->execute($parametros);

        return $stmt->fetchAll();
    }

    /**
     * Devolve a primeira linha ou null.
     *
     * @return array<string, mixed>|null
     */
    public static function um(string $sql, array $parametros = []): ?array
    {
        $stmt = self::ligacao()->prepare($sql);
        $stmt->execute($parametros);
        $linha = $stmt->fetch();

        return $linha === false ? null : $linha;
    }

    /**
     * Devolve um único valor escalar (ex.: COUNT(*)).
     */
    public static function escalar(string $sql, array $parametros = []): mixed
    {
        $stmt = self::ligacao()->prepare($sql);
        $stmt->execute($parametros);
        $valor = $stmt->fetchColumn();

        return $valor === false ? null : $valor;
    }

    /**
     * ID da última inserção na ligação actual.
     */
    public static function ultimoId(): string
    {
        return self::ligacao()->lastInsertId();
    }

    /**
     * Corre um bloco dentro de transacção; faz rollback se lançar exceção.
     *
     * @template T
     * @param callable(): T $bloco
     * @return T
     */
    public static function transacao(callable $bloco): mixed
    {
        $pdo = self::ligacao();
        $pdo->beginTransaction();
        try {
            $resultado = $bloco();
            $pdo->commit();

            return $resultado;
        } catch (\Throwable $e) {
            $pdo->rollBack();
            throw $e;
        }
    }
}
