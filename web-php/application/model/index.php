<?php
if(!defined('IN_SYSTEM')) { exit('Access Denied'); }

if(is_file(ROOT.'index.xml')) {
    $simplexmlobj = simplexml_load_string_to_xmlobj(file_get_contents(ROOT.'index.xml'), 'utf-8');
    $data_arr = simplexml_to_array($simplexmlobj);
}
$hot_destination = isset($data_arr['hotDestination']['item']) ? 
                   $data_arr['hotDestination']['item'] : array();
$reco_estination = isset($data_arr['recoDestination']['item']) ? 
                   $data_arr['recoDestination']['item'] : array();
                   
$hot_region_geo = $hot_region_arr = array();
foreach ($hot_destination AS $index => $id) {
    $hot_region = $_SGLOBAL['db']->Region_select_one(array('_id' => new MongoID($id)));
    $id = (string)$hot_region['_id'];
	//筛选有坐标点的poi
	if(empty($hot_region['center'][0]) && empty($hot_region['center'][1])) { continue; }
	//筛选只有中文的poi
	if(mb_strlen($hot_region['name'], 'utf-8') == strlen($hot_region['name'])){continue;}
	/* 处理google地图信息 */
	$hot_region_geo[$id] = array(
		'position' => array('lt' => $hot_region['center'][0], 'lg' => $hot_region['center'][1]),
		'title' => $hot_region['name'],
		'content' => tpl_get_region_geo_content($hot_region),
	); 
	!$index && $center = array('lt' => $hot_region['center'][0], 'lg' => $hot_region['center'][1]);
	$hot_region_arr[] = $hot_region;
}


foreach($reco_estination AS &$item) {
    foreach ($item['id']['item'] AS &$id) {
        $id = $_SGLOBAL['db']->Region_select_one(array('_id' => new MongoID($id)), array('name'));
    }
}
unset($item, $id);
$_TPL['title'] = "自己游";

include template();

