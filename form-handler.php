<?php

declare(strict_types=1);

function redirect_to(string $target)
{
    header('Location: ' . $target, true, 302);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect_to('/contact?status=invalid-method');
}

$formType = strtolower(trim((string)($_POST['form_type'] ?? '')));
$honeypot = trim((string)($_POST['website'] ?? ''));

if ($honeypot !== '') {
    redirect_to('/thank-you?form=' . rawurlencode($formType ?: 'contact'));
}

$allowedTypes = ['contact', 'get_started'];
if (!in_array($formType, $allowedTypes, true)) {
    redirect_to('/contact?status=invalid-form');
}

$firstName = trim((string)($_POST['first_name'] ?? $_POST['firstName'] ?? ''));
$lastName = trim((string)($_POST['last_name'] ?? $_POST['lastName'] ?? ''));
$email = trim((string)($_POST['email'] ?? ''));
$phone = trim((string)($_POST['phone'] ?? ''));
$topic = trim((string)($_POST['topic'] ?? ''));
$message = trim((string)($_POST['message'] ?? ''));

if ($firstName === '' || $lastName === '' || $email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $target = $formType === 'get_started' ? '/get-started?status=invalid' : '/contact?status=invalid';
    redirect_to($target);
}

$clean = static function (string $value): string {
    return preg_replace('/[\r\n]+/', ' ', $value) ?? '';
};

$fullName = trim($clean($firstName . ' ' . $lastName));
$recipient = getenv('FAMILYVEST_LEADS_EMAIL') ?: 'hello@familyvest.com';
$subject = $formType === 'get_started' ? 'FamilyVest Lead: Get Started Form' : 'FamilyVest Lead: Contact Form';

$bodyLines = [
    'Form Type: ' . $formType,
    'Name: ' . $fullName,
    'Email: ' . $clean($email),
    'Phone: ' . $clean($phone),
    'Topic: ' . $clean($topic),
    'Message:',
    $message !== '' ? $message : '(none provided)',
    '',
    'Submitted At (UTC): ' . gmdate('c'),
    'IP Address: ' . ($_SERVER['REMOTE_ADDR'] ?? 'unknown'),
    'User Agent: ' . ($_SERVER['HTTP_USER_AGENT'] ?? 'unknown'),
];

$body = implode("\n", $bodyLines);
$headers = [
    'From: FamilyVest Website <no-reply@familyvest.com>',
    'Reply-To: ' . $clean($email),
    'Content-Type: text/plain; charset=UTF-8',
];

$mailSent = @mail($recipient, $subject, $body, implode("\r\n", $headers));

if (!$mailSent) {
    $logLine = "[" . gmdate('c') . "] mail_failed form=" . $formType . " email=" . $clean($email) . PHP_EOL;
    @file_put_contents(__DIR__ . '/form-submissions.log', $logLine, FILE_APPEND | LOCK_EX);
    $target = $formType === 'get_started' ? '/get-started?status=error' : '/contact?status=error';
    redirect_to($target);
}

if (function_exists('header_remove')) {
    header_remove('X-Powered-By');
}

redirect_to('/thank-you?form=' . rawurlencode($formType));
