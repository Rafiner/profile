<?php
// includes/functions.php
declare(strict_types=1);

if (session_status() === PHP_SESSION_NONE) {
    session_start([
        'cookie_httponly' => true,
        'cookie_samesite' => 'Lax',
        'use_strict_mode' => true,
    ]);
}

require_once __DIR__ . '/../config/database.php';

function e(?string $v): string { return htmlspecialchars((string)$v, ENT_QUOTES, 'UTF-8'); }
function redirect(string $to): never { header('Location: ' . $to); exit; }
function is_admin_logged_in(): bool { return !empty($_SESSION['admin_id']); }
function require_admin(): void { if (!is_admin_logged_in()) redirect('login.php'); }

function csrf_token(): string {
    if (empty($_SESSION['csrf'])) $_SESSION['csrf'] = bin2hex(random_bytes(32));
    return $_SESSION['csrf'];
}
function csrf_field(): string {
    return '<input type="hidden" name="csrf" value="' . e(csrf_token()) . '">';
}
function verify_csrf(): void {
    $t = $_POST['csrf'] ?? '';
    if (!is_string($t) || !hash_equals($_SESSION['csrf'] ?? '', $t)) {
        http_response_code(419); die('Invalid CSRF token. Reload the page.');
    }
}

function flash(string $key, ?string $msg = null): ?string {
    if ($msg !== null) { $_SESSION['flash'][$key] = $msg; return null; }
    $v = $_SESSION['flash'][$key] ?? null; unset($_SESSION['flash'][$key]); return $v;
}

function slugify(string $s): string {
    $s = strtolower(trim($s));
    $s = preg_replace('/[^a-z0-9]+/', '-', $s) ?? '';
    return trim($s, '-') ?: bin2hex(random_bytes(3));
}

// ---------------------------------------------------------
// UPLOAD TO CLOUDINARY
// ---------------------------------------------------------
function save_upload(array $file, string $kind = 'image'): ?string {
    if (!isset($file['tmp_name']) || $file['error'] === UPLOAD_ERR_NO_FILE) return null;
    if ($file['error'] !== UPLOAD_ERR_OK) throw new RuntimeException('Upload error code ' . $file['error']);
    if ($file['size'] > 8 * 1024 * 1024) throw new RuntimeException('File too large (max 8MB).');

    // If we are running locally and no Cloudinary credentials exist, fallback to local saving
    $cloudName = getenv('CLOUDINARY_CLOUD_NAME');
    $apiKey = getenv('CLOUDINARY_API_KEY');
    $apiSecret = getenv('CLOUDINARY_API_SECRET');

    if (!$cloudName || !$apiKey || !$apiSecret) {
        // Local fallback
        $allowed = [
            'image' => ['image/jpeg' => 'jpg', 'image/png' => 'png', 'image/webp' => 'webp', 'image/gif' => 'gif'],
            'pdf'   => ['application/pdf' => 'pdf'],
        ];
        $mime = mime_content_type($file['tmp_name']) ?: '';
        if (!isset($allowed[$kind][$mime])) throw new RuntimeException('Invalid file type.');

        $dir = __DIR__ . '/../assets/uploads/';
        if (!is_dir($dir)) mkdir($dir, 0775, true);

        $name = bin2hex(random_bytes(12)) . '.' . $allowed[$kind][$mime];
        if (!move_uploaded_file($file['tmp_name'], $dir . $name)) throw new RuntimeException('Failed to store upload.');
        return $name; // Returns just the file name for local
    }

    // Cloudinary Upload
    $timestamp = time();
    $signature = sha1("timestamp={$timestamp}{$apiSecret}");
    $mime = mime_content_type($file['tmp_name']) ?: 'application/octet-stream';

    $postFields = [
        'file' => new CURLFile($file['tmp_name'], $mime, $file['name']),
        'api_key' => $apiKey,
        'timestamp' => $timestamp,
        'signature' => $signature
    ];

    $ch = curl_init("https://api.cloudinary.com/v1_1/{$cloudName}/auto/upload");
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $postFields);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    
    $response = curl_exec($ch);
    $error = curl_error($ch);
    curl_close($ch);

    if ($error) throw new RuntimeException('Cloudinary connection failed: ' . $error);

    $json = json_decode($response, true);
    if (isset($json['secure_url'])) {
        return $json['secure_url']; // Returns full https://res.cloudinary.com/... URL
    }

    throw new RuntimeException('Cloudinary upload failed: ' . ($json['error']['message'] ?? $response));
}

function delete_upload(?string $url): void {
    if (!$url) return;
    
    // If it's a Cloudinary URL, use their API to delete it (keeps your storage clean)
    if (str_starts_with($url, 'http')) {
        $cloudName = getenv('CLOUDINARY_CLOUD_NAME');
        $apiKey = getenv('CLOUDINARY_API_KEY');
        $apiSecret = getenv('CLOUDINARY_API_SECRET');
        if (!$cloudName || !$apiKey || !$apiSecret) return;

        // Extract public_id from URL (e.g. folder/filename without extension)
        $parts = explode('/upload/', $url);
        if (count($parts) < 2) return;
        $publicId = preg_replace('/\.[^.]+$/', '', end($parts));
        if (str_contains($publicId, '/')) {
             // Remove version string like v1234567/ if present
             $publicId = preg_replace('/^v\d+\//', '', $publicId);
        }

        $timestamp = time();
        $signature = sha1("public_id={$publicId}&timestamp={$timestamp}{$apiSecret}");

        $ch = curl_init("https://api.cloudinary.com/v1_1/{$cloudName}/image/destroy");
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, [
            'public_id' => $publicId,
            'api_key' => $apiKey,
            'timestamp' => $timestamp,
            'signature' => $signature
        ]);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_exec($ch);
        curl_close($ch);
        return;
    }

    // Local file deletion
    $p = __DIR__ . '/../assets/uploads/' . basename($url);
    if (is_file($p)) @unlink($p);
}

function get_settings(PDO $pdo): array {
    $rows = $pdo->query('SELECT setting_key, setting_value FROM site_settings')->fetchAll();
    $out = [];
    foreach ($rows as $r) $out[$r['setting_key']] = $r['setting_value'];
    return $out;
}