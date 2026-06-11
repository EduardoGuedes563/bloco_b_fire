<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require_once __DIR__ . '/../vendor/autoload.php';

function enviarCodigo2FA(string $emailDestino, string $nomeUsuario, string $codigo): bool {
    $mail = new PHPMailer(true);

    try {
        // Configurações do servidor SMTP
        $mail->isSMTP();
        $mail->Host       = 'sandbox.smtp.mailtrap.io';
        $mail->SMTPAuth   = true;
        $mail->Username   = '8094b8c58929a0';
        $mail->Password   = '758eec22ce800c';
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port       = 2525;
        $mail->CharSet    = 'UTF-8';

        // Remetente e destinatário
        $mail->setFrom('seuemail@gmail.com', 'Sistema Incêndio – Bloco B');
        $mail->addAddress($emailDestino, $nomeUsuario);

        // Conteúdo do e-mail
        $mail->isHTML(true);
        $mail->Subject = 'Seu código de verificação';
        $mail->Body    = "
            <div style='font-family:sans-serif;max-width:400px;margin:auto;padding:24px;border:1px solid #eee;border-radius:8px'>
                <h2 style='color:#c0392b'>Sistema de Incêndio – Bloco B</h2>
                <p>Olá, <strong>{$nomeUsuario}</strong>!</p>
                <p>Seu código de verificação é:</p>
                <div style='font-size:36px;font-weight:bold;letter-spacing:10px;color:#c0392b;text-align:center;padding:16px'>
                    {$codigo}
                </div>
                <p style='color:#888;font-size:12px'>Este código expira em 10 minutos.</p>
            </div>
        ";

        $mail->send();
        return true;

    } catch (Exception $e) {
        error_log("Erro ao enviar e-mail 2FA: " . $mail->ErrorInfo);
        return false;
    }
}