<?php
$basePath = dirname(__DIR__);
$rawPath = $basePath . '/raw/cec';
if (!file_exists($rawPath)) {
    mkdir($rawPath, 0777, true);
}

$deptFile = $rawPath . '/prvCityDept.json';
if (!file_exists($deptFile)) {
    $deptJson = json_decode(file_get_contents('https://www.cec.gov.tw/data/json/dist/prvCityDept.json'), true);
    file_put_contents($deptFile, json_encode($deptJson, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
} else {
    $deptJson = json_decode(file_get_contents($deptFile), true);
}

$voterFile = $rawPath . '/voter.json';
if (!file_exists($voterFile)) {
    $voterJson = json_decode(file_get_contents('https://www.cec.gov.tw/data/json/tbox/voter.json'), true);
    file_put_contents($voterFile, json_encode($voterJson, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
} else {
    $voterJson = json_decode(file_get_contents($voterFile), true);
}

foreach ($deptJson['prvs'] as $city) {
    $cityTypeFile = $rawPath . '/type_' . $city['prvCityCode'] . '.json';
    if (!file_exists($cityTypeFile)) {
        $cityTypeJson = json_decode(file_get_contents('https://www.cec.gov.tw/data/json/type/' . $city['prvCityCode'] . '.json'), true);
        file_put_contents($cityTypeFile, json_encode($cityTypeJson, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
    } else {
        $cityTypeJson = json_decode(file_get_contents($cityTypeFile), true);
    }

    $cityLiFile = $rawPath . '/li_' . $city['prvCityCode'] . '.json';
    if (!file_exists($cityLiFile)) {
        $cityLiJson = json_decode(file_get_contents('https://www.cec.gov.tw/data/json/dist/' . $city['prvCityCode'] . '_deptLi.json'), true);
        file_put_contents($cityLiFile, json_encode($cityLiJson, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
    } else {
        $cityLiJson = json_decode(file_get_contents($cityLiFile), true);
    }

    $types = array_pop($cityTypeJson['types']);
    foreach ($types as $type) {
        if ($type == 'L1') {
            $candFile = "{$rawPath}/cand_{$type}_{$city['prvCityCode']}.json";
            if (!file_exists($candFile)) {
                $candJson = json_decode(file_get_contents("https://www.cec.gov.tw/data/json/cand/{$type}/{$city['prvCityCode']}.json"), true);
                file_put_contents($candFile, json_encode($candJson, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
            } else {
                $candJson = json_decode(file_get_contents($candFile), true);
            }
        }
    }
    foreach ($voterJson['voters'] as $voter) {
        $voterFile = "{$rawPath}/voter_{$voter['voterCode']}_{$city['prvCityCode']}.json";
        if (!file_exists($voterFile)) {
            $voterZoneJson = json_decode(file_get_contents("https://www.cec.gov.tw/data/json/tbox/{$city['prvCityCode']}/{$voter['voterCode']}{$city['prvCityCode']}.json"), true);
            file_put_contents($voterFile, json_encode($voterZoneJson, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
        } else {
            $voterZoneJson = json_decode(file_get_contents($voterFile), true);
        }
    }
}

foreach ($types as $type) {
    if ($type == 'L1') {
        continue;
    }
    $candFile = "{$rawPath}/cand_{$type}.json";
    if (!file_exists($candFile)) {
        $candJson = json_decode(file_get_contents("https://www.cec.gov.tw/data/json/cand/{$type}/00000.json"), true);
        file_put_contents($candFile, json_encode($candJson, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
    } else {
        $candJson = json_decode(file_get_contents($candFile), true);
    }
}
