<?php

declare(strict_types=1);

namespace App\Core;

class Request
{
    private string $method;
    private string $uri;
    private array $params;
    private array $query;
    private array $body;
    private array $files;
    private array $server;
    private array $headers;

    public function __construct()
    {
        $this->server  = $_SERVER;
        $this->query   = $_GET;
        $this->body    = $_POST;
        $this->files   = $_FILES;
        $this->headers = $this->parseHeaders();

        // Determine actual HTTP method (with _method override)
        $this->method = strtoupper($_SERVER['REQUEST_METHOD'] ?? 'GET');
        if ($this->method === 'POST' && isset($_POST['_method'])) {
            $override = strtoupper($_POST['_method']);
            if (in_array($override, ['PUT', 'PATCH', 'DELETE'], true)) {
                $this->method = $override;
            }
        }

        // Parse URI
        $rawUri    = $_SERVER['REQUEST_URI'] ?? '/';
        $parsed    = parse_url($rawUri);
        $this->uri = rtrim($parsed['path'] ?? '/', '/') ?: '/';

        // Strip the base path if running in a subdirectory
        $scriptDir = dirname($_SERVER['SCRIPT_NAME'] ?? '');
        if ($scriptDir !== '/' && str_starts_with($this->uri, $scriptDir)) {
            $this->uri = substr($this->uri, strlen($scriptDir));
        }
        $this->uri = '/' . ltrim($this->uri, '/');

        $this->params = [];
    }

    private function parseHeaders(): array
    {
        $headers = [];
        foreach ($_SERVER as $key => $value) {
            if (str_starts_with($key, 'HTTP_')) {
                $name           = str_replace('_', '-', substr($key, 5));
                $headers[$name] = $value;
            }
        }
        return $headers;
    }

    public function getMethod(): string
    {
        return $this->method;
    }

    public function getUri(): string
    {
        return $this->uri;
    }

    public function isMethod(string $method): bool
    {
        return $this->method === strtoupper($method);
    }

    public function isGet(): bool
    {
        return $this->method === 'GET';
    }

    public function isPost(): bool
    {
        return $this->method === 'POST';
    }

    public function isPut(): bool
    {
        return $this->method === 'PUT';
    }

    public function isDelete(): bool
    {
        return $this->method === 'DELETE';
    }

    public function get(string $key, mixed $default = null): mixed
    {
        return $this->query[$key] ?? $default;
    }

    public function post(string $key, mixed $default = null): mixed
    {
        return $this->body[$key] ?? $default;
    }

    public function input(string $key, mixed $default = null): mixed
    {
        return $this->body[$key] ?? $this->query[$key] ?? $default;
    }

    public function all(): array
    {
        return array_merge($this->query, $this->body);
    }

    public function only(array $keys): array
    {
        return array_intersect_key($this->all(), array_flip($keys));
    }

    public function setParams(array $params): void
    {
        $this->params = $params;
    }

    public function param(string $key, mixed $default = null): mixed
    {
        return $this->params[$key] ?? $default;
    }

    public function getParams(): array
    {
        return $this->params;
    }

    public function header(string $name, mixed $default = null): mixed
    {
        return $this->headers[strtoupper($name)] ?? $default;
    }

    public function isAjax(): bool
    {
        return ($this->headers['X-REQUESTED-WITH'] ?? '') === 'XMLHttpRequest';
    }

    public function getIp(): string
    {
        return $this->server['REMOTE_ADDR'] ?? '127.0.0.1';
    }

    public function sanitize(string $key, mixed $default = ''): string
    {
        $value = $this->input($key, $default);
        return htmlspecialchars(trim((string)$value), ENT_QUOTES, 'UTF-8');
    }
}
