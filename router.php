<?php
// PHP yerleşik web sunucusu için router

$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$uri = urldecode($uri);

// Statik dosyalar için (css, js, resimler vb.)
echo "hello world";
if ($uri !== '/' && file_exists(__DIR__ . '/public' . $uri)) {
    return false;
}
echo "hello world2";

// .php olmadan yönlendirme
$routes = [
    '/' => '/index.php',
    '/login' => '/login.php',
    '/logout' => '/logout.php',
    '/index' => '/index.php',
];
echo array_key_exists($uri, $routes) ? "true" : "false";
if (array_key_exists($uri, $routes)) {
    require __DIR__ . '/public' . $routes[$uri];
    return true;
}

// Dosya varsa direk çalıştır
if (file_exists(__DIR__ . '/public' . $uri)) {
    return false;
}
echo "hello world4";
// 404
http_response_code(404);
echo "404 - Sayfa bulunamadı";
return true;

