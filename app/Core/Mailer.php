<?php

declare(strict_types=1);

namespace App\Core;

/**
 * Mailer — envio de e-mail com template HTML.
 *
 * Transportes:
 *   - 'log'  (por omissão): grava em storage/logs/mail.log — desenvolvimento
 *   - 'mail': função mail() do PHP (se disponível)
 *   - 'smtp': SMTP autenticado (requer MAIL_HOST, MAIL_PORT, MAIL_USERNAME, MAIL_PASSWORD no .env)
 */
final class Mailer
{
    /**
     * Envia um e-mail HTML. Devolve true se enviado (ou registado no log).
     *
     * @param array<string, mixed> $dados variáveis do template
     */
    public static function enviar(string $para, string $assunto, string $template, array $dados = []): bool
    {
        $corpo = self::corpo($template, $dados);

        // Cabeçalhos básicos anti-spam
        $cabecalhos  = "MIME-Version: 1.0\r\n";
        $cabecalhos .= "Content-Type: text/html; charset=UTF-8\r\n";
        $cabecalhos .= 'From: ' . self::remetente() . "\r\n";
        $cabecalhos .= 'Reply-To: ' . Config::obter('MAIL_FROM_ADDRESS') . "\r\n";

        $transporte = Config::obter('MAIL_TRANSPORT', 'log');

        if ($transporte === 'log') {
            return self::gravarNoLog($para, $assunto, $corpo);
        }

        if ($transporte === 'smtp') {
            return self::enviarSmtp($para, $assunto, $corpo, $cabecalhos);
        }

        // transporte 'mail' ou outro -> função mail() nativa
        if (function_exists('mail')) {
            $ok = @mail($para, '=?UTF-8?B?' . base64_encode($assunto) . '?=', $corpo, $cabecalhos);

            if (!$ok) {
                Logger::erro('mail.falhou', ['para' => $para, 'assunto' => $assunto, 'transporte' => 'mail']);
                return self::gravarNoLog($para, $assunto, $corpo);
            }

            Logger::info('mail.enviado', ['para' => $para, 'assunto' => $assunto, 'transporte' => 'mail']);

            return true;
        }

        // mail() não existe -> fallback para log
        Logger::aviso('mail.funcao_indisponivel', ['para' => $para, 'assunto' => $assunto]);
        return self::gravarNoLog($para, $assunto, $corpo);
    }

    /**
     * Envia via SMTP autenticado.
     */
    private static function enviarSmtp(string $para, string $assunto, string $corpo, string $cabecalhos): bool
    {
        $host     = Config::obter('MAIL_HOST');
        $port     = (int) Config::obter('MAIL_PORT', 587);
        $username = Config::obter('MAIL_USERNAME');
        $password = Config::obter('MAIL_PASSWORD');
        $encryption = Config::obter('MAIL_ENCRYPTION', 'tls'); // tls, ssl, ou vazio
        $from     = Config::obter('MAIL_FROM_ADDRESS');

        if (!$host || !$username || !$password) {
            Logger::erro('smtp.config_falta', ['host' => $host, 'user' => $username]);
            return self::gravarNoLog($para, $assunto, $corpo);
        }

        $socket = @fsockopen(
            ($encryption === 'ssl' ? 'ssl://' : '') . $host,
            $port,
            $errno,
            $errstr,
            10
        );

        if (!$socket) {
            Logger::erro('smtp.conexao_falhou', ['host' => $host, 'port' => $port, 'erro' => $errstr]);
            return self::gravarNoLog($para, $assunto, $corpo);
        }

        stream_set_timeout($socket, 10);

        try {
            $resposta = self::smtpLer($socket);
            if (!str_starts_with($resposta, '220')) {
                throw new \RuntimeException("SMTP banner inesperado: $resposta");
            }

            // EHLO
            self::smtpEscrever($socket, "EHLO " . gethostname());
            $resposta = self::smtpLer($socket);
            if (!str_starts_with($resposta, '250')) {
                throw new \RuntimeException("EHLO falhou: $resposta");
            }

            // STARTTLS se TLS
            if ($encryption === 'tls') {
                self::smtpEscrever($socket, 'STARTTLS');
                $resposta = self::smtpLer($socket);
                if (!str_starts_with($resposta, '220')) {
                    throw new \RuntimeException("STARTTLS falhou: $resposta");
                }
                if (!stream_socket_enable_crypto($socket, true, STREAM_CRYPTO_METHOD_TLS_CLIENT)) {
                    throw new \RuntimeException('Falha ao ativar TLS');
                }
                // EHLO novamente após TLS
                self::smtpEscrever($socket, "EHLO " . gethostname());
                $resposta = self::smtpLer($socket);
                if (!str_starts_with($resposta, '250')) {
                    throw new \RuntimeException("EHLO pós-TLS falhou: $resposta");
                }
            }

            // AUTH LOGIN
            self::smtpEscrever($socket, 'AUTH LOGIN');
            $resposta = self::smtpLer($socket);
            if (!str_starts_with($resposta, '334')) {
                throw new \RuntimeException("AUTH LOGIN falhou: $resposta");
            }

            self::smtpEscrever($socket, base64_encode($username));
            $resposta = self::smtpLer($socket);
            if (!str_starts_with($resposta, '334')) {
                throw new \RuntimeException("Username falhou: $resposta");
            }

            self::smtpEscrever($socket, base64_encode($password));
            $resposta = self::smtpLer($socket);
            if (!str_starts_with($resposta, '235')) {
                throw new \RuntimeException("Password falhou: $resposta");
            }

            // MAIL FROM
            self::smtpEscrever($socket, "MAIL FROM:<$from>");
            $resposta = self::smtpLer($socket);
            if (!str_starts_with($resposta, '250')) {
                throw new \RuntimeException("MAIL FROM falhou: $resposta");
            }

            // RCPT TO
            self::smtpEscrever($socket, "RCPT TO:<$para>");
            $resposta = self::smtpLer($socket);
            if (!str_starts_with($resposta, '250')) {
                throw new \RuntimeException("RCPT TO falhou: $resposta");
            }

            // DATA
            self::smtpEscrever($socket, 'DATA');
            $resposta = self::smtpLer($socket);
            if (!str_starts_with($resposta, '354')) {
                throw new \RuntimeException("DATA falhou: $resposta");
            }

            // Mensagem completa
            $mensagem  = "Subject: =?UTF-8?B?" . base64_encode($assunto) . "?=\r\n";
            $mensagem .= $cabecalhos;
            $mensagem .= "\r\n";
            $mensagem .= $corpo;
            $mensagem .= "\r\n.\r\n";

            self::smtpEscrever($socket, $mensagem);
            $resposta = self::smtpLer($socket);
            if (!str_starts_with($resposta, '250')) {
                throw new \RuntimeException("Envio de dados falhou: $resposta");
            }

            // QUIT
            self::smtpEscrever($socket, 'QUIT');
            self::smtpLer($socket);

            fclose($socket);

            Logger::info('mail.enviado', ['para' => $para, 'assunto' => $assunto, 'transporte' => 'smtp']);

            return true;
        } catch (\Throwable $e) {
            @fclose($socket);
            Logger::erro('smtp.falhou', ['para' => $para, 'assunto' => $assunto, 'erro' => $e->getMessage()]);
            return self::gravarNoLog($para, $assunto, $corpo);
        }
    }

    private static function smtpEscrever($socket, string $comando): void
    {
        fwrite($socket, $comando . "\r\n");
    }

    private static function smtpLer($socket): string
    {
        $resposta = '';
        while (($linha = fgets($socket, 512)) !== false) {
            $resposta .= $linha;
            if (strlen($linha) >= 4 && $linha[3] === ' ') {
                break;
            }
        }
        return trim($resposta);
    }

    /**
     * Envia para o admin (formulários públicos).
     */
    public static function enviarAdmin(string $assunto, string $template, array $dados = []): bool
    {
        return self::enviar(Config::obter('MAIL_ADMIN'), $assunto, $template, $dados);
    }

    /**
     * Renderiza o template do e-mail (resources/views/emails/{nome}.php).
     *
     * @param array<string, mixed> $dados
     */
    private static function corpo(string $template, array $dados): string
    {
        $ficheiro = dirname(__DIR__, 2) . '/resources/views/emails/' . $template . '.php';

        if (!is_file($ficheiro)) {
            Logger::erro('mail.template_falta', ['template' => $template]);
            return '<p>Erro interno no template do e-mail.</p>';
        }

        // Layout base + conteúdo
        ob_start();
        extract($dados, EXTR_SKIP);
        include $ficheiro;
        $conteudo = (string) ob_get_clean();

        $titulo = htmlspecialchars((string) ($dados['assunto'] ?? $assunto ?? ''), ENT_QUOTES, 'UTF-8');

        return <<<HTML
        <!DOCTYPE html>
        <html lang="pt">
        <head><meta charset="utf-8"><title>{$titulo}</title></head>
        <body style="font-family:Arial,Helvetica,sans-serif;line-height:1.6;color:#1a1a1a;max-width:640px;margin:0 auto;padding:24px;">
          {$conteudo}
          <hr style="border:none;border-top:1px solid #e5e5e5;margin:24px 0;">
          <p style="font-size:12px;color:#777;">HAVREDESIGN — Arquitetura e Design de Interiores · Luanda, Angola</p>
        </body>
        </html>
        HTML;
    }

    private static function remetente(): string
    {
        $nome  = Config::obter('MAIL_FROM_NAME', 'HAVREDESIGN');
        $email = Config::obter('MAIL_FROM_ADDRESS');

        return sprintf('=?UTF-8?B?%s?= <%s>', base64_encode($nome), $email);
    }

    private static function gravarNoLog(string $para, string $assunto, string $corpo): bool
    {
        $directorio = dirname(__DIR__, 2) . '/storage/logs';
        if (!is_dir($directorio)) {
            @mkdir($directorio, 0755, true);
        }

        $entrada = sprintf(
            "===== %s =====\nPara: %s\nAssunto: %s\n\n%s\n\n",
            date('Y-m-d H:i:s'),
            $para,
            $assunto,
            $corpo
        );

        $ok = @file_put_contents($directorio . '/mail.log', $entrada, FILE_APPEND | LOCK_EX) !== false;

        if ($ok) {
            Logger::info('mail.log', ['para' => $para, 'assunto' => $assunto]);
        }

        return $ok;
    }
}