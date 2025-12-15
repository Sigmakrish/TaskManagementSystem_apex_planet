<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require_once __DIR__ . '/../vendor/PHPMailer/src/Exception.php';
require_once __DIR__ . '/../vendor/PHPMailer/src/PHPMailer.php';
require_once __DIR__ . '/../vendor/PHPMailer/src/SMTP.php';

function sendOtpMail($email, $otp)
{
    // ✅ DEBUG LOG (NOW INSIDE FUNCTION, CORRECT)
    file_put_contents(
        __DIR__ . '/../error_log.txt',
        "Sending OTP to: " . var_export($email, true) . PHP_EOL,
        FILE_APPEND
    );

    $mail = new PHPMailer(true);

    try {
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'youremail@gmail.com';
        $mail->Password = 'password'; // App password
        $mail->SMTPSecure = 'tls';
        $mail->Port = 587;

        $mail->setFrom('youremail@gmail.com', 'Task Management System');

        // ✅ USE THE CORRECT VARIABLE
        $mail->addAddress($email);

        $mail->isHTML(true);
        $mail->Subject = 'Email Verification OTP';
        $mail->Body = "
            <h3>Email Verification</h3>
            <p>Your OTP is:</p>
            <h2>$otp</h2>
            <p>This OTP is valid for one verification only.</p>
        ";

        return $mail->send();

    } catch (Exception $e) {
        file_put_contents(
            __DIR__ . '/../error_log.txt',
            $e->getMessage() . PHP_EOL,
            FILE_APPEND
        );
        return false;
    }
}

