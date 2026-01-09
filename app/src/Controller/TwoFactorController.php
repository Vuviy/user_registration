<?php

namespace App\Controller;

use App\Response;
use App\Services\Auth;
use App\View;

final class TwoFactorController
{
    public function form(): Response
    {
        session_start();

        if (!array_key_exists('2fa_user_id', $_SESSION)) {
            return Response::redirect('/login');
        }

        return new Response(View::make('2fa'));
    }
    public function verify(): Response
    {
        $code = $_POST['code'] ?? '';

        Auth::verify2fa($code);

        return Response::redirect('/');
    }
}