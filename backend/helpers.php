<?php
declare(strict_types=1);

/**
 * LogixPulse — Backend View & Request Helpers
 */

function h(?string $value): string
{
    return htmlspecialchars($value ?? '', ENT_QUOTES, 'UTF-8');
}

function redirect(string $path): never
{
    header('Location: ' . $path);
    exit;
}

function flash(string $type, string $message): void
{
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    $_SESSION['flash'] = ['type' => $type, 'message' => $message];
}

function show_flash(): void
{
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    if (!empty($_SESSION['flash'])) {
        $flash = $_SESSION['flash'];
        unset($_SESSION['flash']);
        $class = $flash['type'] === 'success' ? 'success' : 'error';
        echo '<div class="alert ' . htmlspecialchars($class, ENT_QUOTES, 'UTF-8') . '">' . h($flash['message']) . '</div>';
    }
}
