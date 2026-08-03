<?php
// json5 格式：带注释、未加引号键名、尾随逗号的 JSON5
header('Content-Type: application/json5; charset=' . $charset);
echo "{\n";
echo "  // 一言数据\n";
echo "  id: " . json_encode($randomItem['id'] ?? null, JSON_OUT) . ",\n";
echo "  uuid: " . json_encode($randomItem['uuid'] ?? null, JSON_OUT) . ",\n";
echo "  hitokoto: " . json_encode($hitokoto, JSON_OUT) . ",\n";
echo "  message: " . json_encode($hitokoto, JSON_OUT) . ", // 兼容旧版\n";
echo "  type: " . json_encode($type, JSON_OUT) . ",\n";
echo "  from: " . json_encode($from, JSON_OUT) . ",\n";
echo "  from_who: " . json_encode($from_who, JSON_OUT) . ",\n";
echo "  creator: " . json_encode($creator, JSON_OUT) . ",\n";
echo "  creator_uid: " . json_encode($creator_uid, JSON_OUT) . ",\n";
echo "  created_at: " . json_encode($created_at, JSON_OUT) . ",\n";
echo "  length: " . json_encode($length, JSON_OUT) . ",\n";
echo "\n";
echo "  // 元信息：API 地址、开源仓库、开源协议\n";
echo "  api: " . json_encode($apiUrl, JSON_OUT) . ",\n";
echo "  repository: {\n";
echo "    gitee: " . json_encode($repositories['gitee'], JSON_OUT) . ",\n";
echo "    github: " . json_encode($repositories['github'], JSON_OUT) . ",\n";
echo "    codeberg: " . json_encode($repositories['codeberg'], JSON_OUT) . ",\n";
echo "  },\n";
echo "  license: " . json_encode($licenseName, JSON_OUT) . ",\n";
echo "  license_url: " . json_encode($licenseUrl, JSON_OUT) . ",\n";
echo "}\n";
