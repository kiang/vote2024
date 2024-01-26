<?php
$basePath = dirname(__DIR__);
require_once($basePath . '/scripts/vendor/autoload.php');

$cunli = [];
foreach (glob($basePath . '/raw/result/全國各村里一覽表/xls/區域立委/*.xlsx') as $xlsFile) {
    $p = pathinfo($xlsFile);
    $parts = preg_split('/[\\(\\)]/', $p['filename'], -1, PREG_SPLIT_NO_EMPTY);
    // extract excel data using phpoffice/phpspreadsheet
    $reader = new \PhpOffice\PhpSpreadsheet\Reader\Xlsx();
    $spreadsheet = $reader->load($xlsFile);
    foreach ($spreadsheet->getAllSheets() as $sheet) {
        $rows = $sheet->toArray();
        array_shift($rows);
        $counterLine = array_shift($rows);
        $idxEnd = array_search('無效票數B', $counterLine) - 1;
        $candidateLine = array_shift($rows);
        $candidateLabel = [];
        for ($i = 2; $i < $idxEnd; $i++) {
            $lines = explode("\n", $candidateLine[$i]);
            array_shift($lines);
            $candidateLabel[$i] = implode('/', $lines);
        }
        foreach ($rows as $k => $v) {
            foreach ($v as $k2 => $v2) {
                $rows[$k][$k2] = str_replace(['　', "\n", ','], '', $v2);
            }
            if (!empty($rows[$k][0])) {
                $zone = $rows[$k][0];
            } elseif (!empty($rows[$k][1])) {
                $key = $parts[1] . $zone . $rows[$k][1];
                $cunli[$key] = [
                    '區域' => [],
                ];
                foreach ($candidateLabel as $k2 => $v2) {
                    $cunli[$key]['區域'][$v2] = $rows[$k][$k2];
                }
            }
        }
    }
}

foreach (glob($basePath . '/raw/result/全國各村里一覽表/xls/山地立委/*.xlsx') as $xlsFile) {
    $p = pathinfo($xlsFile);
    $parts = preg_split('/[\\(\\)]/', $p['filename'], -1, PREG_SPLIT_NO_EMPTY);
    // extract excel data using phpoffice/phpspreadsheet
    $reader = new \PhpOffice\PhpSpreadsheet\Reader\Xlsx();
    $spreadsheet = $reader->load($xlsFile);
    foreach ($spreadsheet->getAllSheets() as $sheet) {
        $rows = $sheet->toArray();
        array_shift($rows);
        $counterLine = array_shift($rows);
        $idxEnd = array_search('無效票數B', $counterLine) - 1;
        $candidateLine = array_shift($rows);
        $candidateLabel = [];
        for ($i = 2; $i < $idxEnd; $i++) {
            $lines = explode("\n", $candidateLine[$i]);
            array_shift($lines);
            $candidateLabel[$i] = implode('/', $lines);
        }
        foreach ($rows as $k => $v) {
            if (empty($rows[$k][18])) {
                continue;
            }
            foreach ($v as $k2 => $v2) {
                $rows[$k][$k2] = str_replace(['　', "\n", ',', '*'], '', $v2);
            }
            if (!empty($rows[$k][0])) {
                $zone = $rows[$k][0];
            } elseif (!empty($rows[$k][1])) {
                $key = $parts[1] . $zone . $rows[$k][1];
                $cunli[$key]['不分區(山地)'] = [];
                foreach ($candidateLabel as $k2 => $v2) {
                    $cunli[$key]['不分區(山地)'][$v2] = $rows[$k][$k2];
                }
            }
        }
    }
}

foreach (glob($basePath . '/raw/result/全國各村里一覽表/xls/平地立委/*.xlsx') as $xlsFile) {
    $p = pathinfo($xlsFile);
    $parts = preg_split('/[\\(\\)]/', $p['filename'], -1, PREG_SPLIT_NO_EMPTY);
    // extract excel data using phpoffice/phpspreadsheet
    $reader = new \PhpOffice\PhpSpreadsheet\Reader\Xlsx();
    $spreadsheet = $reader->load($xlsFile);
    foreach ($spreadsheet->getAllSheets() as $sheet) {
        $rows = $sheet->toArray();
        array_shift($rows);
        $counterLine = array_shift($rows);
        $idxEnd = array_search('無效票數B', $counterLine) - 1;
        $candidateLine = array_shift($rows);
        $candidateLabel = [];
        for ($i = 2; $i < $idxEnd; $i++) {
            $lines = explode("\n", $candidateLine[$i]);
            array_shift($lines);
            $candidateLabel[$i] = implode('/', $lines);
        }
        foreach ($rows as $k => $v) {
            if (empty($rows[$k][17])) {
                continue;
            }
            foreach ($v as $k2 => $v2) {
                $rows[$k][$k2] = str_replace(['　', "\n", ','], '', $v2);
            }
            if (!empty($rows[$k][0])) {
                $zone = $rows[$k][0];
            } elseif (!empty($rows[$k][1])) {
                $key = $parts[1] . $zone . $rows[$k][1];
                $cunli[$key]['不分區(平地)'] = [];
                foreach ($candidateLabel as $k2 => $v2) {
                    $cunli[$key]['不分區(平地)'][$v2] = $rows[$k][$k2];
                }
            }
        }
    }
}

foreach (glob($basePath . '/raw/result/全國各村里一覽表/xls/總統副總統選舉/*.xlsx') as $xlsFile) {
    $p = pathinfo($xlsFile);
    $parts = preg_split('/[\\(\\)]/', $p['filename'], -1, PREG_SPLIT_NO_EMPTY);
    // extract excel data using phpoffice/phpspreadsheet
    $reader = new \PhpOffice\PhpSpreadsheet\Reader\Xlsx();
    $spreadsheet = $reader->load($xlsFile);
    $sheet = $spreadsheet->getActiveSheet();
    $rows = $sheet->toArray();
    foreach ($rows as $k => $v) {
        foreach ($v as $k2 => $v2) {
            $rows[$k][$k2] = str_replace(['　', "\n", ','], '', $v2);
        }
        if (!empty($rows[$k][0])) {
            $zone = $rows[$k][0];
        } elseif (!empty($rows[$k][1]) && $rows[$k][3] !== '') {
            $key = $parts[1] . $zone . $rows[$k][1];
            $cunli[$key]['總統'] = [
                '民主進步黨' => $rows[$k][3],
                '中國國民黨' => $rows[$k][4],
                '台灣民眾黨' => $rows[$k][2],
            ];
        }
    }
}

foreach (glob($basePath . '/raw/result/全國各村里一覽表/xls/不分區立委/*.xlsx') as $xlsFile) {
    $p = pathinfo($xlsFile);
    $parts = preg_split('/[\\(\\)]/', $p['filename'], -1, PREG_SPLIT_NO_EMPTY);
    // extract excel data using phpoffice/phpspreadsheet
    $reader = new \PhpOffice\PhpSpreadsheet\Reader\Xlsx();
    $spreadsheet = $reader->load($xlsFile);
    $sheet = $spreadsheet->getActiveSheet();
    $rows = $sheet->toArray();
    foreach ($rows as $k => $v) {
        foreach ($v as $k2 => $v2) {
            $rows[$k][$k2] = str_replace(['　', "\n", ','], '', $v2);
        }
        if (!empty($rows[$k][0])) {
            $zone = $rows[$k][0];
        } elseif (!empty($rows[$k][1]) && $rows[$k][3] !== '') {
            $key = $parts[1] . $zone . $rows[$k][1];
            $cunli[$key]['不分區'] = [
                '民主進步黨' => $rows[$k][7],
                '中國國民黨' => $rows[$k][10],
                '台灣民眾黨' => $rows[$k][13],
            ];
        }
    }
}

file_put_contents($basePath . '/data/cunli.json', json_encode($cunli, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));