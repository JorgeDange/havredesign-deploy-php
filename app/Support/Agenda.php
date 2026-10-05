<?php

declare(strict_types=1);

namespace App\Support;

use App\Core\Database;

/**
 * Agenda — reimplementação pura do AgendaService do Laravel.
 *
 * Fonte de verdade: settings.agenda (+ availability_slots como template
 * semanal; + appointment_blackouts; + marcações existentes).
 */
final class Agenda
{
    /**
     * Configuração no formato camelCase usado pelo front-end.
     *
     * @return array<string, mixed>
     */
    public static function config(): array
    {
        $a = Site::json('agenda', []);
        $tipos = [];

        foreach (($a['tipos'] ?? []) as $codigo => $t) {
            if (!is_array($t)) {
                continue;
            }
            $tipos[$codigo] = [
                'label'          => (string) ($t['label'] ?? $codigo),
                'precisaEndereco' => (bool) ($t['precisa_endereco'] ?? ($t['precisaEndereco'] ?? false)),
                'nota'           => isset($t['nota']) ? (string) $t['nota'] : null,
            ];
        }

        return [
            'horarios'          => array_values($a['horarios'] ?? ['09:00', '10:00', '11:00', '14:00', '15:00', '16:00', '17:00']),
            'diasIndisponiveis' => array_map('intval', $a['dias_indisponiveis'] ?? [0, 6]),
            'horariosSabado'    => array_values($a['horarios_sabado'] ?? []),
            'tipoPredefinido'   => (string) ($a['tipo_predefinido'] ?? 'ONLINE'),
            'tipos'             => $tipos,
        ];
    }

    /**
     * Horários-base de uma data (sem marcações ainda).
     *
     * @return array<int, string>
     */
    public static function horariosDoDia(string $data): array
    {
        $config = self::config();
        $dia = new \DateTimeImmutable($data);

        if (in_array((int) $dia->format('w'), $config['diasIndisponiveis'], true)) {
            return [];
        }

        $templates = Database::todos(
            'SELECT weekday, start_time, end_time, slot_minutes FROM availability_slots
             WHERE active = 1 AND weekday = ? ORDER BY start_time',
            [(int) $dia->format('w')]
        );

        if ($templates !== []) {
            $horarios = [];

            foreach ($templates as $t) {
                $inicio = new \DateTimeImmutable('2000-01-01 ' . $t['start_time']);
                $fim    = new \DateTimeImmutable('2000-01-01 ' . $t['end_time']);
                $passo  = max(1, (int) $t['slot_minutes']);

                while ($inicio < $fim) {
                    $horarios[] = $inicio->format('H:i');
                    $inicio = $inicio->modify('+' . $passo . ' minutes');
                }
            }

            return array_values(array_unique($horarios));
        }

        if ((int) $dia->format('w') === 6) {
            return $config['horariosSabado'] !== [] ? $config['horariosSabado'] : [];
        }

        return $config['horarios'];
    }

    /**
     * Horários livres de uma data: base − ocupados − blackout − passados.
     *
     * @return array<int, string>
     */
    public static function livres(string $data): array
    {
        $base = self::horariosDoDia($data);

        if ($base === []) {
            return [];
        }

        $bloqueio = Database::escalar('SELECT COUNT(*) FROM appointment_blackouts WHERE date = ?', [$data]);
        if ((int) $bloqueio > 0) {
            return [];
        }

        $ocupados = Database::todos(
            "SELECT appt_time FROM appointments WHERE appt_date = ? AND status <> 'CANCELLED'",
            [$data]
        );
        $ocupados = array_map(
            static fn (array $r): string => substr((string) $r['appt_time'], 0, 5),
            $ocupados
        );

        $agora = new \DateTimeImmutable('now');
        $hoje = $agora->format('Y-m-d');
        $limite = $agora->modify('+1 hour');

        return array_values(array_filter($base, static function (string $h) use ($ocupados, $data, $limite, $hoje): bool {
            if (in_array($h, $ocupados, true)) {
                return false;
            }

            // Antecedência mínima: 1 hora (§7.5 — backend.md)
            if ($data === $hoje && new \DateTimeImmutable($data . ' ' . $h) <= $limite) {
                return false;
            }

            return true;
        }));
    }

    /**
     * Disponibilidade de um mês: [data => horários livres] só para dias com vagas.
     *
     * @return array<string, array<int, string>>
     */
    public static function mes(int $ano, int $mes): array
    {
        $hoje = new \DateTimeImmutable('now');
        $dias = [];

        $primeiro = new \DateTimeImmutable(sprintf('%04d-%02d-01', $ano, $mes));
        $total = (int) $primeiro->format('t');

        for ($i = 0; $i < $total; $i++) {
            $dia = $primeiro->modify('+' . $i . ' days');
            $data = $dia->format('Y-m-d');

            if ($dia < $hoje->setTime(0, 0)) {
                continue;
            }

            $livres = self::livres($data);

            if ($livres !== []) {
                $dias[$data] = $livres;
            }
        }

        return $dias;
    }

    /**
     * Existe já uma marcação nesse dia/hora (qualquer estado)?
     */
    public static function ocupado(string $data, string $hora): bool
    {
        return (int) Database::escalar(
            'SELECT COUNT(*) FROM appointments WHERE appt_date = ? AND appt_time = ?',
            [$data, $hora]
        ) > 0;
    }

    /**
     * Rótulo do tipo de reunião para e-mails e flash.
     */
    public static function rotuloTipo(string $codigo): string
    {
        return self::config()['tipos'][$codigo]['label'] ?? $codigo;
    }
}
