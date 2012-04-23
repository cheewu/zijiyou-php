<?php
/**
 * Config File
 * 
 * @author 
 */
if(!defined('IN_SYSTEM')) { exit('Access Denied'); }

$_SC['debug'] = 1;

//this system DB
$_SC['db_lh_dsn'] = "";
$_SC['db_lh_user'] = "";
$_SC['db_lh_psw'] = "";
$_SC['db_lh_dirver_opt'] = array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8");

//页面编码
$_SC['page_charset'] = 'UTF-8';
//Gzip
$_SC['gzipcompress'] = 1;

//Cookie
$_SC['cookiepre'] = 'wdt_';
$_SC['cookiedomain'] = $_SERVER["HTTP_HOST"];
$_SC['cookiepath'] = '/';
$_SC['cookie_expire'] = 86400*7*2;//记住登录的过期时间