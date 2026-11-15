<?php

namespace App\Core;

class Router
{
    // Store all registered routes
    private array $routes = [];

    // Register a GET route
    public function get(string $uri, callable|array $action): void
    {
        $this->addRoute('GET', $uri, $action);
    }

    // Register a POST route
    public function post(string $uri, callable|array $action): void
    {
        $this->addRoute('POST', $uri, $action);
    }

    // Register a POST route
    public function put(string $uri, callable|array $action): void
    {
        $this->addRoute('PUT', $uri, $action);
    }

    // Register a PATCH route
    public function patch(string $uri, callable|array $action): void
    {
        $this->addRoute('PATCH', $uri, $action);
    }

    // Register a DELETE route
    public function delete(string $uri, callable|array $action): void
    {
        $this->addRoute('DELETE', $uri, $action);
    }

    // Store route together with its HTTP method and action
    private function addRoute(string $method, string $uri, callable|array $action): void
    {
        $this->routes[] = [
            'method' => $method,
            'uri' => $uri,
            'action' => $action
        ];
    }
    // Matching the incoming request against registered routes.

    public function dispatch(): void
    {
        // GET the HTTP method
        $method = $_SERVER['REQUEST_METHOD'];

        // Get the requested URL path, excluding query parameters
        $uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

        // Remove trailing slashes, except for home page
        $uri = rtrim($uri, '/') ?: '/';

        // Check every registered route
        foreach ($this->routes as $route) {
            // compare http method and url
            if ($route['method'] === $method && $route['uri'] === $uri) {
                // Get the controller and method
                $action = $route['action'];

                // Execute a controller method
                if (is_array($action)) {
                    [$controller, $controlleMethod] = $action;

                    // Create the controller object
                    $controller = new $controller();

                    // call the requested controller method
                    $controller->$controlleMethod();
                    return;
                }

                // Execute a regular callable function
                call_user_func($action);
                return;
            }
        }

        // No matching route found
        http_response_code(404);
        echo '404 - Page Not Found.';
    }
}
