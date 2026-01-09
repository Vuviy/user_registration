<?php


use App\Controller\AboutController;
use App\Controller\AuthController;
use App\Controller\MainController;
use App\Controller\TwoFactorController;
use App\Controller\TwoFactorSetupController;
use App\Controller\VerifyController;

/** @var \App\Router $router */
$router->get('/form', [MainController::class, 'form']);
$router->get('/', [MainController::class, 'index']);

$router->get('/about', [AboutController::class, 'index']);

//Auth
$router->post('/register', [AuthController::class, 'register']);
$router->post('/login', [AuthController::class, 'login']);
$router->get('/logout', [AuthController::class, 'logout']);
//Auth

//2fa
$router->get('/2fa', [TwoFactorController::class, 'form']);
$router->post('/2fa', [TwoFactorController::class, 'verify']);

$router->get('/2fa/setup', [TwoFactorSetupController::class, 'form']);
$router->post('/2fa/setup', [TwoFactorSetupController::class, 'confirm']);
//2fa

$router->get('/verify', [VerifyController::class, 'verify']);