<?php
// js 格式：赋值给 var hitokoto
header('Content-Type: text/javascript; charset=' . $charset);
echo "// Random Hitokoto API\n";
echo "// Category: " . $type . "\n";
echo "// License: " . $licenseName . "\n";
echo "var hitokoto = {\n";
echo "  id: " . json_encode($randomItem['id'] ?? null, JSON_OUT) . ",\n";
echo "  uuid: " . json_encode($randomItem['uuid'] ?? null, JSON_OUT) . ",\n";
echo "  hitokoto: " . json_encode($hitokoto, JSON_OUT) . ",\n";
echo "  message: " . json_encode($hitokoto, JSON_OUT) . ",\n";
echo "  type: " . json_encode($type, JSON_OUT) . ",\n";
echo "  from: " . json_encode($from, JSON_OUT) . ",\n";
echo "  from_who: " . json_encode($from_who, JSON_OUT) . ",\n";
echo "  creator: " . json_encode($creator, JSON_OUT) . ",\n";
echo "  creator_uid: " . json_encode($creator_uid, JSON_OUT) . ",\n";
echo "  created_at: " . json_encode($created_at, JSON_OUT) . ",\n";
echo "  length: " . json_encode($length, JSON_OUT) . ",\n";
echo "  api: " . json_encode($apiUrl, JSON_OUT) . ",\n";
echo "  repository: {\n";
echo "    gitee: " . json_encode($repositories['gitee'], JSON_OUT) . ",\n";
echo "    github: " . json_encode($repositories['github'], JSON_OUT) . ",\n";
echo "    codeberg: " . json_encode($repositories['codeberg'], JSON_OUT) . "\n";
echo "  },\n";
echo "  license: " . json_encode($licenseName, JSON_OUT) . ",\n";
echo "  license_url: " . json_encode($licenseUrl, JSON_OUT) . "\n";
echo "};\n";
