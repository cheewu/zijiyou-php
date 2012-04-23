<?php

if(!empty($_GET['fpoi']) && $_GET['fpoi'] == 1) {
	include 'new_poi.php';
	return;
}

$poi_id = @$_GET['poi_id'] ?: '';
$pg = @$_GET['pg'] ?: 1;

$poi = $_SGLOBAL['db']->POI_select_one(array('_id' => new MongoID($poi_id)));

$photos = $_SGLOBAL['flicker']->get_poi_pic($poi);
$img = !empty($photos[0]) ? flicker_photo_url($photos[0], 'm') : "";

if(empty($poi)) {
	shttp_redirect("/");
}

$name = $poi['name'];

$region = $_SGLOBAL['db']->Region_select_one(array('_id' => new MongoID($poi['regionId'])));

$query = array('category' => 'attraction', 'regionId' => new MongoID($poi['regionId']));

$attraction_nearby = $_SGLOBAL['db']->POI_geo_query($poi['center'], null, 10, $query);

$query['category'] = 'subway';

$subway_nearby = $_SGLOBAL['db']->POI_geo_query($poi['center'], null, 10, $query);

$solr_query = array(
	'solr_type' => 'attraction',
	'query_words' => $name,
	'ps' => 3,
);

$_SGLOBAL['solr']->parse_request($solr_query);

$solr_res_original = $_SGLOBAL['solr']->do_request();

$total_res_cnt = $solr_res_original['response']['numFound'];

$solr_res = $solr_res_original['response']['doc'];

foreach($solr_res AS $key => &$value) {
	$fragment = $_SGLOBAL['pagedb']->fragments_select_one(array('_id' => new MongoID($value['_id'])));
	$article = $_SGLOBAL['pagedb']->Article_select_one(array('_id' => new MongoID($value['articleId'])), array('content', 'author'));
	preg_match_all("#<\s*img\s*src\s*=\s*[\"']([^\"]*)[\"'].*?>#", $value['fragment']['str'], $matches);
	$value['fragment']['str'] = strip_tags($value['fragment']['str']);
	$value['keyword'] = get_article_keywords($value['articleId'], 'tpl_article_keyword_format');
	//$value['content'] = strip_tags($article['content']);
	$value['content'] = strip_tags($value['fragment']['str']);
	$value['fragment_keyword'] = $fragment['keyword'];
	$images = array();
	foreach($matches[1] AS $image) {
		!in_array($image, $images) && $images[] = $image;
	}
	$value['images'] = $images;
	$value['content'] = "";
	foreach(explode(" ", $fragment['selected']) AS $fragment_select) {
		$each_paragraph = $fragment['fragment'][$fragment_select];
		//$each_paragraph = strip_tags($fragment['fragment'][$fragment_select]);
		$each_paragraph = preg_replace("#[\r\n\s]+#s", " ", $each_paragraph);
		$each_paragraph = preg_replace_callback("#<\s*img.*?real_src\s*=\s*[\"']([^\"]*)[\"'].*?/\s*>#", 
			create_function('$matches', "return '<img src=\"'.img_proxy(\$matches[1], '{$value['url']}', 9999, 590).'\"/>';"), $each_paragraph);
		$each_paragraph = preg_replace_callback("#<\s*img.*?src\s*=\s*[\"']([^\"]*)[\"'].*?/\s*>#", 
			create_function('$matches', "return '<img src=\"'.img_proxy(\$matches[1], '{$value['url']}', 9999, 590).'\"/>';"), $each_paragraph);
		$value['content'] .= "<p>".trim($each_paragraph)."</p>";
	}
}

$_TPL['title'] = $name;
require template();