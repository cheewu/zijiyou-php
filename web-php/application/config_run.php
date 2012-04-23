<?php
/**
 * 运行配置文件
 * 
 * @author panzhibiao
 * @since 2011-01
 */
if(!defined('IN_SYSTEM')) {	exit('Access Denied'); }

//template
$_SCONFIG['template'] = 'april';
$_SCONFIG['site_name'] = '';

$_SCONFIG['map_category'] = array(
	'pano' => '图片',
	'attraction' => '景点',
	'shoppingcenter' => '购物中心',
	'airport' => '飞机场',
	'subway' => '地铁站',
	'train' => '火车站',
);

$_SCONFIG['poi_category'] = array(
	'airport' => '飞机场',
	'subway' => '地铁站',
	'train' => '火车站',
);