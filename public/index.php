<?php
declare(strict_types=1);

use App\Core\Auth;
use App\Core\Router;

define('BASE_PATH', dirname(__DIR__));

// Compute BASE_URL. Prefer explicit APP_URL (use its path component) when provided
// via environment (recommended for VirtualHosts). Fallback to deriving from
// SCRIPT_NAME for setups without an app URL.
$appUrl = getenv('APP_URL') ?: '';
if ($appUrl === '') {
    $appUrl = $_SERVER['APP_URL'] ?? $_SERVER['REDIRECT_APP_URL'] ?? '';
}

if ($appUrl !== '') {
    $appPath = parse_url($appUrl, PHP_URL_PATH) ?: '';
    define('BASE_URL', rtrim($appPath, '/'));
} else {
    // Use the real script directory for fallback so subfolder installs keep /public in generated URLs.
    $derived = rtrim(str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'] ?? '')), '/');
    $host = $_SERVER['HTTP_HOST'] ?? '';
    if (stripos($host, 'consultora.local') !== false) {
        define('BASE_URL', '');
    } else {
        define('BASE_URL', $derived);
    }
}

require BASE_PATH . '/app/core/helpers.php';

spl_autoload_register(static function (string $class): void {
    $prefix = 'App\\';
    if (! str_starts_with($class, $prefix)) {
        return;
    }

    $relativeClass = substr($class, strlen($prefix));
    $parts = explode('\\', $relativeClass);
    // Las carpetas reales de app/ estan en minuscula (controllers, core, models)
    // mientras que el namespace usa PascalCase (Controllers, Core, Models). En
    // Windows (WAMP) el filesystem ignora mayusculas y esto pasa desapercibido,
    // pero en el hosting Linux es sensible a mayusculas y el autoload fallaba.
    $parts[0] = strtolower($parts[0]);
    $file = BASE_PATH . '/app/' . implode('/', $parts) . '.php';
    if (is_file($file)) {
        require $file;
    }
});

$app = require BASE_PATH . '/config/app.php';
date_default_timezone_set($app['timezone']);

Auth::startSession();

$router = new Router();
require BASE_PATH . '/routes/web.php';
$router->dispatch($_SERVER['REQUEST_METHOD'], $_SERVER['REQUEST_URI']);
