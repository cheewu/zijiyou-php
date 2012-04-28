<?php
/**
 * Config Router
 * 
 * @author panzhibiao & HouRui
 */
if(!defined('IN_SYSTEM')) { exit('Access Denied'); }

/* router */
$_SCONFIG['router'] = array(
	/**
	 * array('pattern', 'replace_pattern', 'flag')
	 *   @param flag: enom(redirect, permanent, continue, break) default: break
	 *   			  continue - 继续从头循环遍历, break - 跳出路由匹配
	 *   
	 *   @example
	 *   	target: /index/2134/ -> /index/list/?list_id=2134
	 *   	router: array("/^\/index\/(\d+)\/?/i", "/index/list/?list_id=${1}", 'break'),
	 */
	array('#/?region/(\w{24})/?#', '/region/?region_id=${1}', 'break'),
	array('#/?poi/(\w{24})/?#', '/poi/?poi_id=${1}', 'break'),
	array('#/?attraction/(\w{24})/?#', '/attraction/?region_id=${1}', 'break'),
	array('#/?article/(\w{24})/?#', '/article/?region_id=${1}', 'break'),
	array('#/?map/(\w{24})/?#', '/map/?region_id=${1}', 'break'),
	
	array('#/?detail/(\w{24})/(\w{24})/?#', '/article/detail/?region_id=${1}&article_id=${2}', 'break'),
);
/* /router */