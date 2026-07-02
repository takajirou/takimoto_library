<?php
declare(strict_types=1);

function h(?string $value): string
{
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
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
