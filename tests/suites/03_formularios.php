<?php

declare(strict_types=1);

// Formulários públicos: validação, gravação, honeypot, anexos, agenda.

$bd = Apoio::bd();

// ------------------------------------------------------------------
// Contacto
// ------------------------------------------------------------------
$antes = (int) $bd->query('SELECT COUNT(*) FROM contact_messages')->fetchColumn();

$r = Apoio::post('/contacto', [
    'name'      => 'Teste Automático',
    'email'     => 'teste@exemplo.ao',
    'subject'   => 'Assunto de teste',
    'message'   => 'Mensagem de teste.',
    'homepage'  => '',
]);
Apoio::verificar('POST /contacto válido → 302 /contacto', $r['status'] === 302 && Apoio::localizacao($r) === '/contacto');

$depois = (int) $bd->query('SELECT COUNT(*) FROM contact_messages')->fetchColumn();
Apoio::verificar('contacto gravado na BD', $depois === $antes + 1);

$pagina = Apoio::get('/contacto');
Apoio::verificar('flash de sucesso visível', str_contains($pagina['corpo'], 'Mensagem enviada com sucesso'));

// Validação: campos obrigatórios em falta
$r = Apoio::post('/contacto', ['name' => '', 'email' => 'invalido', 'subject' => '', 'message' => '']);
Apoio::verificar('POST /contacto inválido → 302', $r['status'] === 302);
$pagina = Apoio::get(Apoio::localizacao($r));
Apoio::verificar('erros de validação visíveis', str_contains($pagina['corpo'], 'obrigat'));

// Honeypot: submissão com campo armadilha preenchido não grava
$antes = (int) $bd->query('SELECT COUNT(*) FROM contact_messages')->fetchColumn();
$r = Apoio::post('/contacto', [
    'name'      => 'Bot',
    'email'     => 'bot@spam.tld',
    'subject'   => 'spam',
    'message'   => 'spam',
    'homepage'  => 'http://spam.tld',
]);
$depois = (int) $bd->query('SELECT COUNT(*) FROM contact_messages')->fetchColumn();
Apoio::verificar('honeypot rejeita sem gravar', $depois === $antes);

// ------------------------------------------------------------------
// Solicitar projeto (com anexo)
// ------------------------------------------------------------------
$antes = (int) $bd->query('SELECT COUNT(*) FROM project_requests')->fetchColumn();

$png = base64_decode('iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mP8z8BQDwAEhQGAhKmMIQAAAABJRU5ErkJggg==');
$ficheiro = tempnam(sys_get_temp_dir(), 't') . '.png';
file_put_contents($ficheiro, $png);

$pagina = Apoio::get('/solicitar-projeto');
preg_match('/name="_token" value="([^"]+)"/', $pagina['corpo'], $tm);

$ck = Apoio::cookies();
$ch = curl_init(Apoio::$base . '/solicitar-projeto');
curl_setopt_array($ch, [
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_HEADER          => true,
    CURLOPT_FOLLOWLOCATION  => false,
    CURLOPT_TIMEOUT         => 20,
    CURLOPT_COOKIEJAR       => $ck,
    CURLOPT_COOKIEFILE      => $ck,
    CURLOPT_POST            => true,
    CURLOPT_POSTFIELDS      => [
        '_token'           => $tm[1] ?? '',
        'user_name'        => 'Teste Anexo',
        'user_email'       => 'anexo@exemplo.ao',
        'user_phone'       => '923000000',
        'project_type'     => 'Casa',
        'location'         => 'Luanda',
        'preferred_channel' => 'email',
        'description'      => 'Teste com anexo.',
        'privacy'          => 'on',
        'homepage'         => '',
        'projectFiles[]'   => new CURLFile($ficheiro, 'image/png', 'planta.png'),
    ],
]);
$bruto = (string) curl_exec($ch);
curl_close($ch);
unlink($ficheiro);
preg_match('#HTTP/[\d.]+ (\d{3})#', $bruto, $ms);

Apoio::verificar('POST /solicitar-projeto com anexo → 302', (int) ($ms[1] ?? 0) === 302);

$depois = (int) $bd->query('SELECT COUNT(*) FROM project_requests')->fetchColumn();
Apoio::verificar('pedido gravado na BD', $depois === $antes + 1);

$anexo = $bd->query("SELECT a.path FROM attachments a JOIN project_requests pr ON pr.id = a.request_id WHERE pr.user_email = 'anexo@exemplo.ao' ORDER BY a.created_at DESC LIMIT 1")->fetchColumn();
Apoio::verificar('anexo registado', $anexo !== false);
Apoio::verificar('anexo em disco privado', $anexo !== false && is_file(dirname(__DIR__, 2) . '/storage/app/private/' . $anexo));
Apoio::verificar('anexo fora de public/', $anexo !== false && !is_file(dirname(__DIR__, 2) . '/public/' . $anexo));

// ------------------------------------------------------------------
// Agendar: disponibilidade, criação e anti-duplicado
// ------------------------------------------------------------------
$disp = Apoio::get('/agendar/disponibilidade?mes=' . date('Y-m'));
$json = json_decode($disp['corpo'], true);
Apoio::verificar('disponibilidade devolve JSON com dias', is_array($json['dias'] ?? null));

$data = null;
$hora = null;
foreach (($json['dias'] ?? []) as $d => $horas) {
    if ($horas !== []) {
        $data = $d;
        $hora = $horas[0];
        break;
    }
}
Apoio::verificar('existe pelo menos um slot livre', $data !== null);

if ($data !== null) {
    $r = Apoio::post('/agendar', [
        'user_name'  => 'Teste Agenda',
        'user_email' => 'agenda@exemplo.ao',
        'user_phone' => '923111222',
        'type'       => 'ONLINE',
        'appt_date'  => $data,
        'appt_time'  => $hora,
        'notes'      => '',
        'homepage'   => '',
    ]);
    Apoio::verificar('POST /agendar → 302', $r['status'] === 302);

    $gravado = $bd->query("SELECT COUNT(*) FROM appointments WHERE user_email = 'agenda@exemplo.ao'")->fetchColumn();
    Apoio::verificar('agendamento gravado', (int) $gravado === 1);

    // Mesmo slot outra vez → rejeitado (ocupado)
    $r = Apoio::post('/agendar', [
        'user_name'  => 'Outro Cliente',
        'user_email' => 'outro@exemplo.ao',
        'user_phone' => '923333444',
        'type'       => 'ONLINE',
        'appt_date'  => $data,
        'appt_time'  => $hora,
        'homepage'   => '',
    ]);
    $erros = $bd->query("SELECT COUNT(*) FROM appointments WHERE user_email = 'outro@exemplo.ao'")->fetchColumn();
    Apoio::verificar('slot ocupado rejeitado', (int) $erros === 0);

    $pagina = Apoio::get('/agendar');
    Apoio::verificar('erro de slot visível', str_contains($pagina['corpo'], 'disponível'));
}

// Data passada rejeitada
$r = Apoio::post('/agendar', [
    'user_name'  => 'Teste Data',
    'user_email' => 'data@exemplo.ao',
    'user_phone' => '923555666',
    'type'       => 'ONLINE',
    'appt_date'  => '2020-01-01',
    'appt_time'  => '10:00',
    'homepage'   => '',
]);
$erros = $bd->query("SELECT COUNT(*) FROM appointments WHERE user_email = 'data@exemplo.ao'")->fetchColumn();
Apoio::verificar('data passada rejeitada', (int) $erros === 0);

// ------------------------------------------------------------------
// Recuperação de palavra-passe (flash sem revelar existência)
// ------------------------------------------------------------------
$r = Apoio::post('/recuperar-palavra-passe', ['email' => 'admin@havredesign.com']);
Apoio::verificar('POST recuperar → 302', $r['status'] === 302);
$pagina = Apoio::get(Apoio::localizacao($r));
Apoio::verificar('mensagem genérica de recuperação', str_contains($pagina['corpo'], 'Se esse e-mail estiver registado'));

// ------------------------------------------------------------------
// Limpeza dos registos de teste
// ------------------------------------------------------------------
$bd->exec("DELETE FROM contact_messages WHERE email LIKE '%@exemplo.ao' OR email LIKE '%@spam.tld'");
$bd->exec("DELETE FROM attachments WHERE request_id IN (SELECT id FROM project_requests WHERE user_email LIKE '%@exemplo.ao')");
$bd->exec("DELETE FROM project_requests WHERE user_email LIKE '%@exemplo.ao'");
$bd->exec("DELETE FROM appointments WHERE user_email LIKE '%@exemplo.ao'");
