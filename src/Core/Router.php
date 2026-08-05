<?php

declare(strict_types=1);

namespace App\Core;

use App\Controllers\Controller;

class Router
{
    private array $routes = [];
    private array $namedRoutes = [];

    public function get(string $path, array $handler, string $name = ''): void
    {
        $this->addRoute('GET', $path, $handler, $name);
    }

    public function post(string $path, array $handler, string $name = ''): void
    {
        $this->addRoute('POST', $path, $handler, $name);
    }

    public function put(string $path, array $handler, string $name = ''): void
    {
        $this->addRoute('PUT', $path, $handler, $name);
    }

    public function delete(string $path, array $handler, string $name = ''): void
    {
        $this->addRoute('DELETE', $path, $handler, $name);
    }

    private function addRoute(string $method, string $path, array $handler, string $name): void
    {
        $pattern = $this->pathToPattern($path);
        $route   = [
            'method'  => $method,
            'path'    => $path,
            'pattern' => $pattern,
            'handler' => $handler,
            'name'    => $name,
        ];
        $this->routes[] = $route;
        if ($name !== '') {
            $this->namedRoutes[$name] = $route;
        }
    }

    private function pathToPattern(string $path): string
    {
        $pattern = preg_replace('/\{([a-zA-Z_]+)\}/', '(?P<$1>[^/]+)', $path);
        return '#^' . $pattern . '$#';
    }

    public function dispatch(Request $request, Response $response): void
    {
        $method = $request->getMethod();
        $uri    = $request->getUri();

        foreach ($this->routes as $route) {
            if ($route['method'] !== $method) {
                continue;
            }
            if (preg_match($route['pattern'], $uri, $matches)) {
                // Extract named params
                $params = array_filter($matches, 'is_string', ARRAY_FILTER_USE_KEY);
                $request->setParams($params);

                [$controllerClass, $action] = $route['handler'];

                /** @var Controller $controller */
                $controller = new $controllerClass($request, $response);
                $controller->$action($request, $response);
                return;
            }
        }

        // 404
        http_response_code(404);
        include ROOT_PATH . '/src/Views/errors/404.php';
    }

    public function url(string $name, array $params = []): string
    {
        if (!isset($this->namedRoutes[$name])) {
            return '/';
        }
        $path = $this->namedRoutes[$name]['path'];
        foreach ($params as $key => $value) {
            $path = str_replace('{' . $key . '}', (string)$value, $path);
        }
        return $path;
    }
}
