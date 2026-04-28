<?php

$secret = 'pardekhan_super_secret_123';

$signature = $_SERVER['HTTP_X_HUB_SIGNATURE_256'] ?? '';
$payload = file_get_contents('php://input');

$hash = 'sha256=' . hash_hmac('sha256', $payload, $secret);

if (! hash_equals($hash, $signature)) {
    http_response_code(403);
    exit('Invalid signature');
}

shell_exec('/home/pardekha/pardekhan/deploy.sh > /home/pardekha/pardekhan/storage/logs/deploy.log 2>&1 &');

echo 'Deploy started';
