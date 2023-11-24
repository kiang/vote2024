<?php
$fh = fopen('/home/kiang/public_html/elections/web/Console/Command/data/2024_election/elections_areas.csv', 'r');
fgetcsv($fh, 2048);
$pool = [];
while ($line = fgetcsv($fh, 2048)) {
    $pool[$line[0]] = $line[1];
}

$basePath = dirname(__DIR__);
$oFh = fopen($basePath . '/data/candidates.csv', 'w');
$fh = fopen($basePath . '/raw/登記/區域.csv', 'r');
$head = fgetcsv($fh, 2048);
/**
Array
(
[0] => 登記日期
[1] => 選舉區
[2] => 姓名
[3] => 性別
[4] => 推薦之政黨
[5] => 受理登記機關
[6] => 備註
)
*/
$head[] = 'id';
$head[] = 'zone';
fputcsv($oFh, $head);
while ($line = fgetcsv($fh, 2048)) {
    $line[1] = str_replace('選舉區', '選區', $line[1]);
    if (false !== strpos($line[1], '第')) {
        $num = preg_replace('/[^0-9]/', '', $line[1]);
        $fixed = str_pad($num, 2, '0', STR_PAD_LEFT);
        $line[1] = str_replace('第' . $num, ' > 第' . $fixed, $line[1]);
    } else {
        $line[1] = str_replace('選區', '', $line[1]);
    }
    foreach ($pool as $k => $v) {
        if (strpos($v, $line[1]) !== false) {
            $line[] = $k;
            $line[] = $v;
            break;
        }
    }
    fputcsv($oFh, $line);
}