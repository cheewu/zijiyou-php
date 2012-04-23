<?php
$frob = $_GET['frob'];


$res = $_SGLOBAL['flicker']->go(array(
	'method' => 'flickr.photos.search',
//	'auth_token' => '72157629642190479-09565cd2248fa635',
//	'bbox' => '113,37,118,41',
	'lat' => 0,
	'lon' => 0,
));

pr($res);