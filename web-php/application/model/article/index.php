<?php
$region_id = @$_GET['region_id'] ?: '';
$pg = @$_GET['pg'] ?: 1;

$region = $_SGLOBAL['db']->Region_select_one(array('_id' => new MongoID($region_id)));

$solr_query = array(
	'solr_type' => 'fullsearch',
	'query_words' => $region['name'],
);

$_SGLOBAL['solr']->parse_request($solr_query);

$solr_res_original = $_SGLOBAL['solr']->do_request();

$total_res_cnt = empty($solr_res_original['response']) ? 0 : $solr_res_original['response']['numFound'];

$solr_res = @$solr_res_original['response']['doc'] ?: array();


foreach($solr_res AS $key => &$value) {
	$value = $_SGLOBAL['pagedb']->Article_select_one(array('_id' => new MongoID($value['_id'])));
//	$value['content'] = strip_tags($value['content']);
	$value['keyword'] = get_article_keywords($value['_id'], 'tpl_article_keyword_format');
	$value['content'] = preg_replace_callback("#<\s*img.*?real_src\s*=\s*[\"']([^\"]*)[\"'].*?/\s*>#", 
		create_function('$matches', "return '<img src=\"'.img_proxy(\$matches[1], '{$value['url']}', 9999, 590).'\"/>';"), $value['content']);
	$value['content'] = preg_replace_callback("#<\s*img.*?src\s*=\s*[\"']([^\"]*)[\"'].*?/\s*>#", 
		create_function('$matches', "return '<img src=\"'.img_proxy(\$matches[1], '{$value['url']}', 9999, 590).'\"/>';"), $value['content']);
}

$_TPL['title'] = $region['name'].'游记';

include template();