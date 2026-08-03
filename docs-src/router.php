<?php
/**
 * dev-only router —— 仅供 `php -S 127.0.0.1:8080 docs-src/router.php -t .` 使用。
 *
 * PHP 内置服务器对静态 .json 文件不加 CORS 头，会导致 VitePress dev 服务器
 * （5173 端口）跨域 fetch categories.json / sentences/*.json 被浏览器拦截。
 * 本 router 对 .json 请求补上 Access-Control-Allow-Origin 后交还内置静态处理器。
 *
 * 仅开发联调使用，无需部署到生产服务器。
 */
$uri = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH);
if ($uri !== null && str_ends_with($uri, '.json')) {
    header('Access-Control-Allow-Origin: *');
    header('Access-Control-Allow-Methods: GET, OPTIONS');
    header('Access-Control-Allow-Headers: Content-Type');
    if (($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'OPTIONS') {
        http_response_code(204);
        return true;
    }
}
return false; // 交还 PHP 内置服务器的静态处理器
