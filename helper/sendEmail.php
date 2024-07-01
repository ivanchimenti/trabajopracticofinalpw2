<?php


require 'vendor/PHPMailer/src/PHPMailer.php';
require 'vendor/PHPMailer/src/SMTP.php';
require 'vendor/PHPMailer/src/Exception.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

function getConfig($filePath)
{
    if (!file_exists($filePath)) {
        throw new Exception("Configuration file not found: " . $filePath);
    }

    $config = parse_ini_file($filePath, true);

    if ($config === false) {
        throw new Exception("Failed to parse configuration file: " . $filePath);
    }

    return $config;
}

function sendVerificationEmail($toEmail, $toName, $subject, $body)
{
    $config = getConfig('config/config.ini');

    $mail = new PHPMailer(true);

    try {
        $mail->isSMTP();
        $mail->Host = $config['host'];
        $mail->SMTPAuth = true;
        $mail->Username = $config['phpmailer_username'];
        $mail->Password = $config['phpmailer_password'];
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = $config['port'];

        $mail->setFrom($config['from_email'], $config['from_name']);
        $mail->addAddress($toEmail, $toName);
        $mail->isHTML(true);
        $mail->Subject = $subject;
        $mail->Body = $body;

        $mail->send();
        return true;
    } catch (Exception $e) {
        return false;
    }
}
