<?php
$basePath = dirname(__DIR__);

$fh = fopen('/home/kiang/public_html/tw_population/docs/population/2023/11/data.csv', 'r');
fgetcsv($fh, 8000);
$head = fgetcsv($fh, 8000);
foreach ($head as $k => $v) {
    $head[$k] = $k . ':' . $v;
}
$population = $deptNames = [];
while ($line = fgetcsv($fh, 8000)) {
    $deptNames[substr($line[1], 0, 8)] = $line[2];
    $key = $line[2] . $line[3];
    $population[$key] = 0;
    for ($i = 48; $i <= 209; $i++) {
        $population[$key] += $line[$i];
    }
}

$items = array(
    '新北市中和區瓦󿾨里' => '新北市中和區瓦磘里',
    '新北市中和區灰󿾨里' => '新北市中和區灰磘里',
    '新北市樹林區󿾵寮里' => '新北市樹林區獇寮里',
    '臺中市大安區龜売里' => '臺中市大安區龜@2F85A@里',
    '臺南市西港區檨林里' => '臺南市西港區@2F8EB@林里',
    '臺南市安南區󻕯南里' => '臺南市安南區@FB56F@南里',
    '臺南市安南區公󻕯里' => '臺南市安南區公@FB56F@里',
    '彰化縣彰化市磚󿾨里' => '彰化縣彰化市磚磘里',
    '彰化縣埔鹽鄉瓦󿾨村' => '彰化縣埔鹽鄉瓦磘村',
    '南投縣竹山鎮硘󿾨里' => '南投縣竹山鎮硘磘里',
    '雲林縣麥寮鄉瓦󿾨村' => '雲林縣麥寮鄉瓦磘村',
    '雲林縣元長鄉瓦󿾨村' => '雲林縣元長鄉瓦磘村',
    '雲林縣四湖鄉󿿀子村' => '雲林縣四湖鄉萡子村',
    '雲林縣四湖鄉󿿀東村' => '雲林縣四湖鄉萡東村',
    '雲林縣水林鄉𣐤埔村' => '雲林縣水林鄉瓊埔村',
    '屏東縣新園鄉瓦󿾨村' => '屏東縣新園鄉瓦磘村',
    '澎湖縣馬公市󼱹裡里' => '澎湖縣馬公市嵵裡里',
    '嘉義市西區磚󿾨里' => '嘉義市西區磚磘里',
);

foreach ($items as $k => $v) {
    $population[$v] = $population[$k];
}

$result = [];
foreach (glob($basePath . '/raw/cec/voter_*.json') as $voterFile) {
    $p = pathinfo($voterFile);
    $cityCode = substr($p['filename'], 9);
    $voterJson = json_decode(file_get_contents($voterFile), true);
    foreach ($voterJson['depts'] as $dept) {
        $deptName = $deptNames[$cityCode . $dept['deptCode']];
        foreach ($dept['lis'] as $li) {
            $liName = $deptName . $li['liName'];
            if (!isset($population[$liName])) {
                continue;
            }
            foreach ($li['tboxs'] as $tbox) {
                $tboxCode = "{$cityCode}-{$tbox['tboxNo']}";
                if (!isset($result[$tboxCode])) {
                    $result[$tboxCode] = [
                        'tboxNo' => $tbox['tboxNo'],
                        'tboxName' => $tbox['tboxName'],
                        'addr' => $tbox['addr'],
                        'li' => [],
                        'population' => 0,
                    ];
                }
                if (!in_array($liName, $result[$tboxCode]['li'])) {
                    $result[$tboxCode]['li'][] = $liName;
                }
                $result[$tboxCode]['population'] += $population[$liName];
            }
        }
    }
}

file_put_contents($basePath . '/data/tboxs.json', json_encode($result, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));

echo '共有 ' . count($result) . ' 個投開票所';
