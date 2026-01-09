<?php

namespace App;

final class Router
{
    private array $routes = [];

    public function get(string $uri, array $action): void
    {
        $this->routes['GET'][$uri] = $action;
    }

    public function post(string $uri, array $action): void
    {
        $this->routes['POST'][$uri] = $action;
    }

    public function dispatch(): Response
    {
        $method = $_SERVER['REQUEST_METHOD'];
        $uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

        if (!array_key_exists($uri, $this->routes[$method])) {
            return new Response('404 Not Found', 404);
        }

        [$controller, $methodName] = $this->routes[$method][$uri];

        $controllerInstance = new $controller();

        return $controllerInstance->$methodName();
    }

}