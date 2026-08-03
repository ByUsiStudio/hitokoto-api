<?php
// bat 格式：Windows 批处理变量
header('Content-Type: application/x-bat; charset=' . $charset);
echo "@echo off\n";
echo "REM Random Hitokoto API\n";
echo "REM Category: " . $type . "\n";
echo "REM License: " . $licenseName . "\n\n";
echo 'set "HITOKOTO_ID=' . ($randomItem['id'] ?? '') . '"' . "\n";
echo 'set "HITOKOTO_UUID=' . ($randomItem['uuid'] ?? '') . '"' . "\n";
echo 'set "HITOKOTO=' . $hitokoto . '"' . "\n";
echo 'set "HITOKOTO_TYPE=' . $type . '"' . "\n";
echo 'set "HITOKOTO_FROM=' . $from . '"' . "\n";
echo 'set "HITOKOTO_FROM_WHO=' . ($from_who ?? '') . '"' . "\n";
echo 'set "HITOKOTO_CREATOR=' . $creator . '"' . "\n";
echo 'set "HITOKOTO_LENGTH=' . $length . '"' . "\n";
echo 'set "API_URL=' . $apiUrl . '"' . "\n";
echo 'set "REPO_GITEE=' . $repositories['gitee'] . '"' . "\n";
echo 'set "REPO_GITHUB=' . $repositories['github'] . '"' . "\n";
echo 'set "REPO_CODEBERG=' . $repositories['codeberg'] . '"' . "\n";
echo 'set "LICENSE=' . $licenseName . '"' . "\n";
echo 'set "LICENSE_URL=' . $licenseUrl . '"' . "\n";
