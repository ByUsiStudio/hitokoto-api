<?php
// text 格式：仅返回一言内容，不包含任何元信息
header('Content-Type: text/plain; charset=' . $charset);
echo $hitokoto;
