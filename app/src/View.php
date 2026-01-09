<?php

namespace App;

final class View
{
    public static function make(string $view, array $params = []): string
    {
        extract($params);

        ob_start();
        require __DIR__ . '/../views/' . $view . '.php';
        return ob_get_clean();
    }

}