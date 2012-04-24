<?php
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

// cache dir
$_SC['img_dir'] = ROOT.'../cache/';

