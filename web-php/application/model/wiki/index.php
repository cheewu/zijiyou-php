<?php

$region_id = $_GET['region_id'];

$article_id = $_GET['wiki_id'];

$wiki = $_SGLOBAL['db']->Wikipedia_select_one(array('_id' => $article_id));

$region = $_SGLOBAL['db']->Region_select_one(array('_id' => new MongoID($region_id)));

$wiki_format = preg_replace_callback("#(={2,})([^=]+?)\\1#", 
	create_function('$matches', '$cnt = strlen($matches[1]); return "<h$cnt style=\"padding-top: 10px; padding-bottom: 10px\">$matches[2]</h$cnt>";'), $wiki['content']);

$wiki_format = preg_replace_callback("#^(\*+)(.*)$#m", 
	create_function('$matches', '$cnt = strlen($matches[1]); $margin_left = $cnt * 10; return "<li style=\'list-style-type:disc;margin-left:{$margin_left}px;\'>{$matches[2]}</li>";'), $wiki_format);

$_TPL['title'] = $wiki['title'];

include template();