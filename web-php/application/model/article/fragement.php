<?php
$region_id = $_GET['region_id'];
$fragement_id = $_GET['fragement_id'];

$region = $_SGLOBAL['db']->Region_select_one(array('_id' => new MongoID($region_id)));
$fragement = $_SGLOBAL['pagedb']->fragments_select_one(array('_id' => new MongoID($fragement_id)));

$selected_num_arr = preg_split("#\s#", $fragement['selected']);
$seelcted_fragement = "";

foreach($selected_num_arr AS $select_one) {
	$item = trim($fragement['fragment'][$select_one]);
	!empty($item) && $seelcted_fragement .= "<p>$item</p>";
}
$seelcted_fragement = preg_replace("#<\s*img.*?real_src\s*=\s*[\"']([^\"]*)[\"'].*?/\s*>#", '<img src="$1" />', $seelcted_fragement);
$seelcted_fragement = preg_replace_callback("#<\s*img.*?src\s*=\s*[\"']([^\"]*)[\"'].*?/\s*>#", 
	create_function('$matches', "return '<img src=\"'.img_proxy(\$matches[1], '{$fragement['url']}', 620, 0).'\" onerror=\"\$(this).css(\'display\', \'none\')\"/>';"), $seelcted_fragement);

$_TPL['title'] = @$fragement['title']."_游记";

include template();