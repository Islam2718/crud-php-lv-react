<?php
declare(strict_types=1);

require_once __DIR__ . '/ProductClass.php';

if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

function database(): PDO
{
    static $db;
    if ($db instanceof PDO) {
        return $db;
    }

    $host = getenv('DB_HOST') ?: '127.0.0.1';
    $name = getenv('DB_NAME') ?: 'shop_db';
    $user = getenv('DB_USER') ?: 'root';
    $password = getenv('DB_PASSWORD') ?: '';

    $db = new PDO(
        "mysql:host=$host;dbname=$name;charset=utf8mb4",
        $user,
        $password,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
        ]
    );

    return $db;
}

function e(mixed $value): string
{
    return htmlspecialchars((string) $value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

function csrfToken(): string
{
    if (!isset($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }

    return $_SESSION['csrf_token'];
}

function verifyCsrfToken(): void
{
    $token = $_POST['csrf_token'] ?? '';
    if (!is_string($token) || !hash_equals(csrfToken(), $token)) {
        http_response_code(400);
        exit('Invalid form token. Please go back and try again.');
    }
}

function cartCount(): int
{
    return array_sum(array_map('intval', $_SESSION['cart'] ?? []));
}

function redirect(string $location): never
{
    header('Location: ' . $location);
    exit;
}