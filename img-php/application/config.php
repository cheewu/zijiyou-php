<?php
/**
 * Config File
 * 
 * @author 
 */
if(!defined('IN_SYSTEM')) { exit('Access Denied'); }

$_SC['debug'] = 1;

// this system DB
$_SC['MongoDB'] = array(
	'server'	=> 'mongodb://202.85.213.54:27017',
	'dbname'	=> 'tripfm',
	'options'	=> array('username' => 'admin', 'password' => 'iamzijiyou'),
);

// flicker
$_SC['flicker'] = array(
	'api_key' 	 => '279ca42bf911e84cf5ca44403a4e7a83',
	'api_secert' => '6cd93d1b4ebf7e07',
	'api_url'	 => 'http://api.flickr.com/services/rest/',
	'format'	 => 'json',
	'auth_url'	 => 'http://flickr.com/services/auth/',
);

$_SC['img_dir'] = ROOT.'cache/';

//页面编码
$_SC['page_charset'] = 'UTF-8';
//Gzip
$_SC['gzipcompress'] = 1;

// memcached
$_SC['memcached'] = array(
    array('127.0.0.1', 11211),//host, port, weight
);

// img cache prefix
$_SC['img_cache_prefix'] = "pic_cache_";

//Cookie
$_SC['cookiepre'] = 'wdt_';
$_SC['cookiedomain'] = $_SERVER["HTTP_HOST"];
$_SC['cookiepath'] = '/';
$_SC['cookie_expire'] = 86400*7*2;//记住登录的过期时间