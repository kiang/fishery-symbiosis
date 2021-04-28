<?php
// generate BB.txt using command `pdftotext -layout BB.pdf > BB.txt`
$content = file_get_contents(__DIR__ . '/BB.txt');
$lines = explode("\n", $content);
$fh = fopen(__DIR__ . '/BB.csv', 'w');
$headerDone = false;
foreach($lines AS $line) {
    $line = preg_replace('/[\x00-\x1F\x7F]/', '', $line);
    $cols = preg_split('/[ ]+/', trim($line));
    if(count($cols) === 9) {
        if($cols[0] === '項次') {
            $header = $cols;
            if(false === $headerDone) {
                $headerDone = true;
                fputcsv($fh, $header);
            }
        } else {
            fputcsv($fh, $cols);
        }
    }
}