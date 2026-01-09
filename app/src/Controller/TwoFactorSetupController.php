<?php

namespace App\Controller;

use App\DB\Database;
use App\Response;
use App\View;
use OTPHP\TOTP;

final class TwoFactorSetupController
{
    public function form(): Response
    {
        session_start();

        if (!array_key_exists('user_id', $_SESSION)) {
            return Response::redirect('/login');
        }

        $totp = TOTP::create();
        $secret = $totp->getSecret();

        $_SESSION['2fa_setup_secret'] = $secret;

        $db = new Database(config());
        $user = $db->table('users')->where('id', '=', $_SESSION['user_id'])->first();

        $email = $user['email'];

        $issuer = 'MyApp';

        $qrUri = "otpauth://totp/{$issuer}:{$email}?secret={$secret}&issuer={$issuer}";

        return new Response(View::make('2fa_setup', ['qr' => $qrUri]));
    }


    public function confirm(): Response
    {
        session_start();

        if (!array_key_exists('user_id', $_SESSION) && !array_key_exists('2fa_setup_secret', $_SESSION))
        {
            return Response::redirect('/login');
        }

        $code = $_POST['code'] ?? '';

        $totp = TOTP::create($_SESSION['2fa_setup_secret']);

        if (!$totp->verify($code)) {
            return new Response('Invalid code', 400);
        }

        $db = new Database(config());

        $db->table('users')
            ->where('id', '=', $_SESSION['user_id'])
            ->update([
                'totp_secret' => $_SESSION['2fa_setup_secret'],
                'is_2fa_enabled' => 1,
                'twofa_confirmed_at' => date('Y-m-d H:i:s'),
            ]);

        unset($_SESSION['2fa_setup_secret']);

        return Response::redirect('/');
    }
}