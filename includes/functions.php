<?php
declare(strict_types=1);

require_once __DIR__ . '/config.php';

function e(?string $value): string
{
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}

function load_content(): array
{
    if (!file_exists(CONTENT_FILE)) {
        return [];
    }

    $raw = file_get_contents(CONTENT_FILE);
    $data = json_decode((string) $raw, true);

    return is_array($data) ? $data : [];
}

function save_content(array $data): bool
{
    $json = json_encode($data, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
    return file_put_contents(CONTENT_FILE, $json . PHP_EOL) !== false;
}

function image_url(?string $path): string
{
    $path = trim((string) $path);
    if ($path === '') {
        return 'assets/images/placeholders/hero.svg';
    }

    if (preg_match('~^https?://~i', $path)) {
        return $path;
    }

    return ltrim(str_replace('\\', '/', $path), '/');
}

function current_year(): string
{
    return date('Y');
}

function admin_logged_in(): bool
{
    return !empty($_SESSION['admin_logged_in']);
}

function redirect(string $url): void
{
    header('Location: ' . $url);
    exit;
}

function require_admin(): void
{
    if (!admin_logged_in()) {
        redirect('login.php');
    }
}

function csrf_token(): string
{
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(24));
    }

    return $_SESSION['csrf_token'];
}

function verify_csrf(?string $token): bool
{
    return is_string($token) && hash_equals((string) ($_SESSION['csrf_token'] ?? ''), $token);
}

function post_string(string $key, string $default = ''): string
{
    return isset($_POST[$key]) ? trim((string) $_POST[$key]) : $default;
}

function build_repeater(string $section, array $fields): array
{
    $raw = $_POST[$section] ?? [];
    if (!is_array($raw)) {
        return [];
    }

    $count = 0;
    foreach ($fields as $field) {
        if (isset($raw[$field]) && is_array($raw[$field])) {
            $count = max($count, count($raw[$field]));
        }
    }

    $items = [];
    for ($i = 0; $i < $count; $i++) {
        $item = [];
        $hasContent = false;

        foreach ($fields as $field) {
            $value = '';
            if (isset($raw[$field][$i])) {
                $value = trim((string) $raw[$field][$i]);
            }
            if ($value !== '') {
                $hasContent = true;
            }
            $item[$field] = $value;
        }

        if ($hasContent) {
            $items[] = $item;
        }
    }

    return $items;
}

function ensure_upload_dir(): bool
{
    if (is_dir(UPLOAD_DIR)) {
        return true;
    }

    return mkdir(UPLOAD_DIR, 0775, true);
}

function relative_upload_path(string $filename): string
{
    return UPLOAD_URL . '/' . ltrim($filename, '/');
}

function save_uploaded_files(array $files): array
{
    $results = [
        'uploaded' => [],
        'errors' => [],
    ];

    if (!ensure_upload_dir()) {
        $results['errors'][] = 'Не удалось создать папку uploads.';
        return $results;
    }

    if (empty($files['name']) || !is_array($files['name'])) {
        return $results;
    }

    $allowedExtensions = ['jpg', 'jpeg', 'png', 'webp', 'gif'];
    $subDir = date('Ym');
    $targetDir = UPLOAD_DIR . '/' . $subDir;

    if (!is_dir($targetDir) && !mkdir($targetDir, 0775, true)) {
        $results['errors'][] = 'Не удалось создать папку для текущего месяца.';
        return $results;
    }

    foreach ($files['name'] as $index => $originalName) {
        $errorCode = $files['error'][$index] ?? UPLOAD_ERR_NO_FILE;
        if ($errorCode === UPLOAD_ERR_NO_FILE) {
            continue;
        }

        if ($errorCode !== UPLOAD_ERR_OK) {
            $results['errors'][] = 'Ошибка загрузки файла: ' . $originalName;
            continue;
        }

        $tmpName = $files['tmp_name'][$index] ?? '';
        $extension = strtolower(pathinfo((string) $originalName, PATHINFO_EXTENSION));

        if (!in_array($extension, $allowedExtensions, true)) {
            $results['errors'][] = 'Недопустимый формат файла: ' . $originalName;
            continue;
        }

        $safeBase = preg_replace('~[^a-zA-Z0-9_-]+~', '-', pathinfo((string) $originalName, PATHINFO_FILENAME));
        $safeBase = trim((string) $safeBase, '-');
        $safeBase = $safeBase !== '' ? $safeBase : 'file';
        $newName = $safeBase . '-' . bin2hex(random_bytes(4)) . '.' . $extension;
        $destination = $targetDir . '/' . $newName;

        if (!move_uploaded_file($tmpName, $destination)) {
            $results['errors'][] = 'Не удалось сохранить файл: ' . $originalName;
            continue;
        }

        $results['uploaded'][] = relative_upload_path($subDir . '/' . $newName);
    }

    return $results;
}

function list_uploaded_files(): array
{
    if (!is_dir(UPLOAD_DIR)) {
        return [];
    }

    $rii = new RecursiveIteratorIterator(new RecursiveDirectoryIterator(UPLOAD_DIR, FilesystemIterator::SKIP_DOTS));
    $files = [];

    foreach ($rii as $file) {
        if ($file->isDir()) {
            continue;
        }

        $absolute = $file->getPathname();
        $relative = str_replace(str_replace('\\', '/', UPLOAD_DIR) . '/', '', str_replace('\\', '/', $absolute));
        $files[] = [
            'path' => relative_upload_path($relative),
            'name' => basename($absolute),
            'size' => $file->getSize(),
            'modified' => date('d.m.Y H:i', $file->getMTime()),
            'timestamp' => $file->getMTime(),
        ];
    }

    usort($files, static function (array $a, array $b): int {
        return ($b['timestamp'] ?? 0) <=> ($a['timestamp'] ?? 0);
    });

    return $files;
}

function format_file_size(int $bytes): string
{
    $units = ['B', 'KB', 'MB', 'GB'];
    $i = 0;

    while ($bytes >= 1024 && $i < count($units) - 1) {
        $bytes /= 1024;
        $i++;
    }

    return round($bytes, 1) . ' ' . $units[$i];
}
