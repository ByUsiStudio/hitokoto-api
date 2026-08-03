<?php
// ps1 格式：PowerShell 变量
header('Content-Type: application/x-powershell; charset=' . $charset);
echo "# Random Hitokoto API\n";
echo "# Category: " . $type . "\n";
echo "# License: " . $licenseName . "\n\n";
echo '$HitokotoId = ' . escPs1($randomItem['id'] ?? '') . "\n";
echo '$HitokotoUuid = ' . escPs1($randomItem['uuid'] ?? '') . "\n";
echo '$Hitokoto = ' . escPs1($hitokoto) . "\n";
echo '$HitokotoType = ' . escPs1($type) . "\n";
echo '$HitokotoFrom = ' . escPs1($from) . "\n";
echo '$HitokotoFromWho = ' . escPs1($from_who ?? '') . "\n";
echo '$HitokotoCreator = ' . escPs1($creator) . "\n";
echo '$HitokotoLength = ' . escPs1($length) . "\n";
echo '$ApiUrl = ' . escPs1($apiUrl) . "\n";
echo '$RepoGitee = ' . escPs1($repositories['gitee']) . "\n";
echo '$RepoGithub = ' . escPs1($repositories['github']) . "\n";
echo '$RepoCodeberg = ' . escPs1($repositories['codeberg']) . "\n";
echo '$License = ' . escPs1($licenseName) . "\n";
echo '$LicenseUrl = ' . escPs1($licenseUrl) . "\n";
