<?php

namespace App\Controller;

use App\Response;
use App\Services\Auth;

final class AuthController
{
    public function register(): Response
    {
        $data = $_POST;

        $this->verifyRecaptcha($data['g-recaptcha-response'] ?? '', 'register');

        Auth::register($data['email'], $data['password']);

        return Response::redirect('/form');
    }

    public function login(): Response
    {
        $data = $_POST;

        $this->verifyRecaptcha($data['g-recaptcha-response'] ?? '', 'login');

        $result  = Auth::login($data['email'], $data['password']);

        if ($result === '2fa_required') {
            return Response::redirect('/2fa');
        }
        if ($result === '2fa_setup_required') {
            return Response::redirect('/2fa/setup');
        }

        return Response::redirect('/');
    }

    public function logout(): Response
    {
        Auth::logout();
        return new Response('Logged out');
    }

    private function verifyRecaptcha(string $token, string $action): void
    {
        $secret = RECAPTCHA_SECRET_KEY;

        $ch = curl_init('https://www.google.com/recaptcha/api/siteverify');
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query([
            'secret' => $secret,
            'response' => $token
        ]));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $response = curl_exec($ch);
        curl_close($ch);

        $result = json_decode($response, true);

        if (
            !array_key_exists('success', $result)
            || !array_key_exists('action', $result)
            || $result['action'] !== $action
            || $result['score'] < 0.5
        )
        {
            throw new \Exception('reCAPTCHA verification failed');
        }
    }
}