<?php
$basePath = dirname(__DIR__);
require_once($basePath . '/scripts/vendor/autoload.php');

$tbox = json_decode(file_get_contents($basePath . '/data/tboxs.json'), true);

$cityCodes = [
    '南投縣' => '10008',
    '嘉義市' => '10020',
    '嘉義縣' => '10010',
    '基隆市' => '10017',
    '宜蘭縣' => '10002',
    '屏東縣' => '10013',
    '彰化縣' => '10007',
    '新北市' => '65000',
    '新竹市' => '10018',
    '新竹縣' => '10004',
    '桃園市' => '68000',
    '澎湖縣' => '10016',
    '臺中市' => '66000',
    '臺北市' => '63000',
    '臺南市' => '67000',
    '臺東縣' => '10014',
    '花蓮縣' => '10015',
    '苗栗縣' => '10005',
    '連江縣' => '09007',
    '金門縣' => '09020',
    '雲林縣' => '10009',
    '高雄市' => '64000',
];
/**
 *     [3] => (1)小民參政歐巴桑聯盟
    [4] => (2)台灣綠黨
    [5] => (3)臺灣雙語無法黨
    [6] => (4)台灣基進
    [7] => (5)中華統一促進黨
    [8] => (6)民主進步黨
    [9] => (7)制度救世島
    [10] => (8)時代力量
    [11] => (9)中國國民黨
    [12] => (10)司法改革黨
    [13] => (11)新黨
    [14] => (12)台灣民眾黨
    [15] => (13)台灣維新
    [16] => (14)親民黨
    [17] => (15)人民最大黨
    [18] => (16)台灣團結聯盟
    [19] => 有效票數AA=1+2+...+N
    [20] => 無效票數B
    [21] => 投票數CC=A+B
    [22] => 已領未投票數DD=E-C
    [23] => 發出票數EE=C+D
    [24] => 用餘票數F
    [25] => 選舉人數GG=E+F
    [26] => 投票率HH=C÷G
 */
$head = [
    3 => '(1)小民參政歐巴桑聯盟',
    4 => '(2)台灣綠黨',
    5 => '(3)臺灣雙語無法黨',
    6 => '(4)台灣基進',
    7 => '(5)中華統一促進黨',
    8 => '(6)民主進步黨',
    9 => '(7)制度救世島',
    10 => '(8)時代力量',
    11 => '(9)中國國民黨',
    12 => '(10)司法改革黨',
    13 => '(11)新黨',
    14 => '(12)台灣民眾黨',
    15 => '(13)台灣維新',
    16 => '(14)親民黨',
    17 => '(15)人民最大黨',
    18 => '(16)台灣團結聯盟',
    19 => '有效票數A',
    20 => '無效票數B',
    21 => '投票數C=A+B',
    22 => '已領未投票數D=E-C',
    23 => '發出票數E=C+D',
    24 => '用餘票數F',
    25 => '選舉人數G=E+F',
    26 => '投票率H=C÷G',
];
foreach (glob($basePath . '/raw/result/全國投開票所一覽表/不分區立委/*.xlsx') as $xlsFile) {
    $p = pathinfo($xlsFile);
    $parts = preg_split('/[\\(\\)]/', $p['filename'], -1, PREG_SPLIT_NO_EMPTY);
    $cityCode = $cityCodes[$parts[1]];
    // extract excel data using phpoffice/phpspreadsheet
    $reader = new \PhpOffice\PhpSpreadsheet\Reader\Xlsx();
    $spreadsheet = $reader->load($xlsFile);
    $sheet = $spreadsheet->getActiveSheet();
    $rows = $sheet->toArray();
    foreach ($rows as $k => $v) {
        foreach ($v as $k2 => $v2) {
            $rows[$k][$k2] = str_replace(['　', "\n", ','], '', $v2);
        }
        if (empty($rows[$k][2])) {
            continue;
        }
        $tboxCode = "{$cityCode}-{$rows[$k][2]}";
        if (isset($tbox[$tboxCode])) {
            $tbox[$tboxCode]['vote'] = [];
            foreach ($head as $k2 => $v2) {
                $tbox[$tboxCode]['vote'][$v2] = $rows[$k][$k2];
            }
        }
    }
}
file_put_contents($basePath . '/data/tbox-vote.json', json_encode($tbox, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));