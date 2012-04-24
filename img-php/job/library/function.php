<?php

/**
 * img cache
 * @param string $url
 * @param int $width
 * @param int $height
 */
function img_cache($url, $weight, $height) {
	return img_proxy($url, $_SERVER['HTTP_HOST'], $height, $weight);
}

/**
 * 获取google图钉icon
 * @param int $count（1->A）最多到26即Z
 */
function google_map_icon_url($count){
	return 'http://www.google.com/mapfiles/marker'.chr($count + 65).'.png';
}

/**
 * proxy img
 * @param string $url
 * @param string $refer
 * @param int $height
 * @param int $weight
 */
function img_proxy($url, $refer, $height, $weight) {
	global $_SC;
	$proxy_host = $_SC['img_proxy_url'];
	$param = array(
		'u' => $url,
		'r' => $refer,
		'w' => $weight,
		'h' => $height,
	);
	return $proxy_host."?".http_build_query($param);
}

/**
 * 初始化memcached
 */
function init_memcached() {
    global $_SGLOBAL, $_SC;

    if( !empty($_SGLOBAL['m']) ) { return; }

    //new
    $m = new Memcached();
    //add servers
    $m->addServers($_SC['memcached']);
    //get stats
    if( $m->getStats() == false ) {
        header("HTTP/1.0 500 Internal Server Error" , true , 500);
        die('connetct to memcached server failure, please check all memcached servers.');
    }

    $_SGLOBAL['m'] = $m;
}

/**
 * 从wikipedia集合中找content信息
 * @param string $name
 * @return array()
 */
function get_wiki_content($name, $id = array()) {
	global $_SGLOBAL;
	$search = array('title' => $name);
	!empty($id) && $search['_id'] = array('$nin' => $id);
	//查询
	$wiki = $_SGLOBAL['db']->Wikipedia_select_one($search);
	if(!empty($wiki) && !empty($wiki['content'])){
		//排除已经查过的
		$id[] = $wiki['_id'];
		//如果有跳转则递归
		if(preg_match("/#REDIRECT\s?(.+)/i", $wiki['content'], $match)){
			$wiki = get_wiki_content($match[1], $id);
		}
	}
	return $wiki;
}

/**
 * get article keywords
 * @param string $articleId
 * @return array
 */
function get_article_keywords($articleId, $callback_func = "") {
	global $_SGLOBAL;
	$keywords = array();
	$solr_res = $_SGLOBAL['pagedb']->fragments_select(array('articleId' => (string)$articleId), array('keyword'));
	foreach($solr_res AS $item) {
		!empty($item['keyword']) && $keywords[] = $item['keyword'];
	}
	return !empty($callback_func) ? call_user_func_array($callback_func, array($keywords)) : $callback_func;
}

/**
 * get the star cnt by rank
 * @param unknown_type $rank
 * @return int half start
 */
function get_start_cnt($rank) {
	$percent = round($rank * 100);
	$residue = $percent % 5;
	$residue > 2 ? $percent += 5 - $residue : $percent -= 5 - $residue;
	return intval($percent / 10);
}

/**
 * 经纬度距离转换为国标距离
 * @param unknown_type $lt_lg_dis
 */
function lt_lg_dis_to_real_dis($lt_lg_dis, $type = 'km', $is_format = true){
	$earth_radius = 6378.137;//km
	$pi = 3.1415926;//元周率
	$ratio = ( (2 * $pi) / 360 ) * $earth_radius;
	$real_dis =  $lt_lg_dis * $ratio;
	if(!$is_format){
		$output =  $type == 'm' ? $real_dis * 1000 : $real_dis;
	}else{
		$output = dis_format($real_dis);
	}
	return $output;
}

/**
 * 国标距离换为经纬度距离转
 * @param unknown_type $lt_lg_dis
 */
function real_dis_to_lt_lg_dis($real_dis){
	$earth_radius = 6378.137;//km
	$pi = 3.1415926;//元周率
	$ratio = ( (2 * $pi) / 360 ) * $earth_radius;
	return $real_dis / $ratio;
}

/**
 * 格式化距离
 * @param string $dis
 * @return string $output
 */
function dis_format($dis){
	if($dis > 1){
		$output =  intval($dis * 10) / 10;
		$output .= 'km';
	}else{
		$output = intval($dis * 10) * 100;
		$output .= 'm';
	}
	return $output;
}

/**
 * generate url
 * @param array() $extra_param
 * @param string $basic_url
 * @return string
 */
function generate_url($extra_param = array(), $basic_url = "") {
	$default_param = array();
	$parser = parse_url($_SERVER['REQUEST_URI']);
	!empty($parser['query']) && parse_str($parser['query'], $default_param);
	empty($basic_url) && $basic_url = "http://".$_SERVER['HTTP_HOST'].$parser['path'];
	$param = array_merge($default_param, $extra_param);
	return $basic_url.'?'.http_build_query($param);
}

/**
 * get filcker photo url
 * @param array $photo_item
 * @param string $type
 */
function flicker_photo_url($photo_item, $type) {
	return "http://farm{$photo_item['farm']}.staticflickr.com/{$photo_item['server']}/{$photo_item['id']}_{$photo_item['secret']}_$type.jpg";
}

/**
 * format keyword
 * @param array $keywords
 * @return array 
 */
function tpl_article_keyword_format($keywords) {
	$res_keyword = array();
	foreach($keywords AS $index => $keyword) {
		$res_keyword[$index] = "<a href='#'>#$keyword</a>";
	}
	return $res_keyword;
}

/**
 * @param string &$contents
 * @return array() $img_tags 
 */
function tpl_extract_img_tags_from_content(&$content) {
	$matches = array();
	preg_match_all("#<\s*img[^<>]*>#", $content, $matches);
	$content = preg_replace("#<\s*img[^<>]*>#", "", $content);
	return $matches[0];
}

/**
 * 格式化article正文
 * @param string $content
 * @param int $length
 */
function tpl_article_substr($content, $length) {
	$content = utf8_substr_ifneeed($content, $length, 0, '');
	$content = trim($content);
	$content = '<p>'.preg_replace("#[\r\n\s]{2,}#s", "</p><p>", $content).'</p>';
	$content = preg_replace("#<p>[\r\n\s]*</p>#", "", $content);
	return $content;
}

/**
 * get poi geo content
 * @param array $poi
 * @return array
 */
function tpl_get_geo_content(&$poi){
	//init
	$content = '';
	//取三个类别
	foreach(array('name' => '名称', 'traffic' => '交通', 'desc' => '描述') AS $key => $val){
		if($key == 'desc' && empty($poi['desc'])){
			$wiki = get_wiki_content($poi['name']);
			$poi['desc'] = trim($wiki['content']);
		}
		if(!empty($poi[$key])){
			$tmp = utf8_substr_ifneeed(trim(strip_tags($poi[$key])), 150);
			if($key == 'name'){
				$tmp = '<a href="/poi/'.$poi['_id'].'" target="_blank">'.$tmp.'</a>';
			}
			$content .= <<<HTML
			<tr>
				<td style="font-weight:bold;vertical-align:top;width:15%;">
					$val:&nbsp;
				</td>
				<td style="width:85%;">
					$tmp
				</td>
			</tr>
HTML;
		}
	}
	return '<table style="width:300px;table-layout:fixed;">'.$content.'</table>';
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