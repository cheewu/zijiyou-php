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
	array("#/?(region|poi)/(\w{24}).png#", '/get/pic/$1/$2', 'break'),
	array("#/?article/(\w{24})(\d+)_(\d+)x(\d+).png#", '/cache/article/$1/$2/$3/$4', 'break'),
);
/* /router */