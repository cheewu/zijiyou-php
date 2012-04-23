<?php
/**
 * system common.php file
 * 
 * path: S_ROOT/system/common.php
 * 
 * @author  & HouRui
 * @since 2011-11
 */
ob_start();
//避免被绝对路径调用
define('IN_SYSTEM', 1); 
//is_win
define('IS_WIN', DIRECTORY_SEPARATOR=='\\');
//set socket timeout
ini_set('default_socket_timeout', 10);
//set time zone
date_default_timezone_set('Asia/Shanghai');
//全局变量初始化
$_SCONFIG = $_SC = $_SGLOBAL = $_TPL = $_SCOOKIE = array();
//包含核心框架函数
require S_ROOT.'core'.DIRECTORY_SEPARATOR.'function_frame.php';
//init frame
fr_init();
