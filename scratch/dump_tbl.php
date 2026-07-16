<?php
$xml = file_get_contents(__DIR__ . '/../storage/app/public/test_corrupt_extracted/word/document.xml');
preg_match_all('/<w:tbl>.*?<\/w:tbl>/', $xml, $matches);
foreach ($matches[0] as $i => $tbl) {
    if (strpos($tbl, 'w:w="0" w:type="auto"') !== false) {
        echo "Found injected table:\n";
        echo $tbl . "\n\n";
    }
}
