<?php
declare(strict_types=1);

namespace App\Core;

final class Flash
{
    public static function success(string $message): void
    {
        $_SESSION['_flash']['success'][] = $message;
    }

    public static function error(string $message): void
    {
        $_SESSION['_flash']['danger'][] = $message;
    }

    public static function errors(array $errors): void
    {
        foreach ($errors as $error) {
            self::error($error);
        }
    }

    public static function all(): array
    {
        $messages = $_SESSION['_flash'] ?? [];
        unset($_SESSION['_flash']);
        return $messages;
    }
}
