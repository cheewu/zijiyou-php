<?php

$region_id = @$_GET['region_id'] ?: '';

$region = $_SGLOBAL['db']->Region_select_one(array('_id' => new MongoID($region_id)));

if(empty($region)) {
	shttp_redirect("/");
}


$geo = (!empty($region['center']) && (!empty($region['center'][0]) || !empty($region['center'][1]))) 
			? array('lt' => $region['center'][0], 'lg' => $region['center'][1]) : null;

$sub_info = $_SGLOBAL['db']->POI_select(
					array(
						'regionId' => new MongoID($region['_id']), 
						'center' => array('$exists' => true), 
						'category' => array('$ne' => 'attraction')), 
					null, 
					array('rank' => -1)
				);
$poi_attraction = $_SGLOBAL['db']->POI_select(
						array(
							'regionId' => new MongoID($region['_id']), 
							'category' => 'attraction'), 
						null, 
						array('rank' => -1), 
						10, 
						@$_GET['pg'] ?: 1
				);
$sub_info = array_merge($sub_info, $poi_attraction);

$sub_geo_arr = array();

foreach($sub_info AS $value){
	$id = (string)$value['_id'];
	//筛选有坐标点的poi
	if(empty($value['center'][0]) && empty($value['center'][1])){continue;}
	//筛选只有中文的poi
	if(mb_strlen($value['name'], 'utf-8') == strlen($value['name'])){continue;}
	/* 处理google地图信息 */
	$sub_geo_arr[$value['category']][$id] = array(
		'position' => array('lt' => $value['center'][0], 'lg' => $value['center'][1]),
		'title' => $value['name'],
	); 
	$sub_geo_arr[$value['category']][$id]['content'] = tpl_get_geo_content($value, $region_id);
	/* 处理google地图信息 end*/
}

$sub_attraction = $sub_geo_arr['attraction'];
unset($sub_geo_arr['attraction']);

$_TPL['title'] = $region['name']."地图";




				
require template();