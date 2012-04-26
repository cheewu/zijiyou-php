<?php
if(!defined('IN_SYSTEM')) { exit('Access Denied'); }
/**
 * common file
 * 
 * @author 
 * @since 2010-04
 */
ini_set('display_errors', $_SC['debug'] ? 'on' : 'off');
ini_set('include_path', '.'.PATH_SEPARATOR.A_ROOT.PATH_SEPARATOR.A_ROOT.'include/');
//初始化访问时间
$_SGLOBAL['REQUEST_TIME'] = $_SERVER['REQUEST_TIME'] ? $_SERVER['REQUEST_TIME'] : time();
$_SGLOBAL['REQUEST_TIME_STR'] = date("Y-m-d H:i:s", $_SGLOBAL['REQUEST_TIME']);

$_TPL['msg_error'] = $_TPL['msg_notice'] = array();

if( $_SC['page_charset'] ) { header('Content-type: text/html; charset='.$_SC['page_charset']); }

/* GPC过滤 */
$magic_quote = get_magic_quotes_gpc();
//COOKIE
$prelength = strlen($_SC['cookiepre']);
foreach($_COOKIE as $key => $val) {
	if(substr($key, 0, $prelength) == $_SC['cookiepre']) {
		$_SCOOKIE[(substr($key, $prelength))] = empty($magic_quote) ? saddslashes($val) : $val;
	}
}

// init db
$_SGLOBAL['db'] = new MongoHandle($_SC['MongoDB']['server'], $_SC['MongoDB']['dbname'], $_SC['MongoDB']['options']);
// page db
$_SGLOBAL['pagedb'] = new MongoHandle($_SC['MongoDB']['server'], 'page', $_SC['MongoDB']['options']);
// image resize
$_SGLOBAL['imager'] = new Image($_SC['img_dir']);
// init flicker
$_SGLOBAL['flicker'] = new Flicker();

//CSS文件
//tpl_include_ex_css('reset.css');//子css文件
//tpl_include_ex_css('main.css');//子css文件
	