<?php

$secretFile = __DIR__ . '/../storage/app/deploy_secret.txt';

if (! file_exists($secretFile)) {
    http_response_code(500);
    exit('Secret file missing');
}

$secret = trim(file_get_contents($secretFile));

$signature = $_SERVER['HTTP_X_HUB_SIGNATURE_256'] ?? '';
$payload = file_get_contents('php://input');

$hash = 'sha256=' . hash_hmac('sha256', $payload, $secret);

if (! hash_equals($hash, $signature)) {
    http_response_code(403);
    exit('Invalid signature');
}

shell_exec('/home/pardekha/pardekhan/deploy.sh > /home/pardekha/pardekhan/storage/logs/deploy.log 2>&1 &');

echo 'Deploy started';
