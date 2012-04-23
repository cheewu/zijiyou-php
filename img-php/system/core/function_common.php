<?php
if(!defined('IN_SYSTEM')) {	exit('Access Denied'); }



/**
 * 初始化客户端ID
 */
function init_client_id()
{
	global $_SCOOKIE, $_SGLOBAL;
	
	//already set
	if( !empty($_SCOOKIE['_id']) ) {
		$_SGLOBAL['client_id'] = $_SCOOKIE['_id'];
		return;
	}
	
	//init client id
	ssetcookie('_id', uuid(), 86400*365*5);
}


/**
 * 返回UTF-8编码第一个字符的A-Z
 * 
 * @param string $str
 * @return mixed
 */
function get_first_pinyin_char($str)
{
    static $py_hd = null;
	$temp_str=substr($str,0,1);
	$ascnum=Ord($temp_str);//得到字符串中第1字节的ascii码
	if ($ascnum>=224){  //如果ASCII位高与224，
		$first_str = substr($str,0,3); //根据UTF-8编码规范，将3个连续的字符计为单个字符,实际Byte计为3
	}else if($ascnum>=192){  //如果ASCII位高与192，
		$first_str = substr($str,0,2); //根据UTF-8编码规范，将2个连续的字符计为单个字符,实际Byte计为2
	}else if($ascnum>=65 && $ascnum<=90){  //如果是大写字母，实际的Byte数仍计1个
		return substr($str,0,1);
	}else{  //其他情况下，包括小写字母和半角标点符号，
		return strtoupper(substr($str,0,1));  //小写字母转换为大写,实际的Byte数计1个
	}
    
    if( empty($first_str) ) { return false; }
    //gbk拼音汉字对照文件
    if( !$py_hd ) {
    	$dat_path = S_ROOT.'data'.DIRECTORY_SEPARATOR.'gbk_pinyin.dat';
        $py_hd = fopen($dat_path, 'r');
    }
    //将字符串的第一个字符转换为GBK编码获取其拼音
    $first_str = iconv("utf8", "gb18030//IGNORE", $first_str);
    //匹配出第一个汉字或者字符
    preg_match("/[\x01-\x7f]|[\x81-\xfe][\x40-\xfe]/", $first_str, $matches);
    $m_str = $matches[0];
    
    /* 未匹配上 */
    if( strlen($m_str) == 0 ) { return null; }
    
    /* 单个字符 */
    if( strlen($m_str) == 1 ) {
        $ascii = ord(strtoupper($m_str));
        if( $ascii >= 65 && $ascii <= 91 ) { return chr($ascii); }
        return null;
    }
    
    /* 汉字 */
    $high = ord($m_str[0]) - 0x81;
    $low = ord($m_str[1]) - 0x40;
    
    // 计算偏移位置
    $off = ($high<<8) + $low - ($high * 0x40);
    //读取数据
    fseek($py_hd, $off * 8, SEEK_SET);
    $ret = unpack('a8', fread($py_hd, 8));
    
    if( $ret ) { 
    	$first_char = strtoupper(substr($ret[1],0,1));
    	return $first_char; 
    }
    
    return null;
}