<?php
declare(strict_types=1);

require_once __DIR__ . '/../app/Core/Database.php';
require_once __DIR__ . '/../app/Models/Book.php';
require_once __DIR__ . '/../app/Models/Student.php';
require_once __DIR__ . '/../app/Models/Loan.php';

use App\Models\Book;
use App\Models\Loan;
use App\Models\Student;

function h(?string $value): string
{
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}

function redirectTo(string $path, array $params = []): void
{
    $query = $params === [] ? '' : '?' . http_build_query($params);

    header('Location: ' . $path . $query);
    exit;
}

function requiredValue(array $data, string $key): string
{
    return trim((string) ($data[$key] ?? ''));
}

function renderMessage(): void
{
    if (isset($_GET['message'])) {
        echo '<p>' . h($_GET['message']) . '</p>';
    }

    if (isset($_GET['error'])) {
        echo '<p>' . h($_GET['error']) . '</p>';
    }
}

