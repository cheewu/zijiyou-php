<?php
if(!defined('IN_SYSTEM')) { exit('Access Denied'); }

$region_id = @$_GET['region_id'] ?: '';
$pg = @$_GET['pg'] ?: 1;

$region = $_SGLOBAL['db']->Region_select_one(array('_id' => new MongoID($region_id)));
$sub_region = $_SGLOBAL['db']->Region_select(array('area' => $region['name']));
$region_correlation = $_SGLOBAL['pagedb']->correlation_select_one(array('name' => $region['name']));

$sub_region_no_order = array();
$sub_region_score = array();
$sub_region_order = array();
foreach ($sub_region AS $value) {
	$id = strval($value['_id']);
	$sub_region_no_order[$id] = $value;
	$sub_region_score[$id] = @$region_correlation['correlation']['destination'][$value['name']] ?: 0; 
}

arsort($sub_region_score);
foreach ($sub_region_score as $id => $score) {
	$sub_region_order[] = $sub_region_no_order[$id];
}

$sub_region = $sub_region_order;

$sub_region_geo = array();

$sub_region_arr = array();

foreach($sub_region AS $index => $value){
	if(count($sub_region_geo) >= 6) { break; }
	$id = (string)$value['_id'];
	//筛选有坐标点的poi
	if(empty($value['center'][0]) && empty($value['center'][1])){continue;}
	//筛选只有中文的poi
	if(mb_strlen($value['name'], 'utf-8') == strlen($value['name'])){continue;}
	/* 处理google地图信息 */
	$sub_region_geo[$id] = array(
		'position' => array('lt' => $value['center'][0], 'lg' => $value['center'][1]),
		'title' => $value['name'],
		'content' => tpl_get_region_geo_content($value),
	); 
	$sub_region_arr[] = $value;
	/* 处理google地图信息 end*/
}
$sub_region = $sub_region_arr;

$sub_poi = $_SGLOBAL['db']->POI_select(array('regionId' => $region['_id']), null, array('rank', -1));

$geo = (!empty($region['center']) && (!empty($region['center'][0]) || !empty($region['center'][1]))) 
			? array('lt' => $region['center'][0], 'lg' => $region['center'][1]) : null;

$map_zoom = ($region['category'] == 'country') ? 4 : 5; 
			
$wiki = get_wiki_content($region['name']);

list($documents) = tpl_article_search($region['name'], $region['name'], 1, 3);

$_TPL['title'] = $region['name'];
include template();
return;

/* solr 
$solr_query = array(
	'solr_type' => 'region',
	'query_words' => $region['name'],
	'ps' => 3,
);

$_SGLOBAL['solr']->parse_request($solr_query);

$solr_res_original = $_SGLOBAL['solr']->do_request();

$solr_res = @$solr_res_original['response']['doc'] ?: array();

foreach($solr_res AS $key => &$value) {
	$value = $_SGLOBAL['pagedb']->Article_select_one(array('_id' => new MongoID($value['_id'])));
	$value['content'] = strip_tags($value['content']);
	$value['keyword'] = get_article_keywords($value['_id'], 'tpl_article_keyword_format');
}
   solr */
