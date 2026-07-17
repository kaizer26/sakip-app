<?php
$xmlPath = __DIR__ . '/../storage/app/public/test_corrupt_extracted/word/document.xml';
if (!file_exists($xmlPath)) {
    die("Not found.\n");
}
$content = file_get_contents($xmlPath);
libxml_use_internal_errors(true);
$doc = new DOMDocument();
if (!$doc->loadXML($content)) {
    echo "XML Parsing Errors:\n";
    foreach (libxml_get_errors() as $error) {
        echo trim($error->message) . " at line " . $error->line . ", column " . $error->column . "\n";
        
        // Show surrounding context
        $start = max(0, $error->column - 30);
        echo "Context: " . substr($content, $start, 60) . "\n\n";
    }
} else {
    echo "XML is valid.\n";
    // Check if there are any <w:tbl> directly inside <w:tc> without a trailing <w:p>
    $xpath = new DOMXPath($doc);
    $xpath->registerNamespace('w', 'http://schemas.openxmlformats.org/wordprocessingml/2006/main');
    
    $tcs = $xpath->query('//w:tc');
    foreach ($tcs as $tc) {
        $lastNode = $tc->lastChild;
        if ($lastNode && $lastNode->nodeName !== 'w:p') {
            echo "Error: <w:tc> ends with " . $lastNode->nodeName . " instead of w:p\n";
        }
    }
}
