<?php
// /home/kiang/public_html/vote2022/reports/result/67000臺南市/村(里)長
$basePath = dirname(__DIR__);
$vote = json_decode(file_get_contents($basePath . '/data/tbox-vote.json'), true);
$pool = [];

foreach ($vote as $k => $tbox) {
    $parts = explode('-', $k);
    $city = mb_substr($tbox['li'][0], 0, 3, 'utf-8');
    if (!isset($pool[$city])) {
        $pool[$city] = [];
        $path = "/home/kiang/public_html/vote2022/reports/result/{$parts[0]}{$city}/村(里)長";
        foreach (glob($path . '/*.json') as $jsonFile) {
            $json = json_decode(file_get_contents($jsonFile), true);
            foreach ($json['data'] as $cunli) {
                $cunliName = $city . $cunli['area'] . $cunli['cunli'];
                print_r($json);
                print_r($cunli); exit();
                $pool[$city][$cunliName] = [
                    '2022' => 0,
                    '2024' => 0,
                ];
                foreach ($cunli['candidates'] as $vote) {
                    if ($vote > $pool[$city][$cunliName]['2022']) {
                        $pool[$city][$cunliName]['2022'] = $vote;
                    }
                }
            }
        }
    }
    $liCount = count($tbox['li']);
    $theVote = round($tbox['vote']['(12)台灣民眾黨'] / $liCount);
    foreach ($tbox['li'] as $li) {
        if (isset($pool[$city][$li])) {
            $pool[$city][$li]['2024'] += intval($theVote);
        }
    }
}

$count = [];
$total = 0;
foreach ($pool as $city => $v1) {
    $count[$city] = 0;
    foreach ($v1 as $cunli => $vote) {
        $total += 1;
        if ($vote['2024'] > $vote['2022']) {
            $count[$city]++;
        }
    }
}
print_r($count);
