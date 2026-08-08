<?php
declare(strict_types=1);

namespace App\Core;

final class Router
{
    private array $routes = [];

    public function get(string $path, array $handler): void
    {
        $this->add('GET', $path, $handler);
    }

    public function post(string $path, array $handler): void
    {
        $this->add('POST', $path, $handler);
    }

    private function add(string $method, string $path, array $handler): void
    {
        $this->routes[$method][$this->normalize($path)] = $handler;
    }

    public function dispatch(string $method, string $uri): void
    {
        $path = parse_url($uri, PHP_URL_PATH) ?: '/';

        // El front controller siempre vive en .../public/index.php, asi que
        // dirname(SCRIPT_NAME) siempre trae el sufijo "/public". Eso sirve
        // para recortar el prefijo cuando se accede con la ruta larga
        // (/sitio/public/login), pero NO cuando se accede con la ruta corta
        // (/sitio/login) que habilita el .htaccess de la raiz: en ese caso
        // hay que recortar la base del sitio SIN "/public".
        $scriptDir = str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'] ?? ''));
        $siteBase  = preg_replace('#/public$#', '', $scriptDir);

        if ($scriptDir !== '/' && str_starts_with($path, $scriptDir)) {
            $path = substr($path, strlen($scriptDir)) ?: '/';
        } elseif ($siteBase !== '' && str_starts_with($path, $siteBase)) {
            $path = substr($path, strlen($siteBase)) ?: '/';
        }

        $path = $this->normalize($path);
        $handler = $this->routes[$method][$path] ?? null;

        if ($handler === null) {
            ErrorHandler::render(404, 'Pagina no encontrada.');
            return;
        }

        [$controllerClass, $action] = $handler;
        $controller = new $controllerClass();
        $controller->{$action}();
    }

    private function normalize(string $path): string
    {
        $path = '/' . trim($path, '/');
        return $path === '/' ? '/' : rtrim($path, '/');
    }
}
