<?php

declare(strict_types=1);

namespace App\Core;

/**
 * Mailer — envio de e-mail com template HTML.
 *
 * Transportes:
 *   - 'log'  (por omissão): grava em storage/logs/mail.log — desenvolvimento
 *   - 'smtp'/'mail': envio real quando configurado no .env
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

        $ok = @mail($para, '=?UTF-8?B?' . base64_encode($assunto) . '?=', $corpo, $cabecalhos);

        if (!$ok) {
            Logger::erro('mail.falhou', ['para' => $para, 'assunto' => $assunto]);
            // Fallback: nunca perder o e-mail em silêncio
            return self::gravarNoLog($para, $assunto, $corpo);
        }

        Logger::info('mail.enviado', ['para' => $para, 'assunto' => $assunto]);

        return true;
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
