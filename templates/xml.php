<?php
// xml 格式
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
echo '<api>' . htmlspecialchars($apiUrl, ENT_XML1) . '</api>';
echo '<repository>';
echo '<gitee>' . htmlspecialchars($repositories['gitee'], ENT_XML1) . '</gitee>';
echo '<github>' . htmlspecialchars($repositories['github'], ENT_XML1) . '</github>';
echo '<codeberg>' . htmlspecialchars($repositories['codeberg'], ENT_XML1) . '</codeberg>';
echo '</repository>';
echo '<license>' . htmlspecialchars($licenseName, ENT_XML1) . '</license>';
echo '<license_url>' . htmlspecialchars($licenseUrl, ENT_XML1) . '</license_url>';
echo '</response>';
