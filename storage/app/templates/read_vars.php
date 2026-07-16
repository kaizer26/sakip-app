<?php
$xml = file_get_contents('d:/ANTIGRAVITY/sakip-app/storage/app/templates/notulen_capkin_extracted/word/document.xml');
$text = strip_tags($xml);
preg_match_all('/\\$\\{([^}]+)\\}/', $text, $matches);
$vars = array_unique($matches[1]);
sort($vars);
print_r($vars);
