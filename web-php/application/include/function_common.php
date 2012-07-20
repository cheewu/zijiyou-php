<?php

/**
 * generate curmbs
 */
function crumbs($region_id = "", $poi_id = "", $append = "") {
	global $_SGLOBAL;
	$curmbs = array(
		'<a href="/">首页</a>',
		'<a href="/">目的地指南</a>'
	);
	if(!empty($region_id)) {
		$region = $_SGLOBAL['db']->Region_select_one(array('_id' => new MongoID($region_id)));
		$father_region = !empty($region['area']) ? $_SGLOBAL['db']->Region_select_one(array('name' => $region['area'])) : "";
		if(!empty($father_region)) {
			$father_region_id = strval($father_region['_id']);
			if(in_array($region['category'], array('province', 'country'))) {
				$curmbs[] = "<a href='#'>{$region['area']}</a>";
				$curmbs[] = "<a href='#'>{$region['name']}</a>";
			} else {
				!empty($father_region['area']) && $curmbs[] = "<a href='#'>{$father_region['area']}</a>";
				$curmbs[] = "<a href='/state/$father_region_id'>{$father_region['name']}</a>";
				$curmbs[] = "<a href='/region/$region_id'>{$region['name']}</a>";
			}
		} else {
			!empty($region['area']) && $curmbs[] = "<a href='#'>{$region['area']}</a>";
			$curmbs[] = "<a href='/region/$region_id'>{$region['name']}</a>";
		}
	}
	if(!empty($poi_id)) {
		$poi = $_SGLOBAL['db']->POI_select_one(array('_id' => new MongoID($poi_id)));
		$curmbs[] = "<a href='/poi/$poi_id'>{$poi['name']}</a>";
	}
	if(!empty($append)) {
		!is_array($append) && $append = array($append);
		foreach($append AS $value) {
			$curmbs[] = "<a href='#'>$value</a>";
		}
	}
	return implode("&nbsp;&gt;&nbsp;", $curmbs);
}

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
 * get poi pic
 * @param string $id
 */
function get_poi_pic($id) {
	global $_SC;
	return $_SC['img_bed'].'poi'.DIRECTORY_SEPARATOR.$id.'.png';
}

/**
 * get region pic
 * @param string $id
 */
function get_region_pic($id) {
	global $_SC;
	return $_SC['img_bed'].'region'.DIRECTORY_SEPARATOR.$id.'.png';
}

/**
 * get article pic
 * @param string $id
 */
function get_article_pic_by_index($id, $index, $width, $height) {
	global $_SC;
	return $_SC['img_bed'].'article'.DIRECTORY_SEPARATOR.$id."{$index}_{$width}x{$height}.png";
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
 * @param int $width
 * @param int $height
 */
function img_proxy($url, $refer, $width, $height) {
	global $_SC, $_SGLOBAL;
	$prefix = $_SC['img_cache_prefix'];
	$md5 = md5($url.$refer);
	$key = $prefix.$md5;
	$_SGLOBAL['m']->get($key);
	//no found
    if( $_SGLOBAL['m']->getResultCode() == Memcached::RES_NOTFOUND ) {
        /* the key does not exist */
        $param = array($url, $refer);
        $_SGLOBAL['m']->set($key, $param);
    }
    return $_SC['img_bed'].'cache'.DIRECTORY_SEPARATOR.$md5."_{$width}x{$height}.png";
}

/**
 * is article image exists
 * @param string $url
 * @return bool
 */
function is_article_image_exists($url) {
    global $_SC;
    //return true;
    return is_file($_SC['article_img_dir'].md5($url));
}

/**
 * get article image by url
 * @param string $url
 * @param int $width
 * @param int $height
 */
function get_article_image($url, $width, $height) {
    global $_SC, $_SGLOBAL;
	$prefix = $_SC['img_cache_prefix'];
    $md5 = md5($url);
	$crc32 = base_convert(crc32($url), 10, 16);
	$key = $prefix.$crc32;
	$_SGLOBAL['m']->get($key);
	//no found
    if( $_SGLOBAL['m']->getResultCode() == Memcached::RES_NOTFOUND ) {
        /* the key does not exist */
        $_SGLOBAL['m']->set($key, $_SC['article_img_dir'].$md5);
    }
    return $_SC['img_bed'].'article/'.$crc32."_{$width}x{$height}.png";
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
function generate_url($extra_param = array(), $extra_param_only = false, $basic_url = "") {
	$default_param = array();
	$parser = parse_url($_SERVER['REQUEST_URI']);
	!empty($parser['query']) && parse_str($parser['query'], $default_param);
	empty($basic_url) && $basic_url = "http://".$_SERVER['HTTP_HOST'].$parser['path'];
	$param = $extra_param_only ? $extra_param : array_merge($default_param, $extra_param);
	$param = array_filter($param, create_function('$var', 'return !is_null($var);'));
	$basic_url = rtrim($basic_url, DIRECTORY_SEPARATOR).DIRECTORY_SEPARATOR;
	!empty($param) && $basic_url .= '?'.http_build_query($param);
	return $basic_url;
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
function tpl_get_geo_content(&$poi, $region_id = ''){
	// init
	$content = '';
	// get wiki
	$wiki = get_wiki_content($poi['name']);
	$poi['desc'] = trim($wiki['content']);
	// 取三个类别
	foreach(array('name' => '名称', 'traffic' => '交通', 'desc' => '描述') AS $key => $val){
		if(!empty($poi[$key])){
			$tmp = utf8_substr_ifneeed(trim(strip_tags($poi[$key])), 150);
			if($key == 'name'){
				$tmp = '<a href="/poi/'.$poi['_id'].'" target="_blank">'.$tmp.'</a>';
			}
			if($key == 'desc' && !empty($tmp) && !empty($region_id)){
				$tmp = $tmp.'<a style="color:#5392CB; padding-left:5px;" href="/wiki/'.$region_id.'/'.strval($wiki['_id']).'" target="_blank">更多</a>';
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
 * get region geo content
 * @param array $poi
 * @return array
 */
function tpl_get_region_geo_content(&$region){
	// init
	$content = '';
	// get wiki
	$wiki = get_wiki_content($region['name']);
	$region_id = strval($region['_id']);
	$region['wiki'] = trim($wiki['content']);
	// 取三个类别
	foreach(array('name' => '名称', 'wiki' => '描述') AS $key => $val){
		if(!empty($region[$key])){
			$tmp = utf8_substr_ifneeed(trim(strip_tags($region[$key])), 150);
			if($key == 'name'){
				$tmp = '<a href="/region/'.$region_id.'" target="_blank">'.$tmp.'</a>';
			}
			if($key == 'wiki' && !empty($tmp) && !empty($region_id)){
				$tmp = $tmp.'<a style="color:#5392CB; padding-left:5px;" href="/wiki/'.$region_id.'/'.strval($wiki['_id']).'" target="_blank">更多</a>';
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
 * trim white space char
 * @param $string
 * @return $string
 */
function tpl_trim($string) {
    $string = @iconv("UTF-8", "UTF-8//IGNORE", $string);
    $string = str_replace(chr(194).chr(160), " ", $string);
    $string = str_replace("　", " ", $string);
    $string = trim($string, "\t\n\0\r\x0B\x20");
    return $string;
}


/**
 * correct the img src
 * @param string $html
 * @param string $refer
 */
function tpl_src_cure($html, $width, $height) {
    global $_tpl_src_cure_param;
    $_tpl_src_cure_param = array($width, $height);
    $html = preg_replace_callback("#<\s*img.*?real_src\s*=\s*[\"']([^\"]*)[\"'].*?/\s*>#", 
               function ($matches) {
                   return '<img src="'.trim($matches[1]).'"/>';
               }, $html);
    $html = preg_replace_callback("#<\s*img.*?src\s*=\s*[\"']([^\"]*)[\"'].*?/\s*>#", 
               function ($matches) {
                   global $_tpl_src_cure_param;
                   return is_article_image_exists($matches[1]) ? 
           		          '<img src="'.get_article_image($matches[1], 
                                          $_tpl_src_cure_param[0], 
                                          $_tpl_src_cure_param[1]).
                   		  '" onerror=\'$(this).css("display", "none")\'/>' : '';
               }, $html);
    unset($_tpl_src_cure_param);
    return $html;
}

/**
 * get google iamge
 * @param string $id
 * @param string $type
 * @return mix bool or string
 */
function tpl_get_google_poi_region_img($id, $type, $size) {
    global $_SC, $_SGLOBAL;
    $collection = (strtolower($type) == 'poi') ? 'POI' : 'Region';
    $func = "{$collection}_select_one";
    $res = $_SGLOBAL['db']->$func(array('_id' => new MongoID($id)), array('googleImages'));
    if (empty($res['googleImages'])) { return false;}
    $pic_arr = array();
    foreach ($res['googleImages'] AS $googleImage) {
        $pic_arr[$googleImage['imageId']] = $googleImage['width'] * $googleImage['height'];
    }
    arsort($pic_arr);
    $pic_dirname = "";
    foreach ($pic_arr AS $pic_id => $pic) {
        $tmp_pic_dirname = $_SC['googleImages_dir'].$collection."/".$pic_id;
        if (!is_file($tmp_pic_dirname)) { continue; }
        $pic_dirname = $tmp_pic_dirname;
        break;
    }
    if (empty($pic_dirname)) { return false; }
    $image_stat = getimagesize($pic_dirname);
    list(, $img_type) = explode('/', $image_stat['mime']);
    return $_SC['upaiyun_domain'].$collection."/{$pic_id}.{$img_type}!{$size}";
}









