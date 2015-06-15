<?php

function httpCall($url, $post, $params, $follow_location, $header, $cookies = null)
{
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    if($post){
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $params);
    }
    
    if($header)
        curl_setopt($ch, CURLOPT_HEADER, true);
    
    if(isset($cookies))
        curl_setopt($ch, CURLOPT_HTTPHEADER, ["Cookie: " . $cookies, 'Content-Type:application/json;charset=UTF-8']);
    
    if($follow_location)
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    $server_output = curl_exec($ch);

    curl_close ($ch);

    return $server_output;
}

function post($url, $params, $encode_params, $header, $cookies = null)
{
    if($encode_params)
        $params = json_encode($params);
    return httpCall($url, true, $params, true, $header, $cookies);
}

function get($url, $follow_location, $header, $cookies = null)
{
    return httpCall($url, FALSE, null, $follow_location, $header, $cookies);
}

