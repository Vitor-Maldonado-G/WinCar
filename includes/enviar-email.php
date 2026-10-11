<?php
use PHPMailer\PHPMailer\PHPMailer;

require_once __DIR__ . '/../lib/PHPMailer/src/Exception.php';
require_once __DIR__ . '/../lib/PHPMailer/src/PHPMailer.php';
require_once __DIR__ . '/../lib/PHPMailer/src/SMTP.php';

function configuracaoEmail() {
    $arquivo = __DIR__ . '/../config/email.local.php';
    if (!is_file($arquivo)) {
        throw new RuntimeException('Configure config/email.local.php.');
    }
    $config = require $arquivo;
    if (empty($config['senha_app']) || empty($config['url_base'])
        || !filter_var($config['usuario'] ?? '', FILTER_VALIDATE_EMAIL)) {
        throw new RuntimeException('Configuracao de e-mail incompleta.');
    }
    return $config;
}

function enviarEmailRecuperacao(array $config, $destinatario, $token) {
    $email = new PHPMailer(true);
    $email->isSMTP();
    $email->Host = $config['host'];
    $email->Port = (int) $config['porta'];
    $email->SMTPAuth = true;
    $email->Username = $config['usuario'];
    $email->Password = str_replace(' ', '', $config['senha_app']);
    $email->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
    $email->Timeout = 15;
    $email->CharSet = 'UTF-8';
    $email->setFrom($config['usuario'], $config['nome_remetente']);
    $email->addAddress($destinatario);
    $email->Subject = 'WinCar - Redefinição de senha';
    // A URL vem da configuracao local, nunca do cabecalho Host enviado pelo visitante.
    $link = rtrim($config['url_base'], '/') . '/redefinir-senha.php?token=' . rawurlencode($token);
    $email->Body = "Recebemos uma solicitação para redefinir sua senha no WinCar.\n\n"
        . "Acesse o link abaixo para escolher uma nova senha:\n" . $link
        . "\n\nEste link é válido por 30 minutos e só pode ser utilizado uma vez."
        . "\nSe você não fez essa solicitação, ignore este e-mail. Sua senha permanece a mesma.";
    $email->send();
}
