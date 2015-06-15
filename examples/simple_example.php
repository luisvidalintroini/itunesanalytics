<?php
include "../src/itunes.php";
use ItunesAnalytics\ItunesConnect;
use ItunesAnalytics\Frequency;
use ItunesAnalytics\Measure;
use ItunesAnalytics\Dimension;

$itunes_user = '';
$itunes_password = '';
$appId = '';

$itunesConnect = new ItunesConnect($itunes_user,$itunes_password);

$result=	json_decode(
		$itunesConnect->getTimeSeries(
			$appId, 
			'2015-05-04',
			'2015-05-10', 
                        Frequency::Day, 
                        array(Measure::Units),
                        Dimension::Platform,
                        array('iPhone')),
		true
	);

var_dump($result);

