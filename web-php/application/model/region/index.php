<?php
if(!defined('IN_SYSTEM')) { exit('Access Denied'); }

$region_id = @$_GET['region_id'] ?: '';
$pg = @$_GET['pg'] ?: 1;
$ps = 10;

$region = $_SGLOBAL['db']->Region_select_one(array('_id' => new MongoID($region_id)));

if(in_array($region['category'], array('province', 'country'))) {
    shttp_redirect("/state/".$region_id);
}

$correlation = $_SGLOBAL['pagedb']->correlation_select_one(array('name' => $region['name']));




$region['images'] = array();

for ($i = 1; $i <= 4; $i++) {
	$filename = $region['name'].'_'.$i.'.jpg';
	if(is_file(ROOT.'destination/'.$filename)) {
		$region['images'][] = "/destination/".$filename;
	}
}

empty($region['timezone']) && $region['timezone'] = 8;
!empty($region['timezone']) && $region['timezone'] !== 0 && $region['timezone'] = 8;

if(empty($region)) {
	shttp_redirect("/");
}

$geo = (!empty($region['center']) && (!empty($region['center'][0]) || !empty($region['center'][1]))) 
			? array('lt' => $region['center'][0], 'lg' => $region['center'][1]) : null;
			
list($documents, $total_res_cnt) = tpl_article_search($region['name'], $region['name'], $pg, $ps);
/*	
$documents = $_SGLOBAL['pagedb']->keywordsParagraph_select(array('keyword' => $region['name']), null, array('score' => -1), $ps, $pg);
$total_res_cnt = ceil($_SGLOBAL['pagedb']->res_count / $ps);
foreach ($documents AS &$document) {
	$article = $_SGLOBAL['pagedb']->Article_select_one(
					array('_id' => new MongoID($document['documentID'])),
					array('title', 'optDateTime', 'author')
				);
	empty($article) && $article = array();
	$document['keyword'] = array(); //get_article_keywords($document['documentID'], 'tpl_article_keyword_format');
	$document['images'] = array();
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
	if (empty($selected_paragraphs_all['paragraphs'])) {
		continue;		
	}
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

$sub_pois = $_SGLOBAL['db']->POI_select(array('regionId' => $region['_id']), null, array('rank' => -1), 12);

//foreach($sub_pois AS $key => $poi) {
//	$photos = $_SGLOBAL['flicker']->get_poi_pic($poi);
//	$sub_pois[$key]['img_icon'] = !empty($photos[0]) ? flicker_photo_url($photos[0], 'q') : "";
//}

$also_go = array();
/*
$_SGLOBAL['db']->Correlation_select(
				array('name' => $region['name'], 'category' => 'region'),
				null,
				array('correlation' => -1),
				9
			);
*/
foreach($also_go AS $key => &$value) {
	$tmp_poi = $_SGLOBAL['db']->Region_select_one(array('name' => array('$regex' => $value['keyword'])));
	$value = array_merge($value, $tmp_poi);
//	$photos = $_SGLOBAL['flicker']->get_poi_pic($value);
//	$value['img_icon'] = !empty($photos[0]) ? flicker_photo_url($photos[0], 'q') : "";
}
unset($value);

$name = @$region['name'] ?: '';

$wiki = !empty($name) ? get_wiki_content($name) : '';

$wiki_substr = utf8_substr_ifneeed($wiki['content'], 500, false, '...');

$region_tag = $_SGLOBAL['db']->Correlation_select(array('name' => $region['name']), array('keyword'));

$_TPL['title'] = $name;

require template();


/*
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
//	foreach($value['images'] AS $image) {
//		preg_match("#real_src\s*=\s*[\"']([^\"]*)[\"']#", $image, $matches) || 
//		preg_match("#src\s*=\s*[\"']([^\"]*)[\"']#", $image, $matches);
//		!in_array($matches[1], $images) && $images[] = $matches[1];
//	}
//	$value['images'] = $images;
}
*/