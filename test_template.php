<?php require 'vendor/autoload.php'; $t = new \PhpOffice\PhpWord\TemplateProcessor('storage/app/templates/notulen_capkin.docx'); print_r($t->getVariables());
