<?php

function Invoke($url, $data = null, $headers = null)
{
    $curl = curl_init();
    $defaultHeaders = array(
        'Content-Type: application/json'
    );

    if (!empty($headers)) {
        foreach ($headers as $k => $v) {
            $defaultHeaders[] = $k.': '. $v;
        }
    }
    curl_setopt_array($curl, array(
        CURLOPT_URL => $url,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_SSL_VERIFYPEER => false,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'POST',
        CURLOPT_POSTFIELDS => is_array($data) ? json_encode($data) : $data,
        CURLOPT_HTTPHEADER => $defaultHeaders,
    ));
    $response = curl_exec($curl);
    curl_close($curl);
    return $response;
}