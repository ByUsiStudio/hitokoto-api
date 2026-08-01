<?php
/**
 * 随机一言 API
 * 支持格式: json, text, xml, ini
 * 支持分类: a, b, c, d, e, f, g, h, i, j, k, l
 *
 * 参数说明（保留原有 + 新增）：
 *   form    - 输出格式（原有）：json | text（默认）| xml | ini
 *   encode  - 输出格式（新增，别名，优先级高于 form）
 *   c       - 分类筛选（新增）：单个分类如 ?c=a ，多个分类用逗号分隔如 ?c=a,b,c
 *             可选值：a(动画) b(漫画) c(游戏) d(文学) e(原创) f(网络)
 *                     g(其他) h(影视) i(诗词) j(网易云) k(哲学) l(抖机灵)
 *   charset - 字符集（新增）：默认 utf-8
 */

// ========== 0. 无参数时重定向到 HTML 文档界面 ==========
$hasApiParams = isset($_GET['form']) || isset($_GET['encode'])
    || isset($_GET['c']) || isset($_GET['charset'])
    || isset($_GET['callback']) || isset($_GET['select'])
    || isset($_GET['min_length']) || isset($_GET['max_length']);

if (!$hasApiParams) {
    header('Location: ./docs.html', true, 302);
    exit;
}

// ========== 1. 参数处理 ==========
$encode = isset($_GET['encode']) ? strtolower(trim($_GET['encode'])) : null;
$form = $encode ?? (isset($_GET['form']) ? strtolower(trim($_GET['form'])) : 'text');

$supportedFormats = ['json', 'text', 'xml', 'ini'];
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

// ========== 2. 数据加载 ==========
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

// ========== 3. 按格式输出 ==========
switch ($form) {
    case 'json':
        header('Content-Type: application/json; charset=' . $charset);
        $response = [
            'id' => $randomItem['id'] ?? null,
            'uuid' => $randomItem['uuid'] ?? null,
            'hitokoto' => $hitokoto,
            'message' => $hitokoto,
            'type' => $type,
            'from' => $from,
            'from_who' => $from_who,
            'creator' => $creator,
            'creator_uid' => $creator_uid,
            'reviewer' => $randomItem['reviewer'] ?? null,
            'commit_from' => $randomItem['commit_from'] ?? null,
            'created_at' => $created_at,
            'length' => $length,
        ];
        echo json_encode($response, JSON_UNESCAPED_UNICODE);
        break;

    case 'xml':
        header('Content-Type: text/xml; charset=' . $charset);
        echo '<?xml version="1.0" encoding="' . strtoupper($charset) . '"?>' . "\n";
        echo '<response>';
        echo '<id>' . htmlspecialchars($randomItem['id'] ?? '', ENT_XML1) . '</id>';
        echo '<uuid>' . htmlspecialchars($randomItem['uuid'] ?? '', ENT_XML1) . '</uuid>';
        echo '<hitokoto><![CDATA[' . $hitokoto . ']]></hitokoto>';
        echo '<message><![CDATA[' . $hitokoto . ']]></message>';
        echo '<type>' . htmlspecialchars($type, ENT_XML1) . '</type>';
        echo '<from>' . htmlspecialchars($from, ENT_XML1) . '</from>';
        echo '<from_who>' . htmlspecialchars($from_who ?? '', ENT_XML1) . '</from_who>';
        echo '<creator>' . htmlspecialchars($creator, ENT_XML1) . '</creator>';
        echo '<created_at>' . htmlspecialchars($created_at, ENT_XML1) . '</created_at>';
        echo '<length>' . $length . '</length>';
        echo '</response>';
        break;

    case 'ini':
        header('Content-Type: text/plain; charset=' . $charset);
        echo "; Random Hitokoto API\n";
        echo "; Category: " . $type . "\n";
        echo "[hitokoto]\n";
        echo "content = " . $hitokoto . "\n";
        echo "[quote]\n";
        echo "content = " . $hitokoto . "\n";
        echo "[meta]\n";
        echo "type = " . $type . "\n";
        echo "from = " . $from . "\n";
        echo "from_who = " . ($from_who ?? '') . "\n";
        echo "creator = " . $creator . "\n";
        echo "created_at = " . $created_at . "\n";
        echo "length = " . $length . "\n";
        break;

    case 'text':
    default:
        header('Content-Type: text/plain; charset=' . $charset);
        echo $hitokoto;
        break;
}

