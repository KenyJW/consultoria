<?php
declare(strict_types=1);

namespace App\Core;

abstract class BaseController
{
    protected function view(string $view, array $data = [], string $layout = 'main'): void
    {
        extract($data, EXTR_SKIP);

        $viewPath = dirname(__DIR__) . '/views/' . $view . '.php';
        if (! is_file($viewPath)) {
            ErrorHandler::render(500, 'Vista no encontrada.');
            return;
        }

        ob_start();
        require $viewPath;
        $content = ob_get_clean();

        $layoutPath = dirname(__DIR__) . '/views/layouts/' . $layout . '.php';
        if (is_file($layoutPath)) {
            require $layoutPath;
            return;
        }

        echo $content;
    }

    protected function redirect(string $path): never
    {
        header('Location: ' . BASE_URL . $path);
        exit;
    }

    protected function back(string $fallback): never
    {
        $target = $_SERVER['HTTP_REFERER'] ?? BASE_URL . $fallback;
        header('Location: ' . $target);
        exit;
    }
}
