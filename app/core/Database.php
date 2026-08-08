<?php
declare(strict_types=1);

namespace App\Core;

use PDO;
use PDOException;

final class Database
{
    private static ?PDO $connection = null;

    private function __construct()
    {
    }

    public static function getConnection(): PDO
    {
        if (self::$connection === null) {
            $config = require dirname(__DIR__, 2) . '/config/database.php';

            $dsn = sprintf(
                'mysql:host=%s;port=%d;dbname=%s;charset=%s',
                $config['host'],
                $config['port'],
                $config['database'],
                $config['charset']
            );

            try {
                self::$connection = new PDO($dsn, $config['username'], $config['password'], [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    // Varias consultas de busqueda (Area, Organization, IsoControl,
                    // IsoDomain, Question, Audit) reutilizan el mismo placeholder
                    // nombrado (:search) varias veces en un mismo query. Con
                    // sentencias preparadas nativas (false) MySQL solo enlaza el
                    // valor a la primera aparicion y lanza
                    // "SQLSTATE[HY093]: Invalid parameter number" en las demas.
                    // Emulando la preparacion, PDO sustituye todas las apariciones
                    // del mismo nombre por el valor enlazado una sola vez.
                    PDO::ATTR_EMULATE_PREPARES => true,
                ]);
            } catch (PDOException $exception) {
                throw new PDOException('No fue posible conectar con la base de datos.', (int) $exception->getCode(), $exception);
            }
        }

        return self::$connection;
    }
}
