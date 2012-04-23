<?php
/**
 * 获取，压缩CSS文件
 * 
 * @author 
 * @since 2010-08
 */
//if (substr_count($_SERVER['HTTP_ACCEPT_ENCODING'], 'gzip')){
//    ob_start("ob_gzhandler");
//}else{
//    ob_start();
//}

//gzip由nginx完成

ob_start();
header("Content-type: text/css");

//client cache
header('Cache-Control: public, max-age='.(86400*1));
header('Pragma: public');

if( empty($_GET['f']) ) {
    die('/* nothing */');
}


define('S_ROOT', dirname(__FILE__));







/**
 * @var array $files css文件数组
 */
$files = explode(';', $_GET['f']);
foreach ( $files as $file )
{
    //检查文件后缀
    if( substr($file, -4) != '.css' ) {
        die('/* URL is NOT all css file */');//必须全部是CSS文件结尾
    }
}


/**
 * 当前合并文件的地址
 * @var string
 */
$_all_in_one_file_name = md5($_GET['f']).'.css';
$_all_in_one_file = S_ROOT.'/tmp/'.$_all_in_one_file_name;
if( !file_exists($_all_in_one_file) ) {
    /**
     * 不存在合并文件
     * 重新合并css文件，刷新css.dict中各文件修改时间
     */
    list($content, $_all_in_one_file_mtime) = get_css_in_one($files);
    header("Last-Modified:" . $_all_in_one_file_mtime);
    echo $content;
    
    exit;
}
$_all_in_one_file_mtime = gmdate('D, d M Y H:i:s', filemtime($_all_in_one_file)) . ' GMT';

/*
 * 第一次生成css.dict文件

file_put_contents(S_ROOT.'/tmp/css.dict', serialize(array()));
exit;

*/


/**
 * css文件过期记录字典
 * @var array
 */
$_css_dict_file = S_ROOT.'/tmp/css.dict';
$_css_dict = @unserialize(file_get_contents($_css_dict_file));
if( $_css_dict === false ) {
	//create
	file_put_contents($_css_dict_file, serialize(array()));
	$_css_dict = @unserialize(file_get_contents($_css_dict_file));
	if( $_css_dict === false ) {
    	die('/* Can NOT open css.dict file */');
	}
}



/**
 * 带有HTTP_IF_MODIFIED_SINCE请求头信息, 且HTTP_IF_MODIFIED_SINCE相同
 */
if( !empty($_SERVER['HTTP_IF_MODIFIED_SINCE']) 
    && $_all_in_one_file_mtime == $_SERVER['HTTP_IF_MODIFIED_SINCE'] 
    && ($is_css_file_changed = check_css_file_is_modified($files)) == false )
{
    /* 输出304 */
    header ("HTTP/1.1 304 Not Modified");
    header("Last-Modified:" . $_all_in_one_file_mtime);
    
    exit;    
}


if( $is_css_file_changed )
{
    /**
     * 从新合并css文件，刷新css.dict中各文件修改时间
     */
    list($content, $_all_in_one_file_mtime) = get_css_in_one($files);
    header("Last-Modified:" . $_all_in_one_file_mtime);
    echo $content;
    
    exit;
}else{
    
    /**
     * 输出合并文件
     */
    header("Last-Modified:" . $_all_in_one_file_mtime);
    echo @unserialize(file_get_contents($_all_in_one_file));
    
    exit;
}


/*------------------------- EOF --------------------------*/












/**
 * Function List
 */

/**
 * 过滤CSS文件
 * 
 * @param string $str
 * @return string
 */
function strip_css_content($str)
{
    //去掉@指令  
    $str = preg_replace("/@.*?;/", '', $str);
    //去掉注释
    $str = preg_replace("/\/\*.*?\*\//s", '', $str);
    //去掉换行
    $str = str_replace("\r\n", '', $str);
    $str = str_replace("\n", '', $str);
    //替换2个以上的空白字符为一个空白符
    $str = preg_replace("/\s{2,}/", " ", $str);

    return $str;
}


/**
 * 检测CSS文件是否修改
 * @param array $files
 */
function check_css_file_is_modified($files)
{
    global $_css_dict, $_all_in_one_file_name;
    
    foreach ( $files as $file )
    {
        $file_path = S_ROOT.$file;
        
        if( empty($_css_dict[$_all_in_one_file_name."-".$file_path]) ) {
            //字典中不存在认为已经修改
            return true;
        }
        
        if( !file_exists($file_path) ) { die("/* CSS File:[{$file}] is Not exist! */"); }
        
        $last_modified_time = gmdate('D, d M Y H:i:s', filemtime($file_path)) . ' GMT';
        if( $_css_dict[$_all_in_one_file_name."-".$file_path] != $last_modified_time ) {
            return true;
        }
    }
    
    return false;
}



/**
 * 返回合并的CSS文件，会更新所有css文件filemtime时间，包括合并文件
 * @param array $files
 */
function get_css_in_one($files)
{
    global $_css_dict, $_all_in_one_file, $_css_dict_file, $_all_in_one_file_name;
    
	$contents = '';
	foreach ( $files as $file )
	{
	    $file_path = S_ROOT.$file;
	    
	    $contents .= @file_get_contents($file_path);
	    $last_modified_time = gmdate('D, d M Y H:i:s', filemtime($file_path)) . ' GMT';
	    
	    //set modify time in css_dict
	    $_css_dict[$_all_in_one_file_name."-".$file_path] = $last_modified_time;
	}
	
	$contents = "@CHARSET \"GB18030\";\n".strip_css_content($contents);
	//写合并文件
	if( file_put_contents($_all_in_one_file, serialize($contents)) === false ) {
	    die('/* write css file cache failure */');
	}
	
	//合并文件的最后修改时间
	$last_modified_time = gmdate('D, d M Y H:i:s', time()) . ' GMT';
	$_css_dict[$_all_in_one_file_name] = $last_modified_time;
	/**
	 * 写css.dict
	 */
	@file_put_contents($_css_dict_file, serialize($_css_dict));
	
	return array($contents, $last_modified_time);
}



