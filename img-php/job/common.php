<?php
define('ROOT', dirname(__FILE__).DIRECTORY_SEPARATOR);
ini_set('include_path', '.'.PATH_SEPARATOR.ROOT.PATH_SEPARATOR.ROOT.'library/');
require_once 'config.php';
require_once 'library/Flicker.php';
require_once 'library/MongoDB.php';
require_once 'library/function.php';


// init db
$_SGLOBAL['db'] = new MongoHandle($_SC['MongoDB']['server'], $_SC['MongoDB']['dbname'], $_SC['MongoDB']['options']);
// init flicker
$_SGLOBAL['flicker'] = new Flicker();
