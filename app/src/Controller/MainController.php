<?php

namespace App\Controller;


use App\DB\Database;
use App\Response;
use App\View;

final class MainController
{
    public function index(): Response
    {
        $db = new Database(config());

//        $users = $db->table("users")->get();
//
//        dd($users);
//        $db->table('users')->where('id', '=', 4)->update(['is_2fa_enabled' => 1]);
//        $user = $db->table('users')->get();


//        $aud = $db->table("audit_logs")->get();
//
//        dd($aud);
        session_start();

        $user = null;

        if (array_key_exists('user_id', $_SESSION)) {
            $user = $db->table('users')->where('id', '=', $_SESSION['user_id'])->first();
            if (count($user) <= 0) {
                $user = null;
            }
        }


        return new Response(View::make('home', ['user' => $user]));
    }

    public function form(): Response
    {
//        $db = new Database(config());

        return new Response(View::make('form'));
    }
}