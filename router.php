<?php

$uri = urldecode(
    parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH)
);

// Explicitly handle static files in the public directory
$publicPath = __DIR__ . '/public' . $uri;

if ($uri !== '/' && file_exists($publicPath)) {
    // Serve the file directly
    $mimeType = mime_content_type($publicPath);

    // Manual mime type overrides for CSS/JS if mime_content_type is wrong
    if (str_ends_with($publicPath, '.css')) {
        $mimeType = 'text/css';
    } elseif (str_ends_with($publicPath, '.js')) {
        $mimeType = 'application/javascript';
    }

    header("Content-Type: $mimeType");
    readfile($publicPath);
    return true;
}

// Special handling for storage (symlink workaround for Windows)
if (str_starts_with($uri, '/storage/')) {
    $storagePath = __DIR__ . '/storage/app/public/' . substr($uri, 9); // Remove '/storage/' prefix
    if (file_exists($storagePath)) {
        $mimeType = mime_content_type($storagePath);
        header("Content-Type: $mimeType");
        readfile($storagePath);
        return true;
    }
}

// Default to Laravel index
require_once __DIR__ . '/public/index.php';
