<?php
// https://twland.ronny.tw/index/search?lands[]=臺北市,華興段三小段,141-1
$fh = fopen(dirname(__DIR__) . '/raw/ChiayiTainan/BB.csv', 'r');
$rawPath = dirname(__DIR__) . '/raw/lands';
$header = fgetcsv($fh, 2048);
$pool = [];
while ($line = fgetcsv($fh, 2048)) {
    $data = array_combine($header, $line);
    $countyPath = $rawPath . '/' . $data['縣市'];
    if (!file_exists($countyPath)) {
        mkdir($countyPath, 0777, true);
    }
    $cacheFile = $countyPath . '/' . $data['段名'] . $data['地號'] . '.json';
    if (!file_exists($cacheFile)) {
        $parameters = implode(',', [$data['縣市'], $data['段名'], $data['地號']]);
        if (!isset($pool[$parameters])) {
            $pool[$parameters] = true;
            $url = 'https://twland.ronny.tw/index/search?lands[]=' . urlencode($parameters);
            $curl_handle = curl_init();
            curl_setopt($curl_handle, CURLOPT_URL, $url);
            curl_setopt($curl_handle, CURLOPT_CONNECTTIMEOUT, 2);
            curl_setopt($curl_handle, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($curl_handle, CURLOPT_USERAGENT, 'kiang');
            $query = curl_exec($curl_handle);
            curl_close($curl_handle);
            if (!empty($query)) {
                $json = json_decode($query, true);
                if (!empty($json['features'])) {
                    file_put_contents($cacheFile, json_encode($json, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
                    echo "{$cacheFile}\n";
                } else {
                    file_put_contents($cacheFile, '{}');
                }
            } else {
                file_put_contents($cacheFile, '{}');
            }
        }
    }
}
