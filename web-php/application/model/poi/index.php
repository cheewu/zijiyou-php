<?php

if(!empty($_GET['fpoi']) && $_GET['fpoi'] == 1) {
	include 'new_poi.php';
	return;
}

$poi_id = @$_GET['poi_id'] ?: '';
$pg = @$_GET['pg'] ?: 1;
$ps = 5;

$poi_pic = tpl_get_google_poi_region_img($poi_id, 'poi', '150x150');
$poi = $_SGLOBAL['db']->POI_select_one(array('_id' => new MongoID($poi_id)));

//$photos = $_SGLOBAL['flicker']->get_poi_pic($poi);
//$img = !empty($photos[0]) ? flicker_photo_url($photos[0], 'm') : "";

$img = get_poi_pic($poi_id);

if(empty($poi)) {
	shttp_redirect("/");
}

$name = $poi['name'];

$region = $_SGLOBAL['db']->Region_select_one(array('_id' => new MongoID($poi['regionId'])));

$region_id = strval($region['_id']);

$query = array('category' => 'attraction', 'regionId' => new MongoID($poi['regionId']));

$attraction_nearby = isset($poi['center']) ? $_SGLOBAL['db']->POI_geo_query($poi['center'], null, 10, $query) : array();

$query['category'] = 'subway';

$subway_nearby = isset($poi['center']) ? $_SGLOBAL['db']->POI_geo_query($poi['center'], null, 10, $query) : array();

$wiki = get_wiki_content($poi['name']);

$_TPL['title'] = $name;

if (!empty($poi['keyword'])) {
	$poi_keyword_arr = explode(",", $poi['keyword']);
	$keyword_search = $poi_keyword_arr[0];
} else {
	$keyword_search = $poi['name'];
}

list($documents, $total_res_cnt) = tpl_article_search($keyword_search, $region['name'], $pg, $ps);

/*
$documents = $_SGLOBAL['pagedb']->keywordsParagraph_select(array('keyword' => $keyword_search, 'region' => $region['name']), null, array('score' => -1), $ps, $pg);

$total_res_cnt = ceil($_SGLOBAL['pagedb']->res_count / $ps);
foreach ($documents AS &$document) {
	$article = $_SGLOBAL['pagedb']->Article_select_one(
					array('_id' => new MongoID($document['documentID'])),
					array('title', 'optDateTime', 'author')
				);
	$document['keyword'] = array(); //get_article_keywords($document['documentID'], 'tpl_article_keyword_format');
	unset($article['_id']);
	$document = array_merge($document, $article);
	$tmp_paragraph_arr = array();
	$tmp_cnt_arr = array();
	foreach ($document['paragraphs'] AS $index => $paragraph) {
		!isset($tmp_paragraph_arr[$paragraph['wordCount']]) &&
		       $tmp_paragraph_arr[$paragraph['wordCount']] = $index;
		$tmp_cnt_arr[$index] = $paragraph['wordCount'];
	}
	$selected_paragraph_num = $tmp_paragraph_arr[max($tmp_cnt_arr)];
	$paragraph_start = $document['paragraphs'][$selected_paragraph_num]['start'];
	$paragraph_end   = $document['paragraphs'][$selected_paragraph_num]['end'];
	$selected_paragraphs_all = $_SGLOBAL['pagedb']->articleParagraphs_select_one(
								array('documentID' => $document['documentID']),
							    array('paragraphs')
							);
	$selected_paragraphs = array_slice($selected_paragraphs_all['paragraphs'], $paragraph_start, $paragraph_end - $paragraph_start + 1);
	
	$document['content'] = strip_tags(implode("", $selected_paragraphs));
	// modified by 2012-6-26
	//$document['images'] = $document['paragraphs'][$selected_paragraph_num]['pictures'];
	$document['images'] = @$document['pictures'] ?: array();
	foreach ($document['images'] AS &$image) {
		if(preg_match_all("#real_src\s*=\s*[\"']([^\"]*)[\"']#", $image, $matches)) {
			$image = 'src="'.$matches[1][0].'"';
		}
		preg_match_all("#src\s*=\s*[\"']([^\"]*)[\"']#", $image, $matches);
		$image = $matches[1][0];
	}
	unset($image);
}
unset($document);
*/
require template();
return;

$solr_query = array(
	'solr_type' => 'attraction',
	'query_words' => $name,
	'ps' => 5,
);

$_SGLOBAL['solr']->parse_request($solr_query);

$solr_res_original = $_SGLOBAL['solr']->do_request();

$total_res_cnt = $solr_res_original['response']['numFound'];

$solr_res = $solr_res_original['response']['doc'];

foreach($solr_res AS $key => &$value) {
	$fragment = $_SGLOBAL['pagedb']->fragments_select_one(array('_id' => new MongoID($value['_id'])));
	$article = $_SGLOBAL['pagedb']->Article_select_one(array('_id' => new MongoID($value['articleId'])), array('content', 'author'));
	$value['author'] = @$article['author'] ?: "";
	$value['fragment']['str'] = preg_replace("#<\s*img.*?real_src\s*=\s*[\"']([^\"]*)[\"'].*?/\s*>#", '<img src="$1" />', $value['fragment']['str']);
	preg_match_all("#<\s*img\s*src\s*=\s*[\"']([^\"]*)[\"'].*?>#", $value['fragment']['str'], $matches);
	$value['fragment']['str'] = strip_tags($value['fragment']['str']);
	$value['keyword'] = get_article_keywords($value['articleId'], 'tpl_article_keyword_format');
	//$value['content'] = strip_tags($article['content']);
	//$value['content'] = strip_tags($value['fragment']['str']);
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
//		$each_paragraph = preg_replace("#[\r\n\s]+#s", " ", $each_paragraph);
//		$each_paragraph = preg_replace_callback("#<\s*img.*?real_src\s*=\s*[\"']([^\"]*)[\"'].*?/\s*>#", 
//			create_function('$matches', "return '<img src=\"'.img_proxy(\$matches[1], '{$value['url']}', 9999, 590).'\"/>';"), $each_paragraph);
//		$each_paragraph = preg_replace_callback("#<\s*img.*?src\s*=\s*[\"']([^\"]*)[\"'].*?/\s*>#", 
//			create_function('$matches', "return '<img src=\"'.img_proxy(\$matches[1], '{$value['url']}', 9999, 590).'\"/>';"), $each_paragraph);
		$value['content'] .= trim($each_paragraph);
	}
	empty($value['content']) && $value['content'] = $article['content'];
}

require template();