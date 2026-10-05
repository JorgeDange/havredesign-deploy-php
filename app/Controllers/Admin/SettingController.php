<?php

declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Core\Database;
use App\Core\Request;
use App\Core\Response;
use App\Core\Session;
use App\Core\Validator;
use App\Core\View;
use App\Support\Site;

/**
 * Definições — settings do site (tabela `settings`, chave/valor).
 *
 * As CHAVES são exactamente as que o site público lê via Site::obter()/Site::json()
 * ('contact.email', 'whatsapp.number', 'social.instagram', …) — por isso a
 * alteração fica visível de imediato no rodapé, no contacto e no WhatsApp.
 * Os campos simples guardam texto simples; a agenda guarda JSON
 * ({horarios, dias_indisponiveis, horarios_sabado, tipo_predefinido, tipos}).
 */
final class SettingController
{
    /**
     * Chaves simples editadas no formulário, agrupadas pelo `group` da BD.
     *
     * @var array<string, string> chave => group
     */
    private const SIMPLES = [
        'contact.email'         => 'contact',
        'contact.phone_1'       => 'contact',
        'contact.phone_2'       => 'contact',
        'contact.phone_3'       => 'contact',
        'contact.whatsapp'      => 'contact',
        'contact.address_full'  => 'contact',
        'contact.hours'         => 'contact',
        'contact.hours_lines'   => 'contact',
        'brand.name'            => 'brand',
        'brand.legal_name'      => 'brand',
        'brand.tagline'         => 'brand',
        'social.instagram'      => 'social',
        'social.facebook'       => 'social',
        'social.linkedin'       => 'social',
        'whatsapp.number'       => 'whatsapp',
        'whatsapp.open_message' => 'whatsapp',
        'process.case_local'    => 'process',
    ];

    /** Formato dos horários: uma linha `HH:MM` por valor. */
    private const REGEX_HORA = '/^([01]\d|2[0-3]):[0-5]\d$/';

    /** Tipos de reunião aceites na agenda (OFFICE foi removido). */
    private const CODIGOS_TIPO = ['SITE', 'ONLINE'];

    /** Caminho fixo do QR Code da página /networking (o URL nunca muda). */
    private const QR_CAMINHO = 'images/qrcode-networking.png';

    /**
     * GET /admin/definicoes — formulário único com todas as secções.
     */
    public function index(Request $request): Response
    {
        $valores = [];
        foreach (array_keys(self::SIMPLES) as $chave) {
            $valores[$chave] = Site::obter($chave);
        }

        $qrCaminho = Site::obter('networking.qr_image') ?: self::QR_CAMINHO;

        return Response::html(View::render('admin/definicoes/index', [
            'valores'   => $valores,
            'agenda'    => Site::json('agenda', []),
            'qrCaminho' => $qrCaminho,
            'qrExiste'  => is_file(dirname(__DIR__, 3) . '/public/' . ltrim($qrCaminho, '/')),
        ]));
    }

    /**
     * POST /admin/definicoes — valida e grava todas as secções de uma vez.
     */
    public function atualizar(Request $request): Response
    {
        $destino = rota('admin.definicoes');

        // ------------------------------------------------------------------
        // 1. Recolha dos campos simples (texto)
        // ------------------------------------------------------------------
        $dados = [];
        foreach (array_keys(self::SIMPLES) as $chave) {
            $dados[$chave] = $this->texto($request, $chave);
        }

        // ------------------------------------------------------------------
        // 2. Recolha da agenda (estruturada)
        // ------------------------------------------------------------------
        $horarios      = $this->texto($request, 'agenda.horarios');
        $horariosSb    = $this->texto($request, 'agenda.horarios_sabado');
        $tipoPredef    = $this->texto($request, 'agenda.tipo_predefinido');
        $diasBrutos    = $this->valorEm($request, 'agenda.dias_indisponiveis');
        $dias          = [];
        $diaInvalido   = false;
        foreach (is_array($diasBrutos) ? $diasBrutos : [] as $diaBruto) {
            if (!is_scalar($diaBruto) || filter_var($diaBruto, FILTER_VALIDATE_INT) === false) {
                $diaInvalido = true;
                continue;
            }
            $dias[] = (int) $diaBruto;
        }
        $dias = array_values(array_unique($dias));

        $rotulos = [];
        $notas   = [];
        foreach (self::CODIGOS_TIPO as $codigo) {
            $rotulos[$codigo] = $this->texto($request, 'agenda.tipos.' . $codigo . '.label');
            $notas[$codigo]   = $this->texto($request, 'agenda.tipos.' . $codigo . '.nota');
        }

        $dados['agenda.horarios']           = $horarios;
        $dados['agenda.horarios_sabado']    = $horariosSb;
        $dados['agenda.tipo_predefinido']   = $tipoPredef;
        $dados['agenda.dias_indisponiveis'] = $dias;
        foreach (self::CODIGOS_TIPO as $codigo) {
            $dados['agenda.tipos.' . $codigo . '.label'] = $rotulos[$codigo];
            $dados['agenda.tipos.' . $codigo . '.nota']   = $notas[$codigo];
        }

        // ------------------------------------------------------------------
        // 3. Validação
        // ------------------------------------------------------------------
        $regras  = [];
        $nomes   = [];
        foreach (array_keys(self::SIMPLES) as $chave) {
            $regras[$chave] = 'nullable|string|max:' . $this->maximo($chave);
            $nomes[$chave]  = $this->rotulo($chave);
        }
        $regras['contact.email']       = 'nullable|email|max:255';
        $regras['social.instagram']    = 'nullable|url|max:255';
        $regras['social.facebook']     = 'nullable|url|max:255';
        $regras['social.linkedin']     = 'nullable|url|max:255';
        $regras['agenda.horarios']           = 'required|string|max:2000';
        $regras['agenda.horarios_sabado']    = 'nullable|string|max:1000';
        $regras['agenda.tipo_predefinido']   = 'required|in:SITE,ONLINE';
        $regras['agenda.dias_indisponiveis'] = 'nullable|string';
        foreach (self::CODIGOS_TIPO as $codigo) {
            $regras['agenda.tipos.' . $codigo . '.label'] = 'required|string|max:120';
            $regras['agenda.tipos.' . $codigo . '.nota']   = 'nullable|string|max:300';
        }

        // As regras acima usam chaves planas; os arrays (dias) não entram.
        unset($regras['agenda.dias_indisponiveis'], $dados['agenda.dias_indisponiveis']);
        $validado = Validator::fazer($dados, $regras, $nomes);

        // Horários: cada linha tem de ser HH:MM
        foreach (['agenda.horarios', 'agenda.horarios_sabado'] as $campo) {
            foreach ($this->linhas($dados[$campo] ?? '') as $linha) {
                if (preg_match(self::REGEX_HORA, $linha) !== 1) {
                    $validado->erro($campo, "Horário «{$linha}» inválido. Use o formato HH:MM (ex.: 09:30).");
                    break;
                }
            }
        }

        // Dias da semana: só inteiros entre 0 e 6
        if ($diaInvalido) {
            $validado->erro('agenda.dias_indisponiveis', 'Dia da semana inválido.');
        }
        foreach ($dias as $dia) {
            if ($dia < 0 || $dia > 6) {
                $validado->erro('agenda.dias_indisponiveis', 'Dia da semana inválido.');
                break;
            }
        }

        // Rótulos obrigatórios mesmo que o POST venha incompleto
        foreach (self::CODIGOS_TIPO as $codigo) {
            if (trim($rotulos[$codigo]) === '') {
                $validado->erro(
                    'agenda.tipos.' . $codigo . '.label',
                    'O rótulo do tipo de reunião não pode ficar vazio.'
                );
            }
        }

        if (!$validado->passou()) {
            Session::colocar('_erros_validacao', $validado->erros());
            Session::colocar('_old_input', $dados + ['agenda.dias_indisponiveis' => $dias]);

            return Response::redirect($destino);
        }

        // ------------------------------------------------------------------
        // 4. Gravação dos campos simples (UPSERT chave/valor)
        // ------------------------------------------------------------------
        foreach (self::SIMPLES as $chave => $grupo) {
            $this->gravar($chave, trim((string) $dados[$chave]), $grupo);
        }

        // ------------------------------------------------------------------
        // 5. Gravação da agenda (JSON)
        // ------------------------------------------------------------------
        $agenda            = Site::json('agenda', []);
        $agenda['horarios']              = $this->linhas($horarios);
        $agenda['horarios_sabado']       = $this->linhas($horariosSb);
        $agenda['dias_indisponiveis']    = $dias;
        $agenda['tipo_predefinido']      = $tipoPredef;
        $agenda['tipos']                 = [];
        foreach (self::CODIGOS_TIPO as $codigo) {
            $agenda['tipos'][$codigo] = [
                'label'            => trim($rotulos[$codigo]),
                // Semântica fixa: só a visita ao local pede morada (não é editável)
                'precisa_endereco' => $codigo === 'SITE',
                'nota'             => trim($notas[$codigo]),
            ];
        }

        $json = json_encode($agenda, JSON_UNESCAPED_UNICODE);
        $this->gravar('agenda', $json === false ? '{}' : $json, 'agenda');

        // ------------------------------------------------------------------
        // 6. QR Code de /networking (opcional — substitui o mesmo ficheiro)
        // ------------------------------------------------------------------
        $this->guardarQr($request);

        Session::flash('ok', 'Definições actualizadas.');

        return Response::redirect($destino);
    }

    // ------------------------------------------------------------------
    // Auxiliares
    // ------------------------------------------------------------------

    /**
     * Valor textual de um campo do POST ('contact.email' ou 'agenda.horarios').
     */
    private function texto(Request $request, string $caminho): string
    {
        $valor = $this->valorEm($request, $caminho);

        if (is_array($valor)) {
            $valor = reset($valor);
        }

        return trim((string) $valor);
    }

    /**
     * Lê um campo do POST por caminho com pontos: 'contact.email' →
     * $_POST['contact']['email'].
     *
     * O formulário usa nomes aninhados ('contact[email]',
     * 'agenda[tipos][SITE][label]') porque o PHP converte pontos e espaços dos
     * nomes em underscores ao popular $_POST — um name="contact.email" seria
     * lido como $_POST['contact_email'].
     */
    private function valorEm(Request $request, string $caminho): mixed
    {
        $direto = $request->bruto($caminho, null);
        if ($direto !== null) {
            return $direto;
        }

        $partes = explode('.', $caminho);
        $atual  = $request->bruto((string) array_shift($partes), null);

        foreach ($partes as $pedaco) {
            if (!is_array($atual) || !array_key_exists($pedaco, $atual)) {
                return null;
            }
            $atual = $atual[$pedaco];
        }

        return $atual;
    }

    /**
     * UPSERT numa linha da tabela settings (chave única).
     */
    private function gravar(string $chave, string $valor, string $grupo): void
    {
        Database::executar(
            'INSERT INTO settings (id, `key`, `value`, `group`)
             VALUES (?, ?, ?, ?)
             ON DUPLICATE KEY UPDATE `value` = VALUES(`value`)',
            [novo_uuid(), $chave, $valor, $grupo]
        );
    }

    /**
     * Uma linha por valor — texto vazio devolve lista vazia.
     *
     * @return list<string>
     */
    private function linhas(string $texto): array
    {
        if (trim($texto) === '') {
            return [];
        }

        $partes = preg_split('/\r\n|\r|\n/', $texto) ?: [];

        return array_values(array_filter(
            array_map('trim', $partes),
            static fn (string $linha): bool => $linha !== ''
        ));
    }

    /**
     * Move o QR carregado para o caminho fixo (URL permanente).
     */
    private function guardarQr(Request $request): void
    {
        $ficheiro = $request->ficheiro('qr_image');
        if ($ficheiro === null) {
            return;
        }

        $tamanho = (int) ($ficheiro['size'] ?? 0);
        $tipo    = (string) ($ficheiro['type'] ?? '');
        $permissoes = ['image/png', 'image/jpeg', 'image/webp'];

        if ($tamanho <= 0 || $tamanho > 4 * 1024 * 1024 || !in_array($tipo, $permissoes, true)) {
            Session::flash('erro', 'O QR Code tem de ser uma imagem PNG, JPEG ou WebP até 4 MB.');

            return;
        }

        $destino = dirname(__DIR__, 3) . '/public/' . self::QR_CAMINHO;

        if (is_uploaded_file((string) $ficheiro['tmp_name'])
            && @move_uploaded_file((string) $ficheiro['tmp_name'], $destino)
        ) {
            $this->gravar('networking.qr_image', self::QR_CAMINHO, 'networking');
        }
    }

    /**
     * Tamanho máximo por chave (espelha as regras do backend Laravel).
     */
    private function maximo(string $chave): int
    {
        return match ($chave) {
            'contact.phone_1', 'contact.phone_2', 'contact.phone_3',
            'contact.whatsapp', 'whatsapp.number'      => 30,
            'contact.address_full'                     => 300,
            'contact.hours'                            => 200,
            'contact.hours_lines'                      => 500,
            'brand.name'                               => 120,
            'brand.legal_name'                         => 200,
            'brand.tagline'                            => 255,
            'whatsapp.open_message'                    => 1000,
            'process.case_local'                       => 255,
            default                                    => 255,
        };
    }

    private function rotulo(string $chave): string
    {
        return match ($chave) {
            'contact.email'         => 'e-mail',
            'contact.phone_1'       => 'telefone 1',
            'contact.phone_2'       => 'telefone 2',
            'contact.phone_3'       => 'telefone 3',
            'contact.whatsapp'      => 'WhatsApp',
            'contact.address_full'  => 'morada',
            'contact.hours'         => 'horário (rodapé)',
            'contact.hours_lines'   => 'horário (página de contacto)',
            'brand.name'            => 'nome da marca',
            'brand.legal_name'      => 'nome legal',
            'brand.tagline'         => 'slogan',
            'social.instagram'      => 'Instagram',
            'social.facebook'       => 'Facebook',
            'social.linkedin'       => 'LinkedIn',
            'whatsapp.number'       => 'número de WhatsApp',
            'whatsapp.open_message' => 'mensagem inicial do WhatsApp',
            'process.case_local'    => 'local do caso real',
            default                 => $chave,
        };
    }
}
