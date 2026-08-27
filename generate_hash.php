<?php
if (PHP_SAPI !== 'cli') {
    http_response_code(403);
    exit('File ini hanya dapat dijalankan melalui terminal.');
}

$password = $argv[1] ?? '';
if ($password === '') {
    fwrite(STDERR, "Penggunaan: php generate_hash.php \"password-baru\"\n");
    exit(1);
}

echo password_hash($password, PASSWORD_DEFAULT) . PHP_EOL;
