<?php

namespace App\Controller;

use App\Response;
use App\Services\Auth;

final class VerifyController
{

    public function verify(): Response
    {
        $token = filter_input(INPUT_GET, 'token', FILTER_SANITIZE_SPECIAL_CHARS);

        if (!$token) {
            return new Response('Missing token', 400);
        }

        $verify = Auth::verifyEmail($token);

        if(!$verify)
        {
            return new Response('Invalid token', 400);
        }
        return Response::redirect('/');
    }

}