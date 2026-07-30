<?php
namespace App\Core;

use PDO;
use PDOException;

class Database
{
    private static ?PDO $pdo = null;

    public static function connect(): PDO
    {
        if (self::$pdo !== null) {
            return self::$pdo;
        }

        $config = self::loadConfig();

        $host = $config['DB_HOST'] ?? '127.0.0.1';
        $port = $config['DB_PORT'] ?? '3306';
        $dbname = $config['DB_DATABASE'] ?? 'takimoto_library';
        $user = $config['DB_USERNAME'] ?? 'takimoto_user';
        $password = $config['DB_PASSWORD'] ?? 'takimoto_password';

        $dsn = "mysql:host={$host};port={$port};dbname={$dbname};charset=utf8mb4";

        try {
            self::$pdo = new PDO($dsn, $user, $password, [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            ]);

            return self::$pdo;
        } catch (PDOException $e) {
            http_response_code(500);
            echo json_encode([
                'message' => 'DB接続に失敗しました',
                'error' => $e->getMessage(),
            ], JSON_UNESCAPED_UNICODE);
            exit;
        }
    }

    private static function loadConfig(): array
    {
        $config = [];
        $envPath = __DIR__ . '/../../.env';

        if (is_file($envPath)) {
            foreach (file($envPath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) as $line) {
                if (str_starts_with(trim($line), '#') || !str_contains($line, '=')) {
                    continue;
                }

                [$key, $value] = explode('=', $line, 2);
                $config[trim($key)] = trim($value);
            }
        }

        foreach ($_ENV as $key => $value) {
            $config[$key] = $value;
        }

        foreach ($_SERVER as $key => $value) {
            if (str_starts_with($key, 'DB_')) {
                $config[$key] = $value;
            }
        }

        return $config;
    }
}
