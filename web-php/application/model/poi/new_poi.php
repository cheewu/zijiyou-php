<?php

$poi_id = @$_GET['poi_id'] ?: '';
$pg = @$_GET['pg'] ?: 1;
$ps = 10;

$poi = $_SGLOBAL['db']->POI_select_one(array('_id' => new MongoID($poi_id)));

$name = $poi['name'];

$query = array('category' => 'attraction', 'regionId' => new MongoID($poi['regionId']));

$attraction_nearby = $_SGLOBAL['db']->POI_geo_query($poi['center'], null, 10, $query);

$query['category'] = 'subway';

$subway_nearby = $_SGLOBAL['db']->POI_geo_query($poi['center'], null, 10, $query);

$solr_res = $_SGLOBAL['pagedb']->fragments_select(array('keyword' => $name), null, null, $ps, $pg);

$total_res_cnt = $_SGLOBAL['pagedb']->res_count;

foreach($solr_res AS &$value) {
	$article_res = $_SGLOBAL['pagedb']->Article_select_one(array('_id' => new MongoID($value['articleId'])), array('images'));
	unset($article_res['_id']);
	$value['images'] = array();
	$value = array_merge($value, $article_res);
	$value['fragment_keyword'] = $value['keyword'];
	$value['keyword'] = get_article_keywords($value['articleId'], 'tpl_article_keyword_format');
	$images = array();
	foreach($value['images'] AS $image) {
		preg_match("#src\s*=\s*[\"']([^\"]*)[\"']#", $image, $matches);
		!in_array($matches[1], $images) && $images[] = $matches[1];
	}
	$value['images'] = $images;
	$value['content'] = "";
	foreach(explode(" ", $value['selected']) AS $fragment_select) {
		$value['content'] .= "<p>".trim(strip_tags($value['fragment'][$fragment_select]))."</p>";
	}
	$title = $value['title'];
	$value['title'] = array();
	$value['title']['str'] = $title;
}
unset($value);
$_TPL['title'] = $name;
include template();