<?php
/**
 * Config Router
 * 
 * @author  & HouRui
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
	array("#/?article/([0-9a-f]{1,8})_(\d+)x(\d+).png#", '/article/get/$1/$2/$3', 'break'),
);
/* /router */