<?php

function dd(mixed ...$args): void
{
    echo '<pre>';
    foreach ($args as $arg) {
        if (is_countable($arg)) {
            print_r($arg);
        } else {
            var_dump($arg);
        }
        echo "\n";
    }
    echo '</pre>';
    die();
}

function config()
{
    return require __DIR__ . '/../config/database.php';
}