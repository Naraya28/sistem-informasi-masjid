<?php
/**
 * Muat konfigurasi lokal tanpa dependency tambahan.
 *
 * Environment dari web server tetap diprioritaskan. File .env hanya mengisi
 * variabel yang belum tersedia dan tidak boleh dimasukkan ke repository.
 */
function loadLocalEnv($path)
{
    if (!is_readable($path)) {
        return;
    }

    $lines = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    if ($lines === false) {
        return;
    }

    foreach ($lines as $line) {
        $line = trim($line);
        if ($line === '' || strpos($line, '#') === 0 || strpos($line, '=') === false) {
            continue;
        }

        list($name, $value) = array_map('trim', explode('=', $line, 2));
        if (!preg_match('/^[A-Z_][A-Z0-9_]*$/i', $name) || getenv($name) !== false) {
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
    }
}

loadLocalEnv(dirname(__DIR__) . DIRECTORY_SEPARATOR . '.env');

$required = array('DB_HOST', 'DB_USER', 'DB_PASS', 'DB_NAME');
$missing = array();
foreach ($required as $name) {
    if (getenv($name) === false) {
        $missing[] = $name;
    }
}

if ($missing) {
    die('Konfigurasi database belum lengkap. Salin .env.example menjadi .env dan isi konfigurasi lokal.');
}

$host = getenv('DB_HOST');
$user = getenv('DB_USER');
$pass = getenv('DB_PASS');
$db   = getenv('DB_NAME');

$conn = mysqli_connect($host, $user, $pass, $db);

if (!$conn) {
    die('Koneksi database gagal.');
}

mysqli_set_charset($conn, 'utf8mb4');
?>
