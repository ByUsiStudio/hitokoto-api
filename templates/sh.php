<?php
// sh 格式：POSIX shell 变量
header('Content-Type: application/x-sh; charset=' . $charset);
echo "#!/bin/sh\n";
echo "# Random Hitokoto API\n";
echo "# Category: " . $type . "\n";
echo "# License: " . $licenseName . "\n\n";
echo "HITOKOTO_ID=" . escSh($randomItem['id'] ?? '') . "\n";
echo "HITOKOTO_UUID=" . escSh($randomItem['uuid'] ?? '') . "\n";
echo "HITOKOTO=" . escSh($hitokoto) . "\n";
echo "HITOKOTO_TYPE=" . escSh($type) . "\n";
echo "HITOKOTO_FROM=" . escSh($from) . "\n";
echo "HITOKOTO_FROM_WHO=" . escSh($from_who ?? '') . "\n";
echo "HITOKOTO_CREATOR=" . escSh($creator) . "\n";
echo "HITOKOTO_LENGTH=" . escSh($length) . "\n";
echo "API_URL=" . escSh($apiUrl) . "\n";
echo "REPO_GITEE=" . escSh($repositories['gitee']) . "\n";
echo "REPO_GITHUB=" . escSh($repositories['github']) . "\n";
echo "REPO_CODEBERG=" . escSh($repositories['codeberg']) . "\n";
echo "LICENSE=" . escSh($licenseName) . "\n";
echo "LICENSE_URL=" . escSh($licenseUrl) . "\n";
