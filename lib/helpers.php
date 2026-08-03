<?php
/**
 * 共享辅助函数
 * 供 index.php 与各输出模板（templates/*.php）共用。
 */

/** 统一的 JSON 编码标志：不转义 Unicode、不转义正斜杠（保持 URL 可读、TOML/JS 字符串合法） */
const JSON_OUT = JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES;

/**
 * 计算当前 API 的基础地址（index.php 所在目录，以 / 结尾）。
 * 服务器会将对该目录的请求自动解析为 /index.php，故无需显式写出脚本名。
 */
function getApiBaseUrl() {
    $https = (!empty($_SERVER['HTTPS']) && strtolower($_SERVER['HTTPS']) !== 'off')
        || (($_SERVER['SERVER_PORT'] ?? '') == 443)
        || (($_SERVER['HTTP_X_FORWARDED_PROTO'] ?? '') === 'https');
    $scheme = $https ? 'https' : 'http';
    $host = $_SERVER['HTTP_HOST'] ?? ($_SERVER['SERVER_NAME'] ?? 'localhost');
    $scriptName = $_SERVER['SCRIPT_NAME'] ?? '/index.php';
    $dir = str_replace('\\', '/', dirname($scriptName));
    if ($dir === '.' || $dir === '/') {
        $dir = '';
    }
    return $scheme . '://' . $host . $dir . '/';
}

/** Shell 单引号转义：值用单引号包裹，内部单引号转为 '\'' */
function escSh($v) {
    if ($v === null) return "''";
    return "'" . str_replace("'", "'\\''", (string)$v) . "'";
}

/** PowerShell 单引号转义：值用单引号包裹，内部单引号转为 '' */
function escPs1($v) {
    if ($v === null) return "''";
    return "'" . str_replace("'", "''", (string)$v) . "'";
}
