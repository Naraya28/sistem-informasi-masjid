<?php
/**
 * Muat konfigurasi lokal tanpa dependency tambahan.
 *
 * Nilai dari file .env project diprioritaskan agar konfigurasi Apache/Windows
 * di luar project tidak menimpa konfigurasi lokal aplikasi.
 */
function loadLocalEnv($path)
{
    $values = array();

    if (!is_readable($path)) {
        return $values;
    }

    $lines = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    if ($lines === false) {
        return $values;
    }

    foreach ($lines as $line) {
        $line = trim($line);
        if ($line === '' || strpos($line, '#') === 0 || strpos($line, '=') === false) {
            continue;
        }

        list($name, $value) = array_map('trim', explode('=', $line, 2));
        if (!preg_match('/^[A-Z_][A-Z0-9_]*$/i', $name)) {
            continue;
        }

        $length = strlen($value);
        if ($length >= 2) {
            $first = $value[0];
            $last = $value[$length - 1];
            if (($first === '"' && $last === '"') || ($first === "'" && $last === "'")) {
                $value = substr($value, 1, -1);
            }
        }

        putenv($name . '=' . $value);
        $_ENV[$name] = $value;
        $_SERVER[$name] = $value;
        $values[$name] = $value;
    }

    return $values;
}

$env = loadLocalEnv(dirname(__DIR__) . DIRECTORY_SEPARATOR . '.env');

$required = array('DB_HOST', 'DB_USER', 'DB_PASS', 'DB_NAME');
$missing = array();
foreach ($required as $name) {
    if (!array_key_exists($name, $env)) {
        $missing[] = $name;
    }
}

if ($missing) {
    die('Konfigurasi database belum lengkap. Salin .env.example menjadi .env dan isi konfigurasi lokal.');
}

$host = $env['DB_HOST'];
$user = $env['DB_USER'];
$pass = $env['DB_PASS'];
$db   = $env['DB_NAME'];

try {
    $conn = @mysqli_connect($host, $user, $pass, $db);
} catch (mysqli_sql_exception $exception) {
    $conn = false;
}

if (!$conn) {
    die('Koneksi database gagal.');
}

mysqli_set_charset($conn, 'utf8mb4');
?>
