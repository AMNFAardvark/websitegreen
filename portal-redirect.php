<?php

declare(strict_types=1);

$email = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' || $_SERVER['REQUEST_METHOD'] === 'GET') {
    $email = trim((string)($_REQUEST['email'] ?? ''));
}

$target = 'https://app.farther.com';
if ($email !== '' && filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $target .= '?email=' . rawurlencode($email);
}

header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
header('Pragma: no-cache');
header('Referrer-Policy: no-referrer');
header('Location: ' . $target, true, 302);
exit;
