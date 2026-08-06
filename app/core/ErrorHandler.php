<?php
declare(strict_types=1);

namespace App\Core;

final class ErrorHandler
{
    public static function render(int $statusCode, string $message): void
    {
        http_response_code($statusCode);
        $title = "Error {$statusCode}";
        $content = '<div class="alert alert-danger"><strong>' . e($title) . '</strong><br>' . e($message) . '</div>';
        $layoutPath = dirname(__DIR__) . '/views/layouts/main.php';
        if (Auth::check() && is_file($layoutPath)) {
            require $layoutPath;
            return;
        }
        echo $content;
    }
}
