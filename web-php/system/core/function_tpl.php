<?php
if(!defined('IN_SYSTEM')) { exit('Access Denied'); }


/**
 * 包含外部css文件，$file不为空则包含，为空则输出css文件
 * 
 * @param string $file
 */
function tpl_include_ex_css($file='')
{
    global $_TPL, $_SCONFIG;
    
    //include
    if( trim($file) != '' && substr($file, -4) == '.css' ) {
        $_TPL['header']['css'][$file] = $file;//避免重复包含css
        return true;
    }
    
    //echo
    $paths = array();
	if( !empty($_TPL['header']['css'] ) ) {
	    foreach ($_TPL['header']['css'] as $val) {
	        if( !empty($_SCONFIG['template']) ) {
	            $tmp_path = 'template/'.$_SCONFIG['template'].'/css/'.$val;
	        }else{
	            $tmp_path = 'css/'.$val;
	        }
	        file_exists(A_ROOT.$tmp_path) && $paths[] = '/'.$tmp_path;
	    }
	}
	
	if( !empty($paths) ) {
	    $url_q = implode(';', $paths);
	    
	    //css合并文件，若修改此文件路径算法，请同步css.php文件算法
        $_all_in_one_file = A_ROOT.'/tmp/'.md5($url_q).'.css';
        $file_time = file_exists($_all_in_one_file) ? filemtime($_all_in_one_file) : '';
        
	    echo '<link href="/get.css?f='.urlencode($url_q).'&_='.$file_time.'" rel="stylesheet" type="text/css" />';
	}
}


/**
 * 输出css文件
 */
function tpl_echo_ex_css()
{
    return tpl_include_ex_css();
}



/**
 * 获取Page Title
 * 
 * @return string
 */
function tpl_get_page_title_str()
{
	global $_TPL, $_SCONFIG;
	
	if( empty($_TPL['title']) ) return $_SCONFIG['site_name'];
	
	if( is_array($_TPL['title']) ) {
	    $_TPL['title'] = array_map('trim', $_TPL['title']);
	    $_TPL['title'] = array_filter($_TPL['title']);
		return implode("_", array_merge($_TPL['title'], array($_SCONFIG['site_name'])));
	}
	
	//string
	return $_TPL['title']."_".$_SCONFIG['site_name'];
}

