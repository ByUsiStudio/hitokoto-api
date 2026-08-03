<?php
// json 格式：完整元数据 + API 地址、开源仓库、开源协议
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
    'api' => $apiUrl,
    'repository' => $repositories,
    'license' => $licenseName,
    'license_url' => $licenseUrl,
];
echo json_encode($response, JSON_OUT);
