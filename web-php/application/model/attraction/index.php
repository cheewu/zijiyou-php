<?php

$region_id = @$_GET['region_id'] ?: '';
$pg = @$_GET['pg'] ?: 1;
$radius = @$_GET['r'] ?: -1;

$ps = 10;

$region = $_SGLOBAL['db']->Region_select_one(array('_id' => new MongoID($region_id)));

if(empty($region)) {
	shttp_redirect("/");
}

$query = array('regionId' => $region['_id']);

$radius > 0 && !empty($region['center']) && $query['center']['$within']['$center'] = array($region['center'], intval($radius));

$sub_pois = $_SGLOBAL['db']->POI_select($query, null, array('rank' => -1), $ps, $pg);

$total_res_cnt = $_SGLOBAL['db']->res_count;

$geo = (!empty($region['center']) && (!empty($region['center'][0]) || !empty($region['center'][1]))) 
			? array('lt' => $region['center'][0], 'lg' => $region['center'][1]) : null;

$_TPL['title'] = $region['name'].'景点';
require template();