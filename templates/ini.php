<?php
// ini 格式
header('Content-Type: text/plain; charset=' . $charset);
echo "; Random Hitokoto API\n";
echo "; Category: " . $type . "\n";
echo "; License: " . $licenseName . "\n";
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
echo "[api]\n";
echo "url = " . $apiUrl . "\n";
echo "[repository]\n";
echo "gitee = " . $repositories['gitee'] . "\n";
echo "github = " . $repositories['github'] . "\n";
echo "codeberg = " . $repositories['codeberg'] . "\n";
echo "[license]\n";
echo "name = " . $licenseName . "\n";
echo "url = " . $licenseUrl . "\n";
