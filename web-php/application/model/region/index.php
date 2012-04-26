<?php
if(!defined('IN_SYSTEM')) { exit('Access Denied'); }

$region_id = @$_GET['region_id'] ?: '';
$pg = @$_GET['pg'] ?: 1;

$region = $_SGLOBAL['db']->Region_select_one(array('_id' => new MongoID($region_id)));

!isset($region['timezone']) && $region['timezone'] = 8;

if(empty($region)) {
	shttp_redirect("/");
}

$geo = (!empty($region['center']) && (!empty($region['center'][0]) || !empty($region['center'][1]))) 
			? array('lt' => $region['center'][0], 'lg' => $region['center'][1]) : null;

$solr_query = array(
	'solr_type' => 'region',
	'query_words' => $region['name'],
	'ps' => 3,
);

$_SGLOBAL['solr']->parse_request($solr_query);

$solr_res_original = $_SGLOBAL['solr']->do_request();

$total_res_cnt = empty($solr_res_original['response']) ? 0 : $solr_res_original['response']['numFound'];

$solr_res = @$solr_res_original['response']['doc'] ?: array();

foreach($solr_res AS $key => &$value) {
	$value = $_SGLOBAL['pagedb']->Article_select_one(array('_id' => new MongoID($value['_id'])));
	$value['content'] = strip_tags($value['content']);
	$value['keyword'] = get_article_keywords($value['_id'], 'tpl_article_keyword_format');
	$images = array();
	foreach($value['images'] AS $image) {
		preg_match("#real_src\s*=\s*[\"']([^\"]*)[\"']#", $image, $matches) || 
		preg_match("#src\s*=\s*[\"']([^\"]*)[\"']#", $image, $matches);
		!in_array($matches[1], $images) && $images[] = $matches[1];
	}
	$value['images'] = $images;
}

$sub_pois = $_SGLOBAL['db']->POI_select(array('regionId' => $region['_id']), null, array('rank' => -1), 12);

foreach($sub_pois AS $key => $poi) {
	$photos = $_SGLOBAL['flicker']->get_poi_pic($poi);
	$sub_pois[$key]['img_icon'] = !empty($photos[0]) ? flicker_photo_url($photos[0], 'q') : "";
}

$also_go = $_SGLOBAL['db']->Correlation_select(
				array('name' => $region['name'], 'category' => 'region'),
				null,
				array('correlation' => -1),
				9
			);

foreach($also_go AS $key => &$value) {
	$tmp_poi = $_SGLOBAL['db']->Region_select_one(array('name' => array('$regex' => $value['keyword'])));
	$value = array_merge($value, $tmp_poi);
	$photos = $_SGLOBAL['flicker']->get_poi_pic($value);
	$value['img_icon'] = !empty($photos[0]) ? flicker_photo_url($photos[0], 'q') : "";
}
unset($value);

$name = @$region['name'] ?: '';

$wiki = !empty($name) ? get_wiki_content($name) : '';

$wiki_substr = utf8_substr_ifneeed($wiki['content'], 500, false, '...');

$region_tag = $_SGLOBAL['db']->Correlation_select(array('name' => $region['name']), array('keyword'));

$_TPL['title'] = $name;

require template();