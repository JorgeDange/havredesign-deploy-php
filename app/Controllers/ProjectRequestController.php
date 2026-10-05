<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Auth;
use App\Core\Config;
use App\Core\Database;
use App\Core\Logger;
use App\Core\Mailer;
use App\Core\Request;
use App\Core\Response;
use App\Core\Session;
use App\Core\Validator;

/**
 * ProjectRequestController — POST /solicitar-projeto.
 *
 * Espelha StoreProjectRequestRequest + ProjectRequestController (backend):
 * grava o pedido, guarda anexos em disco privado e envia e-mails (nunca
 * falha o pedido por causa do e-mail).
 */
final class ProjectRequestController
{
    private const EXTENSOES = ['pdf', 'jpg', 'jpeg', 'png', 'webp', 'zip', 'dwg'];
    private const TAMANHO_MAX = 10 * 1024 * 1024; // 10 MB (10240 KB)
    private const MAX_FICHEIROS = 5;

    public function store(Request $request): Response
    {
        // Throttle por IP (4.8)
        $bloqueio = \App\Core\Throttle::verificar('projeto', $request->ip());
        if ($bloqueio !== null) {
            Session::flash('erro', $bloqueio);

            return Response::redirect('/solicitar-projeto');
        }

        $nome    = $request->input('user_name');
        $email   = $request->input('user_email');
        $telefone = $request->input('user_phone');
        $local   = $request->input('location');
        $tipo    = $request->input('project_type');
        $area    = $request->input('area_approx');
        $estado  = $request->input('project_stage');
        $orcamento = $request->input('budget');
        $prazo   = $request->input('timeline');
        $servico = $request->input('service_id');
        $solucao = $request->input('solution_id');
        $segmento = $request->input('segment');
        $canal   = $request->input('preferred_channel');
        $horario = $request->input('preferred_time');
        $descricao = (string) $request->bruto('description', '');
        $privacidade = (string) $request->bruto('privacy', '');
        $armadilha = (string) $request->bruto('homepage', '');

        $regras = [
            'homepage'       => 'honeypot',
            'user_name'      => 'required|string|max:150',
            'user_email'     => 'required|email|max:255',
            'user_phone'     => 'required|string|max:30',
            'location'       => 'nullable|string|max:255',
            'project_type'   => 'required|string|max:100',
            'area_approx'    => 'nullable|string|max:50',
            'project_stage'  => 'nullable|string|max:60',
            'budget'         => 'nullable|string|max:100',
            'timeline'       => 'nullable|string|max:100',
            'segment'        => 'nullable|in:Investimento imobiliário residencial,Habitação própria,Comércio e serviços,Outro',
            'preferred_channel' => 'required|in:email,phone,whatsapp',
            'preferred_time' => 'nullable|string|max:60',
            'description'    => 'required|string|max:10000',
            'privacy'        => 'required',
        ];

        $rotulos = [
            'user_name'      => 'nome completo',
            'user_email'     => 'e-mail',
            'user_phone'     => 'telefone',
            'location'       => 'localização do projeto',
            'project_type'   => 'tipo de projeto',
            'area_approx'    => 'área aproximada',
            'project_stage'  => 'estado do projeto',
            'budget'         => 'orçamento previsto',
            'timeline'       => 'prazo desejado',
            'segment'        => 'segmento',
            'preferred_channel' => 'canal preferido',
            'preferred_time' => 'horário preferido',
            'description'    => 'descrição',
            'privacy'        => 'consentimento de privacidade',
        ];

        $validado = Validator::fazer(
            [
                'user_name' => $nome, 'user_email' => $email, 'user_phone' => $telefone,
                'location' => $local, 'project_type' => $tipo, 'area_approx' => $area,
                'project_stage' => $estado, 'budget' => $orcamento, 'timeline' => $prazo,
                'segment' => $segmento, 'preferred_channel' => $canal, 'preferred_time' => $horario,
                'description' => $descricao, 'privacy' => $privacidade, 'homepage' => $armadilha,
            ],
            $regras,
            $rotulos
        );

        // service_id/solution_id: têm de existir (Rule::exists do backend)
        if ($validado->passou() && $servico !== '' && $servico !== null) {
            if (Database::escalar('SELECT COUNT(*) FROM services WHERE id = ?', [$servico]) == 0) {
                $validado->erro('service_id', 'Serviço inválido.');
            }
        }
        if ($validado->passou() && $solucao !== '' && $solucao !== null) {
            if (Database::escalar('SELECT COUNT(*) FROM solutions WHERE id = ?', [$solucao]) == 0) {
                $validado->erro('solution_id', 'Solução inválida.');
            }
        }

        // Anexos: validação antes de gravar qualquer coisa
        $ficheiros = $this->ficheirosValidos($request, $validado);

        if (!$validado->passou()) {
            Session::colocar('_erros_validacao', $validado->erros());
            Session::colocar('_old_input', [
                'user_name' => $nome, 'user_email' => $email, 'user_phone' => $telefone,
                'location' => $local, 'project_type' => $tipo, 'area_approx' => $area,
                'project_stage' => $estado, 'budget' => $orcamento, 'timeline' => $prazo,
                'preferred_channel' => $canal, 'preferred_time' => $horario,
                'description' => $descricao,
            ]);

            return Response::redirect('/solicitar-projeto');
        }

        $id = novo_uuid();
        $seguro = null;
        if ($segmento !== '') {
            // O enum da BD tem acentos; só grava se corresponder exactamente
            $seguro = $segmento;
        }

        Database::executar(
            'INSERT INTO project_requests
             (id, user_id, user_name, user_email, user_phone, address, location, area_approx,
              project_stage, service_id, solution_id, segment, preferred_channel, preferred_time,
              project_type, budget, timeline, description, status, privacy_consented_at,
              privacy_version, ip_address)
             VALUES (?, ?, ?, ?, ?, NULL, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, \'NEW\', NOW(), ?, ?)',
            [
                $id,
                Auth::id(),
                $nome,
                $email,
                $telefone,
                $local !== '' ? $local : null,
                $area !== '' ? $area : null,
                $estado !== '' ? $estado : null,
                $servico !== '' ? $servico : null,
                $solucao !== '' ? $solucao : null,
                $seguro,
                $canal,
                $horario !== '' ? $horario : null,
                $tipo,
                $orcamento !== '' ? $orcamento : null,
                $prazo !== '' ? $prazo : null,
                $descricao,
                (string) \App\Support\Site::obter('privacy.version', 'v1'),
                $request->ip(),
            ]
        );

        $anexos = $this->guardarAnexos($request, $id, $ficheiros);

        $resumo = [
            'ref'          => strtoupper(substr($id, 0, 8)),
            'project_type' => $tipo,
            'service'      => $servico !== '' && $servico !== null
                ? (string) Database::escalar('SELECT title FROM services WHERE id = ?', [$servico]) : null,
            'solution'     => $solucao !== '' && $solucao !== null
                ? (string) Database::escalar('SELECT name FROM solutions WHERE id = ?', [$solucao]) : null,
            'location'     => $local !== '' ? $local : null,
            'budget'       => $orcamento !== '' ? $orcamento : null,
            'timeline'     => $prazo !== '' ? $prazo : null,
            'user_name'    => $nome,
            'user_email'   => $email,
            'canal'        => $canal,
            'anexos'       => $anexos,
        ];

        try {
            Mailer::enviarAdmin('Novo pedido de projeto: ' . $tipo, 'project-received', ['p' => $resumo]);
        } catch (\Throwable $e) {
            Logger::erro('project.received_mail_failed', ['id' => $id, 'erro' => $e->getMessage()]);
        }

        try {
            Mailer::enviar($email, 'Pedido de projeto recebido — HAVREDESIGN', 'project-ack', ['p' => $resumo]);
        } catch (\Throwable $e) {
            Logger::erro('project.ack_mail_failed', ['id' => $id, 'erro' => $e->getMessage()]);
        }

        Logger::info('project.received', ['id' => $id, 'anexos' => count($anexos)]);

        \App\Core\Throttle::registar('projeto', $request->ip());

        Session::flash('ok', 'Solicitação enviada com sucesso! Vamos analisar o seu pedido e entraremos em contacto.');
        Session::flash('project_submitted', $resumo);

        return Response::redirect('/solicitar-projeto');
    }

    /**
     * Valida os ficheiros carregados (máx. 5, extensões e tamanho do backend).
     *
     * @return array<int, array{tmp: string, nome: string, mime: string, tamanho: int}>
     */
    private function ficheirosValidos(Request $request, Validator $validado): array
    {
        $lidos = $request->ficheiros('projectFiles');

        if ($lidos === []) {
            return [];
        }

        if (count($lidos) > self::MAX_FICHEIROS) {
            $validado->erro('projectFiles', 'No máximo ' . self::MAX_FICHEIROS . ' ficheiros.');

            return [];
        }

        $validos = [];

        foreach ($lidos as $indice => $f) {
            $chave = 'projectFiles.' . $indice;

            if (($f['error'] ?? UPLOAD_ERR_NO_FILE) === UPLOAD_ERR_NO_FILE) {
                continue;
            }

            if (($f['error'] ?? UPLOAD_ERR_OK) !== UPLOAD_ERR_OK) {
                $validado->erro($chave, 'Falha no carregamento de um dos ficheiros.');
                continue;
            }

            if (($f['size'] ?? 0) > self::TAMANHO_MAX) {
                $validado->erro($chave, 'O ficheiro "' . basename((string) $f['name']) . '" excede 10 MB.');
                continue;
            }

            $extensao = strtolower(pathinfo((string) $f['name'], PATHINFO_EXTENSION));

            if (!in_array($extensao, self::EXTENSOES, true)) {
                $validado->erro($chave, 'Formato "' . $extensao . '" não permitido (permitidos: pdf, jpg, jpeg, png, webp, zip, dwg).');
                continue;
            }

            $validos[] = [
                'tmp'      => (string) $f['tmp_name'],
                'nome'     => (string) $f['name'],
                'mime'     => (string) ($f['type'] ?? 'application/octet-stream'),
                'tamanho'  => (int) $f['size'],
            ];
        }

        return $validos;
    }

    /**
     * Guarda os anexos em disco privado (storage/app/private/requests/{id}/),
     * nome aleatório — nunca em public/ (backend.md 4.7).
     *
     * @param array<int, array{tmp: string, nome: string, mime: string, tamanho: int}> $ficheiros
     * @return array<int, string> nomes originais guardados
     */
    private function guardarAnexos(Request $request, string $pedidoId, array $ficheiros): array
    {
        $guardados = [];

        foreach ($ficheiros as $f) {
            $extensao  = strtolower(pathinfo($f['nome'], PATHINFO_EXTENSION));
            $aleatorio = bin2hex(random_bytes(20)) . '.' . $extensao;
            $directorio = dirname(__DIR__, 2) . '/storage/app/private/requests/' . $pedidoId;

            if (!is_dir($directorio) && !@mkdir($directorio, 0755, true) && !is_dir($directorio)) {
                Logger::erro('project.attachments_mkdir_falhou', ['id' => $pedidoId]);
                continue;
            }

            if (!@move_uploaded_file($f['tmp'], $directorio . '/' . $aleatorio)) {
                // Fallback para testes fora do SAPI web
                if (!@rename($f['tmp'], $directorio . '/' . $aleatorio)) {
                    Logger::erro('project.attachments_move_falhou', ['id' => $pedidoId]);
                    continue;
                }
            }

            @chmod($directorio . '/' . $aleatorio, 0644);

            Database::executar(
                'INSERT INTO attachments (id, request_id, original_name, path, mime_type, size_bytes)
                 VALUES (?, ?, ?, ?, ?, ?)',
                [
                    novo_uuid(),
                    $pedidoId,
                    $f['nome'],
                    'requests/' . $pedidoId . '/' . $aleatorio,
                    $f['mime'] !== '' ? $f['mime'] : 'application/octet-stream',
                    $f['tamanho'],
                ]
            );

            $guardados[] = $f['nome'];
        }

        return $guardados;
    }
}
