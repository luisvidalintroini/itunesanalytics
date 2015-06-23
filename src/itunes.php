<?php
namespace luisvidalintroini\ItunesAnalytics;
include 'http.php';

class Frequency
{
    const Day = 'DAY';
    const Month = 'MONTH';
}

class Dimension
{
    const Territory = 'storefront';
    const Platform = 'platform';
    const AppVersion = 'appVersion';
    const PlatformVersion = 'platformVersion';
}

class Measure
{
    const Units = 'units';
    const Installs = 'installs';
    const Visits = 'pageViewCount';
    const InternalPurchases = 'iap';
    const Sales = 'sales';
    const Sessions = 'sessions';
    const ActiveDevices = 'activeDevices';
    const PayingUsers = 'payingUsers';
    const Errors = 'crashes';
}

class ItunesConnect
{
    private $username, $password;
    private $baseURL = "https://itunesconnect.apple.com";
    private $timeSeriesURL = "https://analytics.itunes.apple.com/analytics/api/v1/data/time-series";
    private $dimensionsURL = "https://analytics.itunes.apple.com/analytics/api/v1/data/app/detail/dimensions";
    private $appListURL = "https://analytics.itunes.apple.com/analytics/api/v1/app-info/app";
    private $allURL = "https://analytics.itunes.apple.com/analytics/api/v1/settings/all";
    
    private $cookies = '';

    public function __construct($username, $password)
    {
        $this->username = $username;
        $this->password = $password;
    }
    
    private function getCookies()
    {
        if (empty($this->cookies)) {
            $this->login();
        }
        return $this->cookies;
    }

    private function getLoginAction()
    {    
        $server_output = get($this->baseURL, true, FALSE);

        preg_match('<form .*action="(.*?)".*?>', $server_output, $matches);
        $form_action = $this->baseURL . $matches[1];

        return $form_action;
    }

    public function login()
    {
        $server_output = post($this->getLoginAction(),
            ['theAccountName' => $this->username, 'theAccountPW' => $this->password, 'theAuxValue' => ''],
            false, true);

        preg_match_all('/^Set-Cookie:\s*([^;]*)/mi', $server_output, $matches);

        $this->cookies = implode('; ', $matches[1]);

        preg_match_all('|<span class="dserror[^>]+>(.*)</[^>]+>|U', 
        $server_output, 
        $errors, PREG_PATTERN_ORDER);
        return $errors[1];
    }

    public function getTimeSeries($appId, $start_time, $end_time, $frequency, array $measures, $dimension, array $dimension_options)
    {   
        $params = array(
            "adamId" => array($appId),
            "startTime" => date('Y-m-d\T00:00:00\Z',strtotime($start_time)),
            "endTime" => date('Y-m-d\T00:00:00\Z', strtotime($end_time)),
            "frequency" => $frequency,
            "measures" => $measures,
            "group" => null,
            "dimensionFilters"=> array(array(
                "dimensionKey" => $dimension,
                "optionKeys" => $dimension_options))
        );
        
        return post($this->timeSeriesURL, $params, true, false, $this->getCookies());
    }

    public function getDimensions($appId, $start_time, $end_time, $frequency, $measure, $dimension, $hide_empty_values, $limit)
    {
        $params = array(
            "adamId" => array($appId),
            "startTime" => date('Y-m-d\T00:00:00\Z',strtotime($start_time)),
            "endTime" => date('Y-m-d\T00:00:00\Z', strtotime($end_time)),
            "frequency" => $frequency,
            "measure" => $measure,
            "hideEmptyValues" => $hide_empty_values,
            "dimensions" => array($dimension),
            "limit" => $limit
        );
        
        return post($this->dimensionsURL, $params, true, false, $this->getCookies());
    }

    public function getApplist()
    {
        return get($this->appListURL, false, false, $this->getCookies());
    }

    public function getAll()
    {
        return get($this->allURL, false, false, $this->getCookies());
    }

}
