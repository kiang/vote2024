<?php
// /home/kiang/public_html/vote2022/reports/result/67000臺南市/村(里)長
$basePath = dirname(__DIR__);
$vote = json_decode(file_get_contents($basePath . '/data/tbox-vote.json'), true);
$pool = [];
$liMap = [
    '臺中市大安區龜@2F85A@里' => '臺中市大安區龜壳里',
    '臺南市西港區@2F8EB@林里' => '臺南市西港區檨林里',
    '臺南市安南區@FB56F@南里' => '臺南市安南區南里',
    '臺南市安南區公@FB56F@里' => '臺南市安南區公里',
];

foreach ($vote as $k => $tbox) {
    $parts = explode('-', $k);
    $city = mb_substr($tbox['li'][0], 0, 3, 'utf-8');
    if (!isset($pool[$city])) {
        $pool[$city] = [];
        $path = "/home/kiang/public_html/vote2022/reports/result/{$parts[0]}{$city}/村(里)長";
        foreach (glob($path . '/*.json') as $jsonFile) {
            $json = json_decode(file_get_contents($jsonFile), true);
            $theVote = 0;
            foreach ($json['data'] as $cunli) {
                $cunliName = $city . $cunli['area'] . $cunli['cunli'];
                if (!isset($pool[$city][$cunliName])) {
                    $pool[$city][$cunliName] = [
                        '2022' => $cunli,
                    ];
                } else {
                    $pool[$city][$cunliName]['2022']['count_wrong'] += $cunli['count_wrong'];
                    $pool[$city][$cunliName]['2022']['count_unused'] += $cunli['count_unused'];
                    $pool[$city][$cunliName]['2022']['count_total'] += $cunli['count_total'];
                }

                foreach ($cunli['candidates'] as $number => $vote) {
                    if (!isset($json['candidates'][$number]['vote'])) {
                        $json['candidates'][$number]['vote'] = 0;
                    }
                    $json['candidates'][$number]['vote'] += $vote;
                    if ($theVote < $json['candidates'][$number]['vote']) {
                        $theVote = $json['candidates'][$number]['vote'];
                        $pool[$city][$cunliName]['2022']['candidate'] = $json['candidates'][$number];
                    }
                }
            }
            $pool[$city][$cunliName]['2022']['vote'] = $theVote;
        }
    }
    $liCount = count($tbox['li']);
    $theVote = round($tbox['vote']['(12)台灣民眾黨'] / $liCount);
    $theVote2 = round($tbox['vote']['投票數C=A+B'] / $liCount);
    $theVote3 = round($tbox['vote']['選舉人數G=E+F'] / $liCount);
    foreach ($tbox['li'] as $li) {
        if (isset($liMap[$li])) {
            $li = $liMap[$li];
        }
        if (isset($pool[$city][$li])) {
            if (!isset($pool[$city][$li]['2024'])) {
                $pool[$city][$li]['2024'] = [
                    'vote' => 0,
                    'total' => 0,
                    'base' => 0,
                    'rate' => 0.0,
                ];
            }
            $pool[$city][$li]['2024']['vote'] += intval($theVote);
            $pool[$city][$li]['2024']['total'] += $theVote2;
            $pool[$city][$li]['2024']['base'] += $theVote3;
            $pool[$city][$li]['2024']['rate'] = round($pool[$city][$li]['2024']['total'] / $pool[$city][$li]['2024']['base'], 1);
        }
    }
}

$reportPath = $basePath . '/data/reports/compare_2022';
if (!file_exists($reportPath)) {
    mkdir($reportPath, 0777, true);
}

$count = [];
foreach ($pool as $city => $v1) {
    $count[$city] = 0;
    $fh = fopen($reportPath . '/' . $city . '.csv', 'w');
    fputcsv($fh, ['村里', '2024-2022', '2024民眾黨', '2022村里長得票', '2024投票數', '2024選舉人數', '2024投票率', '里長姓名', '里長政黨']);
    foreach ($v1 as $cunli => $vote) {
        if ($vote['2024']['vote'] - $vote['2022']['vote'] > 0) {
            ++$count[$city];
        }
        fputcsv($fh, [
            $cunli,
            $vote['2024']['vote'] - $vote['2022']['vote'],
            $vote['2024']['vote'],
            $vote['2022']['vote'],
            $vote['2024']['total'],
            $vote['2024']['base'],
            $vote['2024']['rate'],
            $vote['2022']['candidate']['name'],
            $vote['2022']['candidate']['party'],
        ]);
    }
}
print_r($count);