<?php

namespace App\Controller;

use App\DB\Database;
use App\Response;
use App\View;

final class AboutController
{
    public function index(): Response
    {
        $db = new Database(config());

        session_start();

        $user = null;

        if (array_key_exists('user_id', $_SESSION)) {
            $user = $db->table('users')->where('id', '=', $_SESSION['user_id'])->first();
            if (count($user) <= 0) {
                $user = null;
            }
        }

        return new Response(View::make('about', ['user' => $user]));
    }
}