<?php
// PHP yerleşik web sunucusu için router

$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$uri = urldecode($uri);

// Statik dosyalar için (css, js, resimler vb.)
$filePath = __DIR__ . '/public' . $uri;
if ($uri !== '/' && file_exists($filePath) && !is_dir($filePath)) {
    // MIME type'ı belirle
    $extension = pathinfo($filePath, PATHINFO_EXTENSION);
    $mimeTypes = [
        'css' => 'text/css',
        'js' => 'application/javascript',
        'json' => 'application/json',
        'png' => 'image/png',
        'jpg' => 'image/jpeg',
        'jpeg' => 'image/jpeg',
        'gif' => 'image/gif',
        'svg' => 'image/svg+xml',
        'ico' => 'image/x-icon',
    ];
    
    if (isset($mimeTypes[$extension])) {
        header('Content-Type: ' . $mimeTypes[$extension]);
        readfile($filePath);
        return true;
    }
    
    // Bilinmeyen dosya türleri için PHP'nin kendi işlemesine izin ver
    return false;
}

// .php olmadan yönlendirme
$routes = [
    '/' => '/index.php',
    '/login' => '/login.php',
    '/logout' => '/logout.php',
    '/index' => '/index.php',
];

if (array_key_exists($uri, $routes)) {
    require __DIR__ . '/public' . $routes[$uri];
    return true;
}

// Dosya varsa direk çalıştır
if (file_exists(__DIR__ . '/public' . $uri)) {
    return false;
}

// 404
http_response_code(404);
echo "404 - Sayfa bulunamadı";
return true;

