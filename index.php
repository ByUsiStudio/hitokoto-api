<?php
/**
 * 随机一言 API
 * 支持分类: a, b, c, d, e, f, g, h, i, j, k, l
 *
 * 参数说明（保留原有 + 新增）：
 *   form    - 输出格式（原有）：json | text（默认）| xml | ini
 *   encode  - 输出格式（新增，别名，优先级高于 form），额外支持：json5 | toml | js | sh | bat | ps1
 *   c       - 分类筛选（新增）：单个分类如 ?c=a ，多个分类用逗号分隔如 ?c=a,b,c
 *             可选值：a(动画) b(漫画) c(游戏) d(文学) e(原创) f(网络)
 *                     g(其他) h(影视) i(诗词) j(网易云) k(哲学) l(抖机灵)
 *   charset - 字符集（新增）：默认 utf-8
 *
 * 输出模板位于 templates/ 目录，每个格式一个独立文件；新增格式只需新增模板文件，
 * 支持的格式列表会自动从该目录发现。
 *
 * 元信息说明：
 *   除 text 格式外，所有格式都会返回 API 地址、开源仓库地址与开源协议（AGPLv3）。
 */

require __DIR__ . '/lib/helpers.php';

header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(204);
    exit;
}

$hasApiParams = isset($_GET['form']) || isset($_GET['encode'])
    || isset($_GET['c']) || isset($_GET['charset'])
    || isset($_GET['callback']) || isset($_GET['select'])
    || isset($_GET['min_length']) || isset($_GET['max_length']);

if (!$hasApiParams) {
    header('Location: ./docs.html', true, 302);
    exit;
}

$encode = isset($_GET['encode']) ? strtolower(trim($_GET['encode'])) : null;
$form = $encode ?? (isset($_GET['form']) ? strtolower(trim($_GET['form'])) : 'text');

$templateDir = __DIR__ . '/templates';
$supportedFormats = [];
foreach (glob($templateDir . '/*.php') as $tplFile) {
    $supportedFormats[] = basename($tplFile, '.php');
}
sort($supportedFormats);

if (!in_array($form, $supportedFormats, true)) {
    http_response_code(400);
    header('Content-Type: application/json; charset=utf-8');
    die(json_encode([
        'error' => 'Invalid format requested.',
        'supported' => $supportedFormats,
        'tip' => 'Use parameter "form" or "encode" to specify output format.'
    ], JSON_UNESCAPED_UNICODE));
}

$categories = [];
if (isset($_GET['c']) && $_GET['c'] !== '') {
    $raw = strtolower(trim($_GET['c']));
    $categories = array_filter(explode(',', $raw), function ($v) {
        return preg_match('/^[a-l]$/', $v) === 1;
    });
    $categories = array_values(array_unique($categories));
}

$charset = isset($_GET['charset']) ? trim($_GET['charset']) : 'utf-8';

$sentencesDir = __DIR__ . '/sentences';
if (!is_dir($sentencesDir)) {
    http_response_code(500);
    header('Content-Type: application/json; charset=utf-8');
    die(json_encode(['error' => 'Sentences directory not found.'], JSON_UNESCAPED_UNICODE));
}

$allCategories = ['a', 'b', 'c', 'd', 'e', 'f', 'g', 'h', 'i', 'j', 'k', 'l'];
$loadCategories = !empty($categories) ? $categories : $allCategories;

$validCategories = [];
foreach ($loadCategories as $cat) {
    $file = $sentencesDir . '/' . $cat . '.json';
    if (file_exists($file)) {
        $validCategories[] = $cat;
    }
}

if (empty($validCategories)) {
    http_response_code(500);
    header('Content-Type: application/json; charset=utf-8');
    die(json_encode(['error' => 'No valid category data files found.'], JSON_UNESCAPED_UNICODE));
}

$selectedCat = $validCategories[array_rand($validCategories)];
$dataFile = $sentencesDir . '/' . $selectedCat . '.json';

$rawContent = file_get_contents($dataFile);
if ($rawContent === false) {
    http_response_code(500);
    header('Content-Type: application/json; charset=utf-8');
    die(json_encode(['error' => 'Failed to read data file: ' . $selectedCat . '.json'], JSON_UNESCAPED_UNICODE));
}

$sentences = json_decode($rawContent, true);
if (json_last_error() !== JSON_ERROR_NONE || !is_array($sentences) || empty($sentences)) {
    http_response_code(500);
    header('Content-Type: application/json; charset=utf-8');
    die(json_encode(['error' => 'Invalid or empty data in: ' . $selectedCat . '.json'], JSON_UNESCAPED_UNICODE));
}

$randomItem = $sentences[array_rand($sentences)];

$hitokoto = isset($randomItem['hitokoto']) ? trim($randomItem['hitokoto']) : '';
$type = $randomItem['type'] ?? $selectedCat;
$from = $randomItem['from'] ?? '';
$from_who = $randomItem['from_who'] ?? null;
$creator = $randomItem['creator'] ?? '';
$creator_uid = $randomItem['creator_uid'] ?? 0;
$created_at = $randomItem['created_at'] ?? '';
$length = $randomItem['length'] ?? mb_strlen($hitokoto, 'UTF-8');

$apiUrl = getApiBaseUrl();
$repositories = [
    'gitee'    => 'https://gitee.com/byusistudio/hitokoto-api',
    'github'   => 'https://github.com/ByUsiStudio/hitokoto-api',
    'codeberg' => 'https://codeberg.org/ByUsiStudio/hitokoto-api',
];
$licenseName = 'AGPLv3';
$licenseUrl  = 'https://www.gnu.org/licenses/agpl-3.0.html';

include $templateDir . '/' . $form . '.php';
