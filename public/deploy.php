<?php

/*
 * Webhook استقرار — فقط برای push به شاخه main از گیت‌هاب
 * امنیت: امضای HMAC + بررسی نوع رویداد + بررسی شاخه + قفل ضد اجرای هم‌زمان
 */

$secretFile = __DIR__ . '/../storage/app/deploy_secret.txt';

if (! file_exists($secretFile)) {
    http_response_code(500);
    exit('Secret file missing');
}

$secret = trim(file_get_contents($secretFile));
$payload = file_get_contents('php://input');

// ۱. بررسی امضای HMAC
$signature = $_SERVER['HTTP_X_HUB_SIGNATURE_256'] ?? '';
$hash = 'sha256=' . hash_hmac('sha256', $payload, $secret);

if (! hash_equals($hash, $signature)) {
    http_response_code(403);
    exit('Invalid signature');
}

// ۲. فقط رویداد push پذیرفته می‌شود (نه ping و غیره)
$event = $_SERVER['HTTP_X_GITHUB_EVENT'] ?? '';
if ($event === 'ping') {
    echo 'pong';
    exit;
}
if ($event !== 'push') {
    http_response_code(202);
    exit('Ignored event: ' . htmlspecialchars($event));
}

// ۳. فقط شاخه main باعث استقرار می‌شود
$data = json_decode($payload, true);
$ref = $data['ref'] ?? '';
if ($ref !== 'refs/heads/main') {
    http_response_code(202);
    exit('Ignored branch: ' . htmlspecialchars($ref));
}

// ۴. قفل ضد استقرار هم‌زمان
$lockFile = __DIR__ . '/../storage/app/deploy.lock';
$lock = fopen($lockFile, 'c');
if (! $lock || ! flock($lock, LOCK_EX | LOCK_NB)) {
    http_response_code(429);
    exit('Deploy already in progress');
}

// اجرای استقرار در پس‌زمینه
shell_exec('/home/pardekha/pardekhan/deploy.sh > /home/pardekha/pardekhan/storage/logs/deploy.log 2>&1 &');

flock($lock, LOCK_UN);
fclose($lock);

echo 'Deploy started';
