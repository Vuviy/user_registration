<?php

namespace App\Services;

final class Mailer
{
    private const LOG_FILE = __DIR__ . '/../../storage/mail.log';
    public static function sendMail($to, $token)
    {
        $link = 'http://localhost/verify?token=' . urlencode($token);

        $message = sprintf(
            "[%s] To: %s\nVerification link: %s\n\n",
            date('Y-m-d H:i:s'),
            $to,
            $link
        );

        file_put_contents(
            self::LOG_FILE,
            $message,
            FILE_APPEND | LOCK_EX
        );
    }
}