<?php
$basePath = dirname(__DIR__);

for ($i = 1; $i <= 32; $i++) {
    $url = 'https://c.olc.tw/teams/events/' . $i;
    file_put_contents($basePath . '/raw/c.olc.tw/team_' . $i . '.json', json_encode(json_decode(file_get_contents($url)), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
}
