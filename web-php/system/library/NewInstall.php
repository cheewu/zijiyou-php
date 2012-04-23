<?php
if(!defined('IN_SYSTEM')) { exit('Access Denied'); }
/**
 * @author HouRui
 * @since 2011-11-28
 * 
 */
class NewInstall {
	
	/**
	 * A_ROOT.config/config_router.php
	 * @var 路由默认配置文件
	 */
	private static $config_router = <<<EOF
<?php
/**
 * Config Router
 * 
 * @author  & HouRui
 */
if(!defined('IN_SYSTEM')) { exit('Access Denied'); }

/* router */
\$_SCONFIG['router'] = array(
	/**
	 * array('pattern', 'replace_pattern', 'flag')
	 *   @param flag: enom(redirect, permanent, continue, break) default: break
	 *   			  continue - 继续从头循环遍历, break - 跳出路由匹配
	 *   
	 *   @example
	 *   	target: /index/2134/ -> /index/list/?list_id=2134
	 *   	router: array("/^\/index\/(\d+)\/?/i", "/index/list/?list_id=\${1}", 'break'),
	 */
);
/* /router */
EOF;

	/**
	 * A_ROOT.config/config_preload.php
	 * @var 预加载默认配置文件
	 */
	private static $config_preload = <<<EOF
<?php
if(!defined('IN_SYSTEM')) { exit('Access Denied'); }
/**
 * 
 * @author HouRui
 * @since 2011-11-28
 * 
 */
\$_SCONFIG['preload'] = array(
	/**
	 * 'urlpath' => 'file' || 'url' => array('file1', 'file2', ...) 
	 * @param urlpath string 必须以 / 开头，既urlpath必须为url根目录,目录至多两级 
	 * 	      example: /urlpathA/urlpathB 或者 /urlpathA
	 * @param file string 为application目录下的文件位置
	 *        example: config/config_mail.php 
	 *                 include/mysqlDB.php
	 * 
	 * @example 1. /question/all 域下需要包含application/config/config_question.php
	 *             '/question/all' => 'config/config_question.php'
	 *          2. /question/all 域下需要包含application/config/config_question.php与application/config/config_answer.php两个文件
	 * 				'/question/all' => array(
	 * 										'config/config_question.php',
	 * 										'config/config_answer.php',)
	 * 					       
	 */ 
);
EOF;

	/**
	 * A_ROOT.config.php
	 * @var 根目录默认配置文件
	 */
	private static $config = <<<EOF
<?php
/**
 * Config File
 * 
 * @author 
 */
if(!defined('IN_SYSTEM')) { exit('Access Denied'); }

\$_SC['debug'] = 1;

//this system DB
\$_SC['db_lh_dsn'] = "";
\$_SC['db_lh_user'] = "";
\$_SC['db_lh_psw'] = "";
\$_SC['db_lh_dirver_opt'] = array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8");

//页面编码
\$_SC['page_charset'] = 'UTF-8';
//Gzip
\$_SC['gzipcompress'] = 1;

//Cookie
\$_SC['cookiepre'] = 'wdt_';
\$_SC['cookiedomain'] = \$_SERVER["HTTP_HOST"];
\$_SC['cookiepath'] = '/';
\$_SC['cookie_expire'] = 86400*7*2;//记住登录的过期时间
EOF;

	/**
	 * A_ROOT.config_run.php
	 * @var 根目录默认配置文件_run
	 */
	private static $config_run = <<<EOF
<?php
/**
 * 运行配置文件
 * 
 * @author 
 * @since 2011-01
 */
if(!defined('IN_SYSTEM')) {	exit('Access Denied'); }

//template
\$_SCONFIG['template'] = 'default';
\$_SCONFIG['site_name'] = '';
EOF;

	/**
	 * A_ROOT.common.php
	 * @var 通用application文件
	 */
	private static $common = <<<EOF
<?php
if(!defined('IN_SYSTEM')) { exit('Access Denied'); }
/**
 * common file
 * 
 * @author 
 * @since 2010-04
 */
ini_set('display_errors', \$_SC['debug'] ? 'on' : 'off');
ini_set('include_path', '.'.PATH_SEPARATOR.A_ROOT.PATH_SEPARATOR.A_ROOT.'include/');
//初始化访问时间
\$_SGLOBAL['REQUEST_TIME'] = \$_SERVER['REQUEST_TIME'] ? \$_SERVER['REQUEST_TIME'] : time();
\$_SGLOBAL['REQUEST_TIME_STR'] = date("Y-m-d H:i:s", \$_SGLOBAL['REQUEST_TIME']);

\$_TPL['msg_error'] = \$_TPL['msg_notice'] = array();

if( \$_SC['page_charset'] ) { header('Content-type: text/html; charset='.\$_SC['page_charset']); }

/* GPC过滤 */
\$magic_quote = get_magic_quotes_gpc();
//COOKIE
\$prelength = strlen(\$_SC['cookiepre']);
foreach(\$_COOKIE as \$key => \$val) {
	if(substr(\$key, 0, \$prelength) == \$_SC['cookiepre']) {
		\$_SCOOKIE[(substr(\$key, \$prelength))] = empty(\$magic_quote) ? saddslashes(\$val) : \$val;
	}
}
//init db
\$_SGLOBAL['db'] = "";

//CSS文件
//tpl_include_ex_css('reset.css');//子css文件
//tpl_include_ex_css('main.css');//子css文件
	
EOF;

	/**
	 * A_ROOT.'model/inde.php'
	 * @var 首页model
	 */
	private static $index = <<<EOF
<?php
if(!defined('IN_SYSTEM')) { exit('Access Denied'); }
echo "Hello Word <br />"; 
echo "<br />Install Success<br /> Please set ROOT/index.php define('INSTALL', 0)";
EOF;

	/**
	 * A_ROOT.'template/default/show_message.tpl.php'
	 * @var 错误页模板
	 */
	private static $show_message = <<<EOF
<?php if(!defined('IN_SYSTEM')) { exit('Access Denied'); } ?>
<!DOCTYPE div PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<title><?=\$message['type']?></title>
</head>
<body>
<!--php system error-->
<pre>
<?=ob_get_clean();?>
</pre>
<!-- message content -->
<div class="message msg_<?=\$message['type']?>">
    <span><?=\$message['content']?></span>
</div>
</body>
</html>
EOF;
	
	/**
	 * 安装
	 */
	public static function install() {
		//新建目录
		$application_dirs = array(
			A_ROOT, 
			A_ROOT.'config',
			A_ROOT.'controller',
			A_ROOT.'include',
			A_ROOT.'model',
			A_ROOT.'template/default',
		);
		self::install_mkdir($application_dirs);
		//创建文件
		!file_exists(A_ROOT.'config.php') && self::writeFile(A_ROOT.'config.php', self::$config);
		!file_exists(A_ROOT.'config_run.php') && self::writeFile(A_ROOT.'config_run.php', self::$config_run);
		!file_exists(A_ROOT.'common.php') && self::writeFile(A_ROOT.'common.php', self::$common);
		!file_exists(A_ROOT.'config'.DIRECTORY_SEPARATOR.'config_router.php') && 
			self::writeFile(A_ROOT.'config'.DIRECTORY_SEPARATOR.'config_router.php', self::$config_router);
		!file_exists(A_ROOT.'config'.DIRECTORY_SEPARATOR.'config_preload.php') && 
			self::writeFile(A_ROOT.'config'.DIRECTORY_SEPARATOR.'config_preload.php', self::$config_preload);
		//heloworld
		!file_exists(A_ROOT.'model'.DIRECTORY_SEPARATOR.'index.php') && 
			self::writeFile(A_ROOT.'model'.DIRECTORY_SEPARATOR.'index.php', self::$index);
		!file_exists(A_ROOT.'template'.DIRECTORY_SEPARATOR.'default'.DIRECTORY_SEPARATOR.'show_message.tpl.php') && 
			self::writeFile(A_ROOT.'template'.DIRECTORY_SEPARATOR.'default'.DIRECTORY_SEPARATOR.'show_message.tpl.php', self::$show_message);
		
	}
	
	/**
	 * 写入文件
	 * @param 文件名 $filename
	 * @param 内容 $content
	 */
	private static function writeFile($filename, $content) {
		$fp = fopen($filename, 'w+');
		fwrite($fp, $content);
		fclose($fp);
		unset($fp);
	}
	
	/**
	 * @param mix $dirname 文件夹名称 
	 */
	private static function install_mkdir($dirnames) {
		!is_array($dirnames) && $dirnames = array($dirnames);
		foreach($dirnames AS $dirname) { 
			!file_exists($dirname) && mkdir($dirname, 0755, true);
		}
	}

}