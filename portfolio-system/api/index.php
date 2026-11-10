<?php
// api/index.php
$request_uri = $_SERVER['REQUEST_URI'];
$path = parse_url($request_uri, PHP_URL_PATH);
$path = trim($path, '/');

// If the path is empty, load the homepage
if ($path === '') {
    chdir(__DIR__ . '/..');
    require __DIR__ . '/../index.php';
    exit;
}

// If it's a project details page (e.g., project-details?slug=...)
if (str_starts_with($path, 'project-details')) {
    chdir(__DIR__ . '/..');
    require __DIR__ . '/../project-details.php';
    exit;
}

// Map the URL to the correct PHP file in the root directory
$file = __DIR__ . '/../' . $path . '.php';

if (file_exists($file)) {
    chdir(__DIR__ . '/..'); // Change directory so includes work properly
    require $file;
} else {
    // 404 handling
    http_response_code(404);
    echo "Page not found: " . htmlspecialchars($path);
}