<?php
$region_id = $_GET['region_id'];
$article_id = $_GET['article_id'];

$region = $_SGLOBAL['db']->Region_select_one(array('_id' => new MongoID($region_id)));
$article = $_SGLOBAL['pagedb']->Article_select_one(array('_id' => new MongoID($article_id)));

$content = $article['content'];
$content = preg_replace_callback("#<\s*img.*?real_src\s*=\s*[\"']([^\"]*)[\"'].*?/\s*>#", 
	create_function('$matches', "return '<img src=\"'.trim(\$matches[1]).'\" onerror=\"\$(this).css(\'display\', \'none\')\"/>';"), $content);
$content = preg_replace_callback("#<\s*img.*?src\s*=\s*[\"']([^\"]*)[\"'].*?/\s*>#", 
	create_function('$matches', "return '<img src=\"'.img_proxy(\$matches[1], '{$article['url']}', 620, 0).'\" onerror=\"\$(this).css(\'display\', \'none\')\"/>';"), $content);

$content = nl2br($content);
	
//pr($article);

$_TPL['title'] = @$article['title']."_游记";

include template();