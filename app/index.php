<?php

declare(strict_types=1);


require __DIR__ . '/functions/functions.php';
require __DIR__ . '/vendor/autoload.php';


define('RECAPTCHA_SITE_KEY', '6LdfQkUsAAAAABUKJUQ4UqIfIb7sutMF48G9p7iP');
define('RECAPTCHA_SECRET_KEY', '6LdfQkUsAAAAAGzy6t1VM-53eN0WJQrMe67hsE-v');

use App\Router;

$router = new Router();

require __DIR__ . '/routes/web.php';

$response = $router->dispatch();

$response->send();
