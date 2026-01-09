<?php

namespace App\Services;

use App\DB\Database;
use DateTime;
use DateTimeZone;
use OTPHP\TOTP;

final class Auth
{
    public static function register(string $email, string $password): void
    {
        $db = new Database(config());

        $emailExist = $db->table('users')->where('email', '=', $email)->first();

        if ($emailExist) {
            AuditLogger::log(null, 'register', 'failed', ['email' => $email]);
            throw new \Exception('Error');
        }

        $passwordHash = password_hash($password, PASSWORD_ARGON2ID);

        $userId = $db->table('users')->insert(['email' => $email, 'password_hash' => $passwordHash]);

        $token = bin2hex(random_bytes(32));
        $expiresAt = (new \DateTime('+1 day'))->format('Y-m-d H:i:s');

        $db->table('email_verifications')->insert(['user_id' => $userId, 'token' => $token, 'expires_at' => $expiresAt]);

        AuditLogger::log($userId, 'register', 'succeed', ['email' => $email]);

        Mailer::sendMail($email, $token);
    }


    public static function login(string $email, string $password): string
    {
        $db = new Database(config());

        $user = $db->table('users')->where('email', '=', $email)->first();

        if (!$user) {
            AuditLogger::log(null, 'login', 'failed', ['email' => $email, 'info' => 'user not found']);
            throw new \Exception('Invalid credentials');
        }

        if (!(int)$user['is_verified']) {
            AuditLogger::log($user['id'], 'login', 'failed', ['email' => $email, 'info' => 'user not is_verified']);
            throw new \Exception('Email not verified');
        }

        if ($user['locked_until'] && new DateTime($user['locked_until']) > new DateTime()) {
            AuditLogger::log($user['id'], 'login', 'failed', ['email' => $email, 'info' => 'user is locked']);
            throw new \Exception('Account is temporarily locked. Try again later.');
        }


        if (!password_verify($password, $user['password_hash'])) {

            $db->table('users')
                ->where('id', '=', $user['id'])
                ->update([
                    'failed_login_attempts' => $user['failed_login_attempts'] + 1
                ]);

            if ($user['failed_login_attempts'] + 1 >= 5) {
                $lockedUntil = (new DateTime('+15 minutes'))->format('Y-m-d H:i:s');
                $db->table('users')
                    ->where('id', '=', $user['id'])
                    ->update([
                        'locked_until' => $lockedUntil
                    ]);
            }
            AuditLogger::log($user['id'], 'login', 'failed', ['email' => $email, 'info' => 'password is wrong']);

            throw new \Exception('Invalid credentials');
        }

        $db->table('users')
            ->where('id', '=', $user['id'])
            ->update([
                'failed_login_attempts' => 0,
                'locked_until' => null
            ]);

        session_start();

        if ((int)$user['is_2fa_enabled'] === 1) {

            if (!$user['twofa_confirmed_at'] || !$user['totp_secret']) {
                return '2fa_setup_required';
            }

            $_SESSION['2fa_user_id'] = $user['id'];
            return '2fa_required';
        }
        AuditLogger::log($user['id'], 'login', 'succeed', ['email' => $email, 'info' => 'user login']);

        $_SESSION['user_id'] = $user['id'];
        return 'logged_in';
    }

    public static function verify2fa(string $code): void
    {
        session_start();

        if (!array_key_exists('2fa_user_id', $_SESSION)) {
            AuditLogger::log($_SESSION['2fa_user_id'], 'verify2fa', 'failed', ['info' => 'user havent 2fa_user_id']);
            throw new \Exception('2FA session expired');
        }

        $db = new Database(config());
        $user = $db->table('users')
            ->where('id', '=', $_SESSION['2fa_user_id'])
            ->first();

        if (!$user || !$user['totp_secret']) {
            throw new \Exception('2FA not configured');
        }

        $totp = TOTP::create($user['totp_secret']);

        if (!$totp->verify($code)) {
            throw new \Exception('Invalid 2FA code');
        }

        AuditLogger::log($user['id'], 'verify2fa', 'succeed', ['info' => 'user login']);

        unset($_SESSION['2fa_user_id']);
        $_SESSION['user_id'] = $user['id'];
    }

    public static function logout(): void
    {
        AuditLogger::log($_SESSION['user_id'], 'logout', 'succeed', ['info' => 'user logout']);

        session_start();
        unset($_SESSION['user_id']);
    }

    public static function verifyEmail(string $token): bool
    {
        $db = new Database(config());

        $tokenDb = $db->table('email_verifications')->where('token', '=', $token)->first();

        if(!$tokenDb)
        {
            AuditLogger::log(null, 'verifyEmail', 'failed', ['info' => 'user token not found']);
            return false;
        }
        $expiresAt = new DateTime($tokenDb['expires_at'], new DateTimeZone('UTC'));
        $now = new DateTime('now', new DateTimeZone('UTC'));

        if ($expiresAt < $now) {
            $db->table('email_verifications')->where('token', '=', $token)->delete();
            return false;
        }

        $db->table('users')->where('id', '=', $tokenDb['user_id'])->update(['is_verified' => 1]);

        $db->table('email_verifications')->where('token', '=', $token)->delete();
        AuditLogger::log($tokenDb['user_id'], 'verifyEmail', 'succeed', ['info' => 'user verify email']);
        return true;
    }
}