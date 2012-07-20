<?php
$region_id = @$_GET['region_id'] ?: '';

$pg = @$_GET['pg'] ?: 1;
$ps = 10;

$region = $_SGLOBAL['db']->Region_select_one(array('_id' => new MongoID($region_id)));
$keyword   = @$_GET['keyword'] ?: $region['name'];

list($documents, $total_res_cnt) = tpl_article_search($keyword, $region['name'], $pg, $ps);
/*
$documents = $_SGLOBAL['pagedb']->keywordsParagraph_select(
                   array(
                   		'keyword' => empty($keyword) ? $region['name'] : $keyword,
                        'region'  => $region['name'],
                   ), 
                   array('documentID'), 
                   array('score' => -1), $ps, $pg);
$total_res_cnt = ceil($_SGLOBAL['pagedb']->res_count / $ps);
foreach ($documents AS &$document) {
	$article = $_SGLOBAL['pagedb']->Article_select_one(
					array('_id' => new MongoID($document['documentID'])),
					array('title', 'optDateTime', 'author', 'images', 'content')
				);
	empty($article) && $article = array();
	//$document['keyword'] = get_article_keywords($document['documentID'], 'tpl_article_keyword_format');
	$document['images'] = array();
	$document = array_merge($document, $article);
	foreach ($document['images'] AS &$image) {
		if(preg_match_all("#real_src\s*=\s*[\"']([^\"]*)[\"']#", $image, $matches)) {
			$image = 'src="'.$matches[1][0].'"';
		}
		preg_match_all("#src\s*=\s*[\"']([^\"]*)[\"']#", $image, $matches);
		$image = $matches[1][0];
	}
}
*/
$_TPL['title'] = $region['name'].'游记';

$correlation_res = $_SGLOBAL['pagedb']->correlation_select_one(array('name' => $region['name']));

$correlation_words = array();
if (!empty($correlation_res['correlation'])) {
    if (isset($correlation_res['correlation']['destination'])) {
        unset($correlation_res['correlation']['destination']); 
    }
    foreach ($correlation_res['correlation'] AS $cate_correlation) {
        $correlation_words = array_merge($correlation_words, $cate_correlation);
    }
}
arsort($correlation_words);

include template();
return;


$solr_query = array(
	'solr_type' => 'region',
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
		create_function('$matches', "return '<img src=\"'.img_proxy(\$matches[1], '{$value['url']}', 0, 590).'\"/>';"), $value['content']);
	$value['content'] = preg_replace_callback("#<\s*img.*?src\s*=\s*[\"']([^\"]*)[\"'].*?/\s*>#", 
		create_function('$matches', "return '<img src=\"'.img_proxy(\$matches[1], '{$value['url']}', 0, 590).'\"/>';"), $value['content']);
}

$_TPL['title'] = $region['name'].'游记';

include template();