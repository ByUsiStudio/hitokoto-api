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

/**
 * 读取标量查询参数。
 * PHP 8 起 trim()/strtolower() 等函数收到数组会抛 TypeError，
 * 因此对形如 ?c[]=a 的数组参数统一返回 $default，交由调用方按默认值处理。
 */
function getQueryParam($name, $default = null) {
    if (!isset($_GET[$name]) || !is_scalar($_GET[$name])) {
        return $default;
    }
    return (string)$_GET[$name];
}

/** 按 UTF-8 码点计算字符串长度；未启用 mbstring 时使用 PCRE 等价实现 */
function strLengthUtf8($value) {
    $value = (string)$value;
    if (function_exists('mb_strlen')) {
        return mb_strlen($value, 'UTF-8');
    }
    $count = preg_match_all('/./us', $value, $matches);
    return $count === false ? strlen($value) : $count;
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
