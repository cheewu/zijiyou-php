<?php
/**
 * Config File
 * 
 * @author panzhibiao
 */
if(!defined('IN_SYSTEM')) { exit('Access Denied'); }

$_SC['debug'] = 1;

// this system DB
$_SC['MongoDB'] = array(
	'server'	=> 'mongodb://202.85.213.54:27017',
	'dbname'	=> 'tripfm',
	'options'	=> array('username' => 'admin', 'password' => 'iamzijiyou'),
);

// memcached
$_SC['memcached'] = array(
    array('127.0.0.1', 11211),//host, port, weight
);

// flicker
$_SC['flicker'] = array(
	'api_key' 	 => '279ca42bf911e84cf5ca44403a4e7a83',
	'api_secert' => '6cd93d1b4ebf7e07',
	'api_url'	 => 'http://api.flickr.com/services/rest/',
	'format'	 => 'json',
	'auth_url'	 => 'http://flickr.com/services/auth/',
);

// solr config
$_SC['Solr'] = array(
	'host' => 'http://dev.zijiyou.com',
	'port' => '8080',
	'basic_path' => 'server',
	'region_path' => 'dst/search',
	'attraction_path' => 'att/search',
	'fullsearch_path' => 'dst/fullsearch',
	'default_param' => array(
		'pg' => 1,
		'ps' => 10,
	),
);

//页面编码
$_SC['page_charset'] = 'UTF-8';
//Gzip
$_SC['gzipcompress'] = 1;

//Cookie
$_SC['cookiepre'] = 'zjy_';
$_SC['cookiedomain'] = $_SERVER["HTTP_HOST"];
$_SC['cookiepath'] = '/';
$_SC['cookie_expire'] = 86400*7*2;//记住登录的过期时间

// img proxy
$_SC['img_proxy_url'] = "http://img.zijiyou.com/get.php";

// img zijiyou
$_SC['img_bed'] = "http://img.zijiyou.com/";

// img cache prefix
$_SC['img_cache_prefix'] = "pic_cache_";