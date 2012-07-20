<?php
/**
 * Common Functions list
 * 
 * @author HouRui
 * @since 2010-04
 */
if(!defined('IN_SYSTEM')) {	exit('Access Denied'); }



/**
 * 初始化Framework
 */
function fr_init()
{
	global $_SGLOBAL, $_SCONFIG;//所有全局变量
	
	// 预包含系统文件
	_fr_pre_include_system();
	
	// 新装检测
	_fr_application_new_install_check(); 
	
	//预包含application文件
	_fr_pre_include_application();
	
	if(empty($_GET['_path']) && strpos($_SERVER['PHP_SELF'], "=")){
		$_GET['_path'] = substr($_SERVER['PHP_SELF'], strpos($_SERVER['PHP_SELF'], "=") + 1);
	} else {
		$_GET['_path'] = "";
	}

	/* URL路由 */
	$request_url = _fr_init_router();
	$request_path = _fr_init_router_query($request_url);
	
	/* 获取请求路径 */
	$_SGLOBAL['_path'] = array_filter(
		explode('/', $request_path), // 数组第一个元素是空的，因为第一个字符是'/'
		create_function('$str', 'return trim($str) !== "";')
	);
	if( empty($_SGLOBAL['_path']) ) { 
		$_SGLOBAL['_path'] = array('index'); 
	} else {
		$_SGLOBAL['_path'] = array_values($_SGLOBAL['_path']);//将数组index从0开始
	}
	//设置别名, controller & action
	if( count($_SGLOBAL['_path']) == 1 && $_SGLOBAL['_path'][0] == 'index') { 
		$_SGLOBAL['ctrl'] = 'index';
		$_SGLOBAL['action'] = ''; 
	} else {
		$_SGLOBAL['ctrl'] = strtolower($_SGLOBAL['_path'][0]);
		if(empty($_SGLOBAL['_path'][1])) {
			$_SGLOBAL['action'] = 'index';
			$_SGLOBAL['_path'][] = 'index';
		} else {
			$_SGLOBAL['action'] = strtolower($_SGLOBAL['_path'][1]);
		}
	}
	if(count($_SGLOBAL['_path']) > 2) {
		$_SGLOBAL['param'] = $_SGLOBAL['_path'];
		array_shift($_SGLOBAL['param']);
		array_shift($_SGLOBAL['param']);
	}else{
		$_SGLOBAL['param'] = array();
	}
	//pr($_SGLOBAL);
	//$_SGLOBAL['ctrl'] = empty($_SGLOBAL['_path'][1]) ? '' : strtolower($_SGLOBAL['_path'][1]);
	//$_SGLOBAL['action'] = empty($_SGLOBAL['_path'][2]) ? '' : strtolower($_SGLOBAL['_path'][2]);
	
	_fr_controller_action();
	
	/* CSS文件处理 */
	tpl_include_ex_css($_SGLOBAL['ctrl'].'.css');exit;
}



/**
 * 初始化路由
 */
function _fr_init_router()
{
	global $_SCONFIG;
	
	//router config is empty
	if( empty($_SCONFIG['router']) ) { return empty($_GET['_path'])?'':$_GET['_path']; }
	
	//request url
	$path = @trim($_GET['_path']);
	if( empty($path) ) { return ''; }
	unset($_GET['_path']);//unset $_GET['_path']
	
	$i = 0;//计数器
	while(1) {
		$i++;
		//超过最大重写次数
		if( $i >= 10 ) { echo "router rewrite times more than max";exit; }
		
		$is_continue = false;
		foreach ($_SCONFIG['router'] as $val)
		{
			list($pattern, $replacement, $type) = $val;
			
			//not match
			if( preg_match($pattern, $path) == 0 ) { continue; }
			
			//match, do replace
			$path = preg_replace($pattern, $replacement, $path, 1);
			
			//rewrite type
			if( empty($type) || $type == 'break' ) { return $path; }
			elseif( $type === 'continue' ) { $is_continue = true; break; }
			elseif( $type == 'redirect'/* 302 */ ) { shttp_redirect($path, 302); }
			elseif( $type == 'permanent'/* 301 */ ) { shttp_redirect($path, 301); }
			else { echo 'undefined rewrite type.';exit; }
			
		}// /foreach
		
		//continue loop
		if( $is_continue ) { continue; }
		
		//break while loop
		break;
		
	}// /while
	
	return $path;
}




/**
 * 处理rewrite后新产生的$_GET参数，合并入$_GET，返回请求路径
 * @param string $path
 */
function _fr_init_router_query($url)
{
	//parse_url
	$url_arr = parse_url($url);
	do{
		if( empty($url_arr['query']) ) { break; }
		
		//explode query string to array
		$query_arr = explode('&', $url_arr['query']);
		if( empty($query_arr) ) { break; }
		
		foreach ($query_arr as $val) {
			list($q_key, $q_val) = explode('=', $val, 2);
			//set $_GET
			$_GET[$q_key] = !empty($q_val) ? $q_val : '';
		}
	}while (0);
	
	return $url_arr['path'];
}

/**
 * 处理controller action param 包含对应文件
 */
function _fr_controller_action(){
	global $_SCONFIG, $_SGLOBAL;
	/* 包含控制器预处理文件 
	$controller_pre_file = S_ROOT.'controller_pre/'.$_SGLOBAL['ctrl'].'.php';
	if( file_exists($controller_pre_file) ) {
		require $controller_pre_file;
	}*/
	
	//包含预包含文件
	//A_ROOT.config/config_preload.php可配置
	foreach($_SCONFIG['preload'] AS $path => $preload_files) {
		!is_array($preload_files) && $preload_files = array($preload_files);
		foreach($preload_files AS $preload_file) {
			$preload_file = strtolower($preload_file);
			$path = strtolower($path);
			//$_SGLOBAL['action'] 可能为空 采用trim($xxx, '/')
			if( !empty($_SGLOBAL['ctrl']) 
				&& ( '/' . trim($_SGLOBAL['ctrl'].'/'.$_SGLOBAL['action'], '/') == $path ||
					 '/' . $_SGLOBAL['ctrl'] == $path )
			  ) {
				fr_pre_include_file(A_ROOT.$preload_file);
			}
		}
	}
	
	do{
		//获取一级子目录
		$sub_path = $_SGLOBAL['_path'];
		array_shift($sub_path);
		if($_SGLOBAL['ctrl'] == 'index' && empty($_SGLOBAL['action'])) {
			$model_file = A_ROOT . 'model' . DIRECTORY_SEPARATOR . 'index.php';
		} else {
			$model_file = A_ROOT . 'model' . DIRECTORY_SEPARATOR . $_SGLOBAL['ctrl'] . DIRECTORY_SEPARATOR . implode('_', $sub_path) . '.php';
		}
		if(file_exists($model_file)){
			fr_pre_include_file($model_file); break;
		}
		if(class_exists($_SGLOBAL['ctrl'])){
			$controller = $_SGLOBAL['ctrl'];
			$action = $_SGLOBAL['action'];
			$param = $_SGLOBAL['param'];
			//$_SGLOBAL['_path'] 第2部分之后的为参数
			$_SGLOBAL['controller'] = new $controller();
			if(in_array($action, get_class_methods($controller))){
				call_user_func_array(array($_SGLOBAL['controller'], $action), array_map('rawurldecode', $param));break;
			}
		}
		set_http_response_code(404);
		show_message("Model File:{$model_file} OR Controller File:{$_SGLOBAL['ctrl']} Action: {$_SGLOBAL['action']} is not exist!");
		exit("<pre>file: '".relative_path($model_file)."' is NOT exist.\n OR \n Controller: {$_SGLOBAL['ctrl']} OR Action: {$_SGLOBAL['action']} is not exists</pre>");
	}while(0);
}


/**
 * 预包含系统文件
 */
function _fr_pre_include_system(){
	fr_pre_include_folder(array('core', 'library'), S_ROOT);
}

/**
 * 预包含程序文件
 */
function _fr_pre_include_application(){
	fr_pre_include_folder(array('controller', 'library'), A_ROOT);
	fr_pre_include_file(A_ROOT.'include'.DIRECTORY_SEPARATOR.'function_common.php');
	fr_pre_include_file(A_ROOT.'config'.DIRECTORY_SEPARATOR.'config_router.php');
	fr_pre_include_file(A_ROOT.'config'.DIRECTORY_SEPARATOR.'config_preload.php');
	fr_pre_include_file(A_ROOT.'config.php');
	fr_pre_include_file(A_ROOT.'config_run.php');
	fr_pre_include_file(A_ROOT.'common.php');
}

/**
 * require folder
 * @param mix $folder
 * @param string $path  
 */
function fr_pre_include_folder($folder, $path){
	!is_array($folder) && $folder = array($folder);
	foreach($folder AS $pre_folder){
		$path_ = $path.$pre_folder.DIRECTORY_SEPARATOR;
		$file_name_arr =  scandir($path_);
		foreach($file_name_arr AS $file_name){
			$pre_file = $path_.$file_name;
			fr_pre_include_file($pre_file);
		}
	}
}

/**
 * require folder
 * @param mix $folder
 * @param string $path  
 */
function fr_pre_include_file($pre_file){
	global $_SCONFIG, $_SGLOBAL, $_SC, $_TPL, $_SCOOKIE;//所有全局变量
	is_file($pre_file) && require_once $pre_file;
}

/**
 * application 层包含文件
 * @param string $application_folder application 文件夹
 * @param string $filename 文件名
 */
function fr_load_file($application_folder, $filename) {
	$application_folder = trim($application_folder, DIRECTORY_SEPARATOR);
	$path = A_ROOT.$application_folder.DIRECTORY_SEPARATOR.$filename;
	fr_pre_include_file($path);
}

/**
 * 新装检测
 */
function _fr_application_new_install_check() {
	global $_SGLOBAL;
	if (defined("INSTALL") && INSTALL ) {
		!is_writable(ROOT) && exit('please chown path: '.ROOT.' AS writable');
		NewInstall::install(); 
		!empty($_GET['_path']) && trim($_GET['_path']) != 'index'  && shttp_redirect("/"); 
	}else{
		!is_dir(A_ROOT) && exit('please install first: change ROOT/index.php define("INSTALL", 1);');
	}
}

/**
 * ADDSLASHES
 * 
 * @param string $string
 * 
 * @return string $string
 */
function saddslashes($string) {
	if(is_array($string)) {
		foreach($string as $key => $val) {
			$string[addslashes($key)] = saddslashes($val);
		}
	} else {
		$string = addslashes($string);
	}
	return $string;
}


/**
 * check email
 * @param string $email
 * @return bool
 */
function isemail($email) {
	return strlen($email) > 6 && preg_match("/^[\w\-\.]+@[\w\-\.]+(\.\w+)+$/", $email);
}


/**
 * iconv -R
 * 
 * @param string $in_charset
 * @param string $out_charset
 * @param string $var
 * @return mixed
 */
function siconv($in_charset, $out_charset, $var) {
    if(is_array($var)) {
        foreach($var as $key => $val) {
            $var[siconv($in_charset, $out_charset, $key)] = siconv($in_charset, $out_charset, $val);
        }
    } else {
        $var = iconv($in_charset, $out_charset, $var);
    }
    return $var;
}


/**
 * 发起一个HTTP请求
 * 
 * @param string $url
 * @param mixed $post_data 可以关联数组，也可以直接是经过URL编码后字符串
 * @param array $headers http请求头信息, 格式为KEY=>VALUE形式
 * @param int $timeout 超时，0为不限制
 * @param bool $follow_loc 是否跟踪Location跳转
 * @param bool $output_header 是否输出HTTP头信息
 * @param bool $halt 遇到错误是否exit
 * 
 * @example 
 * 
 * @return string
 */
//function shttp_request($url, $post_data=array(), $headers=array(), $timeout=3, $follow_loc=0, $output_header=0, $halt=1)
function shttp_request($url, $options = array())
{
    global $_SGLOBAL;
    
    //记录debuginfo
	$debug_time_start = microtime(1);
		
	//默认配置
	$default_options = array(
			'post_data' => array(), //可以关联数组，也可以直接是经过URL编码后字符串
			'headers' => array(), //http请求头信息, 格式为KEY=>VALUE形式
			'timeout' => 3, //sec, 超时，0为不限制
			'follow_loc' => 0, //是否跟踪Location跳转
			'output_header' => 0, //是否输出HTTP头信息
			'userpwd' => array(), //用户名和密码，需要验证时使用。格式：array('username', 'password')
			'maxredirs' => 5, // 最大跳转次数
			'halt' => 1, //遇到错误是否exit
		);
	$options = array_merge($default_options, $options);
	
    $ch = curl_init();
    
    //url
    curl_setopt($ch, CURLOPT_URL, $url);
	
    //instead of outputting it out directly
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    //automatically set the Referer
    curl_setopt($ch, CURLOPT_AUTOREFERER, true);
    //TRUE to follow any "Location: " header that the server sends
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, $options['follow_loc'] ? true : false);    
    //maximum amount of HTTP redirections to follow
    curl_setopt($ch, CURLOPT_MAXREDIRS, $options['maxredirs']);
    //The number of seconds to wait whilst trying to connect. Use 0 to wait indefinitely
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $options['timeout']);
    
    if( !empty($options['headers']) ) {
    	$header_user_agent = 0;//is set user agent
    	foreach ($options['headers'] as $hkey=>$hval) {
    		if(strtolower(trim($hkey)) == 'user-agent') { $header_user_agent = 1; }
    		$nheaders[] = trim($hkey).": ".trim($hval);
    	}
    	curl_setopt($ch, CURLOPT_HTTPHEADER, $nheaders);
    }
    
    //Set Default User-Agent
    if( empty($header_user_agent) ) {
    	//IE7 on Windows Xp
    	curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/4.0 (compatible; MSIE 7.0; Windows NT 5.1)');
    }
	
    //TRUE to include the header in the output
    curl_setopt($ch, CURLOPT_HEADER, $options['output_header'] ? true : false);
    
    //HTTPS
    if( stripos($url, "https://") !== FALSE ) {
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    }
    
    //Set Username & Password
    if( !empty($options['userpwd']) ) {
    	curl_setopt($ch, CURLOPT_USERPWD, "[{$options['username']}]:[{$options['password']}]");
    }
    
    //post data
    if( !empty($options['post_data']) ) {
    	curl_setopt($ch, CURLOPT_POST, true);
    	if( is_array($options['post_data']) )
    	{
        	$encoded = "";
            foreach ( $options['post_data'] as $k=>$v)
            {   
                $encoded .= "&".rawurlencode($k)."=".rawurlencode($v);
            }
            $encoded = substr($encoded, 1);//去掉首个'&'
            curl_setopt($ch, CURLOPT_POSTFIELDS, $encoded);
    	}else{
    	    curl_setopt($ch, CURLOPT_POSTFIELDS, $options['post_data']);
    	}
    }
    
    $res = curl_exec($ch);
    
    if( $res === FALSE ) {
        header("HTTP/1.0 500 Internal Server Error" , true , 500);
    	echo "[function shttp_request]REQUEST URL: {$url}，FAILURE! Error: ".curl_error($ch)."\n";
    	if($options['halt']) {
    		curl_close($ch);
    		exit();
    	}else{
    		return FALSE;
    	}
    }
    curl_close($ch);
    
    
    //debug_info
	$_SGLOBAL['debug_info'][] = array('request_url'=>$url, 'time_cost'=>(microtime(1)-$debug_time_start));
		
    
    return $res; 
}


/**
 * htmlspecialchars
 * 
 * @param mixed $var
 * 
 * @return mixed
 */
function h($var)
{
    if( is_array($var) )
    {
        foreach ($var as $key=>$value)
        {
            $var[$key] = h($value);
        }
    } else {
        $var = htmlspecialchars($var);
    }
    return $var;
}


/**
 * htmlspecialchars & trim
 * 
 * @param string $str
 * 
 * @return string
 */
function htrim($str)
{
	return htmlspecialchars(trim($str));
}


/**
 * var_dump var
 * 
 * @param mixed $var 需要打印的变量
 * @param bool $halt 是否在此中断
 *  
 */
function pr()
{
    $var = func_get_args();
    
    $halt = func_get_arg(func_num_args() - 1) == true;
    
	static $is_print_css=null;
	
	$backtrace = debug_backtrace();
	
	if( $is_print_css === null ) {
		echo<<<EOF
<style>
body { color:#fff;background-color:#3c3c3c; }
a { color:#94aefb; }
.func { font-weight:bold;color:#1ad77c; }
.trace_header { background-color:#515252;padding:5px;font-size:12px; }
.var { color:#f9dd1d;margin:3 0 30 20px;border-left:2px solid #88a3f2;background-color:#515252;padding:5px;font-weight:500;font-size:14px; }
.trace { border-left:3px solid #39c4dd;padding-left:2px; }
</style>
EOF;
	        $is_print_css = true;
	}
	
	//函数堆栈
	echo "<div class='trace_header'>";
	$i=0;
	foreach ( $backtrace as $key=>$val ) {	   
	   echo "<div class='trace' style='margin-left:".($i*50)."px;'>";
	   $path_info = pathinfo($val['file']);
	   echo "<span class='func'>{$val['function']}()</span>, <a href='#' onclick='return false;' title=\"".h($val['file'])."\"><b>".($path_info['basename'])."</b></a>: <b>{$val['line']}</b>";
	   echo "</div>";
	   $i++;
	}	
	echo "</div>";
	
	//变量信息
	echo "<pre class='var'><code>";
    call_user_func('var_dump', $var);
    echo '</pre></div>';
    
    //echo "</div>";
    
    if( $halt ) exit();
}



/**
 * 
 * 解析simple_xml对象为关联数组
 * @param $xml xml_string
 * @param $array_tags 根据节点筛选
 * @return array() 关联数组
 * 
 */
function simplexml_to_array($simple_xml_obj) {
	// has sibling keys
	$keys_has_sibling = array();
	// sub func
	_simplexml_to_array_has_sibling_keys($simple_xml_obj, $keys_has_sibling);
	// sub func
	return _simplexml_to_array_recrusive($simple_xml_obj, $keys_has_sibling);
}

/**
 * find the key which has sublings
 * @param simple xml obj $simple_xml_obj
 * @param array &$keys_has_sibling
 */
function _simplexml_to_array_has_sibling_keys($simple_xml_obj, &$keys_has_sibling) {
	$tmp_keys = array();
	foreach($simple_xml_obj AS $xml_node_key => $xml_node_val){
		$xml_node_att = current($xml_node_val->attributes()) ?: array();
		// replace node key with attributes.name
		$array_node_key = !empty($xml_node_att['name']) ? $xml_node_att['name'] : $xml_node_key;
		in_array($array_node_key, $tmp_keys) ? $keys_has_sibling[] = $array_node_key : $tmp_keys[] = $array_node_key;
		_simplexml_to_array_has_sibling_keys($xml_node_val, $keys_has_sibling);
	}
}

/**
 * recrusive parse
 * @param simple xml obj $simple_xml_obj
 * @param array $keys_has_sibling
 */
function _simplexml_to_array_recrusive($simple_xml_obj, $keys_has_sibling) {
	// res init
	$res_array = array();
	if($simple_xml_obj->count() < 1) {
		return trim((string)$simple_xml_obj);
	}
	// recrusive
	foreach($simple_xml_obj AS $xml_node_key => $xml_node_val){
		$xml_node_att = current($xml_node_val->attributes()) ?: array();
		// 如果attributes name为空则用节点名称
		$array_node_key = !empty($xml_node_att['name']) ? $xml_node_att['name'] : $xml_node_key;
		// release name
		unset($xml_node_att['name']);
		// child node
		$child_array_node = _simplexml_to_array_recrusive($xml_node_val, $keys_has_sibling);
		// not the tree node
		is_array($child_array_node) && $child_array_node = array_merge($xml_node_att, $child_array_node);
		// recursive
		if(in_array($array_node_key, $keys_has_sibling)) {
			$res_array[$array_node_key][] = $child_array_node;
		} else {
			$res_array[$array_node_key] = $child_array_node;
		}
	}
	return $res_array;
}


/**
 * 获取XML文档,返回simplexml object
 * 
 * @param string $xml_str
 * @param string $encode 字符编码
 * @param boolean $halt 错误是否停止程序
 * 
 * @return SimpleXMLElement
 */
function simplexml_load_string_to_xmlobj($xml_str, $encode='GBK', $halt=true)
{
	$xml_str = preg_replace("/(^<\?xml.*?encoding.*?)GB2312(.*?\?>)/i" ,'${1}GBK${2}' , trim($xml_str));//如何头部声明是GB2312则替换为GBK
	
	//过滤掉错误字符,防止xml解析错误
	$xml_str = iconv($encode, $encode.'//IGNORE', $xml_str);
	
	//过滤W3C XML规范外的字符
	$xml_str = str_replace(
		array("\x00", "\x01", "\x02", "\x03", "\x04", "\x05", "\x06", "\x07", "\x08",
			  "\x0b", "\x0c",
			  "\x0e", "\x0f", "\x10", "\x11", "\x12", "\x13", "\x14", "\x15", "\x16", "\x17", "\x18", "\x19", "\x1a", "\x1b", "\x1c", "\x1d", "\x1e", "\x1f"
	), "", $xml_str);
	//pr($xml_str);
	if( ( $xmlobj = simplexml_load_string($xml_str, 'SimpleXMLElement', LIBXML_NOCDATA) ) === FALSE )
	{
	    if($halt)
	    {
		    $e = libxml_get_last_error();
		    echo 'xml load error: '.$e->message;
	    	exit;
	    }
	    else
	    {
	    	return false;
	    }
	}
	//pr($xmlobj, 0);
	return $xmlobj;
}



/**
 * length，英文字符长度，汉字算两个
 * 
 * @param $string
 * @param $length
 * @param $is_htmlspecialchars
 * @param $end_with
 * 
 * @return string
 */
function gbk_substr_ifneed($string, $length, $is_htmlspecialchars=0, $end_with='<span class="dot">...</span>')
{
    if( strlen($string) <= $length )
        return $string;
    
    $re_gbk = "/[\x01-\x7f]|[\x81-\xfe][\x40-\xfe]/";    
    preg_match_all($re_gbk, $string, $match);
    
    $new_str = "";
    $now_length = 0;
    $max_length = $length - strlen(strip_tags($end_with));
    //$max_length = $length - 3;
    foreach($match[0] as $char)
    {
        $now_length += strlen($char);//英文字符长度，汉字算两个
        if( $now_length>$max_length ) break;
        
        $new_str .= $char;
    }
    
    if( $is_htmlspecialchars ) {
    	$new_str = htmlspecialchars($new_str);
    }
    
    return $new_str.$end_with;
}

/**
 * utf-8 substr
 * @param string $string
 * @param int $length
 * @param bool $is_htmlspecialchars
 * @param string $end_with
 */
function utf8_substr_ifneeed($string, $length, $is_htmlspecialchars=0, $end_with='<span class="dot">...</span>') {
	$string = @iconv("utf-8", "gbk//ignore", $string);
	$substring = gbk_substr_ifneed($string, $length, $is_htmlspecialchars, $end_with);
	return iconv("gbk", "utf-8//ignore", $substring);
}


/**
 * 获取模板路径
 * @param string $tpl_name
 * @return string
 */
function template($tpl_name='')
{
    global $_SCONFIG, $_SGLOBAL;
    //default template file name
    if(!empty($_SGLOBAL['controller']) && is_a($_SGLOBAL['controller'], $_SGLOBAL['ctrl'])) {
    	$default_tpl_name = trim($_SGLOBAL['ctrl'].'_'.$_SGLOBAL['action'], '_');
    } else {
    	$default_tpl_name = implode('_', $_SGLOBAL['_path']);
    }
	empty($tpl_name) && $tpl_name = $default_tpl_name;
	
	//路径，附带主题
	$path = 'template/'.($_SCONFIG['template'] ? $_SCONFIG['template'].'/' : '');
	
	//模板文件名
	$path .= $tpl_name . ( substr($tpl_name, -8)=='.tpl.php' ? '' : '.tpl.php' );
	if( file_exists(A_ROOT.$path) ) return A_ROOT.$path;
	
	if( $tpl_name != 'show_message' ) {
		//else the template file is NOT exist
		show_message("Template File <b>$path</b> is not exist!");
	}else{
		echo "Template File <b>$path</b> is not exist!";exit;
	}
}



/**
 * 调整输出
 */
function ob_out()
{
	global $_SC;
	
	$content = ob_get_contents();
	obclean();
	
	if( $_SC['page_charset'] ) {header('Content-type: text/html; charset='.$_SC['page_charset']);}
	echo $content;
	exit();
}


/**
 * obclean
 */
function obclean()
{
	global $_SC;

	ob_end_clean();
    if( stripos($_SERVER["HTTP_ACCEPT_ENCODING"], 'gzip')!==FALSE
        && function_exists('ob_gzhandler')
        && $_SC['gzipcompress']
        )
    {
        //support gzip
        ob_start('ob_gzhandler');
    }else {
        ob_start();
    }
}


/**
 * 重定向
 * @param string $url
 * @param bool $halt
 */
function shttp_redirect($url, $code=302, $halt=1)
{
	ob_clean();
	header("Location: ".$url, null, $code);
	if($halt) exit();
}


/**
 * Encrypt Function (编码)
 * @param $encrypt
 * @param $key
 */
function encrypt($encrypt,$key="")
{    
    $iv = mcrypt_create_iv ( mcrypt_get_iv_size ( MCRYPT_RIJNDAEL_256, MCRYPT_MODE_ECB ), MCRYPT_RAND );
    $passcrypt = mcrypt_encrypt ( MCRYPT_RIJNDAEL_256, $key, $encrypt, MCRYPT_MODE_ECB, $iv );
    $encode = base64_encode ( $passcrypt );
    return $encode;
}


/**
 * Decrypt Function (解码)
 * @param unknown_type $decrypt
 * @param unknown_type $key
 */
function decrypt($decrypt,$key="")
{    
    $decoded = base64_decode ( $decrypt );
    $iv = mcrypt_create_iv ( mcrypt_get_iv_size ( MCRYPT_RIJNDAEL_256, MCRYPT_MODE_ECB ), MCRYPT_RAND );
    $decrypted = mcrypt_decrypt ( MCRYPT_RIJNDAEL_256, $key, $decoded, MCRYPT_MODE_ECB, $iv );
    return $decrypted;
}



/**
 * 伪装变量
 * @param  $var
 * @return string
 */
function encode_var($var)
{
	return encrypt($var, DES_KEY);
}


/**
 * 解密被伪装变量
 * @param $encode_var
 * @return string
 */
function decode_var($encode_var)
{
	return decrypt($encode_var, DES_KEY);
}




/**
 * 格式化大小函数
 * @param int $size
 */
function formatsize($size) {
	$prec=3;
	$size = round(abs($size));
	$units = array(0=>" B ", 1=>" KB", 2=>" MB", 3=>" GB", 4=>" TB");
	if ($size==0) return str_repeat(" ", $prec)."0$units[0]";
	$unit = min(4, floor(log($size)/log(2)/10));
	$size = $size * pow(2, -10*$unit);
	$digi = $prec - 1 - floor(log($size)/log(10));
	$size = round($size * pow(10, $digi)) * pow(10, -$digi);
	return $size.$units[$unit];
}



/**
 * 获取客户端IP
 */
function get_onlineip() {
	$onlineip = '';
	if(getenv('HTTP_CLIENT_IP') && strcasecmp(getenv('HTTP_CLIENT_IP'), 'unknown')) {
		$onlineip = getenv('HTTP_CLIENT_IP');
	} elseif(getenv('HTTP_X_FORWARDED_FOR') && strcasecmp(getenv('HTTP_X_FORWARDED_FOR'), 'unknown')) {
		$onlineip = getenv('HTTP_X_FORWARDED_FOR');
	} elseif(getenv('REMOTE_ADDR') && strcasecmp(getenv('REMOTE_ADDR'), 'unknown')) {
		$onlineip = getenv('REMOTE_ADDR');
	} elseif(isset($_SERVER['REMOTE_ADDR']) && $_SERVER['REMOTE_ADDR'] && strcasecmp($_SERVER['REMOTE_ADDR'], 'unknown')) {
		$onlineip = $_SERVER['REMOTE_ADDR'];
	}
	return $onlineip;
}



/**
 * 分页处理
 * 
 * @param int $num
 * @param int $perpage
 * @param int $curpage
 * @param string $mpurl
 * @param string $todiv
 * @param string $callback_func
 * @param array $callback_args array($param_1, $param_2)
 * 
 * @return string
 */
function multi($num, $perpage, $curpage, $mpurl, $todiv='', $callback_func=null, $callback_args=null) {
	global $_SCONFIG, $_SGLOBAL;

	$page = 5;
	if($_SGLOBAL['showpage']) $page = $_SGLOBAL['showpage'];

	$multipage = '';
	$mpurl .= strpos($mpurl, '?') ? (substr($mpurl,-1)!='?' ? '&':'') : '?';
	
	$realpages = 1;
	if($num > $perpage) {
		$offset = 2;
		$realpages = @ceil($num / $perpage);
		$pages = $_SCONFIG['maxpage'] && $_SCONFIG['maxpage'] < $realpages ? $_SCONFIG['maxpage'] : $realpages;
		if($page > $pages) {
			$from = 1;
			$to = $pages;
		} else {
			$from = $curpage - $offset;
			$to = $from + $page - 1;
			if($from < 1) {
				$to = $curpage + 1 - $from;
				$from = 1;
				if($to - $from < $page) {
					$to = $page;
				}
			} elseif($to > $pages) {
				$from = $pages - $page + 1;
				$to = $pages;
			}
		}
		$multipage = '';
		$urlplus = $todiv?"#$todiv":'';
		if($curpage > 1) {
			$multipage .= "<a ";
			$multipage .= "href=\"{$mpurl}page=".($curpage-1)."$urlplus\"";
			//$multipage .= " class=\"prev\">&lsaquo;&lsaquo;</a>";
			$multipage .= " class=\"prev\">上一页</a>";
		}
		if($curpage - $offset > 1 && $pages > $page) {
			$multipage .= "<a ";
			$multipage .= "href=\"{$mpurl}page=1{$urlplus}\"";
			$multipage .= " class=\"first\">1 ...</a>";
		}
		for($i = $from; $i <= $to; $i++) {
			if($i == $curpage) {
				$multipage .= '<strong>'.$i.'</strong>';
			} else {
				$multipage .= "<a ";
				$multipage .= "href=\"{$mpurl}page=$i{$urlplus}\"";
				$multipage .= ">$i</a>";
			}
		}
		if($to < $pages) {
			$multipage .= "<a ";
			$multipage .= "href=\"{$mpurl}page=$pages{$urlplus}\"";
			$multipage .= " class=\"last\">... $realpages</a>";
		}
		if($curpage < $pages) {
			$multipage .= "<a ";
			$multipage .= "href=\"{$mpurl}page=".($curpage+1)."{$urlplus}\"";
			//$multipage .= " class=\"next\">&rsaquo;&rsaquo;</a>";
			$multipage .= " class=\"next\">下一页</a>";
		}
/* 		if($multipage) {
			$multipage = '<em>&nbsp;'.$num.'&nbsp;</em>'.$multipage;
		} */
		
		/* callback */
		if( !empty($callback_func) ) {
    		if( preg_match_all("/href=\"(.*?)\"/", $multipage, $matches) ) {
    		    if( !empty($matches[1]) ) {
                    foreach ($matches[1] as $val) {
                        /* callback function */
                        //callback args
                        $args = !empty($callback_args) ? array_merge(array($val), $callback_args) : array($val);
                        
                        //新的URL格式
                        $new_val = call_user_func_array($callback_func, $args);
                        
                        //replace to new url
                        $multipage = str_replace('"'.$val.'"', '"'.$new_val.'"', $multipage);
                    }
    		    }
            }
		}
        /* /callback */
        
	}
	return $multipage;
}



/**
 * 格式化时间，XX天XX/XX时/XX分/XX秒
 * @param unknown_type $timediff
 * @param unknown_type $to_str
 */
function formattime($timediff, $to_str=1)
{
     $days = intval($timediff/86400);
     $remain = $timediff%86400; 
     $hours = intval($remain/3600); 
     $remain = $remain%3600; 
     $mins = intval($remain/60); 
     $secs = $remain%60; 
     $res = array("day" => $days,"hour" => $hours,"min" => $mins,"sec" => $secs);
     
     if( $to_str ) {
     	return $res['day'].'天'.$res['hour'].'时'.$res['min'].'分'.$res['sec'].'秒';
     }
     return $res;
}



/**
 * 去除多余的空白字符
 * 
 * @param string $str
 */
function strip_more_space($str)
{
    return preg_replace("/\s{2,}/", "  ", $str);
}



//产生随机字符
function random($length, $numeric = 0) {
	PHP_VERSION < '4.2.0' && mt_srand((double)microtime() * 1000000);
	if($numeric) {
		$hash = sprintf('%0'.$length.'d', mt_rand(0, pow(10, $length) - 1));
	} else {
		$hash = '';
		$chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789abcdefghijklmnopqrstuvwxyz';
		$max = strlen($chars) - 1;
		for($i = 0; $i < $length; $i++) {
			$hash .= $chars[mt_rand(0, $max)];
		}
	}
	return $hash;
}

/**
 * 设置cookie
 * @param string $var
 * @param string $value
 * @param int $life
 */
function ssetcookie($var, $value, $life=0) {
	global $_SGLOBAL, $_SC, $_SERVER;
	setcookie($_SC['cookiepre'].$var, $value, $life?($_SGLOBAL['REQUEST_TIME']+$life-5*60/* 避免服务器时间与本地时间不同步 */):0, $_SC['cookiepath'], $_SC['cookiedomain'], $_SERVER['SERVER_PORT']==443?1:0);
}




/**
 * 字符串解密加密
 * 
 * @param $string
 * @param $operation
 * @param $key
 * @param $expiry
 */
function authcode($string, $operation = 'DECODE', $key = '', $expiry = 0) {

        $ckey_length = 4;       // 随机密钥长度 取值 0-32;
                                // 加入随机密钥，可以令密文无任何规律，即便是原文和密钥完全相同，加密结果也会每次不同，增大破解难度。
                                // 取值越大，密文变动规律越大，密文变化 = 16 的 $ckey_length 次方
                                // 当此值为 0 时，则不产生随机密钥

        $key = md5($key ? $key : MS_KEY);
        $keya = md5(substr($key, 0, 16));
        $keyb = md5(substr($key, 16, 16));
        $keyc = $ckey_length ? ($operation == 'DECODE' ? substr($string, 0, $ckey_length): substr(md5(microtime())
, -$ckey_length)) : '';

        $cryptkey = $keya.md5($keya.$keyc);
        $key_length = strlen($cryptkey);

        $string = $operation == 'DECODE' ? base64_decode(substr($string, $ckey_length)) : sprintf('%010d', $expiry
 ? $expiry + time() : 0).substr(md5($string.$keyb), 0, 16).$string;
        $string_length = strlen($string);

        $result = ''; 
        $box = range(0, 255);
        $rndkey = array();
        for($i = 0; $i <= 255; $i++) {
                $rndkey[$i] = ord($cryptkey[$i % $key_length]);
        }   

        for($j = $i = 0; $i < 256; $i++) {
                $j = ($j + $box[$i] + $rndkey[$i]) % 256;
                $tmp = $box[$i];
                $box[$i] = $box[$j];
                $box[$j] = $tmp;
        }   

        for($a = $j = $i = 0; $i < $string_length; $i++) {
                $a = ($a + 1) % 256;
                $j = ($j + $box[$a]) % 256;
                $tmp = $box[$a];
                $box[$a] = $box[$j];
                $box[$j] = $tmp;
                $result .= chr(ord($string[$i]) ^ ($box[($box[$a] + $box[$j]) % 256]));
        }

        if($operation == 'DECODE') {
                if((substr($result, 0, 10) == 0 || substr($result, 0, 10) - time() > 0) && substr($result, 10, 16) == substr(md5(substr($result, 26).$keyb), 0, 16)) {
                        return substr($result, 26);
                } else {
                        return '';
                }
        } else {
                return $keyc.str_replace('=', '', base64_encode($result));
        }
}


/**
 * 打印消息
 * @param string $message
 * @param enom('error', 'notice') $type
 * @param boolean $is_halt
 */
function show_message($message, $type='error', $is_halt=1)
{
	global $_SCONFIG, $_SGLOBAL, $_SC, $_TPL, $_SCOOKIE;//所有全局变量
	
    $message = array('type'=>$type, 'content'=>$message);
    //show message
    include template('show_message');
    if( $is_halt ) { exit(); }
}



/**
 * 获取文件相对网站根目录路径
 * @param string $path
 * @return string
 */
function relative_path($path)
{
	if( substr($path, 0, strlen(S_ROOT)) === S_ROOT ) {
		return substr($path, strlen(S_ROOT));
	}
	
	return $path;
}


/**
 * Set Http Header
 * @param int $num
 */
function set_http_response_code($num, $replace=1)
{
	static $http_status_sets = array(
    	404 => 'HTTP/1.1 404 Not Found',
    	500 => 'HTTP/1.1 500 Internal Server Error',
    	503 => 'HTTP/1.1 503 Service Temporarily Unavailable',
    );
    
    if( !isset($http_status_sets[$num]) ) { return null; }
    
    //set header
    header($http_status_sets[$num], $replace, $num);
}




/**
 * 连接数据库, config, localhost
 */
function connect_db($is_halt=0)
{
	try {
	    global $_SC;
	    $db = new db_handle($_SC['db_lh_dsn'], $_SC['db_lh_user'], $_SC['db_lh_psw'], $_SC['db_lh_dirver_opt']);
	}catch ( PDOException  $e){
	    echo 'Connection failed: '.$e->getMessage()."\n";
	    if( $is_halt ) { exit; }
	}
	
	return $db;
}


/**
 * Generates a Universally Unique IDentifier, version 4.
 * 
 * RFC 4122 (http://www.ietf.org/rfc/rfc4122.txt) defines a special type of Globally
 * Unique IDentifiers (GUID), as well as several methods for producing them. One
 * such method, described in section 4.4, is based on truly random or pseudo-random
 * number generators, and is therefore implementable in a language like PHP.
 * 
 * We choose to produce pseudo-random numbers with the Mersenne Twister, and to always
 * limit single generated numbers to 16 bits (ie. the decimal value 65535). That is
 * because, even on 32-bit systems, PHP's RAND_MAX will often be the maximum *signed*
 * value, with only the equivalent of 31 significant bits. Producing two 16-bit random
 * numbers to make up a 32-bit one is less efficient, but guarantees that all 32 bits
 * are random.
 * 
 * The algorithm for version 4 UUIDs (ie. those based on random number generators)
 * states that all 128 bits separated into the various fields (32 bits, 16 bits, 16 bits,
 * 8 bits and 8 bits, 48 bits) should be random, except : (a) the version number should
 * be the last 4 bits in the 3rd field, and (b) bits 6 and 7 of the 4th field should
 * be 01. We try to conform to that definition as efficiently as possible, generating
 * smaller values where possible, and minimizing the number of base conversions.
 * 
 * @copyright  Copyright (c) CFD Labs, 2006. This function may be used freely for
 *              any purpose ; it is distributed without any form of warranty whatsoever.
 * @author      David Holmes <dholmes@cfdsoftware.net>
 * 
 * @return  string  A UUID, made up of 32 hex digits and 4 hyphens.
 */

function uuid() {
   
   // The field names refer to RFC 4122 section 4.1.2

   return sprintf('%04x%04x-%04x-%03x4-%04x-%04x%04x%04x',
       mt_rand(0, 65535), mt_rand(0, 65535), // 32 bits for "time_low"
       mt_rand(0, 65535), // 16 bits for "time_mid"
       mt_rand(0, 4095),  // 12 bits before the 0100 of (version) 4 for "time_hi_and_version"
       bindec(substr_replace(sprintf('%016b', mt_rand(0, 65535)), '01', 6, 2)),
           // 8 bits, the last two of which (positions 6 and 7) are 01, for "clk_seq_hi_res"
           // (hence, the 2nd hex digit after the 3rd hyphen can only be 1, 5, 9 or d)
           // 8 bits for "clk_seq_low"
       mt_rand(0, 65535), mt_rand(0, 65535), mt_rand(0, 65535) // 48 bits for "node"  
   );  
}

